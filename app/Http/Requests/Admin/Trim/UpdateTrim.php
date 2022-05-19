<?php

namespace App\Http\Requests\Admin\Trim;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateTrim extends FormRequest
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
            'model_id' => ['sometimes', 'string'],
            'series_id' => ['sometimes', 'string'],
            'production_year_start' => ['nullable', 'integer'],
            'production_year_end' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
            'ad_type' => ['sometimes', 'string'],
            'external_id' => ['nullable', Rule::unique('trims', 'external_id')->ignore($this->trim->getKey(), $this->trim->getKeyName()), 'integer'],
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
