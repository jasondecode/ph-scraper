<?php
namespace App\Services\ProductHunt\Profiles;

use App\Services\ProductHunt\Scraper;
use App\Services\ProductHunt\Navigation\GraphQLCursor;
use App\Services\ProductHunt\Convert\Products as ConvertProducts;
use App\Services\ProductHunt\Convert\HomePage as ConvertHomepage;

class HomepageProducts extends Profiles
{
    /** @var App\Services\ProductHunt\Scraper */
    protected $scraper;

    public function __construct(Scraper $scraper)
    {                
        parent::__construct();

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
        
        $this->info('products:');

        collect($this->products)->sortByDesc('votes')->dump();
        
        $this->info('page info:');

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

        $body['variables']['cursor'] = $this->getCursor($graphQLCursor);
        
        return [
            'body' => json_encode($body)
        ];
    }

    protected function getCursor(GraphQLCursor $graphQLCursor): string
    {        
        $startFromPaginationNumber = $this->scraper->getStartFromPaginationNumber();
        
        if ($this->scraper->getRequestCount() === 1 && ! is_null($startFromPaginationNumber)) {
            $cursor = Navigation::where([
                'type' => 'graphql-cursor',                
                'number' => $startFromPaginationNumber
            ])->first();

            if (! is_null($cursor)) {
                return $cursor->getCode();
            }
        }

        return $graphQLCursor->getPageCursor();
    }
}