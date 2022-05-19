<?php

namespace App\Http\Requests\Admin\EquipmentOption;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreEquipmentOption extends FormRequest
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
            'equipment_id' => ['required', 'string'],
            'option_id' => ['required', 'string'],
            'is_base' => ['required', 'boolean'],
            'ad_type' => ['required', 'string'],
            'external_id' => ['nullable', Rule::unique('equipment_options', 'external_id'), 'integer'],
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
