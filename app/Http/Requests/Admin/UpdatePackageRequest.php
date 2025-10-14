<?php

namespace App\Http\Requests\Admin;

use App\Enums\BillingCycle;
use App\Enums\PlatformType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('packages.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $packageId = $this->route('package');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('packages')->ignore($packageId)],
            'display_name' => ['sometimes', 'required', 'string', 'max:255'],
            'platform' => ['sometimes', 'required', 'string', Rule::in(array_merge(
                array_column(PlatformType::cases(), 'value'),
                ['all']
            ))],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'required', 'string', 'max:3'],
            'billing_cycle' => ['sometimes', 'required', 'string', Rule::in(array_column(BillingCycle::cases(), 'value'))],
            'photos_limit' => ['sometimes', 'required', 'integer', 'min:0'],
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
            'name.unique' => 'This package name already exists',
            'display_name.required' => 'Display name is required',
            'platform.in' => 'Invalid platform selected',
            'price.numeric' => 'Price must be a number',
            'billing_cycle.in' => 'Invalid billing cycle',
            'photos_limit.integer' => 'Photos limit must be an integer',
        ];
    }
}
