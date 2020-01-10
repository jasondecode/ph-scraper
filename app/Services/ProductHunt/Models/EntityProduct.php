<?php

namespace App\Services\ProductHunt\Models;

use Illuminate\Database\Eloquent\Model;

class EntityProduct extends Model
{    
    protected $fillable = ['votes', 'name', 'featured_at', 'updated_at'];

    public function entity()
    {
        return $this->morphOne(\App\Services\Scraper\Models\Entity::class, 'entityable');
    }
}