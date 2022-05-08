<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
     use \App\Traits\TraitUuid;
    protected $fillable = [
        'locale_id',
        'translation_key',
        'translation_value',
        'resource_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/translations/'.$this->getKey());
    }
}
