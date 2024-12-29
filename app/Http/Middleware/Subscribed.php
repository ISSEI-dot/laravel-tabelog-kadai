<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Subscribed
{
    /**
     * サブスク登録者のみアクセスを許可する
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // サブスク登録者をチェック
        if (!$user || !$user->subscribed('default')) {
            return redirect()->route('mypage')->with('error', 'このページにアクセスできるのはサブスク登録者のみです。');
        }

        return $next($request);
    }
}
