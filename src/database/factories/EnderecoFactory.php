<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
// use App\Models\Endereco;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Endereco>
 */
class EnderecoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'end_tipo_logradouro' => $this->faker->randomElement(['Rua', 'Avenida', 'Travessa', 'Alameda', 'Estrada', 'Rodovia','Praça']),
            'end_logradouro' => $this->faker->streetName(), // Gerando um nome real de logradouro
            'end_numero' => rand(1, 9999),
            'end_bairro' => $this->faker->randomElement([
                'Centro', 'Jardim União','Boa Esperança', 'Parque Industrial',
                'Residencial São Francisco', 'Alto da Glória', 'Morada do Sol', 'Santa Rosa'
            ]),
        ];
    }
}
