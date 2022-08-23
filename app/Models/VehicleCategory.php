<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleCategory extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    
    protected $fillable = [
        'internal_name',
        'slug',
        'ad_type',
        'icon_url',
        'category'
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/vehicle-categories/'.$this->getKey());
    }
}
