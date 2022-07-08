<?php

namespace App\Http\Requests\Admin\Ad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreAd extends FormRequest
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
            //'slug' => ['required', Rule::unique('ads', 'slug'), 'string'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'string'],
            //'status' => ['required', 'integer'],
            'type' => ['required', 'string'],
            'is_featured' => ['required', 'boolean'],
            'user_id' => ['nullable', 'string'],
            'market_id' => ['required', 'string'],
            'external_id' => ['nullable', 'integer'],
            'source' => ['nullable', 'string'],
            'images_processing_status' => ['required', 'string'],
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
