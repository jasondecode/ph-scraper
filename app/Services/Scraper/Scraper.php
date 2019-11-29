<?php
namespace App\Services\Scraper;

use App\Services\Scraper\Http\GuzzleClient;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use App\Services\Scraper\Profiles\HomepageProducts;
use App\Services\Scraper\Navigation\Cursor;


class Scraper
{   
    /** @var int */
    protected $maximumCrawlCount = 1;

    /** @var int|null */
    protected $startFromPaginationNumber = null;

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var string */
    protected $scrapeUrl;

    /** @var string */
    protected $clientMethod;

    /** @var array */
    protected $clientHeadder = [];

    /** @var array|null */
    protected $clientBody = null;

    /** @var string|null */
    protected $scraperProfileClass = null;

    /** @var string */
    protected $navigationType;

    /** @var GuzzleHttp\Psr7\Response */
    protected $response;

    public static function createClient(array $clientOptions): Scraper
    {   
        $client = new Client([
            RequestOptions::COOKIES => true,
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT => 10,
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::HEADERS => $clientOptions['headers'],
            RequestOptions::BODY => $clientOptions['body']
        ]);

        return new self($client);
    }

    public function __construct(Client $client)
    {
        $this->client = $client;   
    }

    public function setClientHeaders(array $clientHeaders): Scraper
    {
        $this->clientHeaders = $clientHeaders;

        return $this;
    }

    public function getClientHeaders(): array
    {
        return $this->clientHeadder;
    }

    public function setMaximumCrawlCount(int $maximumCrawlCount): Scraper
    {
        $this->maximumCrawlCount = $maximumCrawlCount;

        return $this;
    }

    public function getMaximumCrawlCount(): ?int
    {
        return $this->maximumCrawlCount;
    }

    public function setStartFromPaginationNumber(int $startFromPaginationNumber): Scraper
    {
        $this->startFromPaginationNumber = $startFromPaginationNumber;
        
        return $this;
    }

    public function setScraperProfileClass(string $scraperProfileClass): Scraper
    {
        $this->scraperProfileClass = $scraperProfileClass;

        return $this;
    }

    public function scraperProfileClass(): string
    {
        return $this->scraperProfileClass;
    }

    public function setNavigationType(string $navigationType): Scraper
    {
        $this->navigationType = $navigationType;

        return $this;
    }

    public function getNavigationType(): string
    {
        return $this->navigationType;
    }

    public function getStartFromPaginationNumber(): ?int
    {
        return $this->startFromPaginationNumber;
    }

    public function setScrapeUrl(string $scrapeUrl): Scraper
    {
        $this->scrapeUrl = $scrapeUrl;

        return $this;
    }

    public function getScrapeUrl(): string
    {
        return $this->scrapeUrl;
    }

    public function setRequestMethod(string $requestMethod): Scraper
    {
        $this->getRequestMethod = $requestMethod;

        return $this;
    }

    public function getRequestMethod(): string
    {
        return $this->getRequestMethod;
    }

    public function setClientBody(array $clientBody): Scraper
    {
        $this->clientBody = $clientBody;

        return $this;
    }

    public function getClientBody(): array
    {
        return $this->clientBody;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function fetch()
    {   
        if ($this->getNavigationType() === 'graphql-cursor') {
            return $this->startScraperWithGraphQLCursor();
        }
        
        if ($this->getNavigationType() === 'url-pagination') {
            $this->startScraperWithUrlPagination();
        }

        return $this;
    }

    protected function startScraperWithGraphQLCursor()
    {
        return $this->scrapeThroughCrawlCount(function () {            
            $cursor = new GraphQLCursor;

            $body = json_decode($this->client->getConfig()['body'], true);

            $body['variables']['cursor'] = $cursor->getNextPageCursor();

            $this->response = $this->client->request(
                $this->getRequestMethod(),
                $this->getScrapeUrl(), 
                ['body' => json_encode($body)]
            );
            
            if (! is_null($this->scraperProfileClass())) {
                $parsedResponse = (new $this->scraperProfileClass())->parse($this);

                $nextPageCursor = $parsedResponse->getPageInfo()->getEndCursor();

                $cursor->setNextPageCursor($nextPageCursor);
                
                yield $parsedResponse;
            }
        });
    }

    protected function startScraperWithUrlPagination()
    {

    }

    protected function scrapeThroughCrawlCount($callback)
    {
        for ($i = $this->startFromPaginationNumber; $i <= $this->maximumCrawlCount; $i++) {
            return call_user_func($callback);
        }
    }
}