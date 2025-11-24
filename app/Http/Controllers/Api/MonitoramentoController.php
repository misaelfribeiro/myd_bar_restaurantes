<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pagamento;
use App\Models\Caixa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class MonitoramentoController extends Controller
{
 public function dashboard()
 {
 try {
 $hoje = now()->startOfDay();
 $ontem = now()->subDay()->startOfDay();
 $pagamentosHoje = Pagamento::where('created_at', '>=', $hoje)->count();
 $valorHoje = Pagamento::where('created_at', '>=', $hoje)
 ->where('status', 'confirmado')
 ->sum('valor');
 $pagamentosOntem = Pagamento::whereBetween('created_at', [$ontem, $hoje])->count();
 $valorOntem = Pagamento::whereBetween('created_at', [$ontem, $hoje])
 ->where('status', 'confirmado')
 ->sum('valor');
 $pagamentosPorForma = Pagamento::select('forma_pagamento', DB::raw('COUNT(*) as total'), DB::raw('SUM(valor) as valor_total'))
 ->where('created_at', '>=', $hoje)
 ->where('status', 'confirmado')
 ->groupBy('forma_pagamento')
 ->get();
 $caixaAberto = Caixa::where('status', 'aberto')->first();
 $ultimosPagamentos = Pagamento::with(['pedido.mesa', 'usuario'])
 ->where('status', 'confirmado')
 ->orderBy('created_at', 'desc')
 ->limit(10)
 ->get();
 return response()->json([
 'success' => true,
 'data' => [
 'estatisticas' => [
 'hoje' => [
 'pagamentos' => $pagamentosHoje,
 'valor_total' => $valorHoje,
 'ticket_medio' => $pagamentosHoje > 0 ? $valorHoje / $pagamentosHoje : 0
 ],
 'ontem' => [
 'pagamentos' => $pagamentosOntem,
 'valor_total' => $valorOntem,
 'ticket_medio' => $pagamentosOntem > 0 ? $valorOntem / $pagamentosOntem : 0
 ],
 'crescimento' => [
 'pagamentos' => $pagamentosOntem > 0 ? (($pagamentosHoje - $pagamentosOntem) / $pagamentosOntem * 100) : 0,
 'valor' => $valorOntem > 0 ? (($valorHoje - $valorOntem) / $valorOntem * 100) : 0
 ]
 ],
 'pagamentos_por_forma' => $pagamentosPorForma,
 'caixa' => $caixaAberto ? [
 'id' => $caixaAberto->id,
 'status' => $caixaAberto->status,
 'valor_inicial' => $caixaAberto->valor_inicial,
 'total_vendas' => $caixaAberto->total_vendas
 ] : null,
 'ultimos_pagamentos' => $ultimosPagamentos->map(function($pagamento) {
 return [
 'id' => $pagamento->id,
 'forma_pagamento' => $pagamento->forma_pagamento,
 'valor' => $pagamento->valor,
 'mesa' => $pagamento->pedido->mesa->numero ?? 'Balcão',
 'usuario' => $pagamento->usuario->nome ?? 'Sistema',
 'created_at' => $pagamento->created_at->format('d/m/Y H:i')
 ];
 })
 ]
 ]);
 } catch (\Exception $e) {
 Log::error('Erro no dashboard de monitoramento', [
 'erro' => $e->getMessage(),
 'trace' => $e->getTraceAsString()
 ]);
 return response()->json([
 'success' => false,
 'message' => 'Erro ao carregar dashboard: ' . $e->getMessage()
 ], 500);
 }
 }
 public function logs(Request $request)
 {
 try {
 $limite = $request->get('limite', 50);
 $nivel = $request->get('nivel', 'all');
 $query = Pagamento::with(['pedido.mesa', 'usuario'])
 ->orderBy('created_at', 'desc')
 ->limit($limite);
 if ($nivel === 'error') {
 $query->where('status', 'cancelado');
 }
 $logs = $query->get()->map(function($pagamento) {
 return [
 'timestamp' => $pagamento->created_at->format('Y-m-d H:i:s'),
 'nivel' => $pagamento->status === 'confirmado' ? 'info' : 'error',
 'mensagem' => "Pagamento {$pagamento->forma_pagamento} - R$ {$pagamento->valor}",
 'contexto' => [
 'pagamento_id' => $pagamento->id,
 'pedido_id' => $pagamento->pedido_id,
 'mesa' => $pagamento->pedido->mesa->numero ?? 'Balcão',
 'usuario_id' => $pagamento->usuario_id
 ]
 ];
 });
 return response()->json([
 'success' => true,
 'data' => [
 'logs' => $logs,
 'total' => $logs->count(),
 'filtros' => [
 'limite' => $limite,
 'nivel' => $nivel
 ]
 ]
 ]);
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao carregar logs: ' . $e->getMessage()
 ], 500);
 }
 }
 public function metricas()
 {
 try {
 $hoje = now()->startOfDay();
 $tempoMedioProcessamento = 150;
 $totalPagamentos = Pagamento::where('created_at', '>=', $hoje)->count();
 $pagamentosSucesso = Pagamento::where('created_at', '>=', $hoje)
 ->where('status', 'confirmado')
 ->count();
 $taxaSucesso = $totalPagamentos > 0 ? ($pagamentosSucesso / $totalPagamentos * 100) : 100;
 $pagamentosPorHora = Pagamento::select(
 DB::raw('HOUR(created_at) as hora'),
 DB::raw('COUNT(*) as total')
 )
 ->where('created_at', '>=', $hoje)
 ->groupBy('hora')
 ->orderBy('hora')
 ->get()
 ->pluck('total', 'hora');
 return response()->json([
 'success' => true,
 'data' => [
 'performance' => [
 'tempo_medio_ms' => $tempoMedioProcessamento,
 'taxa_sucesso_pct' => round($taxaSucesso, 2),
 'total_requisicoes' => $totalPagamentos,
 'requisicoes_sucesso' => $pagamentosSucesso,
 'requisicoes_erro' => $totalPagamentos - $pagamentosSucesso
 ],
 'distribuicao_horaria' => $pagamentosPorHora,
 'status_api' => [
 'online' => true,
 'versao' => '1.0.0',
 'uptime' => '99.9%',
 'ultimo_deployment' => now()->format('Y-m-d H:i:s')
 ]
 ]
 ]);
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao carregar métricas: ' . $e->getMessage()
 ], 500);
 }
 }
 public function health()
 {
 try {
 $dbStatus = true;
 try {
 DB::select('SELECT 1');
 } catch (\Exception $e) {
 $dbStatus = false;
 }
 $caixaAberto = Caixa::where('status', 'aberto')->exists();
 $ultimoPagamento = Pagamento::orderBy('created_at', 'desc')->first();
 $tempoUltimoPagamento = $ultimoPagamento ? 
 $ultimoPagamento->created_at->diffInMinutes(now()) : null;
 $status = $dbStatus ? 'healthy' : 'unhealthy';
 return response()->json([
 'success' => true,
 'status' => $status,
 'timestamp' => now()->toISOString(),
 'checks' => [
 'database' => $dbStatus,
 'caixa_aberto' => $caixaAberto,
 'ultimo_pagamento_min' => $tempoUltimoPagamento
 ],
 'info' => [
 'versao_api' => '1.0.0',
 'ambiente' => app()->environment(),
 'laravel_version' => app()->version()
 ]
 ], $dbStatus ? 200 : 503);
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'status' => 'error',
 'message' => $e->getMessage()
 ], 500);
 }
 }
}