<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isPatient
{
    /**
     * Handle an incoming request.
     * Los pacientes son usuarios autenticados con role 'patient'.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'patient') {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
