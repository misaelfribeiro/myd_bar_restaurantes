<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class TenantController extends Controller
{
 public function __construct()
 {
 $this->middleware(function ($request, $next) {
 if (!isMaster()) {
 abort(403, 'Acesso negado. Apenas EatsFood Master pode acessar.');
 }
 return $next($request);
 });
 }
 public function index()
 {
 $stats = [
 'total_tenants' => Empresa::tenants()->count(),
 'tenants_ativos' => Empresa::tenants()->contratoAtivo()->count(),
 'tenants_trial' => Empresa::tenants()->where('status_contrato', 'trial')->count(),
 'tenants_suspensos' => Empresa::tenants()->where('status_contrato', 'suspenso')->count(),
 'receita_mensal' => Empresa::tenants()->contratoAtivo()->sum('valor_mensalidade'),
 'pedidos_hoje' => Pedido::whereDate('created_at', today())->count(),
 'usuarios_total' => User::whereNotNull('tenant_code')->count(),
 ];
 $novos_tenants = Empresa::tenants()
 ->orderBy('created_at', 'desc')
 ->take(10)
 ->get();
 $vencendo = Empresa::tenants()
 ->contratoAtivo()
 ->whereRaw('DATEDIFF(data_fim_contrato, CURDATE()) <= 30')
 ->orderBy('data_fim_contrato', 'asc')
 ->get();
 $por_plano = Empresa::tenants()
 ->select('plano', DB::raw('count(*) as total'))
 ->groupBy('plano')
 ->get();
 return view('admin.tenants.index', compact('stats', 'novos_tenants', 'vencendo', 'por_plano'));
 }
 public function tenants(Request $request)
 {
 $query = Empresa::tenants();
 if ($request->filled('plano')) {
 $query->where('plano', $request->plano);
 }
 if ($request->filled('status')) {
 $query->where('status_contrato', $request->status);
 }
 if ($request->filled('search')) {
 $query->where(function($q) use ($request) {
 $q->where('razao_social', 'like', '%' . $request->search . '%')
 ->orWhere('nome_fantasia', 'like', '%' . $request->search . '%')
 ->orWhere('tenant_code', 'like', '%' . $request->search . '%');
 });
 }
 $tenants = $query->orderBy('created_at', 'desc')->paginate(20);
 return view('admin.tenants.list', compact('tenants'));
 }
 public function show($id)
 {
 $tenant = Empresa::tenants()->findOrFail($id);
 $stats = [
 'usuarios' => User::where('tenant_code', $tenant->tenant_code)->count(),
 'produtos' => \App\Models\Produto::where('tenant_code', $tenant->tenant_code)->count(),
 'pedidos_mes' => Pedido::where('tenant_code', $tenant->tenant_code)
 ->whereMonth('created_at', now()->month)
 ->count(),
 'filiais' => Empresa::where('matriz_id', $tenant->id)->count(),
 'receita_mes' => Pedido::where('tenant_code', $tenant->tenant_code)
 ->whereMonth('created_at', now()->month)
 ->sum('total'),
 ];
 return view('admin.tenants.show', compact('tenant', 'stats'));
 }
 public function suspend($id)
 {
 $tenant = Empresa::tenants()->findOrFail($id);
 $tenant->status_contrato = 'suspenso';
 $tenant->save();
 return redirect()->back()->with('success', 'Tenant suspenso com sucesso!');
 }
 public function activate($id)
 {
 $tenant = Empresa::tenants()->findOrFail($id);
 $tenant->status_contrato = 'ativo';
 $tenant->save();
 return redirect()->back()->with('success', 'Tenant reativado com sucesso!');
 }
 public function changePlan(Request $request, $id)
 {
 $request->validate([
 'plano' => 'required|in:basico,profissional,premium,enterprise',
 ]);
 $tenant = Empresa::tenants()->findOrFail($id);
 $oldPlan = $tenant->plano;
 $tenant->plano = $request->plano;
 $planos = config('tenant.planos');
 $novoPlano = $planos[$request->plano];
 $tenant->max_usuarios = $novoPlano['limites']['usuarios'];
 $tenant->max_produtos = $novoPlano['limites']['produtos'];
 $tenant->max_pedidos_mes = $novoPlano['limites']['pedidos_mes'];
 $tenant->max_filiais = $novoPlano['limites']['filiais'];
 $tenant->valor_mensalidade = $novoPlano['preco'];
 $tenant->taxa_transacao_percent = $novoPlano['taxas']['percentual'];
 $tenant->taxa_fixa_pedido = $novoPlano['taxas']['fixa'];
 $tenant->recursos_habilitados = json_encode($novoPlano['recursos']);
 $tenant->save();
 return redirect()->back()->with('success', "Plano alterado de {$oldPlan} para {$request->plano}!");
 }
 public function financial()
 {
 $mes_atual = now()->month;
 $ano_atual = now()->year;
 $stats = [
 'mensalidades_mes' => Empresa::tenants()
 ->contratoAtivo()
 ->sum('valor_mensalidade'),
 'taxas_mes' => Pedido::whereMonth('created_at', $mes_atual)
 ->whereYear('created_at', $ano_atual)
 ->get()
 ->sum(function($pedido) {
 $empresa = Empresa::where('tenant_code', $pedido->tenant_code)->first();
 return $empresa ? $empresa->calcularTaxaPedido($pedido->total) : 0;
 }),
 'receita_total' => 0,
 ];
 $stats['receita_total'] = $stats['mensalidades_mes'] + $stats['taxas_mes'];
 $por_plano = Empresa::tenants()
 ->contratoAtivo()
 ->select('plano', DB::raw('SUM(valor_mensalidade) as total'))
 ->groupBy('plano')
 ->get();
 return view('admin.tenants.financial', compact('stats', 'por_plano'));
 }
}