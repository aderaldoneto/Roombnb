<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            abort(403, 'Você precisa estar logado para acessar esta página.');
        }

        $user = auth()->user();

        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = array_map('trim', explode(',', $roles[0]));
        }

        // Verificando se o usuário tem algum das permissões pra liberar
        if ($user->hasRole($roles)) {
            return $next($request);
        }

        abort(403, 'Acesso negado! Você não tem permissão para acessar esta rota.');
    }
}
