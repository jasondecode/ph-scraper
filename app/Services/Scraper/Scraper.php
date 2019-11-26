<?php
namespace App\Services\Scraper;

use App\Services\Scraper\Http\GuzzleClient;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use App\Services\Scraper\Profiles\HomepageProducts;

class Scraper
{   
    /** @var int|null */
    protected $maximumCrawlCount = null;

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
        $this->setMaximumCrawlCount = $maximumCrawlCount;

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

    public function fetch()
    {   
        $response = $this->client->request(
            $this->getRequestMethod(),
            $this->getScrapeUrl()
        )
        ->getBody()
        ->getContents();
            
        if (! is_null($this->scraperProfileClass())) {
            return (new $this->scraperProfileClass())->parse($response);
        }

        return $response;
    }
}