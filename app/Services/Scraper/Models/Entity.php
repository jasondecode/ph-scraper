<?php

namespace App\Services\Scraper\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Entity extends Model
{   
    /** @var string */
    const MODE_CREATE = 'create';

    /** @var string */
    const MODE_UPDATE = 'update';

    /** @var array  */
    protected $fillable = ['entity_unique_code', 'source', 'mode', 'entityable_id', 'entityable_type'];

    public function entityable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createOrUpdate($find, $profileAttributes): Entity
    {
        $entity = $this->where($find)->orderBy('id', 'desc')->first();

        $entityProfileClass = $find['entityable_type'];

        if (! is_null($entity)) {
            $entity->entityable->fill($profileAttributes);
        
            if ($entity->entityable->isDirty()) {
                $entityAttributes = array_merge($find, [
                    'mode' => self::MODE_UPDATE
                ]);

                $this->saveEntity($profileAttributes, $entityAttributes, $entityProfileClass);
            }             
        } else {            
            $entityAttributes = array_merge($find, [
                'mode' => self::MODE_CREATE
            ]);            

            $this->saveEntity($profileAttributes, $entityAttributes, $entityProfileClass);
        }

        return $this;
    }

    protected function saveEntity($profileAttributes, $entityAttributes, $entityProfileClass): Entity
    {                   
        $entityProduct = new $entityProfileClass($profileAttributes);

        $entityProduct->save();
        
        $entityProduct->entity()->save(new Entity($entityAttributes));

        return $this;
    }
}
