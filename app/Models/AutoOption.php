<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoOption extends Model
{
    protected $fillable = [
        'internal_name',
        'slug',
        'parent_id',
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
        return url('/admin/auto-options/'.$this->getKey());
    }
}
