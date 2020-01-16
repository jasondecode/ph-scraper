<?php

namespace App\Services\Scraper;

use App\Services\Scraper\Core\LogEntries;
use App\Services\Scraper\Models\LogEntriesScraper as LogEntriesScraperModel;
use Illuminate\Support\ServiceProvider;
use App\Services\Scraper\Utilities\Output;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Services\Scraper\Models\Navigation;

class ScraperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Scraper::class, function () {                
            return new Scraper(                
                new Output(new ConsoleOutput),
                new LogEntries(new LogEntriesScraperModel),
                new Navigation
            );
        });
    }
}
