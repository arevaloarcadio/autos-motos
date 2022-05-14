<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
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
        return url('/admin/options/'.$this->getKey());
    }

    public function parent()
    {
        return $this->belongsTo(Option::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Option::class, 'parent_id')->orderBy('name', 'asc');;
    }
}
