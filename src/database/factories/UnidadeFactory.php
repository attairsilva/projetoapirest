<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
// use App\Models\Unidade;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unidade>
 */
class UnidadeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     # remover caractes epesciais  preparar nome unidade
     private function gerasigla(string $nome): string
    {
        $nome_sespcial = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);
        return strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nome_sespcial), 0, 3));
    }

    public function definition(): array
    {
        $nome = $this->faker->unique()->company();
        $sigla = $this-> gerasigla($nome);
        return [
            'unid_nome' =>  $nome,
            'unid_sigla' => $sigla
        ];
        # nomes aleatorios para unidade
        # unid_sigla recebe as tres primeiras letras do nome em maiusculo
    }
}
