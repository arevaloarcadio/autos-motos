<?php

namespace App\Http\Requests\Admin\CharacteristicPromotionPlan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateCharacteristicPromotionPlan extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.characteristic-promotion-plan.edit', $this->characteristicPromotionPlan);
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
            'shop_ads' => ['sometimes', 'integer'],
            'rental_ads' => ['sometimes', 'integer'],
            'mechanic_ads' => ['sometimes', 'integer'],
            'front_page_promotion' => ['sometimes', 'integer'],
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
