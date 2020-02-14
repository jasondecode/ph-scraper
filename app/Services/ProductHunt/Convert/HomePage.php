<?php
namespace App\Services\ProductHunt\Convert;

class Homepage
{
    /** @var string|null */
    protected $endCursor;

    /** @var bool */
    protected $hasNextPage;

    public function __construct(
        ?string $endCursor,
        bool $hasNextPage
    ) {
        $this->endCursor = $endCursor;

        $this->hasNextPage = $hasNextPage;
    }

    public static function fromArray(array $pageInfo): Homepage
    {
        $endCursor = isset($pageInfo['endCursor']) && ! is_null($pageInfo['endCursor'])
            ? $pageInfo['endCursor']
            : null;

        return new self(
            $endCursor,
            $pageInfo['hasNextPage']
        );
    }

    public function getEndCursor(): ?string
    {
        return $this->endCursor;
    }

    public function getHasNextPage(): bool
    {
        return $this->hasNextPage;
    }
}