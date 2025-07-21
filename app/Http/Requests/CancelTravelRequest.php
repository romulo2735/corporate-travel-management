<?php

namespace App\Http\Requests;

use App\DTOs\CancelTravelData;
use App\DTOs\TravelData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CancelTravelRequest extends FormRequest
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
        return [];
    }

    public function toDto(): CancelTravelData
    {
        return new CancelTravelData(
            id: (int)$this->route('id'),
            userId: (int)$this->user()->id,
        );
    }
}
