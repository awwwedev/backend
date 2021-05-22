<?php

namespace Tests\Feature;

use App\Models\Realty;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RealtyTest extends TestCase
{
    use RefreshDatabase;

    public function testMinMax()
    {
        $response = $this->get('api/realty/minMax');

        $response->assertJsonStructure([
            'pricePerMetrMin',
            'pricePerMetrMax',
            'priceMin',
            'priceMax',
            'areaMin',
            'areaMax'
        ]);
    }
    public function testCreate()
    {
        (new DatabaseSeeder)->run();
        Storage::fake('public');

        $disk = Storage::disk('public');
        $user = User::whereId(1)->first();
        $realty = Realty::factory()->make([
            'created_at' => null,
            'updated_at' => null
        ])->toArray();
        $realty['photo'] = [ UploadedFile::fake()->image('town1.jpg'), UploadedFile::fake()->image('town2.jpg') ];
        $realty['img_path'] = UploadedFile::fake()->image('town.jpg');

        $response = $this->actingAs($user)->post('api/realty', $realty);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id'
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('realties', [
            'id' => $resData['id']
        ]);
        self::assertTrue($disk->exists(str_replace('/storage/', '', $resData['img_path'])));
        self::assertTrue($disk->exists(str_replace('/storage/', '', $resData['photo'][0])));
        self::assertTrue($disk->exists(str_replace('/storage/', '', $resData['photo'][1])));
    }
}
