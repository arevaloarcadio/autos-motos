<?php

namespace App\Http\Requests\Admin\TruckAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexTruckAd extends FormRequest
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
            'orderBy' => 'in:id,ad_id,make_id,custom_make,model,truck_type,fuel_type_id,vehicle_category_id,transmission_type_id,cab,construction_year,first_registration_month,first_registration_year,inspection_valid_until_month,inspection_valid_until_year,owners,construction_height_mm,lifting_height_mm,lifting_capacity_kg_m,permanent_total_weight_kg,allowed_pulling_weight_kg,payload_kg,max_weight_allowed_kg,empty_weight_kg,loading_space_length_mm,loading_space_width_mm,loading_space_height_mm,loading_volume_m3,load_capacity_kg,operating_weight_kg,operating_hours,axes,wheel_formula,hydraulic_system,seats,mileage,power_kw,emission_class,fuel_consumption,co2_emissions,condition,interior_color,exterior_color,price,price_contains_vat,dealer_id,dealer_show_room_id,first_name,last_name,email_address,zip_code,city,country,mobile_number,landline_number,whatsapp_number,youtube_link,created_at|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
