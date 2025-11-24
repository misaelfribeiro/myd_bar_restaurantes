<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class CheckRole
{
 public function handle(Request $request, Closure $next, $role = null)
 {
 if (!$request->user()) {
 return response()->json(['message' => 'Não autenticado'], 401);
 }
 if (!$role) {
 return $next($request);
 }
 return $next($request);
 }
}