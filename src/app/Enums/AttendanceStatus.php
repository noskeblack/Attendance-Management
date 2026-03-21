<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case OffDuty = 'off_duty';
    case Working = 'working';
    case OnBreak = 'on_break';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::OffDuty => '勤務外',
            self::Working => '出勤中',
            self::OnBreak => '休憩中',
            self::Completed => '退勤済',
        };
    }
}
