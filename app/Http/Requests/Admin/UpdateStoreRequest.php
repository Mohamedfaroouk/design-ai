<?php

namespace App\Http\Requests\Admin;

use App\Enums\StoreStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        return $this->user()->can('stores.update');
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'required', Rule::enum(StoreStatus::class)],
            'store_name' => ['sometimes', 'required', 'string', 'max:255'],
            'store_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'store_phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors
     */
    public function messages(): array
    {
        return [
            'status.required' => __('stores.validation.status_required'),
            'store_name.required' => __('stores.validation.store_name_required'),
            'store_email.email' => __('stores.validation.store_email_invalid'),
        ];
    }
}
