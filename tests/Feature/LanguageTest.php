<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    #[Test]
    public function it_can_list_languages()
    {
        Language::factory()->count(15)->create();

        $this->authenticate();

        $response = $this->getJson(route('languages.index'));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data',
                     'links',
                     'meta',
                 ]);

        $this->assertCount(10, $response->json('data')); // paginate 10
    }

    #[Test]
    public function it_can_store_a_language()
    {
        $this->authenticate();

        $payload = [
            'name' => 'French',
            'code' => 'fr',
            'is_active' => true,
        ];

        $response = $this->postJson(route('languages.store'), $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'French',
                     'code' => 'fr',
                     'is_active' => true,
                 ]);

        $this->assertDatabaseHas('languages', [
            'name' => 'French',
            'code' => 'fr',
        ]);
    }

    #[Test]
    public function it_validates_store_language_request()
    {
        $this->authenticate();

        $response = $this->postJson(route('languages.store'), []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'code']);
    }

    #[Test]
    public function it_can_toggle_language_status()
    {
        $this->authenticate();

        Cache::shouldReceive('forget')->once()->with('translations.export');

        $language = Language::factory()->create(['is_active' => true]);

        $response = $this->patchJson(route('languages.toggle', $language->id));

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $language->id,
                     'is_active' => false,
                 ]);

        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            'is_active' => false,
        ]);
    }

    #[Test]
    public function toggle_returns_404_for_non_existing_language()
    {
        $this->authenticate();

        $response = $this->patchJson(route('languages.toggle', 999));

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Record does not exist',
                 ]);
    }
}
