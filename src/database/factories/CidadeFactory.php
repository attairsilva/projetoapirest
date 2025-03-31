<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
// use App\Models\Cidade;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cidade>
 */
class CidadeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $algumascidades = [
            'Cuiabá', 'Várzea Grande', 'Rondonópolis', 'Sinop', 'Tangará da Serra',
            'Cáceres', 'Sorriso', 'Lucas do Rio Verde', 'Primavera do Leste',
            'Barra do Garças', 'Alta Floresta', 'Pontes e Lacerda', 'Campo Verde',
            'Nova Mutum', 'Colíder', 'Guarantã do Norte', 'Juína', 'Peixoto de Azevedo',
            'Paranatinga', 'Juara'
        ];

        return [
            'cid_nome' => $this->faker->randomElement( $algumascidades),
            'cid_uf' => 'MT', // Fixado para Mato Grosso
        ];

    }
}
