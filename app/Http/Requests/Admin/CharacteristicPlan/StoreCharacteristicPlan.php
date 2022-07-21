<?php

namespace App\Http\Requests\Admin\CharacteristicPlan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreCharacteristicPlan extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.characteristic-plan.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'vehicle_ads' => ['required', 'integer'],
            'rental_ads' => ['required', 'integer'],
            'promotion_month' => ['required', 'integer'],
            'front_page_promotion' => ['required', 'integer'],
            'video_a_day' => ['required', 'integer'],
            'mechanics_rental_ads' => ['required', 'integer'],
            'plan_id' => ['required', 'string'],
            
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
