<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Filament\Pages\Home;

class PreventAuthenticatedUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check())
         {
            $user = auth()->user()->role->name;

            if ($user === 'Customer') {
                return redirect()->to(Home::getUrl());
            } else {
                return redirect('/owner'); // Replace 'dashboard' with your actual dashboard route name
            }
         }

         return $next($request);
    }
}
