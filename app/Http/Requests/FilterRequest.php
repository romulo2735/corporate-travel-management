<?php

namespace App\Http\Requests;

use App\TravelStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class FilterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', new Enum(TravelStatusEnum::class)],
            'destination' => 'nullable|string|max:50',
            'fromDate' => 'nullable|date|after_or_equal:today',
            'toDate' => 'nullable|date|after:fromDate',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Invalid status value.',
            'to_date.after_or_equal' => 'from_date must be before to_date.',
        ];
    }
}
