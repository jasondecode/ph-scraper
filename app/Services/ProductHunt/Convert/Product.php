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
    
    /** @var string */
    protected $featuredAt;    

    public function __construct(
        string $id,
        string $name,
        int $votes,
        string $featuredAt
    ) {
        $this->id = $id;

        $this->name = $name;

        $this->votes = $votes;

        $this->featuredAt = $featuredAt;        
    }

    public static function fromArray(array $edge): Product
    {
        $node = $edge['node'];

        return new self(
            $node['_id'],
            $node['name'],
            $node['votes_count'],
            $node['featured_at']
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

    public function getFeaturedAt(): string
    {
        return preg_replace('/T[0-9]{2}.*/', '', $this->featuredAt);
    }
}