<?php

namespace Database\Factories;

use App\Models\TicketMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TicketMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-5years');

        return [
            'message' => $this->faker->realText(random_int(20, 40)),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
