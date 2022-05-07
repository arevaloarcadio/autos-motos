<?php

namespace App\Http\Requests\Admin\CarSpec;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexCarSpec extends FormRequest
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
            'orderBy' => 'in:id,car_make_id,car_model_id,car_generation_id,car_body_type_id,engine,doors,doors_min,doors_max,power_hp,power_rpm,power_rpm_min,power_rpm_max,engine_displacement,production_start_year,production_end_year,car_fuel_type_id,car_transmission_type_id,gears,car_wheel_drive_type_id,battery_capacity,electric_power_hp,electric_power_rpm,electric_power_rpm_min,electric_power_rpm_max,external_id,last_external_update|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
