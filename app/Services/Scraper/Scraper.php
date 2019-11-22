<?php
namespace App\Services\Scraper;

use App\Services\Scraper\Http\GuzzleClient;
use App\Services\Scraper\Model\Products;

class Scraper
{
    /** @var \GuzzleHttp\Client */
    private $client;

    public function __construct(GuzzleClient $client, Products $products)
    {
        $this->client = $client;    

        $this->products = $products;
    }

    public function fetchHomepageProducts(): string
    {                
        $url = 'https://www.producthunt.com/frontend/graphql';
        
        $requestBody = $this->products->getHomepageRequestBody();
        
        return $this->client
            ->create()
            ->post($url, json_encode($requestBody));        
    }
}