<?php
namespace App\Services\ProductHunt\Profiles;

use Exception;
use GuzzleHttp\Psr7\Response;
use App\Services\Scraper\Scraper;
use App\Services\Scraper\Core\ProfileRequest;

class LandingPages implements ProfileRequest
{
    /** @var App\Services\Scraper\Scraper */
    protected $scraper;

    public function __construct(Scraper $scraper)
    {                        
        $this->scraper = $scraper;                      
    }

    public function processOnRequestIsFulfilled(Response $response)
    {        
        $statusCode = $response->getStatusCode();

        $this->scraper->output->info("status code: {$statusCode}");                
    }

    public function processOnRequestIsFailed(Exception $exception)
    {
        
    }
}