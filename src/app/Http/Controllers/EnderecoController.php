<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{

    public function index()
    {
        return response()->json(Endereco::all(), 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            'cid_id' => 'required|exists:cidades,cid_id', // chave estrangeira cidade
            'end_tipo_logradouro' => 'required|string|max:50',
            'end_logradouro' => 'required|string|max:255',
            'end_numero' => 'nullable|string|max:10',
            'end_bairro' => 'required|string|max:100',
            'end_complemento' => 'nullable|string|max:255'
        ]);

        $endereco = Endereco::create($request->all());

        return response()->json($endereco, 201);
    }


    public function show($id)
    {
        $endereco = Endereco::find($id);

        if (!$endereco) {
            return response()->json(['error' => 'Endereço não encontrado'], 404);
        }

        return response()->json($endereco, 200);
    }


    public function update(Request $request, $id)
    {
        $endereco = Endereco::find($id);

        if (!$endereco) {
            return response()->json(['error' => 'Endereço não encontrado'], 404);
        }

        $request->validate([
            'cid_id' => 'exists:cidades,cid_id',
            'end_cep' => 'string|max:10',
            'end_logradouro' => 'string|max:255',
            'end_numero' => 'nullable|string|max:10',
            'end_bairro' => 'string|max:100',
            'end_complemento' => 'nullable|string|max:255'
        ]);

        $endereco->update($request->all());

        return response()->json($endereco, 200);
    }

    public function destroy($id)
    {
        $endereco = Endereco::find($id);

        if (!$endereco) {
            return response()->json(['error' => 'Endereço não encontrado'], 404);
        }

        $endereco->delete();

        return response()->json(['message' => 'Endereço deletado com sucesso'], 200);
    }
}
