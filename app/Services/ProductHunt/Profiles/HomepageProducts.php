<?php
namespace App\Services\ProductHunt\Profiles;

use App\Services\Scraper\Scraper;
use App\Services\Scraper\Navigation\GraphQLCursor;
use App\Services\ProductHunt\Models\EntityProduct;
use App\Services\ProductHunt\Convert\Product as ConvertProduct;
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

    public function processOnRequestIsFulfilled()
    {        
        $responseContent = $this->scraper->getResponse()->getBody()->getContents();

        $sections = json_decode($responseContent, true)['data']['sections'];

        $products = ConvertProducts::fromArray(
            $sections['edges'][0]['node']['posts']['edges']
        )->getProducts();

        $this->pageInfo = ConvertHomepage::fromArray(
            $sections['pageInfo']
        );
        
        $this->scraper->output->info('products:');
        
        collect($products)->each(function (ConvertProduct $product) {        
            $shortendUrl = $product->getShortenedUrl();
            
            $source = $this->scraper->getSource();

            $this->scraper->entity->createOrUpdate([
                'entity_unique_code' => $product->getId(),
                'entityable_type' => EntityProduct::class,
            ], [
                'source' => $source
            ], [
                'votes' => $product->getVotes(),
                'name' => $product->getName(),
                'featured_at' => $product->getFeaturedAt(),
                'topics' => json_encode($product->getTopics()),
                'shortened_url' => $shortendUrl,
                'slug' => $product->getSlug()
            ]);
            
            $this->scraper->crawlQueue->create([
                'url' => "https://producthunt.com{$shortendUrl}", 
                'source' => $source
            ]);
        })
        ->dump();
        
        $this->scraper->output->info('page info:');

        dump($this->pageInfo);              
    }

    public function processOnRequestIsFailed()
    {
                
    }

    public function getEndCursor(): ?string
    {
        return $this->pageInfo->getEndCursor();
    }

    public function getHasNextPage(): bool
    {
        return $this->pageInfo->getHasNextPage();
    }

    public function getRequestOptions(GraphQLCursor $graphQLCursor): array
    {
        $configBody = $this->scraper->getClient()->getConfig()['body'];

        $body = json_decode($configBody, true);
        
        $body['variables']['cursor'] = $graphQLCursor->getCursor($this->scraper);
        
        return [
            'body' => json_encode($body)
        ];
    }
}