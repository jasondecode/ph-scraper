<?php
namespace App\Services\ProductHunt\Profiles;

use Exception;
use GuzzleHttp\Psr7\Response;
use App\Services\Scraper\Scraper;
use App\Services\Scraper\Core\ProfileRequest;
use App\Services\Scraper\Core\ProcessWithGraphQL;
use App\Services\Scraper\Navigation\GraphQLCursor;
use App\Services\ProductHunt\Models\EntityProduct;
use App\Services\ProductHunt\Convert\Product as ConvertProduct;
use App\Services\ProductHunt\Convert\Products as ConvertProducts;
use App\Services\ProductHunt\Convert\HomePage as ConvertHomepage;

class HomepageProducts implements ProfileRequest, ProcessWithGraphQL
{
    /** @var App\Services\Scraper\Scraper */
    protected $scraper;

    public function __construct(Scraper $scraper)
    {                        
        $this->scraper = $scraper;      
    }

    public function processOnRequestIsFulfilled(Response $response)
    {                        
        $responseContent = json_decode($response->getBody()->getContents(), true);

        if (isset($responseContent['errors'])) {
            throw new Exception(json_encode($responseContent));
        }

        $sections = $responseContent['data']['sections'];

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

    public function processOnRequestIsFailed(Exception $exception)
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