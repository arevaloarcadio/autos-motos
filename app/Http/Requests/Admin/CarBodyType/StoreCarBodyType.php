<?php

namespace App\Http\Requests\Admin\CarBodyType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreCarBodyType extends FormRequest
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
            'internal_name' => ['required', Rule::unique('car_body_types', 'internal_name'), 'string'],
            'slug' => ['required', Rule::unique('car_body_types', 'slug'), 'string'],
            'icon_url' => ['nullable', 'string'],
            'external_name' => ['nullable', 'string'],
            'ad_type' => ['required', 'string'],
            
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
