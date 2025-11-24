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
class PagamentoSimplificadoController extends Controller
{
 public function processarPagamentoPedido(Request $request, Pedido $pedido)
 {
 Log::info('=== CONTROLLER SIMPLIFICADO: Processando pagamento ===', [
 'pedido_id' => $pedido->id,
 'request_data' => $request->all()
 ]);
 try {
 $caixaAberto = Caixa::where('status', 'aberto')->first();
 if (!$caixaAberto) {
 throw new \Exception('Nenhum caixa aberto encontrado');
 }
 if ($pedido->status !== 'finalizado') {
 throw new \Exception('Apenas pedidos finalizados podem ser pagos');
 }
 $request->validate([
 'forma_pagamento' => 'required|in:dinheiro,cartao_credito,cartao_debito,pix,vale_refeicao',
 'valor' => 'required|numeric|min:0.01'
 ]);
 DB::beginTransaction();
 $valorRecebido = $request->input('valor_recebido', $request->valor);
 $troco = 0;
 if ($request->forma_pagamento === 'dinheiro' && $request->has('valor_recebido')) {
 $troco = max(0, $valorRecebido - $request->valor);
 }
 $usuarioId = Auth::id() ?? 1;
 $pagamento = Pagamento::create([
 'pedido_id' => $pedido->id,
 'caixa_id' => $caixaAberto->id,
 'usuario_id' => $usuarioId,
 'forma_pagamento' => $request->forma_pagamento,
 'valor' => $request->valor,
 'valor_recebido' => $valorRecebido,
 'troco' => $troco,
 'status' => 'confirmado',
 'data_pagamento' => now(),
 'observacoes' => $request->input('observacoes')
 ]);
 $pedido->refresh();
 if ($pedido->isPago()) {
 $pedido->update(['status' => 'pago']);
 }
 DB::commit();
 Log::info('Pagamento processado com sucesso', [
 'pagamento_id' => $pagamento->id,
 'pedido_id' => $pedido->id,
 'valor' => $pagamento->valor
 ]);
 return response()->json([
 'success' => true,
 'message' => 'Pagamento processado com sucesso!',
 'data' => [
 'pagamento_id' => $pagamento->id,
 'pedido_id' => $pedido->id,
 'valor' => $pagamento->valor,
 'forma_pagamento' => $pagamento->forma_pagamento,
 'troco' => $pagamento->troco,
 'pedido_totalmente_pago' => $pedido->isPago(),
 'saldo_restante' => $pedido->saldo_restante
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
 Log::error('Erro no controller simplificado', [
 'erro' => $e->getMessage(),
 'linha' => $e->getLine(),
 'arquivo' => $e->getFile()
 ]);
 return response()->json([
 'success' => false,
 'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
 'debug' => [
 'file' => basename($e->getFile()),
 'line' => $e->getLine()
 ]
 ], 500);
 }
 }
 public function infoParaPagamentoPedido(Pedido $pedido)
 {
 try {
 $caixaAberto = Caixa::where('status', 'aberto')->first();
 if (!$caixaAberto) {
 return response()->json([
 'success' => false,
 'message' => 'Não há caixa aberto'
 ], 400);
 }
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
 ],
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
 'message' => 'Erro: ' . $e->getMessage()
 ], 500);
 }
 }
}