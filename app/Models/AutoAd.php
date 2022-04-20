<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoAd extends Model
{
    protected $fillable = [
        'ad_id',
        'price',
        'vin',
        'doors',
        'mileage',
        'exterior_color',
        'interior_color',
        'condition',
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
        'ad_fuel_type_id',
        'ad_body_type_id',
        'ad_transmission_type_id',
        'ad_drive_type_id',
        'first_registration_month',
        'first_registration_year',
        'engine_displacement',
        'power_hp',
        'owners',
        'inspection_valid_until_month',
        'inspection_valid_until_year',
        'make_id',
        'model_id',
        'generation_id',
        'series_id',
        'trim_id',
        'equipment_id',
        'additional_vehicle_info',
        'seats',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/auto-ads/'.$this->getKey());
    }

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * @return BelongsTo
     */
    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class, 'make_id');
    }

    /**
     * @return BelongsTo
     */
    public function model(): BelongsTo
    {
        return $this->belongsTo(Model::class, 'model_id');
    }

    /**
     * @return BelongsTo
     */
    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class, 'generation_id');
    }

    /**
     * @return BelongsTo
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class, 'series_id');
    }

    /**
     * @return BelongsTo
     */
    public function trim(): BelongsTo
    {
        return $this->belongsTo(Trim::class, 'trim_id');
    }

    /**
     * @return BelongsTo
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    /**
     * @return BelongsTo
     */
    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(CarFuelType::class, 'ad_fuel_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function bodyType(): BelongsTo
    {
        return $this->belongsTo(CarBodyType::class, 'ad_body_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function transmissionType(): BelongsTo
    {
        return $this->belongsTo(CarTransmissionType::class, 'ad_transmission_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function driveType(): BelongsTo
    {
        return $this->belongsTo(CarWheelDriveType::class, 'ad_drive_type_id');
    }

    /**
     * @return BelongsToMany
     */
    public function autoOptions(): BelongsToMany
    {
        return $this->belongsToMany(AutoOption::class, 'auto_ad_options', 'auto_ad_id')->withTimestamps();
    }

    /**
     * @return BelongsTo
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    /**
     * @return BelongsTo
     */
    public function dealerShowRoom(): BelongsTo
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
            __('ads.make_label')       => $this->make->name,
            __('ads.model_label')      => $this->model->name,
            __('ads.generation_label') => optional($this->generation)->name,
            __('ads.trim_label')       => optional($this->trim)->name,
            __('ads.body_type_label')  => optional($this->bodyType)->name,
        ];
        $filledParts = array_filter(
            $parts,
            function ($part) {
                return ! ($part === null);
            }
        );

        $pairs = [];
        foreach ($filledParts as $key => $part) {
            $pairs[] = sprintf("<strong style='font-weight: 900 !important;'>%s</strong>: %s", $key, $part);
        }

        return implode(', ', $pairs);
    }

    /**
     * @return string
     */
    public function getShortTechnicalDescriptionAttribute(): string
    {
        $parts       = [
            __('ads.make_label')       => $this->make->name,
            __('ads.model_label')      => $this->model->name,
            __('ads.generation_label') => optional($this->generation)->name,
            __('ads.trim_label')       => optional($this->trim)->name,
            __('ads.body_type_label')  => optional($this->bodyType)->name,
        ];
        $filledParts = array_filter(
            $parts,
            function ($part) {
                return ! ($part === null);
            }
        );

        $pairs = [];
        foreach ($filledParts as $key => $part) {
            $pairs[] = sprintf("<strong style='font-weight: 900 !important;'>%s</strong>: %s", $key, $part);
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
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return sprintf(
            '%s, %s, %s, %s',
            $this->address,
            $this->zip_code,
            $this->city,
            ucfirst(mb_strtolower($this->country))
        );
    }

    /**
     * @return Collection
     */
    public function groupedCarOptions(): Collection
    {
        return $this->autoOptions->groupBy(
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

    public function getFirstRegistrationDateAttribute(): Carbon
    {
        return Carbon::createFromDate($this->first_registration_year, $this->first_registration_month, 1);
    }

    public function getFirstRegistrationDateDisplayAttribute(): string
    {
        return $this->getFirstRegistrationDateAttribute()
                    ->format('m/Y');
    }

    public function getAdditionalVehicleInfoAttribute(?string $value): ?string
    {
        return $value ? htmlspecialchars_decode($value) : null;
    }

    public function getMileageShortAttribute(): ?string
    {
        if ($this->mileage > 1000) {
            return sprintf('%dk', $this->mileage / 1000);
        }

        return (string) $this->mileage;
    }
}
