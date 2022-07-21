<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacteristicPromotionPlan extends Model
{
    protected $fillable = [
        'vehicle_ads',
        'shop_ads',
        'rental_ads',
        'mechanic_ads',
        'front_page_promotion',
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
        return url('/admin/characteristic-promotion-plans/'.$this->getKey());
    }
}
