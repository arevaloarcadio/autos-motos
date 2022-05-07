<?php

namespace App\Http\Requests\Admin\EquipmentOption;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateEquipmentOption extends FormRequest
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
            'equipment_id' => ['sometimes', 'string'],
            'option_id' => ['sometimes', 'string'],
            'is_base' => ['sometimes', 'boolean'],
            'ad_type' => ['sometimes', 'string'],
            'external_id' => ['nullable', Rule::unique('equipment_options', 'external_id')->ignore($this->equipmentOption->getKey(), $this->equipmentOption->getKeyName()), 'integer'],
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
