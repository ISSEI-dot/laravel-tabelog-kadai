<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class NotSubscribed
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->subscribed('premium_plan')) {
            return redirect()->route('mypage')->with('error', '既に有料プランに登録済みです。');
        }
        return $next($request);
    }
}
