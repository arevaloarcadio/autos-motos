<?php

namespace App\Http\Requests\Admin\CarWheelDriveType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateCarWheelDriveType extends FormRequest
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
            'internal_name' => ['sometimes', Rule::unique('car_wheel_drive_types', 'internal_name')->ignore($this->carWheelDriveType->getKey(), $this->carWheelDriveType->getKeyName()), 'string'],
            'slug' => ['sometimes', Rule::unique('car_wheel_drive_types', 'slug')->ignore($this->carWheelDriveType->getKey(), $this->carWheelDriveType->getKeyName()), 'string'],
            'external_name' => ['nullable', 'string'],
            'ad_type' => ['sometimes', 'string'],
            
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
