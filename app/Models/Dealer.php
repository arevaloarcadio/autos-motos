<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'slug',
        'company_name',
        'vat_number',
        'address',
        'zip_code',
        'city',
        'country',
        'logo_path',
        'email_address',
        'phone_number',
        'status',
        'description',
        'external_id',
        'source',
        'code'
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['review_ratings'];
    
    public function getReviewRatingsAttribute()
    {
        $score = Review::selectRaw('FORMAT(AVG(score),2) as ratings')
                ->join('ads','ads.id','reviews.ad_id')
                ->leftJoin('auto_ads','auto_ads.ad_id','ads.id')
                ->leftJoin('moto_ads','moto_ads.ad_id','ads.id')
                ->leftJoin('mobile_home_ads','mobile_home_ads.ad_id','ads.id')
                ->leftJoin('truck_ads','truck_ads.ad_id','ads.id')
                ->where(function($query){
                    $query->orWhere('auto_ads.dealer_id',$this->getKey())
                          ->orWhere('moto_ads.dealer_id',$this->getKey())
                          ->orWhere('mobile_home_ads.dealer_id',$this->getKey())
                          ->orWhere('truck_ads.dealer_id',$this->getKey());
                })
                ->first();

         if ($score['ratings'] == null) {
            return 0;
        }
    
        return $score['ratings'];
    }

    /**
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany
     */
    public function showRooms()
    {
        return $this->hasMany(DealerShowRoom::class)->orderBy('name', 'ASC');
    }

    /**
     * @param string|null $value
     *
     * @return string|null
     */
    /*public function getLogoPathAttribute(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return Storage::disk('s3')->url($value);
    }*/
}
