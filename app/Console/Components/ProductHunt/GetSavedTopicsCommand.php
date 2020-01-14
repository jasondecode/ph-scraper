<?php

namespace App\Console\Components\ProductHunt;

use Illuminate\Console\Command;
use App\Services\Scraper\Models\Entity;
use App\Services\ProductHunt\Models\EntityProduct;

class GetSavedTopicsCommand extends Command
{
    /** @var string*/
    protected $signature = 'producthunt:get-saved-topics';

    /** @var string */
    protected $description = 'Get saved topics';

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
            ->map(function ($entity) {
                return $entity->entityable->getTopics();
            })           
            ->flatten()
            ->unique()
            ->dump();
    }
}
