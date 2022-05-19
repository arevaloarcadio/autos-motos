<?php

namespace App\Http\Requests\Admin\Option;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateOption extends FormRequest
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
            'slug' => ['sometimes', Rule::unique('options', 'slug')->ignore($this->option->getKey(), $this->option->getKeyName()), 'string'],
            'parent_id' => ['nullable', 'string'],
            'ad_type' => ['sometimes', 'string'],
            'external_id' => ['nullable', Rule::unique('options', 'external_id')->ignore($this->option->getKey(), $this->option->getKeyName()), 'integer'],
            'external_updated_at' => ['nullable', 'date'],
            
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
