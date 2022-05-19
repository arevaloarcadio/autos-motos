<?php

namespace App\Http\Requests\Admin\DealerShowRoom;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateDealerShowRoom extends FormRequest
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
            'name' => ['sometimes', 'string'],
            'address' => ['sometimes', 'string'],
            'zip_code' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
            'country' => ['sometimes', 'string'],
            'latitude' => ['nullable', 'string'],
            'longitude' => ['nullable', 'string'],
            'email_address' => ['sometimes', 'string'],
            'mobile_number' => ['sometimes', 'string'],
            'landline_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'dealer_id' => ['sometimes', 'string'],
            'market_id' => ['nullable', 'string'],
            
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
