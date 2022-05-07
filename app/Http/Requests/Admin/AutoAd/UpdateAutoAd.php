<?php

namespace App\Http\Requests\Admin\AutoAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateAutoAd extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ad_id' => ['sometimes', Rule::unique('auto_ads', 'ad_id')->ignore($this->autoAd->getKey(), $this->autoAd->getKeyName()), 'string'],
            'price' => ['sometimes', 'numeric'],
            'price_contains_vat' => ['sometimes', 'boolean'],
            'vin' => ['nullable', 'string'],
            'doors' => ['nullable', 'integer'],
            'mileage' => ['sometimes', 'integer'],
            'exterior_color' => ['sometimes', 'string'],
            'interior_color' => ['nullable', 'string'],
            'condition' => ['sometimes', 'string'],
            'dealer_id' => ['nullable', 'string'],
            'dealer_show_room_id' => ['nullable', 'string'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'email_address' => ['sometimes', 'string'],
            'address' => ['sometimes', 'string'],
            'zip_code' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
            'country' => ['sometimes', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'landline_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'ad_fuel_type_id' => ['nullable', 'string'],
            'ad_body_type_id' => ['nullable', 'string'],
            'ad_transmission_type_id' => ['nullable', 'string'],
            'ad_drive_type_id' => ['nullable', 'string'],
            'first_registration_month' => ['sometimes', 'integer'],
            'first_registration_year' => ['sometimes', 'integer'],
            'engine_displacement' => ['nullable', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'make_id' => ['sometimes', 'string'],
            'model_id' => ['sometimes', 'string'],
            'generation_id' => ['nullable', 'string'],
            'series_id' => ['nullable', 'string'],
            'trim_id' => ['nullable', 'string'],
            'equipment_id' => ['nullable', 'string'],
            'additional_vehicle_info' => ['nullable', 'string'],
            'seats' => ['nullable', 'integer'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'latitude' => ['nullable', 'string'],
            'longitude' => ['nullable', 'string'],
            'geocoding_status' => ['nullable', 'string'],
            
        ];
    }

    /**
     * Modify input data
     *
     * @return array
     */
    public function getSanitized(): array
    {
        $sanitized = $this->validated();


        //Add your code for manipulation with request data here

        return $sanitized;
    }
}
