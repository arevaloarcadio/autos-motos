<?php

namespace App\Http\Requests\Admin\Series;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateSeries extends FormRequest
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
            'generation_id' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'ad_type' => ['sometimes', 'string'],
            'external_id' => ['nullable', Rule::unique('series', 'external_id')->ignore($this->series->getKey(), $this->series->getKeyName()), 'integer'],
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
