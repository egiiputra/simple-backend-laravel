<?php

namespace App\Http\Requests\Courier;

use App\Enums\CourierLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCourierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize the comma-separated level filter into an array.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('level')) {
            return;
        }

        $levels = is_array($this->input('level'))
            ? $this->input('level')
            : explode(',', (string) $this->input('level'));

        $this->merge([
            'level' => array_values(array_filter(
                array_map('trim', $levels),
                fn (string $level): bool => $level !== '',
            )),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'level' => ['sometimes', 'array'],
            'level.*' => ['integer', Rule::enum(CourierLevel::class)],
            'sort' => ['sometimes', 'nullable', 'string', Rule::in(['name', 'registered_at', 'level', 'created_at'])],
            'direction' => ['sometimes', 'nullable', 'string', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
