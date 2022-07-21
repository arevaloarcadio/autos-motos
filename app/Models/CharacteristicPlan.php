<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacteristicPlan extends Model
{
    use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
      
    protected $fillable = [
        'vehicle_ads',
        'rental_ads',
        'promotion_month',
        'front_page_promotion',
        'video_a_day',
        'mechanics_rental_ads',
        'plan_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/characteristic-plans/'.$this->getKey());
    }
}
