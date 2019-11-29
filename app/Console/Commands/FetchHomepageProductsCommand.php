<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Scraper\Scraper;
use App\Services\Scraper\Profiles\HomepageProducts;
use App\Services\Scraper\Models\Products;

class FetchHomepageProductsCommand extends Command
{
    /** @var string*/
    protected $signature = 'product:fetch-homepage';

    /** @var string */
    protected $description = 'Fetch homepage products';

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
            'body' => (new Products)->getHomepageRequestBody(),
        ];

        $responses = $scraper::createClient($clientOptions)
            ->setScrapeUrl('https://www.producthunt.com/frontend/graphql')
            ->setRequestMethod('POST')
            ->setScraperProfileClass(HomepageProducts::class)
            ->setMaximumCrawlCount(2)
            ->setMinimumDelayBetweenRequests(1000000)
            ->setMaximumDelayBetweenRequests(2000000)
            ->setNavigationType('graphql-cursor')
            ->fetch();   
        
        collect($responses)                    
            ->sortBy('votes')
            ->each(function ($response) {
                $this->info('products:');

                dump($response->getProducts());

                $this->info('page info:');

                dump($response->getPageInfo());
            });
        
        $this->info('all done 🔥');
    }
}
