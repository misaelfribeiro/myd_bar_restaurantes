<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $modulo
     * @param  string  $acao
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $modulo, $acao = 'visualizar')
    {
        $user = auth()->user();
        
        // Se nÃ£o estiver autenticado, redirecionar para login
        if (!$user) {
            return redirect()->route('login')->with('error', 'VocÃª precisa estar autenticado.');
        }
        
        // Admin tem acesso total
        if ($user->role === 'admin' || $user->role === 'superadmin') {
            return $next($request);
        }
        
        // Verificar permissÃ£o especÃ­fica
        if (!$user->temPermissao($modulo, $acao)) {
            abort(403, 'VocÃª nÃ£o tem permissÃ£o para acessar este recurso.');
        }
        
        return $next($request);
    }
}
