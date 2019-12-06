<?php
namespace App\Services\Scraper\Navigation;

class GraphQLCursor
{   
    /** @var string */
    protected $pageCursor = '';

    public function getPageCursor(): string
    {
        return $this->pageCursor;
    }

    public function setNextPageCursor(string $nextPageCursor): GraphQLCursor
    {
        $this->pageCursor = $nextPageCursor;

        return $this;
    }
}