<?php

namespace App\Http\Requests\Admin\Trim;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreTrim extends FormRequest
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
            'name' => ['required', 'string'],
            'model_id' => ['required', 'string'],
            'series_id' => ['required', 'string'],
            'production_year_start' => ['nullable', 'integer'],
            'production_year_end' => ['nullable', 'integer'],
            'is_active' => ['required', 'boolean'],
            'ad_type' => ['required', 'string'],
            'external_id' => ['nullable', Rule::unique('trims', 'external_id'), 'integer'],
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
