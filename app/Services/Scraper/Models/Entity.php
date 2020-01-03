<?php

namespace App\Services\Scraper\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = ['entity_unique_code', 'source', 'entityable_id', 'entityable_type'];

    public function entityable()
    {
        return $this->morphTo();
    }
}
