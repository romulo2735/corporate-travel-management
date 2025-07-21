<?php

namespace App\Http\Requests;

use App\DTOs\TravelData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTravelRequest extends FormRequest
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
            'requester_name' => 'required|string|max:100',
            'destination' => 'required|string|max:50',
            'departure_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:departure_date',
        ];
    }

    public function toDto(): TravelData
    {
        return new TravelData(
            $this->request->get('requester_name'),
            $this->request->get('destination'),
            $this->request->get('departure_date'),
            $this->request->get('return_date'),
        );
    }
}
