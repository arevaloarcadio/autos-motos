<?php

namespace App\Http\Requests\Admin\Receipt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreReceipt extends FormRequest
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
            'plan_id' => 'required|string|exists:plans,id',
            //'user_id' => ['required', 'string'],
            'file' => ['required', 'file'],
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'country' => 'nullable|string|max:255'
            
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
