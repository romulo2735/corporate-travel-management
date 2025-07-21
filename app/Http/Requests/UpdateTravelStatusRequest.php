<?php

namespace App\Http\Requests;

use App\DTOs\UpdateTravelStatusData;
use App\TravelStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTravelStatusRequest extends FormRequest
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
            'status' => [
                'required',
                new Enum(TravelStatusEnum::class, [TravelStatusEnum::APPROVED, TravelStatusEnum::CANCELED])
            ],
        ];
    }

    public function toDto(): UpdateTravelStatusData
    {
        return new UpdateTravelStatusData(
            id: (int)$this->route('id'),
            newStatus: $this->request->get('status'),
            user_id: $this->user()->id,
        );
    }
}
