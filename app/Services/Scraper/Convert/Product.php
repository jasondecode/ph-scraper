<?php
namespace App\Services\Scraper\Convert;

class Product
{
    /** @var string $id */
    protected $id;

    /** @var string */
    protected $name;

    /** @var int */
    protected $votes;

    public function __construct(
        string $id,
        string $name,
        int $votes
    ) {
        $this->id = $id;

        $this->name = $name;

        $this->votes = $votes;
    }

    public static function fromArray(array $edge): self
    {
        $node = $edge['node'];

        return new self(
            $node['_id'],
            $node['name'],
            $node['votes_count']
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVotes(): int
    {
        return $this->votes;
    }
}