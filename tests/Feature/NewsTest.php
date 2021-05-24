<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\common\FileSupporting;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;
    use FileSupporting;

    protected $seed = true;


    public function testIndex()
    {
        $response = $this->get('api/news');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'header',
                    'content',
                    'photo'
                ]
            ],
            'meta',
            'links'
        ]);
    }

    public function testCreate()
    {
        Storage::fake('public');

        $disk = Storage::disk('public');
        $user = User::whereId(1)->first();
        $news = News::factory()->make([
            'created_at' => null,
            'updated_at' => null
        ])->toArray();
        $news['photo'] = UploadedFile::fake()->image('town.jpg');

        $response = $this->actingAs($user)->post('api/news', $news);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'header',
            'content',
            'photo'
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('news', [
            'id' => $resData['id']
        ]);
        self::assertTrue($this->storageHaveFileInStore($resData['photo'], $disk));
    }

    public function testUpdate()
    {
        Storage::fake('public');

        $disk = Storage::disk('public');
        $user = User::whereId(1)->first();
        $currentNews = News::query()->inRandomOrder()->first();
        $newNews = News::factory()->make([
            'created_at' => null,
            'updated_at' => null,
            'photo' => '',
        ]);
        $newNews->photo= UploadedFile::fake()->image('town.jpg');

        $response = $this->actingAs($user)->putJson('api/news/' . $currentNews->id, $newNews->toArray());
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $currentNews->id,
            'header' => $newNews->header,
            'content' => $newNews->content
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('news', [
            'id' => $resData['id'],
            'header' => $newNews->header,
            'content' => $newNews->content
        ]);
        self::assertFalse($this->storageHaveFileInStore($newNews->photo, $disk));
        self::assertTrue($this->storageHaveFileInStore($resData['photo'], $disk));
    }

    public function testShow()
    {
        $news = News::first();
        $response = $this->get('api/news/' . $news->id);

        $response->assertOk();
        $response->assertJsonStructure(
            [
                'id',
                'header',
                'content',
                'photo'
            ]
        );
    }

    public function testMultipleDelete()
    {
        $user = User::query()->first();
        $news = News::query()->inRandomOrder()->limit(5)->get();
        $newsId = collect($news)->map(fn ($record) => $record->id);
        $response = $this->actingAs($user)->deleteJson('api/news', [
            'id' => $newsId
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('news', [
            'id' => $newsId
        ]);
    }
}
