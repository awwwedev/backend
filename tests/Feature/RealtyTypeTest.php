<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\RealtyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\common\FileSupporting;
use Tests\TestCase;

class RealtyTypeTest extends TestCase
{
    use RefreshDatabase;
    use FileSupporting;

    protected $seed = true;


    public function testIndex()
    {
        $response = $this->get('api/realtyType');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'img_path'
            ]
        ]);
    }

    public function testCreate()
    {
        Storage::fake('public');

        $disk = Storage::disk('public');
        $user = User::whereId(1)->first();
        $inst = [
            'name' => 'гараж'
        ];
        $inst['img_path'] = UploadedFile::fake()->image('town.jpg');

        $response = $this->actingAs($user)->post('api/realtyType', $inst);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'img_path'
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('realty_types', [
            'id' => $resData['id'],
            'name' => $resData['name']
        ]);
        self::assertTrue($this->storageHaveFileInStore($resData['img_path'], $disk));
    }

    public function testUpdate()
    {
        Storage::fake('public');

        $disk = Storage::disk('public');
        $user = User::whereId(1)->first();
        $currentInst = RealtyType::query()->inRandomOrder()->first();
        $newInst = News::factory()->make([
            'name' => 'гараж'
        ]);
        $newInst->img_path= UploadedFile::fake()->image('town.jpg');

        $response = $this->actingAs($user)->putJson('api/realtyType/' . $currentInst->id, $newInst->toArray());
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $currentInst->id,
            'name' => $newInst->name
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('realty_types', [
            'id' => $resData['id'],
            'name' => $resData['name']
        ]);
        self::assertFalse($this->storageHaveFileInStore($newInst->img_path, $disk));
        self::assertTrue($this->storageHaveFileInStore($resData['img_path'], $disk));
    }

    public function testShow()
    {
        $inst = RealtyType::first();
        $response = $this->get('api/realtyType/' . $inst->id);

        $response->assertOk();
        $response->assertJsonStructure(
            [
                'id',
                'name',
                'img_path'
            ]
        );
    }

    // TODO: доработать на случай имеющихся зависимостей
    public function testMultipleDelete()
    {
        $user = User::query()->first();
        $insts = RealtyType::query()->inRandomOrder()->limit(2)->get();
        $instId = collect($insts)->map(fn ($record) => $record->id);
        Log::debug($instId);
        $response = $this->actingAs($user)->deleteJson('api/realtyType', [
            'id' => $instId
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('realty_types', [
            'id' => $instId
        ]);
    }
}
