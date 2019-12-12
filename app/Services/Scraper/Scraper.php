<?php
namespace App\Services\Scraper;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use App\Services\Scraper\Navigation\GraphQLCursor;
use App\Services\Scraper\Utilities\Output;
use App\Services\Scraper\Core\LogEntries;

class Scraper
{   
    /** @var App\Services\Scraper\Utilities\Output */
    public $output;

    /** @var string */
    protected $source;

    /** @var int|null */
    protected $maximumCrawlCount = null;

    /** @var int|null */
    protected $requestCount = null;

    /** @var int|null */
    protected $startFromPaginationNumber = null;
    
    /** @var int|null */
    protected $minimumDelayBetweenRequests = null;

    /** @var int|null */
    protected $maximumDelayBetweenRequests = null;

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var string */
    protected $scrapeUrl;

    /** @var string */
    protected $requestMethod;

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

    public function __construct(Output $output, LogEntries $logEntries)
    {                   
        $this->output = $output;

        $this->logEntries = $logEntries;
    }

    public function createClient(array $clientOptions): Scraper
    {   
        $this->client = new Client([
            RequestOptions::COOKIES => true,
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT => 10,
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::HEADERS => $clientOptions['headers'],
            RequestOptions::BODY => $clientOptions['body']
        ]);

        return $this;
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

    public function setSource(string $source): Scraper
    {
        $this->source = $source;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setMaximumCrawlCount(int $maximumCrawlCount): Scraper
    {
        $this->maximumCrawlCount = $maximumCrawlCount;

        return $this;
    }

    public function getMaximumCrawlCount(): int
    {
        return $this->maximumCrawlCount ?? 1;
    }

    public function setRequestCount(int $requestCount): Scraper
    {
        $this->requestCount = $requestCount;

        return $this;
    }

    public function getRequestCount(): int
    {
        return $this->requestCount ?? 0;
    }
    
    public function setStartFromPaginationNumber(int $startFromPaginationNumber): Scraper
    {
        $this->startFromPaginationNumber = $startFromPaginationNumber;
        
        return $this;
    }

    public function getStartFromPaginationNumber(): ?int
    {
        return $this->startFromPaginationNumber;
    }

    public function setMinimumDelayBetweenRequests(int $minimumDelayBetweenRequests): Scraper
    {
        $this->minimumDelayBetweenRequests = $minimumDelayBetweenRequests;
        
        return $this;
    }

    public function getMinimumDelayBetweenRequests(): int
    {                
        return $this->minimumDelayBetweenRequests ?? 0;
    }

    public function setMaximumDelayBetweenRequests(int $maximumDelayBetweenRequests): Scraper
    {
        $this->maximumDelayBetweenRequests = $maximumDelayBetweenRequests;
        
        return $this;
    }

    public function getMaximumDelayBetweenRequests(): int
    {                
        return $this->maximumDelayBetweenRequests ?? 0;
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
        $this->requestMethod = $requestMethod;

        return $this;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function getClient(): Client
    {
        return $this->client;
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

    public function run()
    {   
        $this->output->info('Running scraper..');

        if (! $this->logEntries->isRunning($this->source)) {
            $this->logEntries->create($this);
                
            if ($this->navigationType === 'graphql-cursor') {
                $this->startScraperWithGraphQLCursor();
            }
            
            if ($this->navigationType === 'url-pagination') {
                $this->startScraperWithUrlPagination();
            }

            $this->logEntries->setRunAtIsFinished();

            $this->output->info('Running scraper completed ');
        } else {
            $this->output->error("Source: {$this->source} is already running");
        }        
    }

    protected function startScraperWithGraphQLCursor()
    {        
        $graphQLCursor = new GraphQLCursor;

        $this->scrapeThroughCrawlCount(function () use ($graphQLCursor) {                                    
            $scraperProfileClass = $this->scraperProfileClass();
    
            $scraperProfile = new $scraperProfileClass($this);
            
            $this->response = $this->client->request(
                $this->requestMethod,
                $this->scrapeUrl, 
                $scraperProfile->getRequestOptions($graphQLCursor)
            );
            
            $scraperProfile->processOnRequestFulfilled();

            $nextPageCursor = $scraperProfile->getEndCursor();

            $graphQLCursor->setNextPageCursor($nextPageCursor);                        
        });
    }

    protected function startScraperWithUrlPagination()
    {
        return $this->scrapeThroughCrawlCount(function () {                        
            return [];
        });
    }    

    protected function scrapeThroughCrawlCount(Closure $callback)
    {        
        for ($requestCount = 1; $requestCount <= $this->getMaximumCrawlCount(); $requestCount++) {                                      
            $this->setRequestCount($requestCount);

            try {
                call_user_func($callback);
            } catch (\Exception $e) {
                $this->output->error($e->getMessage());
                
                $this->logEntries->setError($e->getMessage());
            }

            if ($requestCount > 1) {
                $delayBetweenRequests = rand(
                    $this->getMinimumDelayBetweenRequests(), 
                    $this->getMaximumDelayBetweenRequests()
                );

                usleep($delayBetweenRequests);
            }
        }        
    }
}