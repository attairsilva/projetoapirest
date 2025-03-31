<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Pessoa;
use App\Models\FotoPessoa;
use Illuminate\Support\Facades\Auth;
use DateTime;
use DateInterval;
use Exception;
use Aws\S3\S3Client;
use Illuminate\Http\UploadedFile;


class UploadController extends Controller
{
            private function gerarUrlTemporariaPersonalizadaV4($nomeArquivo, $expiracaoMinutos = 5)
            {
                $s3Client = new S3Client([
                    'version' => 'latest',
                    'region' => env('AWS_DEFAULT_REGION'), // Use a variável de ambiente
                    'endpoint' => env('AWS_ENDPOINT'), // Use a variável de ambiente
                    'use_path_style_endpoint' => true,
                    'credentials' => [
                        'key' => env('AWS_ACCESS_KEY_ID'), // Use a variável de ambiente
                        'secret' => env('AWS_SECRET_ACCESS_KEY'), // Use a variável de ambiente
                    ],
                    'signature_version' => 'v4', // Configuração da assinatura v4
                ]);

                $bucket = env('AWS_BUCKET');
                $key = "arquivos/{$nomeArquivo}";

                $cmd = $s3Client->getCommand('GetObject', [
                    'Bucket' => $bucket,
                    'Key' => $key,
                ]);

                $request = $s3Client->createPresignedRequest($cmd, "+{$expiracaoMinutos} minutes");

                return (string)$request->getUri();
            }

