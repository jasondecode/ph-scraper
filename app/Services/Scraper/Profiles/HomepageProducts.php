<?php
namespace App\Services\Scraper\Profiles;

use App\Services\Scraper\Convert\Products as ConvertProducts;
use App\Services\Scraper\Convert\HomePage as ConvertHomepage;

class HomepageProducts
{
    public function parse(string $response): HomepageProducts
    {
        $sections = json_decode($response, true)['data']['sections'];

        $this->products = ConvertProducts::fromArray(
            $sections['edges'][0]['node']['posts']['edges']
        )->getProducts();

        $this->pageInfo = ConvertHomepage::fromArray(
            $sections['pageInfo']
        );

        return $this;        
    }

    public function getProducts(): array
    {
        return $this->products;
    }
    
    public function getPageInfo(): ConvertHomePage
    {
        return $this->pageInfo;
    }
}