<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class AdminLoginController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (auth()->check()) {
            if (auth()->user()->is_admin) {
                return redirect()->route('admin.attendance.daily');
            }

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('admin.auth.login');
    }

    public function store(
        AdminLoginRequest $request,
        AuthenticatedSessionController $controller
    ): Response|RedirectResponse
    {
        if (auth()->check()) {
            if (auth()->user()->is_admin) {
                return redirect()->route('admin.attendance.daily');
            }

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $controller->store($request);
    }
}
