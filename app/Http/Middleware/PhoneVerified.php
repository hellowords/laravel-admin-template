<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class PhoneVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user() || !$request->user()->hasVerifiedPhone()) {
            return $request->expectsJson()
                ? abort(403, '你的手机号未验证.')
                : Redirect::route('verify.phone');
        }

        return $next($request);
    }
}
