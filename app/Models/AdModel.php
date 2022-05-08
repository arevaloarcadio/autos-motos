<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdModel extends Model
{
     use \App\Traits\TraitUuid;
    protected $fillable = [
        'name',
        'slug',
        'ad_type',
        'parent_id',
        'ad_make_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/ad-models/'.$this->getKey());
    }
}
