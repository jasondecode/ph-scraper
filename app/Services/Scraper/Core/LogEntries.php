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
        $this->logEntriesModel->is_running = true;
        
        $this->logEntriesModel->source = $scraper->getSource();

        $this->logEntriesModel->runned_at = date('Y-m-d h:i:s');

        $this->logEntriesModel->save();

        return $this;
    }

    public function setIsFinished(): LogEntries
    {
        $this->logEntriesModel->is_running = false;

        $this->logEntriesModel->completed_at = date('Y-m-d h:i:s');
        
        $this->logEntriesModel->save();

        return $this;
    }

    public function setError(string $error)
    {
        $this->logEntriesModel->error = $error;
        
        $this->logEntriesModel->save();

        return $this;
    }
}
