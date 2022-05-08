<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
     use \App\Traits\TraitUuid;
    protected $fillable = [
        'name',
        'model_id',
        'generation_id',
        'is_active',
        'ad_type',
        'external_id',
        'external_updated_at',
    
    ];
    
    
    protected $dates = [
        'external_updated_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/series/'.$this->getKey());
    }
}
