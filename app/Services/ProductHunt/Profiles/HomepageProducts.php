<?php
namespace App\Services\ProductHunt\Profiles;

use App\Services\Scraper\Scraper;
use App\Services\Scraper\Navigation\GraphQLCursor;
use App\Services\Scraper\Models\Navigation;
use App\Services\ProductHunt\Convert\Products as ConvertProducts;
use App\Services\ProductHunt\Convert\HomePage as ConvertHomepage;

class HomepageProducts
{
    /** @var App\Services\Scraper\Scraper */
    protected $scraper;

    public function __construct(Scraper $scraper)
    {                        
        $this->scraper = $scraper;                 
    }

    public function processOnRequestFulfilled(): HomepageProducts
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
        
        $this->scraper->output->info('products:');

        collect($this->products)->sortByDesc('votes')->dump();
        
        $this->scraper->output->info('page info:');

        dump($this->pageInfo);

        return $this;        
    }

    public function processOnRequestFailed(): HomepageProducts
    {
        return $this;
    }

    public function getEndCursor(): string
    {
        return $this->pageInfo->getEndCursor();
    }

    public function getRequestOptions(GraphQLCursor $graphQLCursor): array
    {
        $body = json_decode($this->scraper->getClient()->getConfig()['body'], true);
        
        $body['variables']['cursor'] = $graphQLCursor->getCursor($this->scraper);
        
        return [
            'body' => json_encode($body)
        ];
    }
}