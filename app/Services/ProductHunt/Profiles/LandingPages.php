<?php
namespace App\Services\ProductHunt\Profiles;

use App\Services\Scraper\Scraper;
use App\Services\Scraper\Models\CrawlQueue;
use App\Services\Scraper\Models\Entity;

class LandingPages
{
    /** @var App\Services\Scraper\Scraper */
    protected $scraper;

    /** @var App\Services\Scraper\Models\Entity */
    protected $entity;

    /** @var App\Services\Scraper\Models\CrawlQueue */
    protected $crawlQueue;

    public function __construct(Scraper $scraper, Entity $entity, CrawlQueue $crawlQueue)
    {                        
        $this->scraper = $scraper;      
        
        $this->entity = $entity;

        $this->crawlQueue = $crawlQueue;
    }

    public function processOnRequestFulfilled(): LandingPages
    {        
        $statusCode = $this->scraper->getResponse()->getStatusCode();

        $this->scraper->output->info("status code: {$statusCode}");
                
        return $this;        
    }

    public function processOnRequestFailed(): LandingPages
    {
        return $this;
    }

}