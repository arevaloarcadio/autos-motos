<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enum\Ad\AdImageVersionTypeEnum;
use Storage;

class Ad extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'thumbnail',
        'status',
        'type',
        'is_featured',
        'user_id',
        'market_id',
        'external_id',
        'source',
        'images_processing_status',
        'images_processing_status_text',
        'csv_ad_id'
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    /*protected $appends = ['resource_url'];


    public function getResourceUrlAttribute()
    {
        return url('/admin/ads/'.$this->getKey());
    }*/

    public function csv_ad()
    {
        return $this->belongsTo(CsvAd::class,'csv_ad_id');
    }

     public function autoAd()
    {
        return $this->hasOne(AutoAd::class);
    }

    public function motoAd()
    {
        return $this->hasOne(MotoAd::class);
    }

    public function mobileHomeAd()
    {
        return $this->hasOne(MobileHomeAd::class);
    }

    public function truckAd()
    {
        return $this->hasOne(TruckAd::class);
    }

    public function mechanicAd()
    {
        return $this->hasOne(MechanicAd::class);
    }

    public function rentalAd()
    {
        return $this->hasOne(RentalAd::class);
    }

    public function shopAd()
    {
        return $this->hasOne(ShopAd::class);
    }

    /*public function getSpecificAd(): ?Model
    {
        if ($this->autoAd instanceof AutoAd) {
            return $this->autoAd;
        }
        if ($this->motoAd instanceof MotoAd) {
            return $this->motoAd;
        }
        if ($this->mobileHomeAd instanceof MobileHomeAd) {
            return $this->mobileHomeAd;
        }
        if ($this->truckAd instanceof TruckAd) {
            return $this->truckAd;
        }
        if ($this->shopAd instanceof ShopAd) {
            return $this->shopAd;
        }
        if ($this->rentalAd instanceof RentalAd) {
            return $this->rentalAd;
        }
        if ($this->mechanicAd instanceof MechanicAd) {
            return $this->mechanicAd;
        }

        return null;
    }*/

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    public function images()
    {
        return $this->hasMany(AdImage::class, 'ad_id')->orderBy('order_index');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'ad_id');
    }

   /* public function getThumbnailAttribute(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if ( ! (false === filter_var($value, FILTER_VALIDATE_URL))) {
            return $value;
        }

        return Storage::disk('s3')->url($value);
    }*/

    /**
     * @return string|null
     */
    public function getThumbnailPath(): ?string
    {
        if (0 === count($this->images)) {
            return null;
        }
        /** @var AdImage $image */
        $image = $this->images->first();

        $path = $image->path;

        $thumbnailVersion = $image->versions->firstWhere('name', AdImageVersionTypeEnum::THUMBNAIL);

        if ($thumbnailVersion instanceof AdImageVersion) {
            $path = $thumbnailVersion->path;
        }

        return $path;
    }

    public function getStatusTextAttribute(): ?string
    {
        if (null === $this->status) {
            return null;
        }

        return ApprovalStatusEnum::getString($this->status);
    }

    /*public function getTitleAttribute(string $value): string
    {
        return htmlspecialchars_decode($value);
    }*/

    public function onboardingRequests()
    {
        return $this->hasMany(OnboardingRequest::class);
    }

    public function rentalOrMechanicEmailAddress(): ?string
    {
        switch ($this->type) {
            case AdTypeEnum::MECHANIC_SLUG:
                return $this->mechanicAd->email_address;
            case AdTypeEnum::RENTAL_SLUG:
                return $this->rentalAd->email_address;
            default:
                return null;
        }
    }

    public function characteristics(){
      return $this->belongsToMany(SubCharacteristic::class)
                  ->using(AdSubCharacteristic::class);
    }

    /*public function rejected_comments(){
      return $this->belongsToMany('App\Models\RejectedComment','rejected_comments')
                  ->using('App\Models\AdRejectedComment');
    }*/
}
