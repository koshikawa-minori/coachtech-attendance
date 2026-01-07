<?php

namespace App\Http\Requests;

use Carbon\Carbon;
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
            'breaks.0.start' => ['nullable', 'date_format:H:i',],
            'breaks.0.end' => ['nullable', 'date_format:H:i',],
            'breaks.1.start' => ['nullable', 'date_format:H:i',],
            'breaks.1.end' => ['nullable', 'date_format:H:i',],
            'note' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => '備考を記入してください',
        ];
    }

    // 機能要件では文言が共通ですが、ユーザー視点で分かりやすくするため
    // 休憩開始／終了を明示した文言に変更（コーチ了承済）
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $clockIn = $this->input('clock_in_at');
            $clockOut = $this->input('clock_out_at');

            if (blank($clockIn) || blank($clockOut)) {
                return;
            }

            try {
                $clockInTime = \Carbon\Carbon::createFromFormat('H:i', $clockIn);
                $clockOutTime = \Carbon\Carbon::createFromFormat('H:i', $clockOut);
            } catch (\Exception $exception) {
                return;
            }

            if ($clockInTime->gt($clockOutTime)) {
                $validator->errors()->add('clock_in_at', '出勤時間もしくは退勤時間が不適切な値です');
            }

            $breakStart0 = $this->input('breaks.0.start');
            $breakEnd0 = $this->input('breaks.0.end');

            if (!blank($breakStart0) || !blank($breakEnd0)) {

                if (blank($breakStart0)) {
                    $validator->errors()->add('breaks.0.start', '休憩開始時間が不適切な値です');
                }
                if (blank($breakEnd0)) {
                    $validator->errors()->add('breaks.0.end', '休憩終了時間が不適切な値です');
                }

                if (!blank($breakStart0) && !blank($breakEnd0)) {
                    try {
                        $breakStartTime0 = \Carbon\Carbon::createFromFormat('H:i', $breakStart0);
                        $breakEndTime0 = \Carbon\Carbon::createFromFormat('H:i', $breakEnd0);
                    } catch (\Exception $exception) {
                        return;
                    }

                    if ($breakStartTime0->lt($clockInTime) || $breakStartTime0->gt($clockOutTime)) {
                        $validator->errors()->add('breaks.0.start', '休憩開始時間が不適切な値です');
                    }

                    if ($breakEndTime0->gt($clockOutTime)) {
                        $validator->errors()->add('breaks.0.end', '休憩終了時間もしくは退勤時間が不適切な値です');
                    }
                }
            }

            $breakStart1 = $this->input('breaks.1.start');
            $breakEnd1 = $this->input('breaks.1.end');

            if (!blank($breakStart1) || !blank($breakEnd1)) {

                if (blank($breakStart1)) {
                    $validator->errors()->add('breaks.1.start', '休憩開始時間が不適切な値です');
                }
                if (blank($breakEnd1)) {
                    $validator->errors()->add('breaks.1.end', '休憩終了時間が不適切な値です');
                }

                if (!blank($breakStart1) && !blank($breakEnd1)) {
                    try {
                        $breakStartTime1 = \Carbon\Carbon::createFromFormat('H:i', $breakStart1);
                        $breakEndTime1 = \Carbon\Carbon::createFromFormat('H:i', $breakEnd1);
                    } catch (\Exception $exception) {
                        return;
                    }

                    if ($breakStartTime1->lt($clockInTime) || $breakStartTime1->gt($clockOutTime)) {
                        $validator->errors()->add('breaks.1.start', '休憩開始時間が不適切な値です');
                    }

                    if ($breakEndTime1->gt($clockOutTime)) {
                        $validator->errors()->add('breaks.1.end', '休憩終了時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }
}