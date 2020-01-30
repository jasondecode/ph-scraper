<?php

namespace App\Services\Scraper\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CrawlQueue extends Model
{
    protected $fillable = ['source', 'url', 'is_fetched'];

    public function getPendingUrls(): Collection
    {
        return $this->where(['is_fetched' => false])->get();
    }   
}
