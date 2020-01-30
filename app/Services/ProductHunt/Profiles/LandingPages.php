<?php
namespace App\Services\ProductHunt\Profiles;

use App\Services\Scraper\Scraper;
use App\Services\Scraper\Models\CrawlQueue;
use App\Services\Scraper\Models\Entity;

class LandingPages
{
    /** @var App\Services\Scraper\Scraper */
    protected $scraper;

    public function __construct(Scraper $scraper)
    {                        
        $this->scraper = $scraper;                      
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