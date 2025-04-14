<?php

// database/factories/CourierFactory.php

namespace Database\Factories;

use App\Models\Courier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourierFactory extends Factory
{
    protected $model = Courier::class;

    public function definition()
    {
        return [
            'title' => $this->faker->company(),
            'description' => $this->faker->sentence(),
        ];
    }
}
