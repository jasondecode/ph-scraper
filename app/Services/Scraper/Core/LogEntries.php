<?php

namespace App\Services\Scraper\Core;

use App\Services\Scraper\Models\LogEntriesScraper as LogEntriesScraperModel;

class LogEntries
{
    public function __construct(LogEntriesScraperModel $logEntriesModel)
    {
        $this->logEntriesModel = $logEntriesModel;
    }

    public function isRunning($source): bool
    {
        $entries = $this->logEntriesModel->where([
            ['source', '=', $source],
            ['is_running', '=', true]
        ])->get();

        return count($entries) > 0;
    }

    public function create($scraper): LogEntries
    {
        $this->logEntriesModel->is_running = true;
        
        $this->logEntriesModel->source = $scraper->getSource();

        $this->logEntriesModel->run_at = date('Y-m-d h:i:s');

        $this->logEntriesModel->save();

        return $this;
    }

    public function setRunAtIsFinished(): LogEntries
    {
        $this->logEntriesModel->is_running = false;

        return $this;
    }

    public function setError()
    {

    }
}
