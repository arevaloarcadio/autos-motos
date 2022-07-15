<?php

namespace App\Http\Requests\Admin\PaymentHistory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class DestroyPaymentHistory extends FormRequest
{
     use \App\Traits\ErrorMessageValidations;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.payment-history.delete', $this->paymentHistory);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
