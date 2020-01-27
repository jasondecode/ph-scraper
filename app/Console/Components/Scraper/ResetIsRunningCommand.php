<?php

namespace App\Console\Components\Scraper;

use Illuminate\Console\Command;
use App\Services\Scraper\Models\LogEntriesScraper;

class ResetIsRunningCommand extends Command
{
    /** @var string*/
    protected $signature = 'scraper:reset-is-running {--source=}';

    /** @var string */
    protected $description = 'Reset the is running in the log entries';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle(LogEntriesScraper $logEntriesScraper)
    {
        if (is_null($this->option('source'))) {
            $source = $this->option('source');

            $this->info("reset {$source}");

            $logEntriesScraper->where([
                'is_running' => true
            ])
            ->update([
                'is_running' => false
            ]);
        } else {
            $this->info('reset all sources');

            $logEntriesScraper->where([
                'is_running' => true,
                'source' => $this->option('source')
            ])
            ->update([
                'is_running' => false
            ]);
        }

        $this->info('done');
    }
}
