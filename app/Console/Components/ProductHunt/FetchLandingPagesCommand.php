<?php

namespace App\Console\Components\ProductHunt;

use Illuminate\Console\Command;
use App\Services\Scraper\Scraper;
use App\Services\ProductHunt\Profiles\LandingPages;

class FetchLandingPagesCommand extends Command
{
    /** @var string*/
    protected $signature = 'producthunt:fetch-landing-pages';

    /** @var string */
    protected $description = 'Fetch landing pages from the crawl queue';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle(Scraper $scraper)
    {
        $this->info('fetching homepage..');

        $clientOptions = [
            'headers' => [
                'user-agent' => config('scraper.user_agent'),
                'content-type' => 'application/json'
            ],
            'body' => '',
        ];
        
        $scraper->createClient($clientOptions)
            ->setSource('landing-pages')            
            ->setRequestMethod('GET')
            ->setScraperProfileClass(LandingPages::class)                        
            ->setMinimumDelayBetweenRequests(1000000)
            ->setMaximumDelayBetweenRequests(2000000)
            ->runCrawlQueueScraper();   
    }
}
