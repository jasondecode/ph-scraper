<?php
namespace App\Services\Scraper;

use App\Services\Scraper\Http\GuzzleClient;

class Scraper
{
    /** @var \GuzzleHttp\Client */
    private $client;

    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    public function fetchHomepageProducts()
    {
        $url = 'https://www.producthunt.com/frontend/graphql';
                        
        return $this->client->create()->post($url, []);        
    }
}