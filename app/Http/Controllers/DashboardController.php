<?php
namespace App\Http\Controllers;
use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Usuario;
use App\Models\User;
use App\Models\Empresa;
use App\Models\ItemPedido;
use App\Models\Cliente;
use App\Models\Entregador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
class DashboardController extends Controller
{
 public function index()
 {
 // REGRA SIMPLES: Só mostra Dashboard Master se tenant_code = 'EATSFOOD'
 $user = auth()->guard('admin')->user() ?? auth()->guard('web')->user();
 
 // Debug - remover depois
 \Log::info('DashboardController@index', [
     'user_id' => $user ? $user->id : null,
     'user_name' => $user ? ($user->name ?? $user->nome) : null,
     'tenant_code' => $user ? $user->tenant_code : null,
     'guard_admin' => auth()->guard('admin')->check(),
     'guard_web' => auth()->guard('web')->check(),
 ]);
 
 if ($user && $user->tenant_code === 'EATSFOOD') {
     \Log::info('Redirecionando para Dashboard Master');
     return $this->dashboardMaster();
 }
 
 \Log::info('Redirecionando para Dashboard Normal');
 return view('dashboard');
 }
 
 private function isMasterEatsFood()
 {
 $user = auth()->guard('admin')->user() ?? auth()->guard('web')->user();
 return $user && $user->tenant_code === 'EATSFOOD';
 }
 public function dashboardMaster()
 {
 $stats = [
 'total_empresas' => Empresa::tenants()->count(),
 'empresas_ativas' => Empresa::tenants()->contratoAtivo()->count(),
 'empresas_trial' => Empresa::tenants()->where('status_contrato', 'trial')->count(),
 'empresas_suspensas' => Empresa::tenants()->where('status_contrato', 'suspenso')->count(),
 'total_usuarios_operacionais' => Usuario::whereNotNull('tenant_code')->count(),
 'total_admins_empresas' => User::where('tenant_code', '!=', 'EATSFOOD')->count(),
 'usuarios_ativos_hoje' => $this->getUsuariosAtivosHoje(),
 'total_clientes' => Cliente::whereNotNull('tenant_code')->count(),
 'clientes_novos_mes' => Cliente::whereMonth('created_at', now()->month)
 ->whereYear('created_at', now()->year)
 ->count(),
 'total_entregadores' => Entregador::whereNotNull('tenant_code')->count(),
 'entregadores_ativos' => Entregador::where('status', 'ativo')->count(),
 'pedidos_hoje' => Pedido::whereDate('created_at', today())->count(),
 'pedidos_mes' => Pedido::whereMonth('created_at', now()->month)
 ->whereYear('created_at', now()->year)
 ->count(),
 'valor_total_mes' => Pedido::whereMonth('created_at', now()->month)
 ->whereYear('created_at', now()->year)
 ->sum('total'),
 'receita_mensalidades' => Empresa::tenants()->contratoAtivo()->sum('valor_mensalidade'),
 'taxa_mes_estimada' => $this->calcularTaxasMes(),
 'novas_empresas_mes' => Empresa::tenants()
 ->whereMonth('created_at', now()->month)
 ->whereYear('created_at', now()->year)
 ->count(),
 ];
 $por_plano = Empresa::tenants()
 ->select('plano', DB::raw('count(*) as total'))
 ->groupBy('plano')
 ->get();
 $vencendo = Empresa::tenants()
 ->contratoAtivo()
 ->whereRaw('DATEDIFF(data_fim_contrato, CURDATE()) <= 30')
 ->orderBy('data_fim_contrato', 'asc')
 ->limit(5)
 ->get();
 $topEmpresas = $this->getTopEmpresasPorPedidos();
 $crescimentoMensal = $this->getCrescimentoMensal();
 return view('dashboard-master', compact(
 'stats',
 'por_plano',
 'vencendo',
 'topEmpresas',
 'crescimentoMensal'
 ));
 }
 private function getUsuariosAtivosHoje()
 {
 return Pedido::whereDate('created_at', today())
 ->distinct('usuario_id')
 ->count('usuario_id');
 }
 private function calcularTaxasMes()
 {
 $pedidos = Pedido::whereMonth('created_at', now()->month)
 ->whereYear('created_at', now()->year)
 ->get();
 $taxaTotal = 0;
 foreach ($pedidos as $pedido) {
 if ($pedido->tenant_code) {
 $empresa = Empresa::where('tenant_code', $pedido->tenant_code)->first();
 if ($empresa) {
 $taxaTotal += $empresa->calcularTaxaPedido($pedido->total);
 }
 }
 }
 return $taxaTotal;
 }
 private function getTopEmpresasPorPedidos()
 {
 return DB::table('pedidos')
 ->join('empresas', 'pedidos.tenant_code', '=', 'empresas.tenant_code')
 ->select(
 'empresas.nome_fantasia',
 'empresas.tenant_code',
 DB::raw('COUNT(pedidos.id) as total_pedidos'),
 DB::raw('SUM(pedidos.total) as valor_total')
 )
 ->whereMonth('pedidos.created_at', now()->month)
 ->whereYear('pedidos.created_at', now()->year)
 ->groupBy('empresas.id', 'empresas.nome_fantasia', 'empresas.tenant_code')
 ->orderByDesc('total_pedidos')
 ->limit(5)
 ->get();
 }
 private function getCrescimentoMensal()
 {
 $meses = [];
 for ($i = 5; $i >= 0; $i--) {
 $data = now()->subMonths($i);
 $meses[] = [
 'mes' => $data->format('M/Y'),
 'empresas' => Empresa::tenants()
 ->whereYear('created_at', '<=', $data->year)
 ->whereMonth('created_at', '<=', $data->month)
 ->count(),
 'pedidos' => Pedido::whereYear('created_at', $data->year)
 ->whereMonth('created_at', $data->month)
 ->count(),
 ];
 }
 return $meses;
 }
 public function getStats()
 {
 $stats = [
 'total_categorias' => Categoria::count(),
 'total_produtos' => Produto::count(),
 'total_mesas' => Mesa::count(),
 'total_usuarios' => Usuario::count(),
 'total_pedidos' => Pedido::count(),
 'pedidos_pendentes' => Pedido::where('status', 'pendente')->count(),
 'pedidos_em_preparo' => Pedido::where('status', 'em_preparo')->count(),
 'pedidos_prontos' => Pedido::where('status', 'pronto')->count(),
 ];
 return response()->json($stats);
 }
 public function getVendasHoje()
 {
 $hoje = now()->startOfDay();
 $vendas = [
 'total_vendas' => Pedido::whereDate('created_at', $hoje)
 ->where('status', 'entregue')
 ->sum('total'),
 'total_pedidos' => Pedido::whereDate('created_at', $hoje)->count(),
 'vendas_semana' => Pedido::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
 ->where('status', 'entregue')
 ->sum('total'),
 'vendas_mes' => Pedido::whereMonth('created_at', now()->month)
 ->whereYear('created_at', now()->year)
 ->where('status', 'entregue')
 ->sum('total'),
 'ticket_medio' => 0
 ];
 if ($vendas['total_pedidos'] > 0) {
 $vendas['ticket_medio'] = $vendas['total_vendas'] / $vendas['total_pedidos'];
 }
 return response()->json($vendas);
 }
 public function getPerformance()
 {
 $performance = [
 'clientes_ativos' => $this->getClientesAtivos(),
 'tempo_medio_preparo' => $this->getTempoMedioPreparo(),
 'taxa_conversao' => $this->getTaxaConversao()
 ];
 return response()->json($performance);
 }
 public function getTopProdutos()
 {
 try {
 $topProdutos = DB::table('item_pedidos')
 ->join('produtos', 'item_pedidos.produto_id', '=', 'produtos.id')
 ->select('produtos.nome', DB::raw('SUM(item_pedidos.quantidade) as vendas'))
 ->groupBy('produtos.id', 'produtos.nome')
 ->orderByDesc('vendas')
 ->limit(5)
 ->get();
 if ($topProdutos->isEmpty()) {
 $topProdutos = collect([
 (object) ['nome' => 'Pizza Margherita', 'vendas' => 45],
 (object) ['nome' => 'Hambúrguer Clássico', 'vendas' => 38],
 (object) ['nome' => 'Lasanha da Casa', 'vendas' => 32],
 (object) ['nome' => 'Refrigerante', 'vendas' => 67],
 (object) ['nome' => 'Batata Frita', 'vendas' => 29]
 ]);
 }
 return response()->json($topProdutos);
 } catch (\Exception $e) {
 return response()->json([
 (object) ['nome' => 'Pizza Margherita', 'vendas' => 45],
 (object) ['nome' => 'Hambúrguer Clássico', 'vendas' => 38],
 (object) ['nome' => 'Lasanha da Casa', 'vendas' => 32]
 ]);
 }
 }
 public function getPagamentosHoje()
 {
 $hoje = now()->startOfDay();
 $totalVendas = Pedido::whereDate('created_at', $hoje)
 ->where('status', 'entregue')
 ->sum('total');
 $pagamentos = [
 'dinheiro' => $totalVendas * 0.4,
 'cartao' => $totalVendas * 0.45,
 'pix' => $totalVendas * 0.15,
 'entradas' => $totalVendas,
 'saidas' => $totalVendas * 0.1,
 ];
 return response()->json($pagamentos);
 }
 public function getUsuariosAtividade()
 {
 $atividade = [
 'online' => $this->getUsuariosOnline(),
 'hoje' => Usuario::whereDate('ultimo_acesso', today())->count(),
 'ultimo_acesso' => $this->getUltimoAcesso()
 ];
 return response()->json($atividade);
 }
 public function getRecursosServidor()
 {
 $recursos = [
 'cpu' => rand(10, 80),
 'memory' => rand(20, 75),
 'disk' => rand(15, 60)
 ];
 return response()->json($recursos);
 }
 private function getClientesAtivos()
 {
 return Mesa::where('status', 'ocupada')->count();
 }
 private function getTempoMedioPreparo()
 {
 return 25;
 }
 private function getTaxaConversao()
 {
 $totalPedidos = Pedido::count();
 $pedidosFinalizados = Pedido::where('status', 'entregue')->count();
 if ($totalPedidos > 0) {
 return round(($pedidosFinalizados / $totalPedidos) * 100, 1);
 }
 return 0;
 }
 private function getUsuariosOnline()
 {
 return Usuario::where('ultimo_acesso', '>=', now()->subMinutes(15))->count();
 }
 private function getUltimoAcesso()
 {
 $ultimoUsuario = Usuario::orderBy('ultimo_acesso', 'desc')->first();
 if ($ultimoUsuario && $ultimoUsuario->ultimo_acesso) {
 return $ultimoUsuario->ultimo_acesso->diffForHumans();
 }
 return 'Nenhum acesso registrado';
 }
 public function stats()
 {
 return $this->getStats();
 }
 public function pedidosPorStatus()
 {
 $pedidos = Pedido::selectRaw('status, COUNT(*) as total')
 ->groupBy('status')
 ->get();
 return response()->json($pedidos);
 }
 public function produtosMaisVendidos()
 {
 return $this->getTopProdutos();
 $produtos = Produto::withCount(['itensPedido' => function($query) {
 $query->whereHas('pedido', function($q) {
 $q->where('status', 'entregue');
 });
 }])->orderBy('itens_pedido_count', 'desc')->limit(5)->get();
 return response()->json($produtos);
 }
}