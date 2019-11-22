<?php
namespace App\Services\Scraper\Fetch;

use App\Services\Scraper\Http\GuzzleClient;
use App\Services\Scraper\Models\Products as ProductsModel;
use App\Services\Scraper\Convert\Products as ConvertProducts;

class HomepageProducts implements FetchInterface
{
    /** @var string */
    const REQUEST_URL = 'https://www.producthunt.com/frontend/graphql';

    /** @var array */
    protected $products;

    public function fetch(GuzzleClient $guzzleClient)
    {
        $productsModel = new ProductsModel;
        
        $requestBody = $productsModel->getHomepageRequestBody();

        $response = $guzzleClient->create()->post(self::REQUEST_URL, json_encode($requestBody));    

        $convertedProducts = ConvertProducts::fromArray(
            json_decode($response, true)['data']['sections']['edges'][0]['node']['posts']['edges']
        );
        
        $this->products = $convertedProducts->getProducts();

        return $this;
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}