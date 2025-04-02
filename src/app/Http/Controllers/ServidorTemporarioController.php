<?php

namespace App\Http\Controllers;

use App\Models\ServidorTemporario;
use Illuminate\Http\Request;
use App\Models\Pessoa;
use Illuminate\Support\Facades\DB;
use App\Models\Endereco;
use App\Models\PessoaEndereco;
use App\Models\Cidade;
use App\Models\Lotacao;
use App\Models\Unidade;
use App\Models\FotoPessoa;
use Illuminate\Support\Facades\Storage;

class ServidorTemporarioController extends Controller
{

    public function index()
    {
        $servidores = ServidorTemporario::with([
            'pessoa',
            'pessoa.endereco',
            'lotacao',
            'foto'
        ])->paginate(10);

        return response()->json($servidores);

    }


    public function store(Request $request)
    {
        $erros = []; // Array para armazenar os erros

        DB::beginTransaction();
        try {
            # Nova Pessoa
            $pessoa = Pessoa::create($request->input('pessoa'));

            # Lança Temporario
            $servidor = new ServidorTemporario([
                'pes_id' => $pessoa->pes_id,
                'st_data_admissao' => $request->input('servidor_temporario.st_data_admissao'),
                'st_data_demissao' => $request->input('servidor_temporario.st_data_demissao')
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
                            // Verifica se cidade já existe no mesmo estado
                            $cidadeExistente = Cidade::where('cid_nome', $enderecoData['cidade_nome'])
                                ->where('cid_uf', $enderecoData['cidade_uf'])
                                ->first();

                            if ($cidadeExistente) {
                                // adiciona um erro e não insere o endereço se a cidade já existir no mesmo estado
                                $erros[] = "A cidade '{$enderecoData['cidade_nome']}' já está cadastrada no estado '{$enderecoData['cidade_uf']}' (ID: {$cidadeExistente->cid_id}). Utilize o ID informado no campo cid_id. Endereço não inserido.";
                            } else {

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

                    // interrompe a inserção do endereço  se a cidade não foi criada ou já existe,
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
                    $erros[] = "Unidade não encontrada, utilize o ID correto da Unidade, obtenha relação realizando um Get para a rota /Unidade";
                }
            }


            DB::commit();

            // Carrega modelo e retorna os dados atualizados
            $servidorLancado = ServidorTemporario::with(['pessoa', 'pessoa.endereco', 'lotacao'])->find($pessoa->pes_id);
            return response()->json([
                "message" => "Servidor Inserido",
                "servidor" => $servidor,
                "review" => $servidorLancado,
                'erros' => $erros
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "error" => "Erro ao tentar registrar dados",
                "message" => $e->getMessage()
            ], 500);
        }
    }


    public function show(Request $request)
    {
        $id=$request->input('pes_id');
        $erros = [];
        $servidor = ServidorTemporario::with([
            'pessoa',
            'pessoa.endereco',
            'lotacao',
            'foto'
        ])->find($id);;

        if (!$servidor) {
            $erros[] = 'Servidor não encontrado para o ID informado';
        }


        return response()->json([
            'message' => 'Busca por Servidor Temporario',
            'servidor' => $servidor,
            'erros' => $erros
        ], 200);

    }


    public function update(Request $request)
    {
        $id=$request->input('pes_id');

        $erros = [];

        $servidor = ServidorTemporario::with(['pessoa', 'pessoa.endereco', 'lotacao', 'foto'])->find($id);

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

            // Carrega modelo e retornar os dados atualizados
            $servidorAtualizado = ServidorTemporario::with(['pessoa', 'pessoa.endereco', 'lotacao'])->find($id);

            return response()->json([
                        'message' => 'Servidor atualizado com sucesso, situacao atual no banco',
                        'servidor' => $servidorAtualizado,
                        'erros' => $erros
             ], 200);


    }


    public function destroy(Request $request)
    {
        $id = $request->input('pes_id');
        $servidor = ServidorTemporario::where('pes_id', $id)->first();

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
        // Exclui do MinIO
        if (!$fotos->isEmpty()) {
            foreach ($fotos as $foto) {
                $hash = pathinfo($foto->hash, PATHINFO_BASENAME);
                Storage::disk('s3')->delete("{$hash}");
                $foto->delete(); // Exclui do banco
            }
        }

        FotoPessoa::where('pes_id', $id)->delete();

        //   deletar o servidor
        $servidor->delete();

        return response()->json(['message' => 'Servidor deletado com sucesso'], 200);

    }
}
