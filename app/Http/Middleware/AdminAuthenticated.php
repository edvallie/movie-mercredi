<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->get('admin_authenticated')) {
            $request->session()->put('url.intended', $request->url());
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
