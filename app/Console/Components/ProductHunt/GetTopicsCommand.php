<?php

namespace App\Console\Components\ProductHunt;

use Illuminate\Console\Command;
use App\Services\Scraper\Models\Entity;
use App\Services\ProductHunt\Models\EntityProduct;

class GetTopicsCommand extends Command
{
    /** @var string*/
    protected $signature = 'producthunt:get-topics';

    /** @var string */
    protected $description = 'Get saved topics';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle(Entity $entity)
    {        
        $entity->where([
            'entityable_type' => EntityProduct::class
        ])
        ->get()
        ->map(function (Entity $entity) {
            return $entity->entityable->getTopics();
        })           
        ->flatten()
        ->unique()
        ->values()
        ->dump();
    }
}
