<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentOption extends Model
{
    protected $fillable = [
        'equipment_id',
        'option_id',
        'is_base',
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
        return url('/admin/equipment-options/'.$this->getKey());
    }
}
