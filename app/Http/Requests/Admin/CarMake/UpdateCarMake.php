<?php

namespace App\Http\Requests\Admin\CarMake;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateCarMake extends FormRequest
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
            'slug' => ['sometimes', Rule::unique('car_makes', 'slug')->ignore($this->carMake->getKey(), $this->carMake->getKeyName()), 'string'],
            'external_id' => ['nullable', Rule::unique('car_makes', 'external_id')->ignore($this->carMake->getKey(), $this->carMake->getKeyName()), 'integer'],
            'is_active' => ['sometimes', 'boolean'],
            
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
