<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'name',
        'slug',
        'make_id',
        'is_active',
        'ad_type',
        'external_id',
        'external_updated_at',
        'sub_model_id'

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
        return url('/admin/models/'.$this->getKey());
    }

    public function generation()
    {
        return $this->hasMany(Generation::class,'model_id');
    }

    /**
     * Get the sub_model associated with the Models
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sub_model()
    {
        return $this->hasOne(SubmodelByModel::class, 'model_id', 'id');
    }
}
