<?php

namespace App\Http\Requests\Admin\Equipment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateEquipment extends FormRequest
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
            'trim_id' => ['sometimes', 'string'],
            'year' => ['nullable', 'integer'],
            'ad_type' => ['sometimes', 'string'],
            'external_id' => ['nullable', Rule::unique('equipment', 'external_id')->ignore($this->equipment->getKey(), $this->equipment->getKeyName()), 'integer'],
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