    public function getTemporaryUrl(Request $request)
    {



            // Obtém o usuário autenticado
            $user = Auth::user();


            $pes_id = $request->input('pes_id');
            // Busca todas as fotos do banco associadas ao pes_id
            $fotos = FotoPessoa::where('pes_id', $pes_id)->get();

            // Verifica se o pes_id tem fotos associadas
            if ($fotos->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma foto encontrada para este pes_id.',
                ], 404);
            }else{


                $urlsTemporarias = [];

                foreach ($fotos as $foto) {

                    $nomeArquivo = $foto->fp_hash;

                    if (!empty($nomeArquivo)) {
                        try {
                            $urlTemporaria = $this->gerarUrlTemporariaPersonalizadaV4($nomeArquivo);
                            $urlsTemporarias[] = $urlTemporaria;
                        } catch (\Exception $e) {
                            $urlsTemporarias[] = null;
                        }
                    } else {

                        $urlsTemporarias[] = null;
                    }
                }

                return response()->json(['urls_fotos' => $urlsTemporarias]);


            }


    }

    public function store(Request $request)
    {
        $erros = [];
        $mensagem = [];

        function armazenarArquivoPersonalizadoV4(UploadedFile $file, $caminho)
        {
             $s3Client = new S3Client([
                    'version' => 'latest',
                    'region' => env('AWS_DEFAULT_REGION'),
                    'endpoint' => env('AWS_ENDPOINT_ENVIA'),
                    'use_path_style_endpoint' => true,
                    'credentials' => [
                        'key' => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    ],
                    'signature_version' => 'v4',
                ]);

                $bucket = env('AWS_BUCKET');
                $conteudoArquivo = file_get_contents($file->getRealPath());
                $hash = substr(hash('md5', $conteudoArquivo), 0, 16);
                // $hash = hash('sha256', $conteudoArquivo); // Gera o hash SHA-256 do conteúdo do arquivo

                $extensao = $file->getClientOriginalExtension(); // Obtém a extensão original do arquivo
                $nomeArquivo = $hash . '.' . $extensao; // Nome do arquivo será o hash + extensão

                try {
                    $s3Client->putObject([
                        'Bucket' => $bucket,
                        'Key' => "{$caminho}/{$nomeArquivo}",
                        'Body' => $conteudoArquivo,
                    ]);

                    return "{$caminho}/{$nomeArquivo}"; // Retorna o caminho do arquivo no S3 (com o hash)
                } catch (Exception $e) {
                    // Lidar com erros
                    return false;
                }

        }

        $pes_id = $request->input('pes_id');
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);

        // checar se a pessoa existe
        $pessoa = Pessoa::find($pes_id);

        if (!$pessoa) {
            $erros[] = 'Esta pessoa não é Servidor Efetivo e nem Temporário, cadastre primeiro.';
        }

        $arquivos = $request->file('image');
        if (!is_array($arquivos)) {
            $arquivos = [$arquivos];
        }

        $salvos = [];
        $cne = 0;
        $cod = 200;
        foreach ($arquivos as $file) {
            try {
                //  arquivo para a função de armazenamento
               // $caminhoArquivo = armazenarArquivoPersonalizadoV4($file, 'arquivos');
               $caminhoArquivo=False;
                if (!$caminhoArquivo) {
                    $cne++;
                    $erros[] = "Erro enviando arquivo [$cne]";
                    $cod = 500;
                } else {
                    $hash = pathinfo($caminhoArquivo, PATHINFO_BASENAME); // Obtém o hash (nome do arquivo) gerado
                    $foto = FotoPessoa::create([
                        'pes_id' => $pes_id,
                        'fp_data' => now(),
                        'fp_bucket' => 'uploads',
                        'fp_hash' => $hash // Salva o hash no banco de dados
                    ]);

                    if ($foto) {
                        $salvos[] = $hash;
                        $mensagem[] = "Arquivo(s) enviado com sucesso!";
                    }
                }
            } catch (\Exception $e) {
                $erros[] = 'Erro ao processar uma das imagens: ' . $e->getMessage();
                $cod = 500;
            }
        }

        return response()->json([
            'message' => $mensagem,
            'error' => $erros,
            'arquivos' => $salvos
        ], $cod);

    }

    public function apagarFotos(Request $request)
    {
        $erros=[];
        $pes_id = $request->input('pes_id');

        // Obtém o usuário autenticado (se necessário)
        $user = Auth::user();

        $fotos = FotoPessoa::where('pes_id', $pes_id)->get();

       if ($fotos->isEmpty()) {
           return response()->json(['message' => 'Nenhuma foto encontrada para este pes_id.'], 404);
       }

       $erros = [];
       $sucessos = [];

       foreach ($fotos as $foto) {
           try {
               // Lógica para apagar o arquivo do S3
               $s3Client = new S3Client([
                   'version' => 'latest',
                   'region' => env('AWS_DEFAULT_REGION'),
                   'endpoint' => env('AWS_ENDPOINT_ENVIA'),
                   'use_path_style_endpoint' => true,
                   'credentials' => [
                       'key' => env('AWS_ACCESS_KEY_ID'),
                       'secret' => env('AWS_SECRET_ACCESS_KEY'),
                   ],
                   'signature_version' => 'v4',
               ]);

               $bucket = env('AWS_BUCKET');
               $caminhoArquivo = 'arquivos/' . $foto->fp_hash; // Caminho do arquivo no S3

               $s3Client->deleteObject([
                   'Bucket' => $bucket,
                   'Key' => $caminhoArquivo,
               ]);

               // Apagar o registro do banco de dados
               $foto->delete();
               $sucessos[] = "Foto {$foto->fp_hash} apagada com sucesso.";

           } catch (\Exception $e) {
               $erros[] = "Erro ao apagar foto {$foto->fp_hash}: " . $e->getMessage();
           }
       }


       if (!empty($sucessos)) {
           $cod=200;
       }
       if (!empty($erros)) {
            $cod=500;
       }

       return response()->json(['sucesso' => $sucessos, 'erros' => $erros], $cod);


    }

    public function apagarFoto(Request $request)
    {
        $erros=[];
        $fp_id = $request->input('fp_id');

        // Obtém o usuário autenticado (se necessário)
        $user = Auth::user();

        $fotos = FotoPessoa::where('fp_id', $fp_id)->get();

       if ($fotos->isEmpty()) {
           return response()->json(['message' => 'Nenhuma foto encontrada para fp_id ['.$fp_id.']'], 404);
       }

       $erros = [];
       $sucessos = [];

       foreach ($fotos as $foto) {
           try {
               // Lógica para apagar o arquivo do S3
               $s3Client = new S3Client([
                   'version' => 'latest',
                   'region' => env('AWS_DEFAULT_REGION'),
                   'endpoint' => env('AWS_ENDPOINT_ENVIA'),
                   'use_path_style_endpoint' => true,
                   'credentials' => [
                       'key' => env('AWS_ACCESS_KEY_ID'),
                       'secret' => env('AWS_SECRET_ACCESS_KEY'),
                   ],
                   'signature_version' => 'v4',
               ]);

               $bucket = env('AWS_BUCKET');
               $caminhoArquivo = 'arquivos/' . $foto->fp_hash; // Caminho do arquivo no S3

               $s3Client->deleteObject([
                   'Bucket' => $bucket,
                   'Key' => $caminhoArquivo,
               ]);

               // Apagar o registro do banco de dados
               $foto->delete();
               $sucessos[] = "Foto {$foto->fp_hash} apagada com sucesso.";

           } catch (\Exception $e) {
               $erros[] = "Erro ao apagar foto {$foto->fp_hash}: " . $e->getMessage();
           }
       }


       if (!empty($sucessos)) {
           $cod=200;
       }
       if (!empty($erros)) {
            $cod=500;
       }

       return response()->json(['sucesso' => $sucessos, 'erros' => $erros], $cod);

    }


}
