<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Translation;

class SeedLargeTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:seed-large {count=100000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translation Records Command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');

        Translation::withoutEvents(function () use ($count) {
         Translation::factory()->count($count)->create();
        });

        $this->info("Seeded {$count} translations");
    }
}
