<?php

namespace App\Http\Controllers;

use App\Models\ServidorEfetivo;
use Illuminate\Http\Request;
use App\Models\Pessoa;
use Illuminate\Support\Facades\DB;
use App\Models\Endereco;
use App\Models\PessoaEndereco;
use App\Models\Unidade;
use App\Models\Lotacao;
use App\Models\Cidade;
use App\Models\FotoPessoa;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Carbon\Carbon;


class ServidorEfetivoController extends Controller
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

    # função para listar os servidores efetivos de uma Unidade
    public function listarPorUnidade(Request $request)
    {

            $unid_id = $request->input('unid_id');

            $servidores = ServidorEfetivo::whereHas('lotacao', function ($query) use ($unid_id) {
                    $query->where('unid_id', $unid_id);
                })->with(['pessoa', 'lotacao.unidade'])->paginate(10);

            //  idade, nome, unidade e foto
            $servidoresFormatados = $servidores->map(function ($servidor) {

            // Calcular a idade
            $idade = Carbon::parse($servidor->pessoa->pes_data_nascimento)->age;

            // Buscar a foto mais recente do servidor
            $foto = DB::table('foto_pessoa')
                ->where('pes_id', $servidor->pessoa->pes_id)
                ->orderByDesc('fp_id') // Selecione a última foto cadastrada
                ->first();

            return [
                'nome' => $servidor->pessoa->pes_nome,
                'idade' => $idade,
                'unidade_lotacao' => $servidor->lotacao->unidade->nome ?? 'Não informado',
                'fotografia' => $foto ? $this->gerarUrlTemporariaPersonalizadaV4($foto->fp_hash) : null,
                // Gera URL da foto ou retorna null
            ];
        });

        return response()->json([
            'current_page' => $servidores->currentPage(),
            'data' => $servidoresFormatados,
            'first_page_url' => $servidores->url(1),
            'from' => $servidores->firstItem(),
            'last_page' => $servidores->lastPage(),
            'last_page_url' => $servidores->url($servidores->lastPage()),
            'next_page_url' => $servidores->nextPageUrl(),
            'path' => $servidores->path(),
            'per_page' => $servidores->perPage(),
            'prev_page_url' => $servidores->previousPageUrl(),
            'to' => $servidores->lastItem(),
            'total' => $servidores->total(),
        ], 200);

    }

    # função para buscar o endereço funcional de servidor efetivo
    public function buscarEnderecoPorNome(Request $request)
    {
                $nome=$request->input('nome');
                # verfica o campo nome informado
                 if (!$nome) {
                    return response()->json(['error' => 'Parâmetro nome obrigatório.'], 400);
                }

                #  servidores efetivos por nome
                $servidores = ServidorEfetivo::whereIn('pes_id', function ($query) use ($nome) {
                    $query->select('pes_id')
                        ->from('pessoa')
                        ->whereRaw("LOWER(pes_nome) LIKE LOWER(?)", ["%{$nome}%"]);
                })
                ->with(['pessoa', 'lotacao.unidade.enderecos'])
                ->paginate(10);


                return response()->json($servidores, 200);

    }


    # Listar Servidores Efetivos.
    public function index()
    {
        $servidores = ServidorEfetivo::with([
            'pessoa',
            'pessoa.endereco',
            'lotacao',
            'foto'
        ])->paginate(10);

        return response()->json($servidores);

    }

    # Novo Servidor Efetivo

    public function store(Request $request)
    {

        $erros = []; // Array para armazenar os erros

        DB::beginTransaction();
        try {
            # Nova Pessoa
            $pessoa = Pessoa::create($request->input('pessoa'));

            # Lança Efetivo
            $servidor = new ServidorEfetivo([
                'pes_id' => $pessoa->pes_id,
                'se_matricula' => $request->input('servidor_efetivo.se_matricula')
            ]);
            $servidor->save();

        // Endereços da pessoa, se fornecidos
        if ($request->has('endereco')) {
            $enderecoData = $request->endereco;

            // Verifica se a cidade existe pelo ID
            $cidade = Cidade::where('cid_id', $enderecoData['cid_id'])->first();

            if (!$cidade) {
                // Verifica se há informações para cadastrar uma nova cidade
                if (empty($enderecoData['cidade_nome']) || empty($enderecoData['cidade_uf'])) {
                    $erros[] = "Cidade ID {$enderecoData['cid_id']} não encontrado, e nenhum nome de cidade e UF foi fornecido.";
                } else {
                    // Verifica se a cidade já existe no mesmo estado
                    $cidadeExistente = Cidade::where('cid_nome', $enderecoData['cidade_nome'])
                        ->where('cid_uf', $enderecoData['cidade_uf'])
                        ->first();

                    if ($cidadeExistente) {
                        // Se a cidade já existir no mesmo estado, adiciona um erro e não insere o endereço
                        $erros[] = "A cidade '{$enderecoData['cidade_nome']}' já está cadastrada no estado '{$enderecoData['cidade_uf']}' (ID: {$cidadeExistente->cid_id}). Utilize o ID informado no campo cid_id. Endereço não inserido.";
                    } else {
                        // Cria a nova cidade
                        $cidade = Cidade::create([
                            'cid_uf' => $enderecoData['cidade_uf'],
                            'cid_nome' => $enderecoData['cidade_nome'],
                        ]);

                        if (!$cidade) {
                            $erros[] = "Não foi possível cadastrar a cidade '{$enderecoData['cidade_nome']}'.";
                        } else {
                            // Atualiza o `cid_id` do endereço para o novo ID gerado
                            $enderecoData['cid_id'] = $cidade->cid_id;
                        }
                    }
                }
            }

            // Se a cidade não foi criada ou já existia, interrompe a inserção do endereço
            if (!isset($cidade) || in_array("A cidade '{$enderecoData['cidade_nome']}' já está cadastrada", $erros)) {
                $erros[] = "Não foi possível criar o endereço, cidade não foi criada, ou já existe uma cidade com este nome cadastrado, utilize o ID da Cidade, obtenha em rota /Cidades";
            }else{

                // Remove os campos extras que não pertencem à tabela endereco
                unset($enderecoData['cidade_nome'], $enderecoData['cidade_uf']);

                //  cria o endereço
                $novoEndereco = $servidor->pessoa->endereco()->create($enderecoData);
                if (!$novoEndereco) {
                    $erros[] = "Não foi possível criar o endereço";
                }


            }


        }

        // lotação e unidade se fornecido, verifica existencia de unid_id
        if ($request->has('lotacao')) {
            $lotacaoData = $request->lotacao;
            $unidadeExiste = Unidade::where('unid_id', $lotacaoData['unid_id'])->exists();
            if ($unidadeExiste) {
                $servidor->lotacao()->updateOrCreate(
                    ['pes_id' => $servidor->pes_id, 'unid_id' => $lotacaoData['unid_id']],
                    $lotacaoData
                );
            } else {
                $erros[] = "Lotação não atualizada: unidade ID {$lotacaoData['unid_id']} não existe. Unidade não encontrada, utilize o ID correto da Unidade, obtenha relação realizando um Get para a rota /Unidade";
            }
        }

            DB::commit();


            // Recarregar o modelo com os relacionamentos para retornar os dados atualizados
            $servidorLancado = ServidorEfetivo::with(['pessoa', 'pessoa.endereco', 'lotacao'])->find($pessoa->pes_id);
            return response()->json([
                "message" => "Servidor Inserido",
                "servidor" => $servidor,
                "review" => $servidorLancado,
                'erros' => $erros
            ], 201);


        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "error" => "Erro ao tentar registrar dados do Servidor Efetivo",
                "message" => $e->getMessage()
            ], 500);
        }

    }

    # Buscar um Servidor Efetivo pelo ID.
    public function show(Request $request)
    {
        $id=$request->input('pes_id');
        $erros = []; // Array para armazenar os erros
         $servidor = ServidorEfetivo::with([
            'pessoa',
            'pessoa.endereco',
            'lotacao',
            'foto'
        ])->find($id);;

        if (!$servidor) {
            $erros[] = 'Servidor não encontrado para o ID informado';
        }

        // return response()->json($servidor, 200);
        return response()->json([
            'message' => 'Busca por Servidor Efetivo',
            'servidor' => $servidor,
            'erros' => $erros
        ], 200);

    }

    # Atualizar um Servidor Efetivo.
    public function update(Request $request)
    {
            $id=$request->input('pes_id');
            $erros = []; // Array para armazenar os erros

            $servidor = ServidorEfetivo::with(['pessoa', 'pessoa.endereco', 'lotacao', 'foto'])->find($id);

            if (!$servidor) {
                return response()->json(['error' => 'Servidor não encontrado'], 404);
            }

            # Edita dados do servidor
            $servidor->update($request->only(['se_matricula']));

            // Atualizar os dados da pessoa associada
            if ($request->has('pessoa')) {
                $servidor->pessoa->update($request->pessoa);
            }



            // Atualiza endereço da pessoa, se fornecido
            if ($request->has('pessoa.endereco')) {
                foreach ($request->pessoa['endereco'] as $enderecoData) {

                    // Verifica se a cidade existe pelo ID
                    $cidade = isset($enderecoData['cid_id']) ? Cidade::find($enderecoData['cid_id']) : null;

                    if (!$cidade) {
                        // Verifica se há informações para cadastrar uma nova cidade
                        if (empty($enderecoData['cidade_nome']) || empty($enderecoData['cidade_uf'])) {
                            $erros[] = "Cidade ID {$enderecoData['cid_id']} não encontrado, e nenhum nome de cidade e UF foi fornecido.";
                        } else {
                            // Verifica se a cidade já existe no mesmo estado
                            $cidadeExistente = Cidade::where('cid_nome', $enderecoData['cidade_nome'])
                                ->where('cid_uf', $enderecoData['cidade_uf'])
                                ->first();

                            if ($cidadeExistente) {
                                // Cidade já existe, então usamos o ID existente
                                $enderecoData['cid_id'] = $cidadeExistente->cid_id;
                            } else {
                                // Cria a nova cidade
                                $cidade = Cidade::create([
                                    'cid_uf' => $enderecoData['cidade_uf'],
                                    'cid_nome' => $enderecoData['cidade_nome'],
                                ]);

                                if (!$cidade) {
                                    $erros[] = "Não foi possível cadastrar a cidade '{$enderecoData['cidade_nome']}'.";
                                    continue;
                                }

                                // Atualiza o `cid_id` do endereço para o novo ID gerado
                                $enderecoData['cid_id'] = $cidade->cid_id;
                            }
                        }
                    }


                    // Remove campos extras que não pertencem à tabela endereco
                    unset($enderecoData['cidade_nome'], $enderecoData['cidade_uf']);

                    // Obtém o último endereço cadastrado da pessoa
                    $ultimoEndereco = $servidor->pessoa->endereco()->latest('end_id')->first();

                    // Se end_id não for informado, usa o último cadastrado
                    if (empty($enderecoData['end_id']) && $ultimoEndereco) {
                        $enderecoData['end_id'] = $ultimoEndereco->end_id;
                        // Verifica se o endereço existe para atualização
                        $endereco = $servidor->pessoa->endereco()->where('endereco.end_id', $enderecoData['end_id'])->first();
                        if ($endereco) {
                            // Atualiza endereço existente
                            $endereco->update($enderecoData);
                        }else{
                            $erros[] = "Não foi possível atualizar o endereço.";
                        }
                    }else{
                               // Se `end_id` não foi fornecido e não há um endereço anterior, cria um novo endereço
                                $novoEndereco = Endereco::create($enderecoData);
                                if (!$novoEndereco) {
                                    $erros[] = "Não foi possível criar o endereço.";
                                } else {
                                    // Cria a relação na tabela `pessoa_endereco`
                                    $servidor->pessoa->endereco()->attach($novoEndereco->end_id);
                                }
                    }
                }
            }

            // Atualizar lotação e unidade
            if ($request->has('lotacao')) {
                $lotacaoData = $request->lotacao;;
                $unidadeExiste = Unidade::where('unid_id', $lotacaoData['unid_id'])->exists();
                if (!$unidadeExiste) {
                    $erros[] = "Unidade não encontrada, utilize o ID correto da Unidade, obtenha relação realizando um Get para a rota /Unidades";
                }else{
                    $servidor->lotacao()->updateOrCreate(
                        [
                            'pes_id' => $servidor->pes_id,
                            'unid_id' => $lotacaoData['unid_id']],
                            $lotacaoData
                    );
                    if(!$servidor){
                        $erros[] = "Unidade não encontrada, utilize o ID correto da Unidade, obtenha relação realizando um Get para a rota /Unidades";
                    }
                }

            }

                // Recarregar o modelo com os relacionamentos para retornar os dados atualizados
                $servidorAtualizado = ServidorEfetivo::with(['pessoa', 'pessoa.endereco', 'lotacao'])->find($id);

                return response()->json([
                            'message' => 'Servidor atualizado com sucesso, situacao atual no banco',
                            'servidor' => $servidorAtualizado,
                            'erros' => $erros
                 ], 200);


    }

    # Deletar um Servidor Efetivo.

    public function destroy(Request $request)
    {

        $id=$request->input('pes_id');
        $servidor = ServidorEfetivo::find($id);
        if (!$servidor) {
            return response()->json(['error' => 'Servidor não encontrado'], 404);
        }


        // Encontrar todos os end_id relacionados ao pes_id em PessoaEndereco
        $enderecosIds = PessoaEndereco::where('pes_id', $id)->pluck('end_id');

        // Deletar registros relacionados em PessoaEndereco
        PessoaEndereco::where('pes_id', $id)->delete();

        // Deletar os registros correspondentes na tabela Endereco
        if ($enderecosIds->isNotEmpty()) {
            Endereco::whereIn('end_id', $enderecosIds)->delete();
        }

        $fotos = FotoPessoa::where('pes_id', $id)->get();

        if (!$fotos->isEmpty()) {
            foreach ($fotos as $foto) {
                $hash = pathinfo($foto->hash, PATHINFO_BASENAME);
                Storage::disk('s3')->delete("{$hash}"); // Exclui do MinIO
                $foto->delete(); // Exclui do banco
            }
        }

        FotoPessoa::where('pes_id', $id)->delete();

        // Agora deletar o servidor
        $servidor->delete();

        return response()->json(['message' => 'Servidor deletado com sucesso'], 200);
    }
}
