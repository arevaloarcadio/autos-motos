<?php

namespace App\Http\Requests\Admin\Market;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateMarket extends FormRequest
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
            'internal_name' => ['sometimes', Rule::unique('markets', 'internal_name')->ignore($this->market->getKey(), $this->market->getKeyName()), 'string'],
            'slug' => ['sometimes', Rule::unique('markets', 'slug')->ignore($this->market->getKey(), $this->market->getKeyName()), 'string'],
            'domain' => ['sometimes', Rule::unique('markets', 'domain')->ignore($this->market->getKey(), $this->market->getKeyName()), 'string'],
            'default_locale_id' => ['sometimes', 'string'],
            'icon' => ['nullable', 'string'],
            'mobile_number' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'email_address' => ['nullable', 'string'],
            'order_index' => ['sometimes', 'boolean'],
            
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
