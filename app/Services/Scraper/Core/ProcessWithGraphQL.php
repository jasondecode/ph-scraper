<?php
namespace App\Services\Scraper\Core;

use App\Services\Scraper\Navigation\GraphQLCursor;

interface ProcessWithGraphQL
{
    public function getEndCursor(): ?string;

    public function getHasNextPage(): bool;

    public function getRequestOptions(GraphQLCursor $graphQLCursor): array;
}