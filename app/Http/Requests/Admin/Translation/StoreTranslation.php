<?php

namespace App\Http\Requests\Admin\Translation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreTranslation extends FormRequest
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
            'locale_id' => ['required', Rule::unique('translations', 'locale_id'), 'string'],
            'translation_key' => ['required', Rule::unique('translations', 'translation_key'), 'string'],
            'translation_value' => ['required', 'string'],
            'resource_id' => ['nullable', 'string'],
            
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
