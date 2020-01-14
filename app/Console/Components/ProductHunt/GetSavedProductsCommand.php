<?php

namespace App\Console\Components\ProductHunt;

use Illuminate\Console\Command;
use App\Services\Scraper\Models\Entity;
use App\Services\ProductHunt\Models\EntityProduct;

class GetSavedProductsCommand extends Command
{
    /** @var string*/
    protected $signature = 'producthunt:get-saved-products';

    /** @var string */
    protected $description = 'Get saved products';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle(Entity $entity)
    {        
        $entity->all()
            ->reverse()
            ->filter(function ($entity) {
                return $entity->entityable_type === EntityProduct::class;            
            })
            ->unique('entity_unique_code')
            ->map(function ($entity) {
                return [
                    'name' => $entity->entityable->name,
                    'votes' => $entity->entityable->votes,
                    'featured_at' => $entity->entityable->featured_at,
                    'topics' => $entity->entityable->getTopics()
                ];
            })
            ->sortByDesc('votes')
            ->slice(0, 10)
            ->dump();
    }
}
