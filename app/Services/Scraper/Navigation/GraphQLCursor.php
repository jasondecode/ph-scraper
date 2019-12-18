<?php
namespace App\Services\Scraper\Navigation;

use App\Services\Scraper\Scraper;
use App\Services\Scraper\Models\Navigation;

class GraphQLCursor
{   
    /** @var string */
    protected $pageCursor = '';

    public function setNextPageCursor(string $nextPageCursor): GraphQLCursor
    {        
        $this->pageCursor = $nextPageCursor;

        return $this;
    }

    public function getCursor(Scraper $scraper): string
    {
        $startFromPaginationNumber = $scraper->getStartFromPaginationNumber();
        
        if ($scraper->getRequestCount() === 1 && ! is_null($startFromPaginationNumber)) {            
            $cursor = Navigation::where([
                ['type', Navigation::TYPE_GRAPHQL_CURSOR],
                ['source', $scraper->getSource()],
                ['page_number', $startFromPaginationNumber]
            ])
            ->orderBy('id', 'desc')
            ->first();
                          
            if (! is_null($cursor)) {
                return $cursor->code;
            }
        }
        
        return $this->pageCursor;
    } 
    
    public function saveNextPageCursor(Scraper $scraper)
    {
        Navigation::firstOrCreate([
            'type' => Navigation::TYPE_GRAPHQL_CURSOR,
            'source' => $scraper->getSource(),
            'page_number' => $scraper->getCurrentRequestedPageNumber(),
            'code' => $this->pageCursor
        ]);
    }
}