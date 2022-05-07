<?php

namespace App\Http\Requests\Admin\MotoAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateMotoAd extends FormRequest
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
            'ad_id' => ['sometimes', Rule::unique('moto_ads', 'ad_id')->ignore($this->motoAd->getKey(), $this->motoAd->getKeyName()), 'string'],
            'make_id' => ['nullable', 'string'],
            'custom_make' => ['nullable', 'string'],
            'model_id' => ['nullable', 'string'],
            'custom_model' => ['nullable', 'string'],
            'fuel_type_id' => ['sometimes', 'string'],
            'body_type_id' => ['sometimes', 'string'],
            'transmission_type_id' => ['nullable', 'string'],
            'drive_type_id' => ['nullable', 'string'],
            'first_registration_month' => ['sometimes', 'integer'],
            'first_registration_year' => ['sometimes', 'integer'],
            'inspection_valid_until_month' => ['nullable', 'integer'],
            'inspection_valid_until_year' => ['nullable', 'integer'],
            'last_customer_service_month' => ['nullable', 'integer'],
            'last_customer_service_year' => ['nullable', 'integer'],
            'owners' => ['nullable', 'integer'],
            'weight_kg' => ['nullable', 'numeric'],
            'engine_displacement' => ['nullable', 'integer'],
            'mileage' => ['sometimes', 'integer'],
            'power_kw' => ['nullable', 'integer'],
            'gears' => ['nullable', 'integer'],
            'cylinders' => ['nullable', 'integer'],
            'emission_class' => ['nullable', 'string'],
            'fuel_consumption' => ['nullable', 'numeric'],
            'co2_emissions' => ['nullable', 'numeric'],
            'condition' => ['sometimes', 'string'],
            'color' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric'],
            'price_contains_vat' => ['sometimes', 'boolean'],
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
