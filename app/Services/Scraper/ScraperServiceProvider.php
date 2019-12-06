<?php

namespace App\Services\Scraper;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class ScraperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Scraper::class, function () {                
            return new Scraper(
                new Client()
            );
        });
    }
}
