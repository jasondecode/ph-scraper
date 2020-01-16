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

    /** @var array */
    protected $topics;

    /** @var string */
    protected $shortenedUrl;

    /** @var string */
    protected $slug;

    public function __construct(
        string $id,
        string $name,
        int $votes,
        string $featuredAt,
        array $topics,
        string $shortenedUrl,
        string $slug
    ) {
        $this->id = $id;

        $this->name = $name;

        $this->votes = $votes;

        $this->featuredAt = $featuredAt; 
        
        $this->topics = $topics;

        $this->shortenedUrl = $shortenedUrl;

        $this->slug = $slug;
    }

    public static function fromArray(array $edge): Product
    {
        $node = $edge['node'];

        $featuredAt = preg_replace('/T[0-9]{2}.*/', '', $node['featured_at']);

        $topics = collect($node['topics']['edges'])->map(function (array $topic) {
            return $topic['node']['name'];
        })
        ->toArray();

        return new self(
            $node['_id'],
            $node['name'],
            $node['votes_count'],
            $featuredAt,
            $topics,
            $node['shortened_url'],
            $node['slug']
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
        return $this->featuredAt;
    }

    public function getTopics(): array
    {        
        return $this->topics;
    }

    public function getShortenedUrl(): string
    {
        return $this->shortenedUrl;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}