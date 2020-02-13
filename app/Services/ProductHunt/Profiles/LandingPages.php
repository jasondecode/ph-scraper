<?php
namespace App\Services\ProductHunt\Profiles;

use App\Services\Scraper\Scraper;

class LandingPages
{
    /** @var App\Services\Scraper\Scraper */
    protected $scraper;

    public function __construct(Scraper $scraper)
    {                        
        $this->scraper = $scraper;                      
    }

    public function processOnRequestIsFulfilled(): LandingPages
    {        
        $statusCode = $this->scraper->getResponse()->getStatusCode();

        $this->scraper->output->info("status code: {$statusCode}");
                
        return $this;        
    }

    public function processOnRequestIsFailed(): LandingPages
    {
        return $this;
    }

}