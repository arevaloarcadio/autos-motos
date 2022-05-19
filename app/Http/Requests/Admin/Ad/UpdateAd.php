<?php

namespace App\Http\Requests\Admin\Ad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateAd extends FormRequest
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
            'slug' => ['sometimes', Rule::unique('ads', 'slug')->ignore($this->ad->getKey(), $this->ad->getKeyName()), 'string'],
            'title' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'thumbnail' => ['nullable', 'string'],
            'status' => ['sometimes', 'integer'],
            'type' => ['sometimes', 'string'],
            'is_featured' => ['sometimes', 'boolean'],
            'user_id' => ['nullable', 'string'],
            'market_id' => ['sometimes', 'string'],
            'external_id' => ['nullable', 'integer'],
            'source' => ['nullable', 'string'],
            'images_processing_status' => ['sometimes', 'string'],
            'images_processing_status_text' => ['nullable', 'string'],
            
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
