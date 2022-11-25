<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Make extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'ad_type',
        'external_id',
        'external_updated_at',
        'has_sub_model',

    ];


    protected $dates = [
        'external_updated_at',
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];

    public function grupos()
    {
    return $this->hasMany('App\Models\Submodel','make_id');
    }

    public function models()
    {
    return $this->hasMany('App\Models\Models','make_id');
    }

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/makes/'.$this->getKey());
    }
}
