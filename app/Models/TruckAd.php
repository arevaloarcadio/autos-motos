<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TruckAd extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    
    protected $fillable = [
        'ad_id',
        'make_id',
        'custom_make',
        'model',
        'truck_type',
        'fuel_type_id',
        'vehicle_category_id',
        'transmission_type_id',
        'cab',
        'construction_year',
        'first_registration_month',
        'first_registration_year',
        'inspection_valid_until_month',
        'inspection_valid_until_year',
        'owners',
        'construction_height_mm',
        'lifting_height_mm',
        'lifting_capacity_kg_m',
        'permanent_total_weight_kg',
        'allowed_pulling_weight_kg',
        'payload_kg',
        'max_weight_allowed_kg',
        'empty_weight_kg',
        'loading_space_length_mm',
        'loading_space_width_mm',
        'loading_space_height_mm',
        'loading_volume_m3',
        'load_capacity_kg',
        'operating_weight_kg',
        'operating_hours',
        'axes',
        'wheel_formula',
        'hydraulic_system',
        'seats',
        'mileage',
        'power_kw',
        'emission_class',
        'fuel_consumption',
        'co2_emissions',
        'condition',
        'interior_color',
        'exterior_color',
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
        return url('/admin/truck-ads/'.$this->getKey());
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
    public function vehicleCategory()
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
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
    public function transmissionType()
    {
        return $this->belongsTo(CarTransmissionType::class, 'transmission_type_id');
    }

    /**
     * @return BelongsToMany
     */
    public function options()
    {
        return $this->belongsToMany(
            AutoOption::class,
            'truck_ad_options',
            'truck_ad_id',
            'option_id'
        )->withTimestamps();
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

    public function getInternalTruckTypeAttribute(): string
    {
        return (string) Str::of($this->truck_type)->replace('-', '_')->lower();
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
    public function getVehicleCategoryNameAttribute(): ?string
    {
        return optional($this->vehicleCategory)->name;
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
        return $this->formatDecimal(floatval($this->fuel_consumption));
    }

    public function getFormattedCo2EmissionsAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->co2_emissions));
    }

    public function getFormattedLoadingSpaceLengthMmAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->loading_space_length_mm));
    }

    public function getFormattedLoadingSpaceWidthMmAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->loading_space_width_mm));
    }

    public function getFormattedLoadingSpaceHeightMmAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->loading_space_height_mm));
    }

    public function getFormattedMaxWeightAllowedKgAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->max_weight_allowed_kg));
    }

    public function getFormattedPayloadKgAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->payload_kg));
    }

    public function getFormattedConstructionHeightMmAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->construction_height_mm));
    }

    public function getFormattedLiftingHeightMmAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->lifting_height_mm));
    }

    public function getFormattedLiftingCapacityKgMAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->lifting_capacity_kg_m));
    }

    public function getFormattedLoadCapacityKgAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->load_capacity_kg));
    }

    public function getFormattedOperatingWeightKgAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->operating_weight_kg));
    }

    public function getFormattedPermanentTotalWeightKgAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->permanent_total_weight_kg));
    }

    public function getFormattedAllowedPullingWeightKgAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->allowed_pulling_weight_kg));
    }

    public function getFormattedEmptyWeightKgAttribute(): ?string
    {
        return $this->formatDecimal(floatval($this->empty_weight_kg));
    }

    public function getFormattedLoadingVolumeM3Attribute(): ?string
    {
        return $this->formatDecimal(floatval($this->loading_volume_m3));
    }

    private function formatDecimal(?float $value, int $numberOfDecimals = 1): ?string
    {
        return $value ? number_format($value, $numberOfDecimals) : null;
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
            __('ads.make_label')  => optional($this->make)->name ?? $this->custom_make,
            __('ads.model_label') => $this->model,
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
            __('ads.make_label')  => optional($this->make)->name ?? $this->custom_make,
            __('ads.model_label') => $this->model,
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function groupedOptions(): Collection
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

    public function getFirstRegistrationDateAttribute(): ?Carbon
    {
        if ($this->first_registration_year && $this->first_registration_month) {
            return Carbon::createFromDate($this->first_registration_year, $this->first_registration_month, 1);
        }

        return null;
    }

    public function getFirstRegistrationDateDisplayAttribute(): ?string
    {
        return optional($this->getFirstRegistrationDateAttribute())
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
