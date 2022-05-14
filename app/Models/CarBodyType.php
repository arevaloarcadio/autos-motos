<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarBodyType extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'internal_name',
        'slug',
        'icon_url',
        'external_name',
        'ad_type',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/car-body-types/'.$this->getKey());
    }

    /*public function getIconUrlAttribute(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }
        $file = Storage::disk('s3')->url($value);
        
        return $file;
    }*/
}
