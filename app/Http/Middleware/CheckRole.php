<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        // Se não especificar role, apenas verifica autenticação
        if (!$role) {
            return $next($request);
        }

        // Verificar se o usuário tem a role necessária (implementação futura)
        // Por enquanto, todos os usuários autenticados têm acesso
        
        return $next($request);
    }
}
