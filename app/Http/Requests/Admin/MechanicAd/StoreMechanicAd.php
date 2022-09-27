<?php

namespace App\Http\Requests\Admin\MechanicAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreMechanicAd extends FormRequest
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
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'string'],
            'market_id' => ['required', 'string'],
            'address' => ['required', 'string'],
            'latitude' => ['nullable', 'string'],
            'longitude' => ['nullable', 'string'],
            'zip_code' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'website_url' => ['nullable', 'string'],
            'email_address' => ['nullable', 'string'],
            'geocoding_status' => ['nullable', 'string'],
            'images'=>  ['required', 'file'],
            
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
