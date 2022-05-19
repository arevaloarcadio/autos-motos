<?php

namespace App\Http\Requests\Admin\AutoOption;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateAutoOption extends FormRequest
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
            'internal_name' => ['sometimes', Rule::unique('auto_options', 'internal_name')->ignore($this->autoOption->getKey(), $this->autoOption->getKeyName()), 'string'],
            'slug' => ['sometimes', Rule::unique('auto_options', 'slug')->ignore($this->autoOption->getKey(), $this->autoOption->getKeyName()), 'string'],
            'parent_id' => ['nullable', 'string'],
            'ad_type' => ['sometimes', 'string'],
            
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
