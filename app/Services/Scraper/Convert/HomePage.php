<?php
namespace App\Services\Scraper\Convert;

class Homepage
{
    /** @var string */
    protected $endCursor;

    /** @var bool */
    protected $hasNextPage;

    public function __construct(
        string $endCursor,
        bool $hasNextPage
    ) {
        $this->endCursor = $endCursor;

        $this->hasNextPage = $hasNextPage;
    }

    public static function fromArray(array $pageInfo): self
    {
        return new self(
            $pageInfo['endCursor'],
            $pageInfo['hasNextPage']
        );
    }

    public function getEndCursor(): string
    {
        return $this->endCursor;
    }

    public function getHasNextPage(): bool
    {
        return $this->hasNextPage;
    }
}