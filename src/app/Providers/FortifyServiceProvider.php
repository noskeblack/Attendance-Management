<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Laravel\Fortify\Http\Requests\LoginRequest::class,
            LoginRequest::class
        );
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));
        Fortify::verifyEmailView(fn () => view('auth.verify-email'));
        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', [
            'request' => $request,
        ]));

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->input('email'))->first();

            if (! $user || ! Hash::check($request->input('password'), $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません'],
                ]);
            }

            $isAdminLogin = $request->is('admin/login');

            if ($isAdminLogin && ! $user->is_admin) {
                throw ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません'],
                ]);
            }

            if (! $isAdminLogin && $user->is_admin) {
                throw ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません'],
                ]);
            }

            return $user;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
