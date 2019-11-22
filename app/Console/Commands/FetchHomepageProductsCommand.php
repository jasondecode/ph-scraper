<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Scraper\Scraper;

class FetchHomepageProductsCommand extends Command
{
    /** @var string*/
    protected $signature = 'product:fetch-homepage';

    /** @var string */
    protected $description = 'Fetch homepage products';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle(Scraper $scraper)
    {
        $this->info('fetching homepage..');

        $fetch = $scraper->fetchHomepageProducts();

        $this->info('products:');

        dump($fetch->getProducts());

        $this->info('page info:');

        dump($fetch->getPageInfo());

        $this->info('all done 🔥');
    }
}
