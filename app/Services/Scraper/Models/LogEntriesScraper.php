<?php

namespace App\Services\Scraper\Models;

use Illuminate\Database\Eloquent\Model;

class LogEntriesScraper extends Model
{
    public function isRunning(string $source): bool
    {
        $entries = $this->where([
            ['source', '=', $source],
            ['is_running', '=', true]
        ])->get();

        return count($entries) > 0;
    }
}
