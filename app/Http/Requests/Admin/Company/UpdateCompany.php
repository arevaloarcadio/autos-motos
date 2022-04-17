<?php

namespace App\Http\Requests\Admin\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateCompany extends FormRequest
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
            'name' => ['sometimes', 'string'],
            'cif' => ['sometimes', 'string'],
            'phone' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
            'code_postal' => ['sometimes', 'string'],
            'whatsapp' => ['sometimes', 'string'],
            'logo' => ['sometimes', 'file'],
            'description' => ['sometimes', 'string'],
            'country_id' => ['sometimes', 'int'],
            'user_id' => ['sometimes', 'int'],
            
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
