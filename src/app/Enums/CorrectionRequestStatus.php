<?php

namespace App\Enums;

enum CorrectionRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
}
