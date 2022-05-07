<?php

namespace App\Http\Requests\Admin\TrimSpecification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateTrimSpecification extends FormRequest
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
            'trim_id' => ['sometimes', 'string'],
            'specification_id' => ['sometimes', 'string'],
            'value' => ['sometimes', 'string'],
            'unit' => ['nullable', 'string'],
            'ad_type' => ['sometimes', 'string'],
            'external_id' => ['nullable', Rule::unique('trim_specifications', 'external_id')->ignore($this->trimSpecification->getKey(), $this->trimSpecification->getKeyName()), 'integer'],
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
