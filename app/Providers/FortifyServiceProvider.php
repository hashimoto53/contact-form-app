<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser; // ★1. 追加：CreateNewUser クラスのインポート
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ★2. 追加：ユーザー作成のアクションクラスをFortifyに登録
        Fortify::createUsersUsing(CreateNewUser::class);

        // ★3. 追加：ユーザー登録画面のビュー指定
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // ログイン画面のビュー定義（仕様書要件のauth.login画面を紐付け）
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // ログイン試行時のレートリミット設定
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(5)->by($email.$request->ip());
        });
    }
}
