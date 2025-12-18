<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Translation;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create authenticated user
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');

        // Seed 100k translations
        Translation::factory()->count(100000)->create();
    }

    #[Test]
    public function translations_index_performance()
    {
        $start = microtime(true);
        
        $response = $this->getJson('/api/translations');

        $end = microtime(true);
        $duration = ($end - $start) * 1000; // ms

        $response->assertStatus(200);

        echo "\nTranslations Index Response Time: {$duration} ms\n";

        $this->assertLessThan(200, $duration, 'Translations index is too slow');
    }

    #[Test]
    public function translations_search_performance()
    {
        $start = microtime(true);

        $response = $this->getJson('/api/translation/search?key=translation_1');

        $end = microtime(true);
        $duration = ($end - $start) * 1000;

        $response->assertStatus(200);

        echo "\nTranslations Search Response Time: {$duration} ms\n";

        $this->assertLessThan(500, $duration, 'Translations search is too slow');
    }

    #[Test]
    public function translations_export_performance()
    {
        $start = microtime(true);

        $response = $this->getJson('/api/export/translations');

        $end = microtime(true);
        $duration = ($end - $start) * 1000;

        $response->assertStatus(200);

        echo "\nTranslations Export Response Time: {$duration} ms\n";

        $this->assertLessThan(500, $duration, 'Translations export is too slow');
    }
}
