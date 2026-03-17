<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'email_address' => ['required', 'email', 'max:255'],
            'seats_booked' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return[
            'seats_booked.min' => 'Booking requires at least one seat.'
        ];
    }
}
