<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
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

    public function trim()
    {
        return $this->belongsTo(Trim::class);
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'equipment_options')
                    ->withPivot('is_base');
    }
}
