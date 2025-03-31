<?php

namespace App\Http\Controllers;

use App\Models\Lotacao;
use Illuminate\Http\Request;

class LotacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return response()->json(Lotacao::all(), 200);
        return response()->json(Lotacao::paginate(10), 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

                $request->validate([
                    'pes_id' => 'required|exists:pessoa,pes_id', //chave pessoas
                    'unid_id' => 'required|exists:unidade,unid_id', // chave unidades
                    'lot_data_lotacao' => 'required|date',
                    'lot_data_remocao' => 'nullable|date|after_or_equal:lot_data_inicio'
                ]);

                $lotacao = Lotacao::create($request->all());

                return response()->json($lotacao, 201);

            } catch (\Exception $e) {

                return response()->json([
                    'error' => 'Ocorreu um erro ao processar sua requisição',
                    'message' => 'Verifique os campos pes_id, unid_id, se são existentes, ou a data de lotação. ('.$e->getMessage().')',
                ], 500);

            }
        }



    /**
    * Display the specified resource.
    */
    public function show(Request $request)
        {
        try{
            $id=$request->input('lot_id');
            $lotacao = Lotacao::find($id);

            if (!$lotacao) {
                return response()->json(['error' => 'Lotação não encontrada'], 404);
            }

            return response()->json($lotacao, 200);
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



        try{

            $request->validate([
                'pes_id' => 'required|exists:pessoa,pes_id',
                'unid_id' => 'required|exists:unidade,unid_id',
                'lot_data_lotacao' => 'required|date',
                'lot_data_remocao' => 'nullable|date|after_or_equal:lot_data_lotacao' // Corrigido o erro de digitação
            ]);

            $id=$request->input('lot_id');
            $lotacao = Lotacao::find($id);

            if (!$lotacao) {
                return response()->json(['error' => 'Lotação não encontrada'], 404);
            }

            $request->validate([
                'lot_data_inicio' => 'date',
                'lot_data_fim' => 'nullable|date|after_or_equal:lot_data_inicio'
            ]);

            $lotacao->update($request->all());

            return response()->json($lotacao, 200);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Ocorreu um erro ao processar sua requisição',
                'message' => 'Verifique os campos pes_id, unid_id, se são existentes, ou a data de lotação. ('.$e->getMessage().')',
            ], 500);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try{
            $id=$request->input('lot_id');
            $lotacao = Lotacao::find($id);

            if (!$lotacao) {
                return response()->json(['error' => 'Lotação não encontrada'], 404);
            }

            $lotacao->delete();

            return response()->json(['message' => 'Lotação excluída!'], 200);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Ocorreu um erro ao processar sua requisição',
                'message' =>  $e->getMessage(),
            ], 500);

        }
    }
}
