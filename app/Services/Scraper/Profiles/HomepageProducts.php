<?php
namespace App\Services\Scraper\Profiles;

use App\Services\Scraper\Scraper;
use App\Services\Scraper\Convert\Products as ConvertProducts;
use App\Services\Scraper\Convert\HomePage as ConvertHomepage;

class HomepageProducts
{
    /** App\Services\Scraper\Scraper */
    protected $scraper;

    public function __construct(Scraper $scraper)
    {
        $this->scraper = $scraper;
    }

    public function getRequestOptions($cursor): array
    {
        $body = json_decode($this->scraper->getClient()->getConfig()['body'], true);

        $body['variables']['cursor'] = $cursor->getNextPageCursor();
        
        return [
            'body' => json_encode($body)
        ];
    }

    public function parse(): HomepageProducts
    {
        $responseContent = $this->scraper->getResponse()
            ->getBody()
            ->getContents();

        $sections = json_decode($responseContent, true)['data']['sections'];

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