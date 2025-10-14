<?php

namespace App\Http\Requests\Admin;

use App\Enums\BillingCycle;
use App\Enums\PlatformType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('packages.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:packages,name'],
            'display_name' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'string', Rule::in(array_merge(
                array_column(PlatformType::cases(), 'value'),
                ['all']
            ))],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'billing_cycle' => ['required', 'string', Rule::in(array_column(BillingCycle::cases(), 'value'))],
            'photos_limit' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string'],
            'metadata' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Package name is required',
            'name.unique' => 'This package name already exists',
            'display_name.required' => 'Display name is required',
            'platform.required' => 'Platform is required',
            'platform.in' => 'Invalid platform selected',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'currency.required' => 'Currency is required',
            'billing_cycle.required' => 'Billing cycle is required',
            'billing_cycle.in' => 'Invalid billing cycle',
            'photos_limit.required' => 'Photos limit is required',
            'photos_limit.integer' => 'Photos limit must be an integer',
        ];
    }
}
