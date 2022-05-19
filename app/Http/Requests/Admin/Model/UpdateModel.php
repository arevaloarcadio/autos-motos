<?php

namespace App\Http\Requests\Admin\Model;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateModel extends FormRequest
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
            'slug' => ['sometimes', Rule::unique('models', 'slug')->ignore($this->model->getKey(), $this->model->getKeyName()), 'string'],
            'make_id' => ['sometimes', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'ad_type' => ['sometimes', 'string'],
            'external_id' => ['nullable', Rule::unique('models', 'external_id')->ignore($this->model->getKey(), $this->model->getKeyName()), 'integer'],
            'external_updated_at' => ['nullable', 'date'],
            
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
