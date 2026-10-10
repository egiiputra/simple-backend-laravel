<?php

namespace App\Http\Requests\Courier;

use App\Enums\CourierLevel;
use App\Enums\VehicleType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('couriers', 'email')],
            'phone' => ['required', 'string', 'max:20', Rule::unique('couriers', 'phone')],
            'level' => ['required', 'integer', Rule::enum(CourierLevel::class)],
            'vehicle_type' => ['required', 'string', Rule::enum(VehicleType::class)],
            'vehicle_plate' => ['nullable', 'string', 'max:20', Rule::unique('couriers', 'vehicle_plate')],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'registered_at' => ['required', 'date'],
        ];
    }
}
