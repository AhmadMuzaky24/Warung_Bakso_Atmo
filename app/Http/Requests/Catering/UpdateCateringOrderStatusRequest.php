<?php

namespace App\Http\Requests\Catering;

use App\Models\CateringOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCateringOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Staff only can update order status.
     * Request with payment_status must be rejected by server.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Staff can only update order status; payment_status modification is strictly forbidden.
        if ($this->has('payment_status')) {
            return false;
        }

        return $user !== null && $user->role === 'staff';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(CateringOrder::STATUSES)],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status pesanan wajib dipilih.',
            'status.in' => 'Status pesanan yang dipilih tidak valid.',
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'status' => 'status pesanan',
        ];
    }
}
