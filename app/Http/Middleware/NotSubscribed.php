<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotSubscribed
{
    /**
     * サブスク未登録者のみアクセスを許可する
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // サブスク未登録をチェック
        if ($user && $user->subscribed('default')) {
            return redirect()->route('mypage')->with('error', 'すでに登録済みのためアクセスできません。');
        }

        return $next($request);
    }
}
