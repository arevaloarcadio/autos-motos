<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'name',
        'trim_id',
        'year',
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
        return url('/admin/equipment/'.$this->getKey());
    }
}
