<?php

namespace App\Services\Scraper\Core;

use App\Services\Scraper\Scraper;
use App\Services\Scraper\Models\LogEntriesScraper as LogEntriesScraperModel;

class LogEntries
{
    /** @var  App\Services\Scraper\Models\LogEntriesScraper */
    protected $logEntriesModel;

    public function __construct(LogEntriesScraperModel $logEntriesModel)
    {
        $this->logEntriesModel = $logEntriesModel;
    }

    public function isRunning(string $source): bool
    {
        return $this->logEntriesModel->isRunning($source);
    }

    public function create(Scraper $scraper): LogEntries
    {        
        $isRunning = ! $scraper->getInputOption('silent');
        
        $this->logEntriesModel->fill([
            'is_running' => $isRunning,
            'source' => $scraper->getSource(),
            'runned_at' => date('Y-m-d h:i:s')            
        ])
        ->save();
        
        return $this;
    }

    public function setIsFinished(): LogEntries
    {         
        $this->logEntriesModel->fill([
            'is_running' => false,
            'completed_at' => date('Y-m-d h:i:s')
        ])
        ->save();

        return $this;
    }

    public function setNavigationSettings(Scraper $scraper): LogEntries
    {
        $this->logEntriesModel->fill([
            'start_from_pagination_number' => $scraper->getStartFromPaginationNumber(),
            'maximum_crawl_count' => $scraper->getMaximumCrawlCount()
        ])
        ->save();

        return $this;        
    }

    public function setError(string $error)
    {
        $this->logEntriesModel->fill([
            'error' => $error
        ])
        ->save();

        return $this;
    }
}
