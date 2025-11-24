<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class CheckAdminAccess
{
 protected $adminRoutes = [
 'empresas.*',
 'admin.*',
 'configuracoes.*',
 'relatorios.financeiro',
 ];
 public function handle(Request $request, Closure $next)
 {
 $user = auth()->user();
 if (!$user) {
 return $next($request);
 }
 if (auth()->guard('admin')->check()) {
 return $next($request);
 }
 $routeName = $request->route()->getName();
 foreach ($this->adminRoutes as $pattern) {
 if (fnmatch($pattern, $routeName)) {
 if ($user->role !== 'admin') {
 abort(403, 'Acesso negado. Esta área é restrita a administradores.');
 }
 }
 }
 return $next($request);
 }
}