<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
     use \App\Traits\TraitUuid;
    protected $fillable = [
        'internal_name',
        'slug',
        'domain',
        'default_locale_id',
        'icon',
        'mobile_number',
        'whatsapp_number',
        'email_address',
        'order_index',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/markets/'.$this->getKey());
    }
}
