<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Models\User;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Requests\FortifyLoginRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;

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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        // ログイン画面
        Fortify::loginView(function () {
            return view('auth.login', ['headerType' => 'auth']);
        });

        // 会員登録画面
        Fortify::registerView(function () {
            return view('auth.register', ['headerType' => 'auth']);
        });

        // 会員登録後メール認証へ
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect()->route('register.verify');
            }
        });

        // ログイン時リダイレクト先を分岐
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                $user = $request->user();

                // 管理者なら管理者勤怠一覧画面へ
                if ($user && $user->is_admin) {
                    return redirect()->route('admin.attendance.index');
                }

                // 一般ユーザー未認証なら認証誘導画面へ
                if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
                    return redirect()->route('register.verify');
                }
                // 一般ユーザー認証済みは勤怠登録画面へ
                return redirect()->intended(config('fortify.home'));
            }
        });

        // ログアウト後の分岐
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                $isAdminLogout = $request->input('logout_type') === 'admin';

                if($isAdminLogout) {
                    return redirect()->route('admin.login');
                }

                return redirect()->route('login');
            }
        });

        $this->app->bind(
            LoginRequest::class,
            FortifyLoginRequest::class
        );

        // ログイン認証
        Fortify::authenticateUsing(function (Request $request) {
            $isAdminLogin = ($request->login_type === 'admin');
            $user = User::where('email', $request->email)->first();

            if($isAdminLogin) {
                if($user && $user->is_admin && Hash::check($request->password, $user->password)) {
                    return $user;
                }

                throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
                ]);
            }

            if ($user && ! $user->is_admin && Hash::check($request->password, $user->password)) {
                return $user;
            }

            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        });

    }
}
