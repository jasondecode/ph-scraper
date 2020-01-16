<?php

namespace App\Console\Components\ProductHunt;

use Illuminate\Console\Command;
use App\Services\Scraper\Models\Entity;
use App\Services\ProductHunt\Models\EntityProduct;
use Illuminate\Support\Collection;

class GetaverageProductVotesCommand extends Command
{
    /** @var string*/
    protected $signature = 'producthunt:get-average-product-votes';

    /** @var string */
    protected $description = 'Get average product votes from each month';

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
                $featuredAt = $entity->entityable->featured_at;

                return [                    
                    'votes' => $entity->entityable->votes,                    
                    'featured_month' => preg_replace('/-[0-9]{2}$/', '', $featuredAt),                    
                ];
            })
            ->sortByDesc('votes')            
            ->groupBy('featured_month')                
            ->each(function (Collection $products, string $month) {
                $totalVotes = $products->sum('votes');

                $totalProducts = $products->count();
                
                dump([
                    'featured_month' => $month,
                    'average_product_votes' => (int) round($totalVotes / $totalProducts, 0),
                    'total_products' => $totalProducts
                ]);
            });            
    }
}
