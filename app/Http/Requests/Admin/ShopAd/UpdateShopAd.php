<?php

namespace App\Http\Requests\Admin\ShopAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateShopAd extends FormRequest
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
            'ad_id' => ['sometimes', Rule::unique('shop_ads', 'ad_id')->ignore($this->shopAd->getKey(), $this->shopAd->getKeyName()), 'string'],
            'category' => ['sometimes', 'string'],
            'make_id' => ['sometimes', 'string'],
            'model' => ['nullable', 'string'],
            'manufacturer' => ['sometimes', 'string'],
            'code' => ['nullable', 'string'],
            'condition' => ['sometimes', 'string'],
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
            'latitude' => ['nullable', 'string'],
            'longitude' => ['nullable', 'string'],
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
