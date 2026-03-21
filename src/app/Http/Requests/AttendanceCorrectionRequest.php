<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $attendance = $this->route('attendance');

        return $attendance instanceof Attendance
            && $this->user()?->id === $attendance->user_id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i'],
            'breaks' => ['nullable', 'array'],
            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end' => ['nullable', 'date_format:H:i'],
            'remark' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'remark.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var Attendance|null $attendance */
            $attendance = $this->route('attendance');
            if (! $attendance instanceof Attendance) {
                return;
            }

            $base = Carbon::parse($attendance->work_date->format('Y-m-d'));

            try {
                $in = $base->copy()->setTimeFromTimeString($this->input('clock_in'));
                $out = $base->copy()->setTimeFromTimeString($this->input('clock_out'));
            } catch (\Throwable) {
                $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');

                return;
            }

            if ($in->gt($out)) {
                $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

            $breaks = $this->input('breaks', []);
            foreach ($breaks as $index => $row) {
                $start = $row['start'] ?? null;
                $end = $row['end'] ?? null;
                if (! $start || ! $end) {
                    continue;
                }
                try {
                    $bs = $base->copy()->setTimeFromTimeString($start);
                    $be = $base->copy()->setTimeFromTimeString($end);
                } catch (\Throwable) {
                    $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');

                    continue;
                }

                if ($bs->lt($in) || $bs->gt($out) || $be->lt($in)) {
                    $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                }

                if ($be->gt($out)) {
                    $validator->errors()->add("breaks.$index.end", '休憩時間もしくは退勤時間が不適切な値です');
                }
            }
        });
    }
}
