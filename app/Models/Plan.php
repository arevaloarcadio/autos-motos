<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'name',
        'price',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/plans/'.$this->getKey());
    }

    public function items()
    {
        return $this->hasMany(ItemPlan::class, 'plan_id');
    }
}
