<?php

namespace Tests\Feature;

use App\Models\Slide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\common\FileSupporting;
use Tests\TestCase;

class SlideTest extends TestCase
{
    use RefreshDatabase;
    use FileSupporting;

    protected $seed = true;


    public function testIndex()
    {
        $response = $this->get('api/slide');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'header',
                'content',
                'image'
            ]
        ]);
    }

    public function testCreate()
    {
        Storage::fake('public');

        $disk = Storage::disk('public');
        $user = User::whereId(1)->first();
        $inst = [
            'header' => 'hdfghdfghdfgh',
            'content' => 'fhjfghjfghj',
        ];
        $inst['image'] = UploadedFile::fake()->image('town.jpg');

        $response = $this->actingAs($user)->post('api/slide', $inst);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'header',
            'content',
            'image'
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('slides', [
            'id' => $resData['id']
        ]);
        self::assertTrue($this->storageHaveFileInStore($resData['image'], $disk));
    }

    public function testUpdate()
    {
        Storage::fake('public');

        $disk = Storage::disk('public');
        $user = User::whereId(1)->first();
        $currentSlide = Slide::query()->inRandomOrder()->first();
        $newSlide = [
            'header' => 'hdfghdfghdfgh',
            'content' => 'fhjfghjfghj',
        ];
        $newSlide['image' ]= UploadedFile::fake()->image('town.jpg');

        $response = $this->actingAs($user)->putJson('api/slide/' . $currentSlide->id, $newSlide);
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $currentSlide->id,
            'header' => $newSlide['header'],
            'content' => $newSlide['content']
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('slides', [
            'id' => $resData['id'],
            'header' => $newSlide['header'],
            'content' => $newSlide['content']
        ]);
        self::assertTrue($this->storageHaveFileInStore($resData['image'], $disk));
        self::assertFalse($this->storageHaveFileInStore($currentSlide->image, $disk));
    }

    public function testShow()
    {
        $slid = Slide::first();
        $response = $this->get('api/slide/' . $slid->id);

        $response->assertOk();
        $response->assertJsonStructure(
            [
                'id',
                'header',
                'content',
                'image'
            ]
        );
    }

    public function testMultipleDelete()
    {
        $user = User::query()->first();
        $slide = Slide::query()->inRandomOrder()->limit(5)->get();
        $slidId = collect($slide)->map(fn ($record) => $record->id);
        $response = $this->actingAs($user)->deleteJson('api/slide', [
            'id' => $slidId
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('slides', [
            'id' => $slidId
        ]);
    }
}
