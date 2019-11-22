<?php
namespace App\Services\Scraper\Fetch;

use App\Services\Scraper\Http\GuzzleClient;
use App\Services\Scraper\Models\Products as ProductsModel;
use App\Services\Scraper\Convert\Products as ConvertProducts;
use App\Services\Scraper\Convert\HomePage as ConvertHomePage;

class HomepageProducts implements FetchInterface
{
    /** @var string */
    const REQUEST_URL = 'https://www.producthunt.com/frontend/graphql';

    /** @var array */
    protected $products;

    /** @var \App\Services\Scraper\Convert\HomePage */
    protected $pageInfo;

    public function fetch(GuzzleClient $guzzleClient)
    {
        $productsModel = new ProductsModel;
        
        $requestBody = $productsModel->getHomepageRequestBody();

        $response = $guzzleClient->create()->post(self::REQUEST_URL, json_encode($requestBody));    

        $sections = json_decode($response, true)['data']['sections'];

        $this->products = ConvertProducts::fromArray(
            $sections['edges'][0]['node']['posts']['edges']
        )->getProducts();

        $this->pageInfo = ConvertHomePage::fromArray(
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