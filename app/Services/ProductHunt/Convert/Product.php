<?php
namespace App\Services\ProductHunt\Convert;

class Product
{
    /** @var string */
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

    public static function fromArray(array $edge): Product
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