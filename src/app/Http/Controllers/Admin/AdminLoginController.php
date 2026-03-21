<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class AdminLoginController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(
        AdminLoginRequest $request,
        AuthenticatedSessionController $controller
    ): Response
    {
        return $controller->store($request);
    }
}
