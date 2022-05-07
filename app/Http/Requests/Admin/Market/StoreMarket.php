<?php

namespace App\Http\Requests\Admin\Market;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreMarket extends FormRequest
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
            'internal_name' => ['required', Rule::unique('markets', 'internal_name'), 'string'],
            'slug' => ['required', Rule::unique('markets', 'slug'), 'string'],
            'domain' => ['required', Rule::unique('markets', 'domain'), 'string'],
            'default_locale_id' => ['required', 'string'],
            'icon' => ['nullable', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'email_address' => ['nullable', 'string'],
            'order_index' => ['required', 'boolean'],
            
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
