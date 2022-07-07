<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopAd extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    
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

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    public function model()
    {
        return $this->belongsTo(Model::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function dealerShowRoom()
    {
        return $this->belongsTo(DealerShowRoom::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format(floatval($this->price));
    }

    public function getShortAddressAttribute(): string
    {
        return sprintf(
            '%s, %s, %s',
            $this->zip_code,
            $this->city,
            $this->country
        );
    }

    public function getMaskedMobileNumberAttribute(): string
    {
        return substr($this->mobile_number, 0, 3) . '*******' . substr($this->mobile_number, -2);
    }

    public function getMaskedWhatsappNumberAttribute(): ?string
    {
        if (null === $this->whatsapp_number) {
            return null;
        }

        return substr($this->whatsapp_number, 0, 3) . '*******' . substr($this->whatsapp_number, -2);
    }

    public function getMaskedLandlineNumberAttribute(): ?string
    {
        if (null === $this->landline_number) {
            return null;
        }

        return substr($this->landline_number, 0, 3) . '*******' . substr($this->landline_number, -2);
    }

    public function getMaskedEmailAddressAttribute(): string
    {
        $emailComponents    = explode("@", $this->email_address);
        $emailComponents[0] = substr($emailComponents[0], 0, 2) . '***' . substr($emailComponents[0], -2);

        return implode('@', $emailComponents);
    }
}
