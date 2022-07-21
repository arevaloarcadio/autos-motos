<?php

namespace App\Http\Requests\Admin\CharacteristicPlan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateCharacteristicPlan extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.characteristic-plan.edit', $this->characteristicPlan);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'vehicle_ads' => ['sometimes', 'integer'],
            'rental_ads' => ['sometimes', 'integer'],
            'promotion_month' => ['sometimes', 'integer'],
            'front_page_promotion' => ['sometimes', 'integer'],
            'video_a_day' => ['sometimes', 'integer'],
            'mechanics_rental_ads' => ['sometimes', 'integer'],
            'plan_id' => ['sometimes', 'string'],
            
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
