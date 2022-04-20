<?php

namespace App\Http\Requests\Admin\AutoAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexAutoAd extends FormRequest
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
            'orderBy' => 'in:id,ad_id,price,vin,doors,mileage,exterior_color,interior_color,condition,dealer_id,dealer_show_room_id,first_name,last_name,email_address,zip_code,city,country,mobile_number,landline_number,whatsapp_number,ad_fuel_type_id,ad_body_type_id,ad_transmission_type_id,ad_drive_type_id,first_registration_month,first_registration_year,engine_displacement,power_hp,owners,inspection_valid_until_month,inspection_valid_until_year,make_id,model_id,generation_id,series_id,trim_id,equipment_id,additional_vehicle_info,seats|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
