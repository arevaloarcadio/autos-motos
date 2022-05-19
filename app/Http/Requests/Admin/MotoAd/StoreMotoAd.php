<?php

namespace App\Http\Requests\Admin\MotoAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreMotoAd extends FormRequest
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
            'ad_id' => ['required', Rule::unique('moto_ads', 'ad_id'), 'string'],
            'make_id' => ['nullable', 'string'],
            'custom_make' => ['nullable', 'string'],
            'model_id' => ['nullable', 'string'],
            'custom_model' => ['nullable', 'string'],
            'fuel_type_id' => ['required', 'string'],
            'body_type_id' => ['required', 'string'],
            'transmission_type_id' => ['nullable', 'string'],
            'drive_type_id' => ['nullable', 'string'],
            'first_registration_month' => ['required', 'integer'],
            'first_registration_year' => ['required', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'last_customer_service_month' => ['nullable', 'integer'],
            'last_customer_service_year' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'weight_kg' => ['nullable', 'numeric'],
            'engine_displacement' => ['nullable', 'integer'],
            'mileage' => ['required', 'integer'],
            'power_kw' => ['nullable', 'integer'],
            'gears' => ['nullable', 'integer'],
            'cylinders' => ['nullable', 'integer'],
            'emission_class' => ['nullable', 'string'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'condition' => ['required', 'string'],
            'color' => ['required', 'string'],
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
