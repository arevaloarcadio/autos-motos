<?php

namespace App\Http\Requests\Admin\MotoAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexMotoAd extends FormRequest
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
            'orderBy' => 'in:id,ad_id,make_id,custom_make,model_id,custom_model,fuel_type_id,body_type_id,transmission_type_id,drive_type_id,first_registration_month,first_registration_year,inspection_valid_until_month,inspection_valid_until_year,last_customer_service_month,last_customer_service_year,owners,weight_kg,engine_displacement,mileage,power_kw,gears,cylinders,emission_class,fuel_consumption,co2_emissions,condition,color,price,price_contains_vat,dealer_id,dealer_show_room_id,first_name,last_name,email_address,zip_code,city,country,mobile_number,landline_number,whatsapp_number,youtube_link|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
