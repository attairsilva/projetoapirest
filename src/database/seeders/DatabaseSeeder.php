<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\ServidorEfetivo;
use App\Models\PessoaEndereco;
use App\Models\Endereco;
use App\Models\Cidade;
use App\Models\Lotacao;
use App\Models\ServidorTemporario;
use App\Models\Unidade;
use App\Models\UnidadeEndereco;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::table('users')->insert([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456'),
        ]);

        # Criar 50 cidades aleatorias
        $cidades = Cidade::factory(50)->create(); # gera 100 cidades aleatorias

        # Criar 50 pessoas aleatorias
        $pessoas = Pessoa::factory(50)->create()->each(function ($pessoa) use ($cidades) {

            ServidorEfetivo::create([
                'pes_id' => $pessoa->pes_id,
                'se_matricula' => 'MAT-' . rand(1000, 9999)
            ]);

            $enderecos = Endereco::factory(1)->create([
                'cid_id' => $cidades->random()->cid_id, // Relaciona a uma cidade aleatória
            ]);


            # Criar relacionamento com PessoaEndereco
            PessoaEndereco::create([
                'pes_id' => $pessoa->pes_id,
                'end_id' =>  $enderecos->random()->end_id,
            ]);

        });

        # Criar 50 pessoas aleatorias
        $pessoast = Pessoa::factory(50)->create()->each(function ($pessoa) use ($cidades) {

            ServidorTemporario::create([
                'pes_id' => $pessoa->pes_id,
                'st_data_admissao' => fake()->dateTimeBetween('-1 years', 'now')->format('Y-m-d')
            ]);

            $enderecos = Endereco::factory(1)->create([
                'cid_id' => $cidades->random()->cid_id, // Relaciona a uma cidade aleatória
            ]);


            # Criar relacionamento com PessoaEndereco
            PessoaEndereco::create([
                'pes_id' => $pessoa->pes_id,
                'end_id' =>  $enderecos->random()->end_id,
            ]);

        });


        # Criar 4 unidade aleatorias
        $unidades=Unidade::factory(4)->create()->each(function ($unidade) use ($cidades) {

            $enderecos = Endereco::factory(1)->create([
                'cid_id' => $cidades->random()->cid_id, // Relaciona a uma cidade aleatória
            ]);

            # Criar relacionamento com UnidadeEndereco
            UnidadeEndereco::create([
                'unid_id' => $unidade->unid_id,
                'end_id' => $enderecos->random()->end_id,
                 # fn () cada repetição deve ter um ID diferente
            ]);

        });

        Lotacao::factory(50)->create([
                'unid_id' => fn () => $unidades->random()->unid_id, // Relaciona a uma unidade aleatória
                'pes_id' => fn () => $pessoas->random()->pes_id, // Relaciona a uma pessoa aleatória
                # fn () cada repetição deve ter um ID diferente
        ]);

        Lotacao::factory(50)->create([
            'unid_id' => fn () => $unidades->random()->unid_id, // Relaciona a uma unidade aleatória
            'pes_id' => fn () => $pessoast->random()->pes_id, // Relaciona a uma pessoa aleatória
            # fn () cada repetição deve ter um ID diferente
        ]);




    }
}
