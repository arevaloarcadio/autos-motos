<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Model extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'make_id',
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
        return url('/admin/models/'.$this->getKey());
    }
}
