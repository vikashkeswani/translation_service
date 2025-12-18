<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Language;
use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExportTranslationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    #[Test]
    public function it_streams_translations_as_json()
    {
        $activeLanguage = Language::factory()->create(['is_active' => true]);
        $inactiveLanguage = Language::factory()->create(['is_active' => false]);
        $tag = Tag::factory()->create();

        $translation1 = Translation::factory()->create([
            'language_id' => $activeLanguage->id,
            'tag_id' => $tag->id,
            'key' => 'hello',
            'value' => 'Hello',
        ]);

        Translation::factory()->create([
            'language_id' => $inactiveLanguage->id,
            'tag_id' => $tag->id,
            'key' => 'bye',
            'value' => 'Goodbye',
        ]);

        $controller = new \App\Http\Controllers\Api\ExportController();
        $data = $controller->getTranslations();

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals('hello', $data[0]['key']);
    }


    #[Test]
    public function it_returns_empty_array_if_no_translations()
    {
        $this->authenticate();

        $response = $this->get(route('translations.export'));

        $response->assertStatus(200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));

        $content = trim($response->getContent());
        $data = json_decode($content, true);

        // $this->assertIsArray($data);
        $this->assertEmpty($data);
    }
}
