<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            [
                RoleSeeder::class,
                UsersSeeder::class,
                NewsSeeder::class,
                SlidesSeeder::class,
                ContactsSeeder::class,
                RealtyTypeSeeder::class,
                EquipmentSeeder::class,
                RealtySeeder::class,
                Object1cSeeder::class
            ]
        );
    }
}
