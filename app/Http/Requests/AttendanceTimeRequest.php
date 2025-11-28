<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceTimeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'clock_in_at' => ['required', 'date_format:H:i',],
            'clock_out_at' => ['required', 'date_format:H:i',],
            'breaks' => ['array'],
            'breaks.0.start' => ['required', 'date_format:H:i',],
            'breaks.0.end' => ['required', 'date_format:H:i',],
            'breaks.1.start' => ['nullable', 'date_format:H:i',],
            'breaks.1.end' => ['nullable', 'date_format:H:i',],
            'note' => ['required', 'string', 'max:255'],
        ];
    }

    /* public function messages(): array
    {
        return [
            'clock_in_at.' => '',
            'clock_in_at' => '',
            'clock_out_at' => '',
            'clock_out_at' => '',
            'breaks.0.start' => '',
            'breaks.0.start' => '',
            'breaks.0.end' => '',
            'breaks.0.end' => '',
            'breaks.1.start' => '',
            'breaks.1.start' => '',
            'breaks.1.end' => '',
            'breaks.1.end' => '',
            'note.required' => '備考を記入してください',
        ];
    }*/
}