<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Caixa;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class CaixaController extends Controller
{    
 public function index()
 {
 $caixaAberto = Caixa::caixaAbertoHoje();
 if (!$caixaAberto) {
 return view('caixa.abertura');
 }
 $totaisCaixa = $caixaAberto->getTotalizacoesPorPeriodo();
 $pedidosFinalizados = Pedido::with('mesa', 'itens.produto', 'pagamentos', 'delivery')
 ->whereIn('status', ['finalizado', 'entregue'])
 ->get()->filter(function($pedido) {
 return !$pedido->isPago();
 });
 $pedidosAbertos = Pedido::with('mesa', 'itens.produto', 'delivery')
 ->whereIn('status', ['aberto', 'entregue'])
 ->get();
 $pedidosPendentes = $pedidosFinalizados->merge($pedidosAbertos);
 return view('caixa.dashboard', compact('caixaAberto', 'totaisCaixa', 'pedidosPendentes'));
 }
 public function abrir(Request $request)
 {
 $request->validate([
 'saldo_inicial' => 'required|numeric|min:0',
 'observacoes' => 'nullable|string'
 ]);
 $caixaAberto = Caixa::caixaAbertoHoje();
 if ($caixaAberto) {
 return redirect()->route('caixa.index')
 ->with('error', 'Já existe um caixa aberto hoje.');
 }
 try {
 DB::beginTransaction();
 $usuarioId = Auth::id();
 if (!$usuarioId) {
 $usuario = Usuario::first();
 if (!$usuario) {
 throw new \Exception('Nenhum usuário encontrado no sistema. Crie um usuário primeiro.');
 }
 $usuarioId = $usuario->id;
 }
 $caixa = Caixa::create([
 'usuario_id' => $usuarioId,
 'data_abertura' => now(),
 'saldo_inicial' => $request->saldo_inicial,
 'status' => 'aberto',
 'observacoes_abertura' => $request->observacoes
 ]);
 DB::commit();
 return redirect()->route('caixa.index')
 ->with('success', 'Caixa aberto com sucesso!');
 } catch (\Exception $e) {
 DB::rollBack();
 return back()->with('error', 'Erro ao abrir o caixa: ' . $e->getMessage());
 }
 }
 public function fechar(Request $request)
 {
 $request->validate([
 'observacoes' => 'nullable|string'
 ]);
 $caixaAberto = Caixa::caixaAbertoHoje();
 if (!$caixaAberto) {
 return back()->with('error', 'Não há caixa aberto para fechar.');
 }
 try {
 DB::beginTransaction();
 $totalizacoes = $caixaAberto->getTotalizacoesPorPeriodo();
 $caixaAberto->update([
 'data_fechamento' => now(),
 'status' => 'fechado',
 'observacoes_fechamento' => $request->observacoes,
 'total_vendas' => $totalizacoes['total_vendas'],
 'total_dinheiro' => $totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0,
 'total_cartao_credito' => $totalizacoes['por_forma_pagamento']['cartao_credito']['total'] ?? 0,
 'total_cartao_debito' => $totalizacoes['por_forma_pagamento']['cartao_debito']['total'] ?? 0,
 'total_pix' => $totalizacoes['por_forma_pagamento']['pix']['total'] ?? 0,
 'total_vale' => $totalizacoes['por_forma_pagamento']['vale_refeicao']['total'] ?? 0,
 ]);
 DB::commit();
 return redirect()->route('caixa.fechamento.confirmacao', $caixaAberto->id)
 ->with('success', 'Caixa fechado com sucesso!');
 } catch (\Exception $e) {
 DB::rollBack();
 Log::error('Erro ao fechar caixa: ' . $e->getMessage());
 return back()->with('error', 'Erro ao fechar o caixa: ' . $e->getMessage());
 }
 }
 public function confirmacaoFechamento($id)
 {
 $caixa = Caixa::with('usuario')->findOrFail($id);
 if ($caixa->status !== 'fechado') {
 return redirect()->route('caixa.index')
 ->with('error', 'Este caixa ainda não foi fechado.');
 }
 $totalizacoes = $caixa->getTotalizacoes();
 $saldoFinalDinheiro = $caixa->saldo_inicial + 
 ($totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0) - 
 ($totalizacoes['total_troco'] ?? 0);
 return view('caixa.fechamento-confirmacao', compact('caixa', 'totalizacoes', 'saldoFinalDinheiro'));
 }
 public function historico(Request $request)
 {        
 $query = Caixa::with(['usuario'])
 ->orderBy('data_abertura', 'desc');
 if ($request->filled('data_inicio')) {
 $query->whereDate('data_abertura', '>=', $request->data_inicio);
 }
 if ($request->filled('data_fim')) {
 $query->whereDate('data_abertura', '<=', $request->data_fim);
 }
 if ($request->filled('status')) {
 $query->where('status', $request->status);
 }
 if ($request->filled('usuario_id')) {
 $query->where('usuario_id', $request->usuario_id);
 }
 $caixas = $query->paginate(10);// Calcular totalizações para cada caixa
 $caixas->getCollection()->transform(function($caixa) {
 $totalizacoes = $caixa->getTotalizacoes();
 $caixa->total_vendas_real = $totalizacoes['total_vendas'];
 $caixa->total_dinheiro_real = $totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0;
 $caixa->total_cartao_credito_real = $totalizacoes['por_forma_pagamento']['cartao_credito']['total'] ?? 0;
 $caixa->total_cartao_debito_real = $totalizacoes['por_forma_pagamento']['cartao_debito']['total'] ?? 0;
 $caixa->total_pix_real = $totalizacoes['por_forma_pagamento']['pix']['total'] ?? 0;
 $caixa->total_vale_real = $totalizacoes['por_forma_pagamento']['vale_refeicao']['total'] ?? 0;
 $caixa->quantidade_vendas = $totalizacoes['quantidade_vendas'];
 $caixa->total_cartao_real = $caixa->total_cartao_credito_real + $caixa->total_cartao_debito_real;
 $caixa->diferenca_total = $totalizacoes['total_vendas'] - ($caixa->total_vendas ?? 0);
 return $caixa;
 });
 $totaisGerais = [
 'total_vendas' => $caixas->sum('total_vendas_real'),
 'total_dinheiro' => $caixas->sum('total_dinheiro_real'),
 'total_cartao_credito' => $caixas->sum('total_cartao_credito_real'),
 'total_cartao_debito' => $caixas->sum('total_cartao_debito_real'),
 'total_cartao' => $caixas->sum('total_cartao_real'),
 'total_pix' => $caixas->sum('total_pix_real'),
 'total_vale' => $caixas->sum('total_vale_real'),
 'quantidade_caixas' => $caixas->count(),
 'quantidade_vendas' => $caixas->sum('quantidade_vendas')
 ];
 return view('caixa.historico', compact('caixas', 'totaisGerais'));
 }    
 public function relatorio(Caixa $caixa)
 {
 $query = Pagamento::with(['pedido.mesa', 'usuario'])
 ->where('status', 'confirmado')
 ->where('data_pagamento', '>=', $caixa->data_abertura);
 if ($caixa->data_fechamento) {
 $query->where('data_pagamento', '<=', $caixa->data_fechamento);
 }
 $pagamentos = $query->get();
 $totalVendas = $pagamentos->sum('valor');
 $quantidadeVendas = $pagamentos->count();
 $formasPagamento = $pagamentos->groupBy('forma_pagamento')
 ->map(function ($pagamentosForma, $forma) {
 return [
 'quantidade' => $pagamentosForma->count(),
 'total' => $pagamentosForma->sum('valor'),
 ];
 })->toArray();
 $formasDefault = [
 'dinheiro' => ['quantidade' => 0, 'total' => 0],
 'cartao_credito' => ['quantidade' => 0, 'total' => 0],
 'cartao_debito' => ['quantidade' => 0, 'total' => 0],
 'pix' => ['quantidade' => 0, 'total' => 0],
 'vale_refeicao' => ['quantidade' => 0, 'total' => 0],
 ];
 $formasPagamento = array_merge($formasDefault, $formasPagamento);
 if ($caixa->total_vendas != $totalVendas) {
 $totalCartaoCredito = $formasPagamento['cartao_credito']['total'];
 $totalCartaoDebito = $formasPagamento['cartao_debito']['total'];
 $caixa->update([
 'total_vendas' => $totalVendas,
 'total_dinheiro' => $formasPagamento['dinheiro']['total'],
 'total_cartao_credito' => $totalCartaoCredito,
 'total_cartao_debito' => $totalCartaoDebito,
 'total_cartao' => $totalCartaoCredito + $totalCartaoDebito,
 'total_pix' => $formasPagamento['pix']['total'],
 'total_vale' => $formasPagamento['vale_refeicao']['total']
 ]);
 }
 return view('caixa.relatorio', compact('caixa', 'pagamentos', 'totalVendas', 'quantidadeVendas', 'formasPagamento'));
 }
 public function totaisTempoReal()
 {
 $caixaAberto = Caixa::caixaAbertoHoje();
 if (!$caixaAberto) {
 return response()->json([
 'success' => false,
 'message' => 'Nenhum caixa aberto encontrado'
 ], 404);
 }
 $totais = $caixaAberto->getTotalizacoesPorPeriodo();
 return response()->json([
 'success' => true,
 'totais' => $totais,
 'caixa_id' => $caixaAberto->id
 ]);
 }
 private function calcularTotaisHoje()
 {
 $hoje = Carbon::today();
 $pagamentos = Pagamento::where('status', 'confirmado')
 ->whereDate('data_pagamento', $hoje)
 ->get();
 return [
 'total_vendas' => $pagamentos->sum('valor'),
 'total_recebido' => $pagamentos->sum('valor_recebido'),
 'total_troco' => $pagamentos->sum('troco'),
 'quantidade_vendas' => $pagamentos->count(),
 'por_forma' => $pagamentos->groupBy('forma_pagamento')
 ->map(function ($pagamentosForma) {
 return [
 'quantidade' => $pagamentosForma->count(),
 'total' => $pagamentosForma->sum('valor'),
 'recebido' => $pagamentosForma->sum('valor_recebido'),
 'troco' => $pagamentosForma->sum('troco')
 ];
 })
 ];
 }
 public function recebimento(Pedido $pedido)
 {
 $caixaAberto = Caixa::caixaAbertoHoje();
 if (!$caixaAberto) {
 return redirect()->route('caixa.index')->with('error', 'Não há caixa aberto!');
 }
 if ($pedido->status === 'aberto') {
 $pedido->update(['status' => 'finalizado']);
 }
 $pedido->load('mesa', 'itens.produto', 'pagamentos', 'delivery');
 if ($pedido->isPago()) {
 return redirect()->route('caixa.index')->with('info', 'Pedido já foi totalmente pago!');
 }
 return view('caixa.recebimento', compact('pedido'));
 }
 public function processarPagamento(Request $request, Pedido $pedido)
 {
 Log::info('=== INICIANDO PROCESSAMENTO DE PAGAMENTO ===');
 Log::info('Request completo', [
 'method' => $request->method(),
 'url' => $request->fullUrl(),
 'headers' => $request->headers->all(),
 'all_data' => $request->all(),
 'raw_input' => $request->getContent(),
 'pedido_id' => $pedido->id,
 'has_multiplos' => $request->has('multiplos_pagamentos'),
 'expects_json' => $request->expectsJson(),
 'content_type' => $request->header('Content-Type'),
 'user_agent' => $request->header('User-Agent')
 ]);
 try {
 if ($request->has('teste_conectividade')) {
 Log::info('Teste de conectividade executado com sucesso');
 return response()->json([
 'success' => true, 
 'message' => 'Conectividade OK! Servidor Laravel funcionando.',
 'timestamp' => now()->toDateTimeString()
 ]);
 }
 $caixaAberto = Caixa::caixaAbertoHoje();
 if (!$caixaAberto) {
 Log::error('Nenhum caixa aberto encontrado');
 if ($request->expectsJson()) {
 return response()->json(['success' => false, 'message' => 'Não há caixa aberto!'], 400);
 }
 return redirect()->back()->with('error', 'Não há caixa aberto!');
 }
 if (!$request->has('multiplos_pagamentos') && !$request->has('forma_pagamento')) {
 Log::error('Dados de pagamento inválidos', $request->all());
 if ($request->expectsJson()) {
 return response()->json(['success' => false, 'message' => 'Dados de pagamento inválidos!'], 400);
 }
 return redirect()->back()->with('error', 'Dados de pagamento inválidos!');
 }
 DB::beginTransaction();
 if ($request->has('multiplos_pagamentos')) {
 Log::info('Processando múltiplos pagamentos');
 $multiplosPagamentos = json_decode($request->multiplos_pagamentos, true);
 Log::info('Dados decodificados', ['multiplos_pagamentos' => $multiplosPagamentos]);
 if (!is_array($multiplosPagamentos) || empty($multiplosPagamentos)) {
 Log::error('Dados de múltiplos pagamentos inválidos', ['dados' => $multiplosPagamentos]);
 throw new \Exception('Dados de múltiplos pagamentos inválidos!');
 }
 $totalMultiplos = 0;
 foreach ($multiplosPagamentos as $index => $pagamentoData) {
 Log::info("Processando pagamento {$index}", $pagamentoData);
 if (!isset($pagamentoData['forma_pagamento']) || !isset($pagamentoData['valor'])) {
 Log::error("Dados incompletos no pagamento {$index}", $pagamentoData);
 throw new \Exception('Dados incompletos no pagamento ' . ($index + 1));
 }
 if (!is_numeric($pagamentoData['valor']) || $pagamentoData['valor'] <= 0) {
 Log::error("Valor inválido no pagamento {$index}", ['valor' => $pagamentoData['valor']]);
 throw new \Exception('Valor inválido no pagamento ' . ($index + 1));
 }
 $totalMultiplos += $pagamentoData['valor'];
 $pagamentoCriado = $this->criarPagamento($pedido, $pagamentoData, $caixaAberto->id);
 Log::info("Pagamento criado", ['id' => $pagamentoCriado->id, 'valor' => $pagamentoCriado->valor]);
 }
 Log::info('Verificando totais', [
 'total_multiplos' => $totalMultiplos,
 'saldo_restante' => $pedido->saldo_restante,
 'total_pedido' => $pedido->total
 ]);
 $pedido->refresh();
 if (abs($totalMultiplos - $pedido->total) > 0.01) {
 Log::error('Total não confere', [
 'total_multiplos' => $totalMultiplos,
 'total_pedido' => $pedido->total,
 'diferenca' => abs($totalMultiplos - $pedido->total)
 ]);
 throw new \Exception('Total dos pagamentos (' . number_format($totalMultiplos, 2, ',', '.') . ') não confere com o total do pedido (' . number_format($pedido->total, 2, ',', '.') . ')');
 }
 } else {
 $pagamentoData = [
 'forma_pagamento' => $request->forma_pagamento,
 'valor' => $request->valor,
 'valor_recebido' => $request->valor_recebido,
 'observacoes' => $request->observacoes
 ];
 $this->criarPagamento($pedido, $pagamentoData, $caixaAberto->id);
 }
 $pedido->refresh();
 Log::info('Verificando se pedido foi totalmente pago', [
 'pedido_id' => $pedido->id,
 'isPago' => $pedido->isPago(),
 'total' => $pedido->total,
 'total_pago' => $pedido->total_pago,
 'saldo_restante' => $pedido->saldo_restante
 ]);
 if ($pedido->isPago()) {
 $pedido->update(['status' => 'pago']);
 Log::info('Pedido marcado como pago', ['pedido_id' => $pedido->id]);
 }
 DB::commit();
 Log::info('Transação commitada com sucesso');
 if ($request->expectsJson()) {
 return response()->json(['success' => true, 'message' => 'Pagamento processado com sucesso!']);
 }
 return redirect()->route('caixa.index')->with('success', 'Pagamento processado com sucesso!');
 } catch (\Exception $e) {
 DB::rollback();
 Log::error('Erro ao processar pagamento', [
 'pedido_id' => $pedido->id ?? 'N/A',
 'erro' => $e->getMessage(),
 'trace' => $e->getTraceAsString(),
 'request_headers' => $request->headers->all(),
 'expects_json' => $request->expectsJson(),
 'ajax' => $request->ajax()
 ]);
 if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
 return response()->json([
 'success' => false, 
 'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
 'error_type' => get_class($e),
 'line' => $e->getLine(),
 'file' => basename($e->getFile())
 ], 500);
 }            return redirect()->back()->with('error', 'Erro ao processar pagamento: ' . $e->getMessage());
 }
 }
 private function criarPagamento(Pedido $pedido, array $dados, int $caixaId)
 {
 if (!isset($dados['forma_pagamento']) || !isset($dados['valor'])) {
 throw new \Exception('Dados de pagamento incompletos');
 }
 $usuarioId = Auth::id();
 if (!$usuarioId) {
 $usuario = Usuario::first();
 if (!$usuario) {
 throw new \Exception('Nenhum usuário encontrado no sistema');
 }
 $usuarioId = $usuario->id;
 }
 $valorRecebido = $dados['valor_recebido'] ?? $dados['valor'];
 $troco = 0;
 if ($dados['forma_pagamento'] === 'dinheiro' && isset($dados['valor_recebido'])) {
 $troco = max(0, $valorRecebido - $dados['valor']);
 }
 return Pagamento::create([
 'pedido_id' => $pedido->id,
 'caixa_id' => $caixaId,
 'usuario_id' => $usuarioId,
 'forma_pagamento' => $dados['forma_pagamento'],
 'valor' => $dados['valor'],
 'valor_recebido' => $valorRecebido,
 'troco' => $troco,
 'status' => 'confirmado',
 'data_pagamento' => now(),
 'observacoes' => $dados['observacoes'] ?? null
 ]);
 }
}