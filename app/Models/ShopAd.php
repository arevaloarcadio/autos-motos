<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopAd extends Model
{
    protected $fillable = [
        'ad_id',
        'category',
        'make_id',
        'model',
        'manufacturer',
        'code',
        'condition',
        'price',
        'price_contains_vat',
        'dealer_id',
        'dealer_show_room_id',
        'first_name',
        'last_name',
        'email_address',
        'address',
        'zip_code',
        'city',
        'country',
        'latitude',
        'longitude',
        'mobile_number',
        'landline_number',
        'whatsapp_number',
        'youtube_link',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/shop-ads/'.$this->getKey());
    }
}
