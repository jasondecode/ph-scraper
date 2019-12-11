<?php

namespace App\Services\Scraper;

use Illuminate\Support\ServiceProvider;
use App\Services\Scraper\Utilities\Output;

class ScraperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Scraper::class, function () {                
            return new Scraper(                
                new Output()                
            );
        });
    }
}
