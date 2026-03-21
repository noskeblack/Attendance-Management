<?php

namespace App\Http\Controllers;

use App\Enums\CorrectionRequestStatus;
use App\Models\StampCorrectionRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->is_admin) {
            $pending = StampCorrectionRequest::query()
                ->with(['user', 'attendance'])
                ->where('status', CorrectionRequestStatus::Pending->value)
                ->orderByDesc('created_at')
                ->get();

            $approved = StampCorrectionRequest::query()
                ->with(['user', 'attendance'])
                ->where('status', CorrectionRequestStatus::Approved->value)
                ->orderByDesc('approved_at')
                ->orderByDesc('created_at')
                ->get();

            return view('admin.requests.list', [
                'pending' => $pending,
                'approved' => $approved,
            ]);
        }

        $pending = StampCorrectionRequest::query()
            ->with('attendance')
            ->where('user_id', $user->id)
            ->where('status', CorrectionRequestStatus::Pending->value)
            ->orderByDesc('created_at')
            ->get();

        $approved = StampCorrectionRequest::query()
            ->with('attendance')
            ->where('user_id', $user->id)
            ->where('status', CorrectionRequestStatus::Approved->value)
            ->orderByDesc('approved_at')
            ->orderByDesc('created_at')
            ->get();

        return view('requests.index', [
            'pending' => $pending,
            'approved' => $approved,
        ]);
    }
}
