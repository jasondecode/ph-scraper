<?php

namespace App\Console\Components\ProductHunt;

use App\Services\Scraper\Core\ProfileCommand;
use App\Services\Scraper\Scraper;
use App\Services\ProductHunt\Profiles\HomepageProducts;
use App\Services\ProductHunt\Models\EntityProduct;
use App\Services\Scraper\Models\Navigation;

class FetchHomepageProductsCommand extends ProfileCommand
{
    /** @var string*/
    protected $signature = 'producthunt:fetch-homepage';

    /** @var string */
    protected $description = 'Fetch homepage products';
        
    public function handle(Scraper $scraper, EntityProduct $entityProduct)
    {           
        $this->boot($scraper);

        $this->info('fetching homepage..');

        $clientOptions = [
            'headers' => [
                'user-agent' => config('scraper.user_agent'),
                'content-type' => 'application/json'
            ],
            'body' => $entityProduct->getHomepageRequestBody(),
        ];
        
        $scraper->createClient($clientOptions)
            ->setSource('homepage-products')
            ->setScrapeUrl('https://www.producthunt.com/frontend/graphql')
            ->setRequestMethod('POST')
            ->setScraperProfileClass(HomepageProducts::class)            
            ->setNavigationType(Navigation::TYPE_GRAPHQL_CURSOR)
            ->setMaximumCrawlCount(20)            
            // ->setStartFromPaginationNumber(2)
            ->continueFromLastSavedPageNumber()
            ->setMinimumDelayBetweenRequests(1000000)
            ->setMaximumDelayBetweenRequests(2000000)
            ->runNavigationScraper();   
    }
}
