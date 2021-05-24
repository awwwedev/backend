<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\common\FileSupporting;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;
    use FileSupporting;

    protected $seed = true;


    public function testIndex()
    {
        $response = $this->get('api/contact');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'value',
                'header',
                'is_rent_department',
                'type'
            ]
        ]);
    }

    public function testCreate()
    {
        $user = User::whereId(1)->first();
        $inst = [
            'value' => 'hjkghjkghjk',
            'header' => 'dfhdfghdfgh',
            'is_rent_department' => false,
            'type' => Contact::TYPE_EMAIL
        ];

        $response = $this->actingAs($user)->post('api/contact', $inst);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'value',
            'header',
            'is_rent_department',
            'type'
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('contacts', [
            'id' => $resData['id']
        ]);
    }

    public function testUpdate()
    {
        $user = User::whereId(1)->first();
        $currentSlide = Contact::query()->inRandomOrder()->first();
        $newInst = [
            'value' => 'hjkghjkghjk',
            'header' => 'dfhdfghdfgh',
            'is_rent_department' => false,
            'type' => Contact::TYPE_EMAIL
        ];

        $response = $this->actingAs($user)->putJson('api/contact/' . $currentSlide->id, $newInst);
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $currentSlide->id,
            'header' => $newInst['header'],
            'value' => $newInst['value'],
            'is_rent_department' => $newInst['is_rent_department'],
        ]);
        $resData = $response->json();
        $this->assertDatabaseHas('contacts', [
            'id' => $resData['id'],
            'header' => $newInst['header'],
            'is_rent_department' => $newInst['is_rent_department'],
            'value' => $newInst['value']
        ]);
    }

    public function testShow()
    {
        $contact = Contact::first();
        $response = $this->get('api/contact/' . $contact->id);

        $response->assertOk();
        $response->assertJsonStructure(
            [
                'id',
                'value',
                'header',
                'is_rent_department',
                'type'
            ]
        );
    }

    public function testMultipleDelete()
    {
        $user = User::query()->first();
        $slide = Contact::query()->inRandomOrder()->limit(5)->get();
        $slidId = collect($slide)->map(fn ($record) => $record->id);
        $response = $this->actingAs($user)->deleteJson('api/contact', [
            'id' => $slidId
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('contacts', [
            'id' => $slidId
        ]);
    }
}
