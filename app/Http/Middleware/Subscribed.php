<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Subscribed
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->subscribed('premium_plan')) {
            return redirect()->route('subscription.create')->with('error', '有料プランの登録が必要です。');
        }
        return $next($request);
    }
}

