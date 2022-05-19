<?php

namespace App\Http\Requests\Admin\ShopAd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexShopAd extends FormRequest
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
            'orderBy' => 'in:id,ad_id,category,make_id,model,manufacturer,code,condition,price,price_contains_vat,dealer_id,dealer_show_room_id,first_name,last_name,email_address,zip_code,city,country,latitude,longitude,mobile_number,landline_number,whatsapp_number,youtube_link|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
