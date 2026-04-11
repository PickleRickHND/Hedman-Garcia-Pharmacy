<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => 'SKU-'.strtoupper(fake()->unique()->lexify('?????').fake()->numberBetween(1000, 9999)),
            'name' => ucfirst(fake()->words(2, true)),
            'description' => fake()->sentence(),
            'stock' => fake()->numberBetween(10, 200),
            'price' => fake()->randomFloat(2, 10, 500),
            'expiration_date' => fake()->dateTimeBetween('+6 months', '+3 years'),
            'presentation' => fake()->randomElement(['Tableta', 'Capsula', 'Jarabe', 'Crema']),
            'administration_form' => fake()->randomElement(['Oral', 'Topica', 'Inhalada']),
            'storage' => 'Temperatura ambiente',
            'packaging' => 'Caja',
        ];
    }

    public function lowStock(): static
    {
        return $this->state(['stock' => $this->faker->numberBetween(0, 5)]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }

    public function expiringSoon(): static
    {
        return $this->state(['expiration_date' => now()->addDays(10)]);
    }

    public function expired(): static
    {
        return $this->state(['expiration_date' => now()->subDays(10)]);
    }
}
