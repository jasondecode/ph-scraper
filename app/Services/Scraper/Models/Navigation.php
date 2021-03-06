<?php

namespace App\Services\Scraper\Models;

use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    protected $fillable = ['source', 'type', 'page_number', 'code'];

    /** @var int */
    const TYPE_GRAPHQL_CURSOR = 1;

    /** @var int */
    const TYPE_URL_PAGINATION = 2;

    public function getLastPageNumber(string $source): ?int
    {
        $navigation = $this->where('source', $source)
            ->orderBy('page_number', 'desc')
            ->first();

        return ! is_null($navigation)
            ? $navigation->page_number
            : null;
    }
}
