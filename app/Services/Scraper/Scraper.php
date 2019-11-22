<?php
namespace App\Services\Scraper;

use App\Services\Scraper\Http\GuzzleClient;
use App\Services\Scraper\Fetch\HomepageProducts;
use App\Services\Scraper\Fetch\Makers;

class Scraper
{
    /** @var int */
    private $requestMaxPages = 5;

    /** @var \GuzzleHttp\Client */
    private $client;

    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;    
    }

    public function fetchHomepageProducts(): HomepageProducts
    {                
        return (new HomepageProducts)->fetch($this->client);
    }

    public function fetchMakers(): Makers
    {
        return (new Makers)->fetch($this->client);
    }
}