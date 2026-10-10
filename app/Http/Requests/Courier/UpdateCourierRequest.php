<?php

namespace App\Http\Requests\Courier;

use App\Enums\CourierLevel;
use App\Enums\VehicleType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourierRequest extends FormRequest
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
        $courier = $this->route('courier');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('couriers', 'email')->ignore($courier)],
            'phone' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('couriers', 'phone')->ignore($courier)],
            'level' => ['sometimes', 'required', 'integer', Rule::enum(CourierLevel::class)],
            'vehicle_type' => ['sometimes', 'required', 'string', Rule::enum(VehicleType::class)],
            'vehicle_plate' => ['sometimes', 'nullable', 'string', 'max:20', Rule::unique('couriers', 'vehicle_plate')->ignore($courier)],
            'address' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'registered_at' => ['sometimes', 'required', 'date'],
        ];
    }
}
