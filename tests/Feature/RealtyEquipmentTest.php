<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\RealtyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\common\FileSupporting;
use Tests\TestCase;

class RealtyEquipmentTest extends TestCase
{
    use RefreshDatabase;
    use FileSupporting;

    protected $seed = true;


    public function testIndex()
    {
        $response = $this->get('api/equipment');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'realty_type_id'
            ]
        ]);
    }

    public function testCreate()
    {
        $user = User::whereId(1)->first();
        $type = RealtyType::query()->inRandomOrder()->first();
        $inst = [
            'name' => 'гараж',
            'realty_type_id' => $type->id
        ];

        $response = $this->actingAs($user)->post('api/equipment', $inst);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'realty_type_id'
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('equipment', [
            'id' => $resData['id'],
            'name' => $resData['name'],
            'realty_type_id' => $type->id
        ]);
    }

    public function testUpdate()
    {
        $user = User::whereId(1)->first();
        $type = RealtyType::query()->inRandomOrder()->first();
        $currentInst = Equipment::query()->inRandomOrder()->first();
        $newInst = [
            'name' => 'гараж',
            'realty_type_id' => $type->id
        ];

        $response = $this->actingAs($user)->putJson('api/equipment/' . $currentInst->id, $newInst);
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $currentInst->id,
            'name' => $newInst['name'],
            'realty_type_id' => $type->id
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('equipment', [
            'id' => $resData['id'],
            'name' => $resData['name'],
            'realty_type_id' => $resData['realty_type_id']
        ]);
    }

    public function testShow()
    {
        $inst = Equipment::first();
        $response = $this->get('api/equipment/' . $inst->id);

        $response->assertOk();
        $response->assertJsonStructure(
            [
                'id',
                'name',
                'realty_type_id'
            ]
        );
    }

    // TODO: доработать на случай имеющихся зависимостей
    public function testMultipleDelete()
    {
        $user = User::query()->first();
        $insts = Equipment::query()->inRandomOrder()->limit(2)->get();
        $instId = collect($insts)->map(fn ($record) => $record->id);
        $response = $this->actingAs($user)->deleteJson('api/equipment', [
            'id' => $instId
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('equipment', [
            'id' => $instId
        ]);
    }
}
