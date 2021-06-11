<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $photos = [
            '/storage/image/image.jpg',
            '/storage/image/town.png',
            '/storage/image/town1.png',
            '/storage/image/town2.png',
            '/storage/image/town3.png',
            '/storage/image/town4.png',
        ];
        $date = $this->faker->dateTimeBetween('-5years');

        return [
            'header' => $this->faker->unique()->realText(70),
            'content' => $this->faker->unique()->realText(),
            'short_description' => $this->faker->unique()->realText(),
            'user_id' => 1,
            'photo' => $this->faker->randomElement($photos),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
