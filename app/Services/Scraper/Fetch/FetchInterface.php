<?php
namespace App\Services\Scraper\Fetch;

use App\Services\Scraper\Http\GuzzleClient;

interface FetchInterface
{
    public function fetch(GuzzleClient $guzzleClient);
}