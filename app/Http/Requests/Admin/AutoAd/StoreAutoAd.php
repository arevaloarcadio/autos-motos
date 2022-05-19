<?php

namespace App\Http\Requests\Admin\AutoAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreAutoAd extends FormRequest
{
    use \App\Traits\ErrorMessageValidations;
    
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
            'ad_id' => ['required', Rule::unique('auto_ads', 'ad_id'), 'string'],
            'price' => ['required', 'numeric'],
            'price_contains_vat' => ['required', 'boolean'],
            'vin' => ['nullable', 'string'],
            'doors' => ['nullable', 'integer'],
            'mileage' => ['required', 'integer'],
            'exterior_color' => ['required', 'string'],
            'interior_color' => ['nullable', 'string'],
            'condition' => ['required', 'string'],
            'dealer_id' => ['nullable', 'string'],
            'dealer_show_room_id' => ['nullable', 'string'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'email_address' => ['required', 'string'],
            'address' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'landline_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'ad_fuel_type_id' => ['nullable', 'string'],
            'ad_body_type_id' => ['nullable', 'string'],
            'ad_transmission_type_id' => ['nullable', 'string'],
            'ad_drive_type_id' => ['nullable', 'string'],
            'first_registration_month' => ['required', 'integer'],
            'first_registration_year' => ['required', 'integer'],
            'engine_displacement' => ['nullable', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'make_id' => ['required', 'string'],
            'model_id' => ['required', 'string'],
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
