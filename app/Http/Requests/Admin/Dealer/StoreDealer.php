<?php

namespace App\Http\Requests\Admin\Dealer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreDealer extends FormRequest
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
            //'slug' => ['required', Rule::unique('dealers', 'slug'), 'string'],
            'company_name' => ['required', Rule::unique('dealers', 'company_name'), 'string'],
            'vat_number' => ['nullable', 'string'],
            'address' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'logo_path' => ['nullable', 'string'],
            'email_address' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'status' => ['required', 'integer'],
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
