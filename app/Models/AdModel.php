<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdModel extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'name',
        'slug',
        'ad_type',
        'parent_id',
        'ad_make_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/ad-models/'.$this->getKey());
    }

    public function make()
    {
        return $this->belongsTo(AdMake::class, 'ad_make_id');
    }

    public function children()
    {
        return $this->hasMany(AdModel::class, 'parent_id')->orderBy('name', 'asc');
    }

    public function parent()
    {
        return $this->belongsTo(AdModel::class, 'parent_id');
    }

    protected function getSluggableField(): string
    {
        return 'name';
    }
}
