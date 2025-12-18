<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Translation;
use App\Models\Language;
use App\Models\Tag;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_users_cannot_access_routes(): void
    {
        $this->getJson('/api/translations')
            ->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_lists_translations(): void
    {
        $this->authenticate();

        $language = Language::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create();

        $translation = Translation::factory()->create([
            'language_id' => $language->id,
            'tag_id'      => $tag->id,
        ]);

        $this->getJson('/api/translations')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id','key','value','language','tag','created_at','updated_at']
                ],
                'links',
                'meta',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_shows_a_single_translation(): void
    {
        $this->authenticate();

        $language = Language::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create();

        $translation = Translation::factory()->create([
            'language_id' => $language->id,
            'tag_id'      => $tag->id,
        ]);

        $this->getJson("/api/translations/{$translation->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $translation->id,
                'key' => $translation->key,
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_if_translation_not_found(): void
    {
        $this->authenticate();

        $this->getJson('/api/translations/999')
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Record does not exist',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_stores_a_translation(): void
    {
        $this->authenticate();

        $language = Language::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create();

        $response = $this->postJson('/api/translations', [
            'key'         => 'welcome',
            'value'       => 'Hello World',
            'language_id' => $language->id,
            'tag_id'      => $tag->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('translations', [
            'key' => 'welcome',
            'value' => 'Hello World',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_a_translation(): void
    {
        $this->authenticate();

        $language = Language::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create();

        $translation = Translation::factory()->create([
            'language_id' => $language->id,
            'tag_id'      => $tag->id,
        ]);

        $response = $this->putJson("/api/translations/{$translation->id}", [
            'key'         => $translation->key,
            'value'       => 'Updated value',
            'language_id' => $language->id,
            'tag_id'      => $tag->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('translations', [
            'id'    => $translation->id,
            'value' => 'Updated value',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_on_update_if_translation_not_found(): void
    {
        $this->authenticate();

        $language = Language::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create();

        $this->putJson('/api/translations/999', [
            'key'         => 'dummy',
            'value'       => 'Test',
            'language_id' => $language->id,
            'tag_id'      => $tag->id,
        ])->assertStatus(404)
          ->assertJson([
              'message' => 'Record does not exist',
          ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_a_translation(): void
    {
        $this->authenticate();

        $language = Language::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create();

        $translation = Translation::factory()->create([
            'language_id' => $language->id,
            'tag_id'      => $tag->id,
        ]);

        $this->deleteJson("/api/translations/{$translation->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Record Deleted Successfully!']);

        $this->assertDatabaseMissing('translations', [
            'id' => $translation->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_searches_translations(): void
    {
        $this->authenticate();

        $language = Language::factory()->create(['is_active' => true]);
        $tag = Tag::factory()->create(['name' => 'web']);

        $translation = Translation::factory()->create([
            'key'         => 'dashboard',
            'value'       => 'some value',
            'language_id' => $language->id,
            'tag_id'      => $tag->id,
        ]);

        $this->getJson('/api/translations/search?key=dashboard&tag=web')
            ->assertStatus(200)
            ->assertJsonFragment([
                'key'   => 'dashboard',
                'value' => 'some value',
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'key',
                        'value',
                        'language' => ['id', 'code', 'name'],
                        'tag'      => ['id', 'name'],
                        'created_at',
                        'updated_at',
                    ]
                ],
                'links',
                'meta',
            ]);
    }
}
