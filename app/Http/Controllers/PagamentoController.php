<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\Caixa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class PagamentoController extends Controller
{
 public function index(Request $request)
 {
 $query = Pagamento::with(['pedido.mesa', 'usuario'])
 ->orderBy('data_pagamento', 'desc');
 if ($request->filled('data_inicio')) {
 $query->whereDate('data_pagamento', '>=', $request->data_inicio);
 }
 if ($request->filled('data_fim')) {
 $query->whereDate('data_pagamento', '<=', $request->data_fim);
 }
 if ($request->filled('forma_pagamento')) {
 $query->where('forma_pagamento', $request->forma_pagamento);
 }
 if ($request->filled('status')) {
 $query->where('status', $request->status);
 }
 $pagamentos = $query->paginate(20);
 $formasPagamento = Pagamento::formasPagamento();
 return view('pagamentos.index', compact('pagamentos', 'formasPagamento'));
 }
 public function show(Pagamento $pagamento)
 {
 $pagamento->load(['pedido.mesa', 'pedido.itens.produto', 'usuario']);
 return view('pagamentos.show', compact('pagamento'));
 }
 public function estornar(Request $request, Pagamento $pagamento)
 {
 $request->validate([
 'motivo' => 'required|string|max:255'
 ]);
 if ($pagamento->status !== 'pago') {
 return back()->with('error', 'Só é possível estornar pagamentos confirmados.');
 }
 $caixaAberto = Caixa::caixaAbertoHoje();
 if (!$caixaAberto) {
 return back()->with('error', 'É necessário ter um caixa aberto para processar estornos.');
 }
 try {
 DB::beginTransaction();
 $pagamento->update([
 'status' => 'estornado',
 'observacoes' => ($pagamento->observacoes ? $pagamento->observacoes . "\n" : '') . 
 "ESTORNADO: " . $request->motivo . " (Por: " . Auth::user()->name . " em " . now()->format('d/m/Y H:i') . ")"
 ]);
 $pagamento->pedido->update(['status' => 'finalizado']);
 $caixaAberto->calcularTotais();
 DB::commit();
 return back()->with('success', 'Pagamento estornado com sucesso!');
 } catch (\Exception $e) {
 DB::rollBack();
 return back()->with('error', 'Erro ao estornar pagamento: ' . $e->getMessage());
 }
 }
 public function relatorioVendas(Request $request)
 {
 $dataInicio = $request->input('data_inicio', Carbon::today()->startOfMonth()->toDateString());
 $dataFim = $request->input('data_fim', Carbon::today()->toDateString());
 $pagamentos = Pagamento::with(['pedido.mesa'])
 ->whereDate('data_pagamento', '>=', $dataInicio)
 ->whereDate('data_pagamento', '<=', $dataFim)
 ->where('status', 'pago')
 ->orderBy('data_pagamento')
 ->get();
 $resumoGeral = [
 'total_vendas' => $pagamentos->sum('valor'),
 'total_recebido' => $pagamentos->sum('valor_recebido'),
 'total_troco' => $pagamentos->sum('troco'),
 'quantidade_vendas' => $pagamentos->count()
 ];
 $resumoPorForma = $pagamentos->groupBy('forma_pagamento')
 ->map(function ($pagamentosForma) {
 return [
 'quantidade' => $pagamentosForma->count(),
 'total' => $pagamentosForma->sum('valor'),
 'percentual' => 0
 ];
 });
 $vendasPorDia = $pagamentos->groupBy(function ($pagamento) {
 return $pagamento->data_pagamento->toDateString();
 })->map(function ($pagamentosDia) {
 return [
 'quantidade' => $pagamentosDia->count(),
 'total' => $pagamentosDia->sum('valor')
 ];
 });
 return view('pagamentos.relatorio-vendas', compact(
 'pagamentos', 'resumoGeral', 'resumoPorForma', 'vendasPorDia',
 'dataInicio', 'dataFim'
 ));
 }
 public function pagamentosPedido(Pedido $pedido)
 {
 $pagamentos = $pedido->pagamentos()
 ->with('usuario')
 ->orderBy('data_pagamento', 'desc')
 ->get();
 return response()->json($pagamentos);
 }
 public function estatisticas()
 {
 $hoje = Carbon::today();
 $mesAtual = Carbon::now()->startOfMonth();
 $stats = [
 'hoje' => [
 'vendas' => Pagamento::whereDate('data_pagamento', $hoje)
 ->where('status', 'pago')->sum('valor'),
 'quantidade' => Pagamento::whereDate('data_pagamento', $hoje)
 ->where('status', 'pago')->count()
 ],
 'mes' => [
 'vendas' => Pagamento::whereDate('data_pagamento', '>=', $mesAtual)
 ->where('status', 'pago')->sum('valor'),
 'quantidade' => Pagamento::whereDate('data_pagamento', '>=', $mesAtual)
 ->where('status', 'pago')->count()
 ],
 'formas_hoje' => Pagamento::whereDate('data_pagamento', $hoje)
 ->where('status', 'pago')
 ->selectRaw('forma_pagamento, sum(valor) as total, count(*) as quantidade')
 ->groupBy('forma_pagamento')
 ->get()
 ];
 return response()->json($stats);
 }
}