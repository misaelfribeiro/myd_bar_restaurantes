<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\AccessLog;
class RoleMiddleware
{
 public function handle(Request $request, Closure $next, ...$roles)
 {
 if (!$request->user()) {
 return response()->json([
 'error' => 'Não autorizado. Token de acesso necessário.',
 'message' => 'Por favor, faça login para acessar este recurso.'
 ], Response::HTTP_UNAUTHORIZED);
 }
 $userRole = $request->user()->role;
 if (!in_array($userRole, $roles)) {
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
 AccessLog::logApiAccess(
 $request->user(),
 $request->getPathInfo(),
 $request->getMethod(),
 true
 );
 return $next($request);
 }
}