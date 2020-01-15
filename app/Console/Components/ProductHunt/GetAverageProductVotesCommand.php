<?php

namespace App\Console\Components\ProductHunt;

use Illuminate\Console\Command;
use App\Services\Scraper\Models\Entity;
use App\Services\ProductHunt\Models\EntityProduct;

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
            ->filter(function ($entity) {
                return $entity->entityable_type === EntityProduct::class;            
            })
            ->reverse()
            ->unique('entity_unique_code')
            ->map(function ($entity) {
                $featured_at = $entity->entityable->featured_at;

                return [                    
                    'votes' => $entity->entityable->votes,                    
                    'featured_month' => preg_replace('/-[0-9]{2}$/', '', $featured_at),                    
                ];
            })
            ->sortByDesc('votes')            
            ->groupBy('featured_month')                          
            ->each(function ($products, $month) {
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
