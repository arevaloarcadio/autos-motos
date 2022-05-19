<?php

namespace App\Http\Requests\Admin\CarModel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateCarModel extends FormRequest
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
            'slug' => ['sometimes', Rule::unique('car_models', 'slug')->ignore($this->carModel->getKey(), $this->carModel->getKeyName()), 'string'],
            'car_make_id' => ['sometimes', 'string'],
            'external_id' => ['nullable', Rule::unique('car_models', 'external_id')->ignore($this->carModel->getKey(), $this->carModel->getKeyName()), 'integer'],
            
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
