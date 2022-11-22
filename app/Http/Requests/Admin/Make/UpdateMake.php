<?php

namespace App\Http\Requests\Admin\Make;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateMake extends FormRequest
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
            'slug' => ['sometimes', Rule::unique('makes', 'slug')->ignore($this->make->getKey(), $this->make->getKeyName()), 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'has_sub_model' => ['sometimes', 'boolean'],
            'ad_type' => ['sometimes', 'string'],
            'external_id' => ['nullable', Rule::unique('makes', 'external_id')->ignore($this->make->getKey(), $this->make->getKeyName()), 'integer'],
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
