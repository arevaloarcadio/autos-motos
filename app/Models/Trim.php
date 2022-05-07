<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trim extends Model
{
    protected $fillable = [
        'name',
        'model_id',
        'series_id',
        'production_year_start',
        'production_year_end',
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
        return url('/admin/trims/'.$this->getKey());
    }
}
