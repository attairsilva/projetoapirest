<?php

namespace App\Http\Controllers;

use App\Models\Unidade;
use App\Models\UnidadeEndereco;
use App\Models\Endereco;
use App\Models\Cidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnidadeController extends Controller
{


    //Função para listar os endereços vinculados a Unidade
    public function listarEnderecos($unid_id)
    {
             try{
                $unidade = Unidade::with('enderecos')->find($unid_id);

                if (!$unidade) {
                    return response()->json(['error' => 'Unidade não encontrada'], 404);
                }

                return response()->json($unidade->enderecos, 200);

            } catch (\Exception $e) {

                return response()->json([
                    'error' => 'Ocorreu um erro ao processar sua requisição',
                    'message' =>  $e->getMessage(),
                ], 500);

            }

    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Unidade::paginate(10), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {


                $unidade = Unidade::create($request->input('unidade'));

                $enderecoData =  $request->endereco;

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
                            }

                            // Atualiza o `cid_id` do endereço para o novo ID gerado
                            $enderecoData['cid_id'] = $cidade->cid_id;
                        }
                    }
                }


                // Criando o endereço primeiro
                $endereco = Endereco::create([
                    'end_tipo_logradouro' => $enderecoData['end_tipo_logradouro'],
                    'end_logradouro' => $enderecoData['end_logradouro'],
                    'end_numero' => $enderecoData['end_numero'],
                    'end_bairro' => $enderecoData['end_bairro'],
                    'cid_id' => $enderecoData['cid_id'],
                ]);

                // Relacionando unidade e endereço
                UnidadeEndereco::create([
                    'unid_id' => $unidade->unid_id,
                    'end_id' => $endereco->end_id,
                ]);
                DB::commit();
                return response()->json(['unidade' => $unidade, 'endereco' => $endereco], 201);

            } catch (\Exception $e) {
                DB::rollBack();
              return response()->json([
                    'error' => 'Ocorreu um erro ao processar sua requisição',
                    'message' =>  $e->getMessage(),
                ], 500);


            }

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        try{
            $id=$request->input('unid_id');
            $unidade = Unidade::find($id);

            if (!$unidade) {
                return response()->json(['error' => 'Unidade não encontrada'], 404);
            }

            return response()->json($unidade, 200);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Ocorreu um erro ao processar sua requisição',
                'message' =>  $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id=$request->input('unid_id');

        try {
            // Localizando a unidade
            $unidade = Unidade::findOrFail($id);
            $unidade->update($request->input('unidade'));

            $enderecoData = $request->endereco;

            // Verifica se a cidade existe pelo ID
            $cidade = isset($enderecoData['cid_id']) ? Cidade::find($enderecoData['cid_id']) : null;

            if (!$cidade) {
                if (empty($enderecoData['cidade_nome']) || empty($enderecoData['cidade_uf'])) {
                    return response()->json(['error' => "Cidade ID {$enderecoData['cid_id']} não encontrado, e nenhum nome de cidade e UF foi fornecido."], 422);
                } else {
                    // Verifica se já existe a cidade no estado
                    $cidadeExistente = Cidade::where('cid_nome', $enderecoData['cidade_nome'])
                        ->where('cid_uf', $enderecoData['cidade_uf'])
                        ->first();

                    if ($cidadeExistente) {
                        $enderecoData['cid_id'] = $cidadeExistente->cid_id;
                    } else {
                        $cidade = Cidade::create([
                            'cid_uf' => $enderecoData['cidade_uf'],
                            'cid_nome' => $enderecoData['cidade_nome'],
                        ]);

                        if (!$cidade) {
                            return response()->json(['error' => "Não foi possível cadastrar a cidade '{$enderecoData['cidade_nome']}'."], 500);
                        }

                        $enderecoData['cid_id'] = $cidade->cid_id;
                    }
                }
            }

            // Busca o endereço vinculado
            $unidadeEndereco = UnidadeEndereco::where('unid_id', $unidade->unid_id)->first();

            if (!$unidadeEndereco) {
                return response()->json(['error' => "Vínculo Unidade-Endereço não encontrado."], 404);
            }

            $endereco = Endereco::findOrFail($unidadeEndereco->end_id);

            // Atualiza o endereço
            $endereco->update([
                'end_tipo_logradouro' => $enderecoData['end_tipo_logradouro'],
                'end_logradouro' => $enderecoData['end_logradouro'],
                'end_numero' => $enderecoData['end_numero'],
                'end_bairro' => $enderecoData['end_bairro'],
                'cid_id' => $enderecoData['cid_id'],
            ]);

            DB::commit();
            return response()->json(['unidade' => $unidade, 'endereco' => $endereco], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Ocorreu um erro ao processar sua requisição',
                'message' =>  $e->getMessage(),
            ], 500);


        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try{
            $id=$request->input('unid_id');
            $unidade = Unidade::find($id);

            if (!$unidade) {
                return response()->json(['error' => 'Unidade não encontrada'], 404);
            }

            $unidade->delete();

            return response()->json(['message' => 'Unidade deletada com sucesso'], 200);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Ocorreu um erro ao processar sua requisição',
                'message' =>  $e->getMessage(),
            ], 500);

        }
    }
}
