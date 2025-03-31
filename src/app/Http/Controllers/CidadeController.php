<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use Illuminate\Http\Request;

class CidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Cidade::all(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cid_nome' => 'required|string|max:255',
            'cid_uf' => 'required|string|max:2'
        ]);

        $cidade = Cidade::create($request->all());

        if (!$cidade) {
            return response()->json(['error' => 'Erro ao registrar'], 404);
        }

        return response()->json($cidade, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cidade = Cidade::find($id);

        if (!$cidade) {
            return response()->json(['error' => 'Cidade não encontrada'], 404);
        }

        return response()->json($cidade, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Cidade $cidade)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cidade = Cidade::find($id);

        if (!$cidade) {
            return response()->json(['error' => 'Cidade não encontrada'], 404);
        }

        $request->validate([
            'cid_nome' => 'string|max:255',
            'cid_estado' => 'string|max:2'
        ]);

        $cidade->update($request->all());

        return response()->json($cidade, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cidade = Cidade::find($id);

        if (!$cidade) {
            return response()->json(['error' => 'Cidade não encontrada'], 404);
        }

        $cidade->delete();

        return response()->json(['message' => 'Cidade deletada com sucesso'], 200);
    }
}
