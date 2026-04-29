<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isWorker
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (Auth::user()->role === 'doctor' || Auth::user()->role === 'admin' || Auth::user()->role === 'secretary')) {
            return $next($request);
        }

        return redirect()->route('loginWorker');
    }
}
