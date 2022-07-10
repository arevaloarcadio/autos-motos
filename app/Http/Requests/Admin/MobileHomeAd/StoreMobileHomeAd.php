<?php

namespace App\Http\Requests\Admin\MobileHomeAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreMobileHomeAd extends FormRequest
{
     use \App\Traits\ErrorMessageValidations;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.mobile-home-ad.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'string'],
            'type' => ['required', 'string'],
            'market_id' => ['required', 'string'],
            
            'make_id' => ['nullable', 'string'],
            'custom_make' => ['nullable', 'string'],
            'model_id' => ['nullable', 'string'],
            'custom_model' => ['nullable', 'string'],
            'fuel_type_id' => ['required', 'string'],
            'vehicle_category_id' => ['required', 'string'],
            'transmission_type_id' => ['nullable', 'string'],
            'construction_year' => ['nullable', 'integer'],
            'first_registration_month' => ['required', 'integer'],
            'first_registration_year' => ['required', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'length_cm' => ['nullable', 'numeric'],
            'width_cm' => ['nullable', 'numeric'],
            'height_cm' => ['nullable', 'numeric'],
            'max_weight_allowed_kg' => ['nullable', 'numeric'],
            'payload_kg' => ['nullable', 'numeric'],
            'engine_displacement' => ['nullable', 'integer'],
            'mileage' => ['required', 'integer'],
            'power_kw' => ['nullable', 'integer'],
            'axes' => ['nullable', 'integer'],
            'seats' => ['nullable', 'integer'],
            'sleeping_places' => ['nullable', 'integer'],
            'beds' => ['nullable', 'string'],
            'emission_class' => ['nullable', 'string'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'condition' => ['required', 'string'],
            'color' => ['nullable', 'string'],
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
