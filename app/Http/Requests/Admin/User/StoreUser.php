<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreUser extends FormRequest
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
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'landline_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'country_code_mobile_number' => ['nullable', 'string'],
            'country_code_whatsapp_number' => ['nullable', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email'), 'string'],
            //'email_verified_at' => ['nullable', 'date'],
            'image' => ['nullable'],
            'password' => ['required', 'confirmed', 'min:7', 'string'],
            'dealer_id' => ['nullable', 'string'],
            'type' => ['required', 'string'],
            //'code_postal' => ['required', 'string'],
            //'address' => ['required', 'string'],
           // 'country' => ['required', 'string'],
           // 'city' => ['required', 'string'],
            //, 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/',
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
