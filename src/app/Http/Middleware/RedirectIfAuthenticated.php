<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ログイン済みなのにログイン画面にアクセスされた場合のリダイレクト処理
 */
class RedirectIfAuthenticated
{
    /** auth.phpの「guard」項目で設定した値を定数化 */
    private const GUARD_USER = 'users';
    private const GUARD_OWNER = 'owners';
    private const GUARD_ADMIN = 'admin';
    /**
     * Handle an incoming request.
     * (ログイン済みなのにログイン画面にアクセスされた場合のリダイレクト処理)
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // $guards = empty($guards) ? [null] : $guards;

        // foreach ($guards as $guard) {
        //     if (Auth::guard($guard)->check()) {
        //         return redirect(RouteServiceProvider::HOME);
        //     }
        // }
        // ユーザーが認証済み && ログイン関連のルートだった場合
        if (Auth::guard(self::GUARD_USER)->check() && $request->routeIs('user.*')) {
            return redirect(RouteServiceProvider::HOME);
        }

        // オーナーが認証済み && オーナー関連のルートだった場合
        if (Auth::guard(self::GUARD_OWNER)->check() && $request->routeIs('owner.*')) {
            return redirect(RouteServiceProvider::OWNER_HOME);
        }

        // 管理者が認証済み && 管理者関連のルートだった場合
        if (Auth::guard(self::GUARD_ADMIN)->check() && $request->routeIs('admin.*')) {
            return redirect(RouteServiceProvider::ADMIN_HOME);
        }


        return $next($request);
    }
}
