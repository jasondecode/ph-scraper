<?php

namespace App\Console\Components\ProductHunt;

use Illuminate\Console\Command;
use App\Services\Scraper\Models\Entity;
use App\Services\ProductHunt\Models\EntityProduct;

class GetProductsCommand extends Command
{
    /** @var string*/
    protected $signature = 'producthunt:get-products';

    /** @var string */
    protected $description = 'Get saved products';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle(Entity $entity)
    {        
        $entity->all()
            ->filter(function (Entity $entity) {
                return $entity->entityable_type === EntityProduct::class;            
            })
            ->reverse()
            ->unique('entity_unique_code')
            ->map(function (Entity $entity) {
                $featured_at = $entity->entityable->featured_at;

                return [
                    'name' => $entity->entityable->name,
                    'votes' => $entity->entityable->votes,
                    'featured_at' => $featured_at,
                    'featured_month' => preg_replace('/-[0-9]{2}$/', '', $featured_at),
                    'topics' => $entity->entityable->getTopics(),
                    'landing_page' => "https://producthunt.com{$entity->entityable->shortened_url}",
                    'post' => "https://producthunt.com/posts/{$entity->entityable->slug}"
                ];
            })
            ->filter(function (array $entity) {
                if (empty($entity['topics'])) {
                    return false;
                }
                
                $filter = config('producthunt.filter_topics_regex');

                $reject = config('producthunt.reject_topics_regex');
                
                return ! collect($entity['topics'])->map(function (string $topic) use ($filter, $reject) {
                    $isRejected = ! empty($reject)
                        ? preg_match($reject, $topic)
                        : false;

                    if (preg_match($filter, $topic) && ! $isRejected) {
                        return true;   
                    }
                })
                ->contains(false);
            })
            ->sortByDesc('votes')
            ->slice(0, 10)    
            ->dump();          
    }
}
