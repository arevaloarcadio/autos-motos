<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Characteristic extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    
    protected $fillable = [
        'name',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/characteristics/'.$this->getKey());
    }

    public function sub_characteristics()
    {
        return $this->hasMany(SubCharacteristic::class);
    }
}
