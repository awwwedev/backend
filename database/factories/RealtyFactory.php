<?php

namespace Database\Factories;

use App\Models\Realty;
use Illuminate\Database\Eloquent\Factories\Factory;

class RealtyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Realty::class;

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
        $price_per_metr = $this->faker->randomFloat(2, 100, 1000);
        $area = $this->faker->randomFloat(2, 100, 1000);

        return [
            'description' => $this->faker->unique()->realText(700),
            'name' => $this->faker->unique()->realText(50),
            'area' => $area,
            'price' => $price_per_metr * $area,
            'price_per_metr' => $price_per_metr,
            'latitude' => mt_rand( 44450000, 44700000) / 1000000,
            'longitude' => mt_rand( 33450000, 33700000) / 1000000,
            'short_description' => $this->faker->unique()->realText(),
            'user_id' => 1,
            'is_published' => $this->faker->boolean(),
            'img_path' => $this->faker->randomElement($photos),
            'type_id' => random_int(1, 4),
            'photo'=> $this->faker->randomElements($photos, 3),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
