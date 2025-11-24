<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Caixa;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
class PagamentoController extends Controller
{
 public function processarPagamentoPedido(Request $request, Pedido $pedido)
 {
 Log::info('=== API UNIFICADA: Processando pagamento de pedido ===', [
 'pedido_id' => $pedido->id,
 'usuario_id' => Auth::id(),
 'request_data' => $request->all()
 ]);
 try {
 $caixaAberto = $this->verificarCaixaAberto();
 $this->validarPedidoParaPagamento($pedido);
 DB::beginTransaction();
 $totalProcessado = 0;
 $pagamentosCriados = collect();
 if ($request->has('multiplos_pagamentos')) {
 $resultado = $this->processarMultiplosPagamentos($request, $pedido, $caixaAberto->id);
 $totalProcessado = $resultado['total'];
 $pagamentosCriados = collect($resultado['pagamentos']);
 } else {
 $this->validarDadosPagamentoUnico($request);
 $pagamento = $this->criarPagamento($pedido, [
 'forma_pagamento' => $request->forma_pagamento,
 'valor' => $request->valor,
 'valor_recebido' => $request->valor_recebido,
 'observacoes' => $request->observacoes
 ], $caixaAberto->id);
 $totalProcessado = $pagamento->valor;
 $pagamentosCriados = collect([$pagamento]);
 }
 $pedido->refresh();
 if ($pedido->isPago()) {
 $pedido->update(['status' => 'pago']);
 Log::info('Pedido marcado como pago', ['pedido_id' => $pedido->id]);
 }
 $this->atualizarTotaisCaixa($caixaAberto, $pagamentosCriados->toArray());
 DB::commit();
 Log::info('Pagamento processado com sucesso', [
 'pedido_id' => $pedido->id,
 'total_processado' => $totalProcessado,
 'quantidade_pagamentos' => $pagamentosCriados->count(),
 'pedido_pago' => $pedido->isPago()
 ]);
 return response()->json([
 'success' => true,
 'message' => 'Pagamento processado com sucesso!',
 'data' => [
 'pedido_id' => $pedido->id,
 'total_processado' => $totalProcessado,
 'pedido_totalmente_pago' => $pedido->isPago(),
 'saldo_restante' => $pedido->saldo_restante,
 'pagamentos' => $pagamentosCriados->map(function($p) {
 return [
 'id' => $p->id,
 'forma_pagamento' => $p->forma_pagamento,
 'valor' => $p->valor,
 'valor_recebido' => $p->valor_recebid, 
 'troco' => $p->troco
 ];
 })
 ]
 ]);
 } catch (ValidationException $e) {
 DB::rollback(); 
 return response()->json([
 'success' => false,
 'message' => 'Dados inválidos',
 'errors' => $e->validator->errors()
 ], 422);
 } catch (\Exception $e) {
 DB::rollback();
 Log::error('Erro ao processar pagamento', [
 'pedido_id' => $pedido->id,
 'erro' => $e->getMessage(),
 'trace' => $e->getTraceAsString()
 ]);
 return response()->json([
 'success' => false,
 'message' => 'Erro ao processar pagamento: ' . $e->getMessage()
 ], 500);
 }
 }
 public function processarPagamentoMesa(Request $request, Mesa $mesa)
 {
 Log::info('=== API UNIFICADA: Processando pagamento de mesa ===', [
 'mesa_id' => $mesa->id,
 'usuario_id' => Auth::id(),
 'request_data' => $request->all()
 ]);
 try {
 $caixaAberto = $this->verificarCaixaAberto();
 $pedidos = $mesa->pedidos()
 ->whereIn('status', ['aberto', 'finalizado', 'entregue'])
 ->get();
 if ($pedidos->isEmpty()) {
 return response()->json([
 'success' => false,
 'message' => 'Não há pedidos finalizados aguardando pagamento nesta mesa'
 ], 400);
 }
 $totalMesa = $pedidos->sum('total');
 DB::beginTransaction();
 $totalProcessado = 0;
 $pagamentosCriados = collect();
 if ($request->has('multiplos_pagamentos')) {
 $multiplosPagamentos = json_decode($request->multiplos_pagamentos, true);
 $this->validarMultiplosPagamentos($multiplosPagamentos);
 $totalPagamentos = collect($multiplosPagamentos)->sum('valor');
 if (abs($totalPagamentos - $totalMesa) > 0.01) {
 throw new \Exception("Total dos pagamentos (R$ " . number_format($totalPagamentos, 2, ',', '.') . 
 ") não confere com o total da mesa (R$ " . number_format($totalMesa, 2, ',', '.') . ")");
 }
 foreach ($pedidos as $pedido) {
 $proporcao = $pedido->total / $totalMesa;
 foreach ($multiplosPagamentos as $pagamentoData) {
 $valorProporcional = $pagamentoData['valor'] * $proporcao;
 if ($valorProporcional > 0.01) {
 $dadosPagamento = $pagamentoData;
 $dadosPagamento['valor'] = $valorProporcional;
 $pagamento = $this->criarPagamento($pedido, $dadosPagamento, $caixaAberto->id);
 $pagamentosCriados->push($pagamento);
 $totalProcessado += $pagamento->valor;
 }
 }
 }
 } else {
 $this->validarDadosPagamentoUnico($request);
 foreach ($pedidos as $pedido) {
 $proporcao = $pedido->total / $totalMesa;
 $valorProporcional = $request->valor * $proporcao;
 if ($valorProporcional > 0.01) {
 $pagamento = $this->criarPagamento($pedido, [
 'forma_pagamento' => $request->forma_pagamento,
 'valor' => $valorProporcional,
 'valor_recebido' => $request->valor_recebido ? ($request->valor_recebido * $proporcao) : $valorProporcional,
 'observacoes' => $request->observacoes
 ], $caixaAberto->id);
 $pagamentosCriados->push($pagamento);
 $totalProcessado += $pagamento->valor;
 }
 }
 }
 $pedidosPagos = 0;
 foreach ($pedidos as $pedido) {
 $pedido->refresh();
 if ($pedido->isPago()) {
 $pedido->update(['status' => 'pago']);
 $pedidosPagos++;
 }
 }
 $this->atualizarTotaisCaixa($caixaAberto, $pagamentosCriados->toArray());
 DB::commit();
 Log::info('Pagamento de mesa processado com sucesso', [
 'mesa_id' => $mesa->id,
 'total_processado' => $totalProcessado,
 'quantidade_pagamentos' => $pagamentosCriados->count(),
 'pedidos_pagos' => $pedidosPagos
 ]);
 return response()->json([
 'success' => true,
 'message' => 'Pagamento da mesa processado com sucesso!',
 'data' => [
 'mesa_id' => $mesa->id,
 'total_processado' => $totalProcessado,
 'pedidos_processados' => $pedidos->count(),
 'pedidos_totalmente_pagos' => $pedidosPagos,
 'pagamentos' => $pagamentosCriados->map(function($p) {
 return [
 'id' => $p->id,
 'pedido_id' => $p->pedido_id,
 'forma_pagamento' => $p->forma_pagamento,
 'valor' => $p->valor,
 'troco' => $p->troco
 ];
 })
 ]
 ]);
 } catch (ValidationException $e) {
 DB::rollback();
 return response()->json([
 'success' => false,
 'message' => 'Dados inválidos',
 'errors' => $e->validator->errors()
 ], 422);
 } catch (\Exception $e) {
 DB::rollback();
 Log::error('Erro ao processar pagamento da mesa', [
 'mesa_id' => $mesa->id,
 'erro' => $e->getMessage(),
 'trace' => $e->getTraceAsString()
 ]);
 return response()->json([
 'success' => false,
 'message' => 'Erro ao processar pagamento da mesa: ' . $e->getMessage()
 ], 500);
 }
 }
 public function infoParaPagamentoPedido(Pedido $pedido)
 {
 try {
 $this->verificarCaixaAberto();
 $this->validarPedidoParaPagamento($pedido);
 $pedido->load('itens.produto', 'mesa', 'pagamentos');
 return response()->json([
 'success' => true,
 'data' => [
 'pedido' => [
 'id' => $pedido->id,
 'mesa' => $pedido->mesa->numero ?? 'Balcão',
 'total' => $pedido->total,
 'total_pago' => $pedido->total_pago,
 'saldo_restante' => $pedido->saldo_restante,
 'status' => $pedido->status
 ],                    'itens' => $pedido->itens->map(function($item) {
 return [
 'produto' => $item->produto->nome,
 'quantidade' => $item->quantidade,
 'preco_unitario' => $item->preco_unitario,
 'subtotal' => $item->subtotal,
 'observacoes' => $item->observacoes
 ];
 }),
 'pagamentos_existentes' => $pedido->pagamentos->where('status', 'confirmado')->map(function($pag) {
 return [
 'forma_pagamento' => $pag->forma_pagamento,
 'valor' => $pag->valor,
 'data' => $pag->data_pagamento->format('d/m/Y H:i')
 ];
 })->values(),
 'formas_pagamento_disponiveis' => [
 'dinheiro' => 'Dinheiro',
 'cartao_credito' => 'Cartão de Crédito',
 'cartao_debito' => 'Cartão de Débito',
 'pix' => 'PIX',
 'vale_refeicao' => 'Vale Refeição'
 ]
 ]
 ]);
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'message' => $e->getMessage()
 ], 400);
 }    }
 public function infoParaPagamentoMesa(Mesa $mesa)
 {
 try {
 $this->verificarCaixaAberto();
 $pedidos = $mesa->pedidos()
 ->with(['itens.produto', 'pagamentos'])
 ->whereIn('status', ['aberto', 'finalizado', 'entregue'])
 ->get()
 ->filter(function($pedido) {
 return !$pedido->isPago();
 });
 if ($pedidos->isEmpty()) {
 throw new \Exception('Não há pedidos finalizados aguardando pagamento nesta mesa');
 }
 $totalMesa = $pedidos->sum('total');
 return response()->json([
 'success' => true,
 'data' => [
 'mesa' => [
 'id' => $mesa->id,
 'numero' => $mesa->numero,
 'identificador' => $mesa->identificador,
 'capacidade' => $mesa->capacidade,
 'lugares' => $mesa->lugares,
 'status' => $mesa->status,
 'total_geral' => $totalMesa
 ],
 'pedidos' => $pedidos->map(function($pedido) {
 return [
 'id' => $pedido->id,
 'total' => $pedido->total,
 'total_pago' => $pedido->total_pago,
 'saldo_restante' => $pedido->saldo_restante,
 'created_at' => $pedido->created_at,
 'status' => $pedido->status,
 'itens_count' => $pedido->itens->count(),
 'principal_item' => ($pedido->itens->first() ? $pedido->itens->first()->produto->nome : 'N/A'),
 'usuario' => [
 'id' => $pedido->usuario->id,
 'nome' => $pedido->usuario->nome
 ]
 ];
 }),
 'total_mesa' => $totalMesa,
 'formas_pagamento_disponiveis' => [
 'dinheiro' => 'Dinheiro',
 'cartao_credito' => 'Cartão de Crédito',
 'cartao_debito' => 'Cartão de Débito',
 'pix' => 'PIX',
 'vale_refeicao' => 'Vale Refeição'
 ]
 ]
 ]);
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'message' => $e->getMessage()
 ], 400);
 }
 }
 private function verificarCaixaAberto()
 {
 $caixaAberto = Caixa::caixaAbertoHoje();
 if (!$caixaAberto) {
 throw new \Exception('Não há caixa aberto hoje. Abra o caixa antes de processar pagamentos.');
 }
 return $caixaAberto;
 }
 private function validarPedidoParaPagamento(Pedido $pedido)
 {
 if (!in_array($pedido->status, ['finalizado', 'entregue'])) {
 throw new \Exception('Apenas pedidos finalizados ou entregues podem receber pagamento');
 }
 if ($pedido->isPago()) {
 throw new \Exception('Este pedido já foi totalmente pago');
 }
 }
 private function validarDadosPagamentoUnico(Request $request)
 {
 $request->validate([
 'forma_pagamento' => 'required|in:dinheiro,cartao_credito,cartao_debito,pix,vale_refeicao',
 'valor' => 'required|numeric|min:0.01',
 'valor_recebido' => 'nullable|numeric|min:0',
 'observacoes' => 'nullable|string|max:500'
 ]);
 }
 private function processarMultiplosPagamentos(Request $request, Pedido $pedido, int $caixaId)
 {
 $multiplosPagamentos = json_decode($request->multiplos_pagamentos, true);
 $this->validarMultiplosPagamentos($multiplosPagamentos);
 $totalMultiplos = collect($multiplosPagamentos)->sum('valor');
 if (abs($totalMultiplos - $pedido->total) > 0.01) {
 throw new \Exception('Total dos pagamentos (' . number_format($totalMultiplos, 2, ',', '.') . 
 ') não confere com o total do pedido (' . number_format($pedido->total, 2, ',', '.') . ')');
 }
 $pagamentos = collect();
 foreach ($multiplosPagamentos as $pagamentoData) {
 $pagamento = $this->criarPagamento($pedido, $pagamentoData, $caixaId);
 $pagamentos->push($pagamento);
 }
 return [
 'total' => $totalMultiplos,
 'pagamentos' => $pagamentos
 ];
 }
 private function validarMultiplosPagamentos(array $multiplosPagamentos)
 {
 if (empty($multiplosPagamentos)) {
 throw new \Exception('Dados de múltiplos pagamentos inválidos');
 }
 foreach ($multiplosPagamentos as $index => $pagamentoData) {
 if (!isset($pagamentoData['forma_pagamento']) || !isset($pagamentoData['valor'])) {
 throw new \Exception('Dados incompletos no pagamento ' . ($index + 1));
 }
 if (!is_numeric($pagamentoData['valor']) || $pagamentoData['valor'] <= 0) {
 throw new \Exception('Valor inválido no pagamento ' . ($index + 1));
 }
 $formasValidas = ['dinheiro', 'cartao_credito', 'cartao_debito', 'pix', 'vale_refeicao'];
 if (!in_array($pagamentoData['forma_pagamento'], $formasValidas)) {
 throw new \Exception('Forma de pagamento inválida no pagamento ' . ($index + 1));
 }
 }
 }
 private function criarPagamento(Pedido $pedido, array $dados, int $caixaId)
 {
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
 private function atualizarTotaisCaixa(Caixa $caixa, $pagamentos)
 {
 $pagamentosCollection = collect($pagamentos);
 $totalVendas = $pagamentosCollection->sum('valor');
 $caixa->total_vendas += $totalVendas;
 $pagamentosPorForma = $pagamentosCollection->groupBy('forma_pagamento');
 foreach ($pagamentosPorForma as $forma => $pagamentosForma) {
 $total = $pagamentosForma->sum('valor');
 switch ($forma) {
 case 'dinheiro':
 $caixa->total_dinheiro += $total;
 break;
 case 'cartao_credito':
 $caixa->total_cartao_credito += $total;
 $caixa->total_cartao += $total;
 break;
 case 'cartao_debito':
 $caixa->total_cartao_debito += $total;
 $caixa->total_cartao += $total;
 break;
 case 'pix':
 $caixa->total_pix += $total;
 break;
 case 'vale_refeicao':
 $caixa->total_vale += $total;
 break;
 }
 }
 $caixa->save();
 }
 public function syncOffline(Request $request)
 {
 try {
 $validated = $request->validate([
 'pedido_id' => 'nullable|exists:pedidos,id',
 'mesa_id' => 'nullable|exists:mesas,id',
 'tipo' => 'required|in:pedido,mesa',
 'formas_pagamento' => 'required|array|min:1',
 'formas_pagamento.*.forma' => 'required|in:dinheiro,cartao_credito,cartao_debito,pix,vale_refeicao',
 'formas_pagamento.*.valor' => 'required|numeric|min:0.01',
 'valor_recebido' => 'nullable|numeric|min:0',
 'troco' => 'nullable|numeric|min:0'
 ]);
 DB::beginTransaction();
 if ($validated['tipo'] === 'pedido') {
 $pedido = Pedido::findOrFail($validated['pedido_id']);
 $result = $this->processarPagamentoPedido($request, $pedido);
 } else {
 $mesa = Mesa::findOrFail($validated['mesa_id']);
 $result = $this->processarPagamentoMesa($request, $mesa);
 }
 DB::commit();
 return response()->json([
 'success' => true,
 'message' => 'Pagamento sincronizado com sucesso',
 'data' => $result->getData()
 ], 200);
 } catch (\Exception $e) {
 DB::rollBack();
 Log::error('Erro ao sincronizar pagamento offline: ' . $e->getMessage());
 return response()->json([
 'success' => false,
 'message' => 'Erro ao sincronizar pagamento',
 'error' => $e->getMessage()
 ], 500);
 }
 }
}