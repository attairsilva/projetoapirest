<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Aws\S3\S3Client;

class MinioBucketMiddleware
{
    // O método handle é o principal para um middleware em Laravel
    public function handle($request, Closure $next)
    {

        $clientS3= new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION', ''),
            'endpoint' => env('AWS_ENDPOINT', ''),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', ''),
            'credentials' => [
                'key'    =>  env('AWS_ACCESS_KEY_ID', ''),
                'secret' => env('AWS_SECRET_ACCESS_KEY', '')
            ],
        ]);

        try {
            $objects = $clientS3->listObjects([
                    'Bucket' => env('AWS_BUCKET','')
                ]);
            // resto do código
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Falha na conexão com S3',
                'message' => $e->getMessage()
            ], 500);
        }

        // $temporariaLista=[];
        // $i=0;
        // if ($clientS3){ 

        //             $objects = $clientS3->listObjects([
        //                 'Bucket' => env('AWS_BUCKET','')
        //             ]);

        //             foreach ($objects['Contents'] as $object) {
        //                     $objs=$object['Key'];
        //                     $temporariaLista[] = [
        //                         'objeto' => $objs,
        //                      ];
        //             }
        //             return response()->json([
        //                                 'message' =>  $temporariaLista 
        //                             ], 200);
        
                        
        //             //     $result = $clientS3->listBuckets();
        //             //     foreach ($result['Buckets'] as $bucket) {
        //             //         $i=$i+1;
        //             //         $temporariaLista[] = [
        //             //              'objeto' =>  $bucket['Name'],
        //             //          ];
        //             //     }
        //             //     if($i==0){
        //             //         return response()->json([
        //             //                 'message' => 'Erro ao obter Buckets do Minio',
        //             //                 'error' => $error
        //             //             ], 500);
        //             // }else{ 
        //             //             return response()->json(['temporary_urls' => $temporariaList ], 200);
        //             // }
        // }

        

        // $filePath = '/caminho/do/arquivo.txt';
        // $key = 'arquivo.txt';
        // $clientS3->putObject([
        //     'Bucket' => 'meu-bucket',
        //     'Key'    => $key,
        //     'SourceFile' => $filePath
        // ]);

        // $clientS3->getObject([
        //     'Bucket' => 'meu-bucket',
        //     'Key'    => 'arquivo.txt',
        //     'SaveAs' => '/caminho/local/arquivo.txt'
        // ]);

        // $objects = $clientS3->listObjects([
        //     'Bucket' => 'meu-bucket'
        // ]);
        
        // foreach ($objects['Contents'] as $object) {
        //     echo $object['Key'] . "\n";
        // }

        // // Comando para configurar o Minio Client (mc)
        // $command = 'mc alias set myminio http://localhost:9001 admin adminpassword 2>&1';
        // exec($command, $output, $status);

        // if ($status !== 0) {
        //     $error = implode("\n", $output);
        //     error_log('MinIO configuration failed: ' . $error);
        //     return response()->json([
        //         'message' => 'Erro ao configurar o Minio',
        //         'error' => $error
        //     ], 500);
        // }

        // // Comando para criar a pasta no bucket
        // $command = 'mc mkdir myminio/servidor/upload';
        // exec($command, $output, $status);

        // if ($status === 0) {
        //     Log::info('Pasta criada com sucesso no Minio');
        //     return response()->json(['message' => 'Pasta Criada com Sucesso'], 200);
        // } else {
        //     Log::error('Erro ao criar pasta no Minio: ' . implode("\n", $output));
        //     return response()->json(['message' => 'Erro ao criar pasta no Minio'], 500);
        // }

        // Passa a requisição para o próximo middleware ou controlador
        return $next($request);
    }
}
