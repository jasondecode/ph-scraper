<?php
namespace App\Services\Scraper\Navigation;

class GraphQLCursor
{   
    /** @var string */
    protected $nextPageCursor = '';

    public function getNextPageCursor(): string
    {
        return $this->nextPageCursor;
    }

    public function setNextPageCursor(string $nextPageCursor): GraphQLCursor
    {
        $this->nextPageCursor = $nextPageCursor;

        return $this;
    }
}