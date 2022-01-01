<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

/**
 * 未認証だった場合のリダイレクト処理
 */
class Authenticate extends Middleware
{
    // ログイン画面のルート (ユーザー)
    protected $user_route = 'user.login';
    // ログイン画面のルート (オーナー)
    protected $owner_route = 'owner.login';
    // ログイン画面のルート (管理者)
    protected $admin_route = 'admin.login';

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * (もしも未認証だった場合のリダイレクト先を記載する)
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // オーナー関連のルートで未認証だった場合
            if(Route::is('owner.*')) {
                return route($this->owner_route);
            } elseif(Route::is('admin.*')) { // 管理者関連のルートで未認証だった場合
                return route($this->admin_route);
            } else { // それ以外
                return route($this->user_route);
            }
        }
    }
}
