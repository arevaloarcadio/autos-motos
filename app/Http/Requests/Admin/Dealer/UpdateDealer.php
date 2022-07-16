<?php

namespace App\Http\Requests\Admin\Dealer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateDealer extends FormRequest
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
            'slug' => ['sometimes', Rule::unique('dealers', 'slug')->ignore($this->dealer->getKey(), $this->dealer->getKeyName()), 'string'],
            'company_name' => ['sometimes', Rule::unique('dealers', 'company_name')->ignore($this->dealer->getKey(), $this->dealer->getKeyName()), 'string'],
            'vat_number' => ['nullable', 'string'],
            'address' => ['sometimes', 'string'],
            'zip_code' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
            'country' => ['sometimes', 'string'],
            'logo_path' => ['nullable'],
            'email_address' => ['sometimes', 'string'],
            'whatsapp_number' => ['sometimes', 'string'],
            'phone_number' => ['sometimes', 'string'],
            'status' => ['sometimes', 'integer'],
            'description' => ['nullable', 'string'],
            'external_id' => ['nullable', 'integer'],
            'source' => ['nullable', 'string'],
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
