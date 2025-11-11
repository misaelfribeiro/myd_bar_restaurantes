<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\AccessLog;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verificar se o usuário está autenticado
        if (!$request->user()) {
            return response()->json([
                'error' => 'Não autorizado. Token de acesso necessário.',
                'message' => 'Por favor, faça login para acessar este recurso.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Verificar se o usuário tem uma das roles permitidas
        $userRole = $request->user()->role;
        
        if (!in_array($userRole, $roles)) {
            // Log acesso negado
            AccessLog::logApiAccess(
                $request->user(),
                $request->getPathInfo(),
                $request->getMethod(),
                false
            );
            
            return response()->json([
                'error' => 'Acesso negado',
                'message' => "Você precisa ter perfil de: " . implode(' ou ', $roles) . ". Seu perfil atual: {$userRole}",
                'required_roles' => $roles,
                'user_role' => $userRole
            ], Response::HTTP_FORBIDDEN);
        }

        // Log acesso permitido
        AccessLog::logApiAccess(
            $request->user(),
            $request->getPathInfo(),
            $request->getMethod(),
            true
        );

        return $next($request);
    }
}
