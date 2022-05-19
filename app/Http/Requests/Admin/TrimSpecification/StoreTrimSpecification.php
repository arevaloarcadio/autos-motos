<?php

namespace App\Http\Requests\Admin\TrimSpecification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreTrimSpecification extends FormRequest
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
            'trim_id' => ['required', 'string'],
            'specification_id' => ['required', 'string'],
            'value' => ['required', 'string'],
            'unit' => ['nullable', 'string'],
            'ad_type' => ['required', 'string'],
            'external_id' => ['nullable', Rule::unique('trim_specifications', 'external_id'), 'integer'],
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
