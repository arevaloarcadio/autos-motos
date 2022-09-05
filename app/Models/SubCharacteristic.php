<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCharacteristic extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;

    protected $fillable = [
        'name',
        'characteristic_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
   // protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/sub-characteristics/'.$this->getKey());
    }

    public function characteristic()
    {
        return $this->belongsTo(Characteristic::class);
    }
}
