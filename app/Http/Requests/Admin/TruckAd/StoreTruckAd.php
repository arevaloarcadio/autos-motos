<?php

namespace App\Http\Requests\Admin\TruckAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreTruckAd extends FormRequest
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
            'ad_id' => ['required', Rule::unique('truck_ads', 'ad_id'), 'string'],
            'make_id' => ['nullable', 'string'],
            'custom_make' => ['nullable', 'string'],
            'model' => ['required', 'string'],
            'truck_type' => ['required', 'string'],
            'fuel_type_id' => ['nullable', 'string'],
            'vehicle_category_id' => ['required', 'string'],
            'transmission_type_id' => ['nullable', 'string'],
            'cab' => ['nullable', 'string'],
            'construction_year' => ['nullable', 'integer'],
            'first_registration_month' => ['nullable', 'integer'],
            'first_registration_year' => ['nullable', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'construction_height_mm' => ['nullable', 'numeric'],
            'lifting_height_mm' => ['nullable', 'numeric'],
            'lifting_capacity_kg_m' => ['nullable', 'numeric'],
            'permanent_total_weight_kg' => ['nullable', 'numeric'],
            'allowed_pulling_weight_kg' => ['nullable', 'numeric'],
            'payload_kg' => ['nullable', 'numeric'],
            'max_weight_allowed_kg' => ['nullable', 'numeric'],
            'empty_weight_kg' => ['nullable', 'numeric'],
            'loading_space_length_mm' => ['nullable', 'numeric'],
            'loading_space_width_mm' => ['nullable', 'numeric'],
            'loading_space_height_mm' => ['nullable', 'numeric'],
            'loading_volume_m3' => ['nullable', 'numeric'],
            'load_capacity_kg' => ['nullable', 'numeric'],
            'operating_weight_kg' => ['nullable', 'numeric'],
            'operating_hours' => ['nullable', 'integer'],
            'axes' => ['nullable', 'integer'],
            'wheel_formula' => ['nullable', 'string'],
            'hydraulic_system' => ['nullable', 'string'],
            'seats' => ['nullable', 'integer'],
            'mileage' => ['nullable', 'integer'],
            'power_kw' => ['nullable', 'integer'],
            'emission_class' => ['nullable', 'string'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'condition' => ['required', 'string'],
            'interior_color' => ['nullable', 'string'],
            'exterior_color' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
            'price_contains_vat' => ['required', 'boolean'],
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
