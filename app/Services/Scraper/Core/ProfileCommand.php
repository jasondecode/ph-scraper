<?php
namespace App\Services\Scraper\Core;

use Illuminate\Console\Command;
use App\Services\Scraper\Scraper;

abstract class ProfileCommand extends Command
{   
    public function __construct()
    {        
        $this->signature = $this->signature . '{--silent} {--force}';

        parent::__construct();
    }

    public function boot(Scraper $scraper)
    {
        $scraper->setInputOptions($this->options());        
    }
}