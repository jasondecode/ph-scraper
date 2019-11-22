<?php

namespace App\Services\Scraper;

use Illuminate\Support\ServiceProvider;
use App\Services\Scraper\Http\GuzzleClient;

class TelegramServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Scraper::class, function () {                
            return new Scraper(
                new GuzzleClient()
            );
        });
    }
}
