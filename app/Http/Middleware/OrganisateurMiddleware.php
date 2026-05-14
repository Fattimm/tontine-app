<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrganisateurMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isAdminOrOrganisateur()) {
            abort(403, 'Accès réservé aux organisateurs.');
        }
        return $next($request);
    }
}
