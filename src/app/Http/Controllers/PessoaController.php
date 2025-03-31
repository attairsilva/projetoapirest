<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use Illuminate\Http\Request;

class PessoaController extends Controller
{
    //Função para associar um endereço a uma pessoa
    public function adicionarEndereco(Request $request, $pes_id)
    {
        $request->validate([
            'end_id' => 'required|exists:enderecos,end_id'
        ]);

        $pessoa = Pessoa::find($pes_id);

        if (!$pessoa) {
            return response()->json(['error' => 'A pessoa não foi encontrada'], 404);
        }

        $pessoa->enderecos()->attach($request->end_id);

        return response()->json(['message' => 'Endereço atribuido a pessoa!']);
    }

    // Função para desassociar um endereço da pessoa
    public function desassociarEndereco($pes_id, $end_id)
    {
        $pessoa = Pessoa::find($pes_id);

        if (!$pessoa) {
            return response()->json(['error' => 'Pessoa não localizada'], 404);
        }

        // Verifica se a relação existe antes de remover
        if (!$pessoa->enderecos()->where('pessoa_endereco.end_id', $end_id)->exists()) {
            return response()->json(['error' => 'Endereço não está associado a pessoa'], 404);
        }

        $pessoa->enderecos()->detach($end_id);

        return response()->json(['message' => 'Endereço desassociados da pessoa!'], 200);
    }

    // Função para listar os endereços da pessoa
    public function listarEnderecos($pes_id)
    {
        $pessoa = Pessoa::with('enderecos')->find($pes_id);

        if (!$pessoa) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        }

        return response()->json($pessoa->enderecos, 200);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Pessoa::all(), 200);
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
            'pes_nome' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|in:M,F',
            'pes_mae' => 'nullable|string|max:255',
            'pes_pai' => 'nullable|string|max:255',
        ]);

        $pessoa = Pessoa::create($request->all());

        return response()->json($pessoa, 201);
    }

    # ver pessoa
    public function show($id)
    {
        $pessoa = Pessoa::find($id);

        if (!$pessoa) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        }

        return response()->json($pessoa, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Pessoa $pessoa)
    // {
    //     //
    // }

    # atualizar pessoa
    public function update(Request $request, $id)
    {
        $pessoa = Pessoa::find($id);

        if (!$pessoa) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        }

        $request->validate([
            'pes_nome' => 'string|max:255',
            'pes_data_nascimento' => 'date',
            'pes_sexo' => 'in:M,F',
            'pes_mae' => 'nullable|string|max:255',
            'pes_pai' => 'nullable|string|max:255',
        ]);

        $pessoa->update($request->all());

        return response()->json($pessoa, 200);
    }

    # deletar pessoa
    public function destroy($id)
    {
        $pessoa = Pessoa::find($id);

        if (!$pessoa) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        }

        $pessoa->delete();

        return response()->json(['message' => 'Pessoa excluída com sucesso'], 200);
    }
}
