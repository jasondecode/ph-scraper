<?php
namespace App\Services\Scraper;

use Closure;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use App\Services\Scraper\Models\Navigation;
use App\Services\Scraper\Navigation\GraphQLCursor;
use App\Services\Scraper\Utilities\Output;
use App\Services\Scraper\Core\LogEntries;
use App\Services\Scraper\Models\CrawlQueue;
use App\Services\Scraper\Models\Entity;

class Scraper
{       
    /** @var App\Services\Scraper\Utilities\Output */
    public $output;

    /** @var App\Services\Scraper\Core\LogEntries */
    public $logEntries;

    /** @var App\Services\Scraper\Models\Entity */
    public $entity;

    /** @var App\Services\Scraper\Models\CrawlQueue */
    public $crawlQueue;

    /** @var App\Services\Scraper\Models\Navigation */
    public $navigation;

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
    
    /** @var int */
    protected $concurrency = 2;

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

    /** @var array */
    protected $inputOptions;

    /** @var int */
    protected $navigationType;

    /** @var bool */
    protected $hasNextPage = true;

    /** @var bool */
    protected $continueFromLastSavedPageNumber = false;

    /** @var int|null */
    protected $currentRequestedPageNumber = null;
    
    public function __construct(
        Output $output, 
        LogEntries $logEntries, 
        Navigation $navigation, 
        Entity $entity, 
        CrawlQueue $crawQueue
    ) {                   
        $this->output = $output;

        $this->logEntries = $logEntries;

        $this->navigation = $navigation;

        $this->entity = $entity;

        $this->crawlQueue = $crawQueue;
    }

