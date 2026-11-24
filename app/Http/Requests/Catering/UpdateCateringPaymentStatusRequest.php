<?php

namespace App\Http\Requests\Catering;

use App\Models\CateringOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCateringPaymentStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Admin only can update payment status.
     * Request with order status modification is strictly forbidden.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Admin can only update payment status; order status modification is forbidden.
        if ($this->has('status')) {
            return false;
        }

        return $user !== null && $user->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'payment_status' => ['required', 'string', Rule::in(CateringOrder::PAYMENT_STATUSES)],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'payment_status.required' => 'Status pembayaran wajib dipilih.',
            'payment_status.in' => 'Status pembayaran yang dipilih tidak valid.',
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'payment_status' => 'status pembayaran',
        ];
    }
}
