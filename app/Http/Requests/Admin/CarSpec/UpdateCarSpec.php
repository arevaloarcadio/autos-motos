<?php

namespace App\Http\Requests\Admin\CarSpec;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateCarSpec extends FormRequest
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
            'car_make_id' => ['sometimes', 'string'],
            'car_model_id' => ['sometimes', 'string'],
            'car_generation_id' => ['sometimes', 'string'],
            'car_body_type_id' => ['nullable', 'string'],
            'engine' => ['sometimes', 'string'],
            'doors' => ['nullable', 'string'],
            'doors_min' => ['nullable', 'integer'],
            'doors_max' => ['nullable', 'integer'],
            'power_hp' => ['nullable', 'integer'],
            'power_rpm' => ['nullable', 'string'],
            'power_rpm_min' => ['nullable', 'integer'],
            'power_rpm_max' => ['nullable', 'integer'],
            'engine_displacement' => ['nullable', 'integer'],
            'production_start_year' => ['nullable', 'date'],
            'production_end_year' => ['nullable', 'date'],
            'car_fuel_type_id' => ['sometimes', 'string'],
            'car_transmission_type_id' => ['sometimes', 'string'],
            'gears' => ['nullable', 'integer'],
            'car_wheel_drive_type_id' => ['nullable', 'string'],
            'battery_capacity' => ['nullable', 'numeric'],
            'electric_power_hp' => ['nullable', 'integer'],
            'electric_power_rpm' => ['nullable', 'string'],
            'electric_power_rpm_min' => ['nullable', 'integer'],
            'electric_power_rpm_max' => ['nullable', 'integer'],
            'external_id' => ['nullable', Rule::unique('car_specs', 'external_id')->ignore($this->carSpec->getKey(), $this->carSpec->getKeyName()), 'integer'],
            'last_external_update' => ['nullable', 'date'],
            
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