    public function createClient(array $clientOptions): Scraper
    {   
        $this->client = new Client([
            RequestOptions::COOKIES => true,
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT => 10,
            RequestOptions::ALLOW_REDIRECTS => true,
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

    public function setConcurrency(int $concurrency): Scraper
    {
        $this->concurrency = $concurrency;

        return $this;
    }

    public function getConcurrency(): int
    {
        return $this->concurrency;
    }
    
    public function setStartFromPaginationNumber(?int $startFromPaginationNumber): Scraper
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

    public function setInputOptions(array $options): Scraper
    {
        $this->inputOptions = $options;

        return $this;
    }

    public function getInputOption(string $option)
    {
        return $this->inputOptions[$option];
    }

    public function setNavigationType(int $navigationType): Scraper
    {
        $this->navigationType = $navigationType;

        return $this;
    }

    public function getNavigationType(): int
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

    public function hasNextPage(): Scraper
    {
        $this->hasNextPageCursor = true;

        return $this;
    }

    public function doesntHaveNextPage(): Scraper
    {
        $this->hasNextPageCursor = false;

        return $this;
    }

    public function continueFromLastSavedPageNumber(): Scraper
    {
        $this->continueFromLastSavedPageNumber = true;

        return $this;
    }

    public function dontContinueFromLastSavedPageNumber(): Scraper
    {
        $this->continueFromLastSavedPageNumber = false;

        return $this;
    }

    public function setCurrentRequestedPageNumber(int $requestCount): Scraper
    {   
        if (! is_null($this->startFromPaginationNumber)) {
            $this->currentRequestedPageNumber = $this->startFromPaginationNumber + $requestCount;
        } else {
            $this->currentRequestedPageNumber = $requestCount;
        }
        
        return $this;
    }

    public function getCurrentRequestedPageNumber(): int
    {
        return $this->currentRequestedPageNumber;
    }

    public function runNavigationScraper()
    {                   
        $this->runScraper('navigation', function () {
            if ($this->navigationType === Navigation::TYPE_GRAPHQL_CURSOR) {
                $this->startScraperWithGraphQLCursor();
            }
            
            if ($this->navigationType === Navigation::TYPE_URL_PAGINATION) {
                $this->startScraperWithUrlPagination();
            }
        });     
    }

    public function runCrawlQueueScraper()
    {
        $this->runScraper('queue', function () {
            $this->startScraperThrougCrawlQueue();
        });
    }

    protected function runScraper(string $scraperTypeName, Closure $callback)
    {
        if ($this->getInputOption('force') || ! $this->logEntries->isRunning($this->source)) {
            if ($this->getInputOption('silent')) {
                $this->output->info("Running {$scraperTypeName} scraper in silent mode..");                
            } else {
                $this->output->info("Running {$scraperTypeName} scraper..");
            }            

            $this->logEntries->create($this);
            
            call_user_func($callback);
            
            $this->logEntries->setIsFinished();

            $this->output->info('Running scraper completed');
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
                                    
            try {
                $response = $this->client->request(
                    $this->requestMethod,
                    $this->scrapeUrl, 
                    $scraperProfile->getRequestOptions($graphQLCursor)
                );

                $scraperProfile->processOnRequestIsFulfilled($response);

                if ($scraperProfile->getHasNextPage()) {
                    $this->hasNextPage();

                    $nextPageCursor = $scraperProfile->getEndCursor();

                    $graphQLCursor->setNextPageCursor($nextPageCursor);                                    

                    $graphQLCursor->saveNextPageCursor($this);   
                } else {
                    $this->doesntHaveNextPage();
                }
            } catch (Exception $exception) {
                $this->output->error($exception->getMessage());

                $scraperProfile->processOnRequestIsFailed($exception);
            }                        
        });
    }

    protected function startScraperWithUrlPagination()
    {
        return $this->scrapeThroughCrawlCount(function () {                        
            return [];
        });
    }    
    
    protected function startScraperThrougCrawlQueue()
    {
        $scraperProfileClass = $this->scraperProfileClass();
    
        $scraperProfile = new $scraperProfileClass($this);
        
        $crawlQueueRequests = (function () {
            foreach ($this->crawlQueue->getPendingUrls() as $pendingUrl) {            
                yield $pendingUrl->id => new Request($this->requestMethod, $pendingUrl->url);
            }
        })();

        $pool = new Pool($this->client, $crawlQueueRequests, [
            'concurrency' => $this->concurrency,
            'options' => $this->client->getConfig(),
            'fulfilled' => function (Response $response, int $crawlQueueId) use ($scraperProfile) {                                
                $this->crawlQueue->setUrlIsFetched($crawlQueueId);

                $scraperProfile->processOnRequestIsFulfilled($response);
            },
            'rejected' => function (Exception $exception) use ($scraperProfile) {                
                $scraperProfile->processOnRequestIsFailed($exception);    
                
                $this->output->error($exception->getMessage());
                
                $this->logEntries->setError($exception->getMessage());
            }
        ]);

        $promise = $pool->promise();

        $promise->wait();
    }
    
    protected function scrapeThroughCrawlCount(Closure $callback)
    {               
        if ($this->continueFromLastSavedPageNumber) {
            $lastSavedPageNumber = $this->navigation->getLastPageNumber($this->source);

            $this->setStartFromPaginationNumber($lastSavedPageNumber);
        }

        try {            
            $requestCount = 1;
            
            while (true) {
                $this->setRequestCount($requestCount);
    
                $this->setCurrentRequestedPageNumber($requestCount);
    
                call_user_func($callback);                
                
                if ($requestCount >= $this->getMaximumCrawlCount() || ! $this->hasNextPage) {
                    break;
                }

                if ($requestCount > 1) {
                    $delayBetweenRequests = rand(
                        $this->getMinimumDelayBetweenRequests(), 
                        $this->getMaximumDelayBetweenRequests()
                    );
    
                    usleep($delayBetweenRequests);
                }
                
                $requestCount++;
            }
        } catch (Exception $exception) {
            $this->output->error($exception->getMessage());
                
            $this->logEntries->setError($exception->getMessage());
        }        
    }       
}