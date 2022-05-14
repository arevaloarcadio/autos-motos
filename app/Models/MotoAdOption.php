<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotoAdOption extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'moto_ad_id',
        'option_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/moto-ad-options/'.$this->getKey());
    }
}
