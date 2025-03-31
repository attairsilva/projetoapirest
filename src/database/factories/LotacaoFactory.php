<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
// use App\Models\Lotacao;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lotacao>
 */
class LotacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $data_lotacao = $this->faker->dateTimeBetween('-5 years', 'now'); # últimos 5 anos
        $data_remocao = $this->faker->dateTimeBetween($data_lotacao, '+1 years'); # posterior à lotação 1 ano

        return [
            'lot_data_lotacao' => $data_lotacao->format('Y-m-d'),
            'lot_data_remocao' => $data_remocao->format('Y-m-d'),
            'lot_portaria' => rand(100, 999). '/' . rand(2020, 2025),
            # portaria em um range de 100 a 999 / ano 2020 a 2025
        ];
    }
}
