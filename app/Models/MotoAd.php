<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MotoAd extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'ad_id',
        'make_id',
        'custom_make',
        'model_id',
        'custom_model',
        'fuel_type_id',
        'body_type_id',
        'transmission_type_id',
        'drive_type_id',
        'first_registration_month',
        'first_registration_year',
        'inspection_valid_until_month',
        'inspection_valid_until_year',
        'last_customer_service_month',
        'last_customer_service_year',
        'owners',
        'weight_kg',
        'engine_displacement',
        'mileage',
        'power_kw',
        'gears',
        'cylinders',
        'emission_class',
        'fuel_consumption',
        'co2_emissions',
        'condition',
        'color',
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
        return url('/admin/moto-ads/'.$this->getKey());
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * @return BelongsTo
     */
    public function make()
    {
        return $this->belongsTo(Make::class, 'make_id');
    }

    /**
     * @return BelongsTo
     */
    public function model()
    {
        return $this->belongsTo(Models::class, 'model_id');
    }

    /**
     * @return BelongsTo
     */
    public function fuelType()
    {
        return $this->belongsTo(CarFuelType::class, 'fuel_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function bodyType()
    {
        return $this->belongsTo(CarBodyType::class, 'body_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function transmissionType()
    {
        return $this->belongsTo(CarTransmissionType::class, 'transmission_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function driveType()
    {
        return $this->belongsTo(CarWheelDriveType::class, 'drive_type_id');
    }

    /**
     * @return BelongsToMany
     */
    public function options()
    {
        return $this->belongsToMany(AutoOption::class, 'moto_ad_options', 'moto_ad_id', 'option_id')->withTimestamps();
    }

    /**
     * @return BelongsTo
     */
    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    /**
     * @return BelongsTo
     */
    public function dealerShowRoom()
    {
        return $this->belongsTo(DealerShowRoom::class);
    }

    /**
     * @return string|null
     */
    public function getTransmissionTypeNameAttribute(): ?string
    {
        return optional($this->transmissionType)->name;
    }

    /**
     * @return string|null
     */
    public function getTransmissionTypeShortAttribute(): ?string
    {
        if (null === $this->transmissionType) {
            return null;
        }

        return strtoupper(mb_substr($this->transmissionType->name, 0, 1, 'UTF-8'));
    }

    /**
     * @return string|null
     */
    public function getFuelTypeNameAttribute(): ?string
    {
        return optional($this->fuelType)->name;
    }

    /**
     * @return string|null
     */
    public function getFuelTypeShortAttribute(): ?string
    {
        if (null === $this->fuelType) {
            return null;
        }

        return strtoupper(mb_substr($this->fuelType->name, 0, 1, 'UTF-8'));
    }

    /**
     * @return string|null
     */
    public function getWheelDriveTypeNameAttribute(): ?string
    {
        return optional($this->driveType)->name;
    }

    /**
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format(floatval($this->price));
    }

    /**
     * @return string
     */
    public function getFormattedMileageAttribute(): string
    {
        return number_format(floatval($this->mileage));
    }

    public function getFormattedFuelConsumptionAttribute(): ?string
    {
        return $this->fuel_consumption ? number_format(floatval($this->fuel_consumption), 1) : null;
    }

    public function getFormattedCo2EmissionsAttribute(): ?string
    {
        return $this->co2_emissions ? number_format(floatval($this->co2_emissions), 1) : null;
    }

    public function getFormattedWeightKgAttribute(): ?string
    {
        return $this->weight_kg ? number_format(floatval($this->weight_kg), 1) : null;
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

    public function getTechnicalDescriptionAttribute(): string
    {
        $parts       = [
            __('ads.make_label')      => optional($this->make)->name ?? $this->custom_make,
            __('ads.model_label')     => optional($this->model)->name ?? $this->custom_model,
            __('ads.body_type_label') => optional($this->bodyType)->name,
        ];
        $filledParts = array_filter(
            $parts,
            function ($part) {
                return ! ($part === null);
            }
        );

        $pairs = [];
        foreach ($filledParts as $key => $part) {
            $pairs[] = sprintf('<strong>%s</strong>: %s', $key, $part);
        }

        return implode(', ', $pairs);
    }

    /**
     * @return string
     */
    public function getShortTechnicalDescriptionAttribute(): string
    {
        $parts       = [
            __('ads.make_label')      => optional($this->make)->name ?? $this->custom_make,
            __('ads.model_label')     => optional($this->model)->name ?? $this->custom_model,
            __('ads.body_type_label') => optional($this->bodyType)->name,
        ];
        $filledParts = array_filter(
            $parts,
            function ($part) {
                return ! ($part === null);
            }
        );

        $pairs = [];
        foreach ($filledParts as $key => $part) {
            $pairs[] = sprintf('<strong>%s</strong>: %s', $key, $part);
        }

        return implode(', ', $pairs);
    }

    /**
     * @return string
     */
    public function getShortAddressAttribute(): string
    {
        return sprintf(
            '%s, %s, %s',
            $this->zip_code,
            $this->city,
            $this->country
        );
    }

    /**
     * @return Collection
     */
    public function groupedOptions()
    {
        return $this->options->groupBy(
            function (AutoOption $option) {
                return $option->parent->name;
            }
        );
    }

    /**
     * @return string|null
     */
    public function getFormattedEngineDisplacementAttribute(): ?string
    {
        if (null === $this->engine_displacement) {
            return null;
        }

        return number_format(floatval($this->engine_displacement));
    }

    public function getInspectionValidUntilDateAttribute(): ?Carbon
    {
        if (null === $this->inspection_valid_until_month || null === $this->inspection_valid_until_year) {
            return null;
        }

        return Carbon::createFromDate($this->inspection_valid_until_year, $this->inspection_valid_until_month, 1);
    }

    public function getLastCustomerServiceDateAttribute(): ?Carbon
    {
        if (null === $this->last_customer_service_month || null === $this->last_customer_service_year) {
            return null;
        }

        return Carbon::createFromDate($this->last_customer_service_year, $this->last_customer_service_month, 1);
    }


    public function getFirstRegistrationDateAttribute(): Carbon
    {
        return Carbon::createFromDate($this->first_registration_year, $this->first_registration_month, 1);
    }

    public function getFirstRegistrationDateDisplayAttribute(): string
    {
        return $this->getFirstRegistrationDateAttribute()
                    ->format('m/Y');
    }

    public function getMileageShortAttribute(): ?string
    {
        if ($this->mileage > 1000) {
            return sprintf('%dk', $this->mileage / 1000);
        }

        return (string) $this->mileage;
    }
}
