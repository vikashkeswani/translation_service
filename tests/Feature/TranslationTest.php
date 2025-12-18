<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Language;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;

class TranslationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
     use RefreshDatabase;

    protected function authenticate(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    #[Test]
    public function unauthenticated_users_cannot_access_translation_routes(): void
    {
        $this->getJson('/api/translations')
            ->assertStatus(401);
    }

    #[Test]
    public function it_lists_translations(): void
    {
        $this->authenticate();

        Translation::factory()->count(3)->create();

        $this->getJson('/api/translations')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    #[Test]
    public function it_shows_a_single_translation(): void
    {
        $this->authenticate();

        $translation = Translation::factory()->create();

        $this->getJson("/api/translations/{$translation->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'key',
                    'value',
                    'language',
                    'tag',
                ],
            ]);
    }

    #[Test]
    public function it_returns_404_if_translation_not_found(): void
    {
        $this->authenticate();

        $this->getJson('/api/translations/999')
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Record does not exist',
            ]);
    }

    #[Test]
    public function it_stores_a_translation(): void
    {
        $this->authenticate();

        $language = Language::factory()->create([
            'is_active' => true,
        ]);

        $tag     = Tag::factory()->create();

        $response = $this->postJson('/api/translations', [
            'key'         => 'welcome',
            'value'       => 'Welcome',
            'language_id' => $language->id,
            'tag_id'        => $tag->id, //['web', 'mobile','desktop'],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('translations', [
            'key' => 'welcome',
        ]);
    }

    #[Test]
    public function it_updates_a_translation(): void
    {
        $this->authenticate();

        $language = Language::factory()->create();
        $translation = Translation::factory()->create([
            'language_id' => $language->id,

        ]);
        $tag = Tag::factory()->create();
        $response = $this->putJson("/api/translations/{$translation->id}", [
            'value' => 'Updated value',
            'key' => 'translation_key',
            'language_id' => $language->id,
            'tag_id' => $tag->id
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('translations', [
            'id'    => $translation->id,
            'value' => 'Updated value',
        ]);
    }

    #[Test]
    public function it_returns_404_on_update_if_translation_not_found(): void
    {
        $this->authenticate();

        $language = Language::factory()->create();
        $tag = Tag::factory()->create();
        
        $this->putJson('/api/translations/999', [
            'value' => 'Test',
            'key' => 'key',
            'language_id' => $language->id,
            'tag_id' => $tag->id,
        ])
        ->assertStatus(404)
        ->assertJson([
            'message' => 'Record does not exist',
        ]);
    }

    #[Test]
    public function it_deletes_a_translation(): void
    {
        $this->authenticate();

        $translation = Translation::factory()->create();

        $this->deleteJson("/api/translations/{$translation->id}")
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Record Deleted Successfully!',
            ]);

        $this->assertDatabaseMissing('translations', [
            'id' => $translation->id,
        ]);
    }

    #[Test]
    public function it_searches_translations()
    {
        // 1️⃣ Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum'); // <-- important for auth:sanctum

        // 2️⃣ Create a tag
        $tag = Tag::factory()->create(['name' => 'web']);

        // 3️⃣ Create an active language
        $language = Language::factory()->create(['is_active' => true]);

        // 4️⃣ Create a translation matching search query
        $translation = Translation::factory()->create([
            'key' => 'translation_1',
            'value' => 'some value',
            'tag_id' => $tag->id,
            'language_id' => $language->id,
        ]);

        // 5️⃣ Hit the search endpoint as authenticated user
        $response = $this->getJson('/api/translation/search?key=translation_1&tag=web');

        // 6️⃣ Assert response
        $response->assertStatus(200)
            ->assertJsonFragment([
                'key' => 'translation_1',
                'value' => 'some value',
            ]);
    }
    
}
