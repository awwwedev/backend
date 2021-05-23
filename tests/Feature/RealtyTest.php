<?php

namespace Tests\Feature;

use App\Models\Realty;
use App\Models\RealtyEquipment;
use App\Models\RealtyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\URL;

class RealtyTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

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

    public function testMap()
    {
        $response = $this->get('api/realty/map');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id'
                ]
            ]
        ]);
    }

    public function testFiltersTypes()
    {
        $type = RealtyType::query()->first('id');
        $url = URL::to('api/realty/map?types[]=' . $type->id);

        $response = $this->get($url);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description'
                ]
            ]
        ]);
        $data = collect($response->json()['data']);

        self::assertTrue($data->every(fn($item) => $item['type_id'] === 1));
    }

    public function testFiltersEquipments()
    {
        $equips = collect(RealtyEquipment::query()->limit(2)->select('id')->get()->toArray())->map(fn ($value) => $value['id']);
        $url = URL::to('api/realty/map?' . $equips->map(fn ($equipId) => "equipments[]=" . $equipId)->join('&'));
        $response = $this->get($url);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description'
                ]
            ]
        ]);
        $data = collect($response->json()['data']);

        self::assertTrue($data->every(function($item) use ($equips) {
            $resEquips = collect($item['equipments'])->map(fn($_item) => $_item['id']);
            return $resEquips->contains(fn($value) => $value === $equips[0] or $value === $equips[1]);
        }));
    }

    public function testIndex()
    {
        $response = $this->get('api/realty');

        $response->assertOk();
        Log::debug($response->json());
        $response->assertJsonStructure([
            'data',
            'meta',
            'links'
        ]);
    }

    public function testShow()
    {
        $realty = Realty::first();
        $response = $this->get('api/realty/' . $realty->id);

        $response->assertOk();
        $response->assertJsonStructure(
            [
                'id',
                'name',
                'description'
            ]
        );
    }

    public function testDelete()
    {
        $user = User::query()->first();
        $realty = Realty::query()->first();
        $response = $this->actingAs($user)->deleteJson('api/realty/' . $realty->id);

        $response->assertOk();
        $this->assertDatabaseMissing('realties', [
            'id' => $realty->id
        ]);
    }

    public function testMultipleDelete()
    {
        $user = User::query()->first();
        $realties = Realty::query()->inRandomOrder()->limit(5)->get();
        $restiesId = collect($realties)->map(fn ($record) => $record->id);
        $response = $this->actingAs($user)->deleteJson('api/realty', [
            'id' => $restiesId
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('realties', [
            'id' => $restiesId
        ]);
    }
}
