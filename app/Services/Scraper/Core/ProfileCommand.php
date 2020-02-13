<?php
namespace App\Services\Scraper\Core;

use Illuminate\Console\Command;
use App\Services\Scraper\Scraper;

class ProfileCommand extends Command
{   
    public function __construct()
    {        
        $this->signature = $this->signature . '{--silent}';

        parent::__construct();
    }

    public function boot(Scraper $scraper)
    {
        $scraper->setInputOptions($this->options());        
    }
}