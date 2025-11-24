<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use App\Models\Empresa;
class TenantMiddleware
{
 public function handle(Request $request, Closure $next)
 {
 $tenant_code = null;
 $empresa = null;
 
 // Verifica autenticação em ambos os guards
 if (auth()->guard('web')->check()) {
 $user = auth()->guard('web')->user();
 $tenant_code = $user->tenant_code;
 }
 if (!$tenant_code && auth()->guard('admin')->check()) {
 $user = auth()->guard('admin')->user();
 $tenant_code = $user->tenant_code;
 }
 
 // Rotas que EXIGEM tenant (bloqueio se não houver)
 $rotasProtegidas = [
 'api/clientes',
 'api/pedidos-public',
 'api/item-pedidos-public',
 'api/produtos-public',
 'api/categorias-public'
 ];
 
 foreach ($rotasProtegidas as $rota) {
 if ($request->is($rota . '*') && !$tenant_code) {
 return response()->json([
 'success' => false,
 'message' => 'Acesso negado. Faça login para continuar.'
 ], 401);
 }
 }
 
 if ($tenant_code) {
 $empresa = Empresa::where('tenant_code', $tenant_code)->first();
 if ($empresa) {
 app()->instance('tenant', $empresa);
 view()->share('tenant', $empresa);
 if (!$empresa->is_master && $empresa->status_contrato !== 'ativo') {
 if (!$request->is('login') && !$request->is('logout') && !$request->is('api/*')) {
 if (!session()->has('contrato_aviso_exibido')) {
 session()->flash('warning', 'Atenção: Seu contrato está ' . $empresa->status_contrato . '. Entre em contato com o suporte.');
 session(['contrato_aviso_exibido' => true]);
 }
 }
 }
 }
 }
 return $next($request);
 }
}