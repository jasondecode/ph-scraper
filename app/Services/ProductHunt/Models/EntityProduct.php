<?php

namespace App\Services\ProductHunt\Models;

use Illuminate\Database\Eloquent\Model;

class EntityProduct extends Model
{    
    public function entity()
    {
        return $this->morphOne(\App\Services\Scraper\Models\Entity::class, 'entityable');
    }
}
