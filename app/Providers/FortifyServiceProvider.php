<?php

namespace App\Providers;


use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
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
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);

        Fortify::authenticateUsing(function (Request $request) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = \App\Models\User::where('email', $request->email)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => 'Email tidak terdaftar dalam sistem.',
                ]);
            }

            if (!Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'password' => 'Password yang Anda masukkan salah.',
                ]);
            }

            if ($user->employee->is_active != 1) {
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda tidak aktif. Silakan hubungi admin.',
                ]);
            }

            return $user;
        });


        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        Fortify::loginView(function () {
            return view('pages.auth.⚡login');
        });



        // Fortify::createUsersUsing(CreateNewUser::class);
        // Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        // Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);
        // RateLimiter::for('two-factor', function (Request $request) {
        //     return Limit::perMinute(5)->by($request->session()->get('login.id'));
        // });
    }
}
