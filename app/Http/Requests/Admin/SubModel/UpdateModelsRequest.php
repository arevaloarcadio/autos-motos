<?php

namespace App\Http\Requests\Admin\SubModel;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModelsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "models"    => "required|array",
            "models.*"  => "required|exists:models,id",
        ];
    }
}
