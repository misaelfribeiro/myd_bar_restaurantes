<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PlanoController extends Controller
{
 public function index()
 {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado. Apenas Master pode gerenciar planos.');
 }
 $estatisticas = [
 'basico' => [
 'total' => Empresa::where('plano', 'basico')->count(),
 'ativos' => Empresa::where('plano', 'basico')->where('status_contrato', 'ativo')->count(),
 'trial' => Empresa::where('plano', 'basico')->where('status_contrato', 'trial')->count(),
 'receita_mensal' => Empresa::where('plano', 'basico')->where('status_contrato', 'ativo')->sum('valor_mensalidade'),
 ],
 'profissional' => [
 'total' => Empresa::where('plano', 'profissional')->count(),
 'ativos' => Empresa::where('plano', 'profissional')->where('status_contrato', 'ativo')->count(),
 'trial' => Empresa::where('plano', 'profissional')->where('status_contrato', 'trial')->count(),
 'receita_mensal' => Empresa::where('plano', 'profissional')->where('status_contrato', 'ativo')->sum('valor_mensalidade'),
 ],
 'premium' => [
 'total' => Empresa::where('plano', 'premium')->count(),
 'ativos' => Empresa::where('plano', 'premium')->where('status_contrato', 'ativo')->count(),
 'trial' => Empresa::where('plano', 'premium')->where('status_contrato', 'trial')->count(),
 'receita_mensal' => Empresa::where('plano', 'premium')->where('status_contrato', 'ativo')->sum('valor_mensalidade'),
 ],
 'enterprise' => [
 'total' => Empresa::where('plano', 'enterprise')->count(),
 'ativos' => Empresa::where('plano', 'enterprise')->where('status_contrato', 'ativo')->count(),
 'trial' => Empresa::where('plano', 'enterprise')->where('status_contrato', 'trial')->count(),
 'receita_mensal' => Empresa::where('plano', 'enterprise')->where('status_contrato', 'ativo')->sum('valor_mensalidade'),
 ],
 ];
 $empresasPorPlano = Empresa::select('id', 'plano', 'nome_fantasia', 'tenant_code', 'status_contrato', 'valor_mensalidade', 'data_fim_contrato')
 ->where('is_master', false)
 ->orderBy('plano')
 ->orderBy('nome_fantasia')
 ->get()
 ->groupBy('plano');
 return view('admin.planos.index', compact('estatisticas', 'empresasPorPlano'));
 }
 public function changePlan(Request $request, $empresaId)
 {
 try {
 \Log::info('=== INÍCIO changePlan ===');
 \Log::info('Empresa ID: ' . $empresaId);
 \Log::info('Request data: ' . json_encode($request->all()));
 
 if (!$this->isMaster()) {
 \Log::warning('Acesso negado - não é master');
 return response()->json([
 'success' => false,
 'message' => 'Acesso negado. Apenas usuários Master podem alterar planos.'
 ], 403);
 }
 
 \Log::info('Validando dados...');
 $validated = $request->validate([
 'plano' => 'required|in:basico,profissional,premium,enterprise',
 'valor_mensalidade' => 'required|numeric|min:0',
 'max_usuarios' => 'required|integer|min:1',
 'max_produtos' => 'required|integer|min:1',
 'max_pedidos_mes' => 'required|integer|min:1',
 'max_filiais' => 'required|integer|min:1',
 ]);
 DB::beginTransaction();
 $empresa = Empresa::findOrFail($empresaId);
 $planoAntigo = $empresa->plano;
 $valorAntigo = $empresa->valor_mensalidade;
 $contratoAtual = \App\Models\Contrato::where('empresa_id', $empresaId)
 ->where('status', 'ativo')
 ->orderBy('created_at', 'desc')
 ->first();
 $valorProporcional = 0;
 $diasRestantes = 0;
 if ($contratoAtual && $contratoAtual->data_fim) {
 $hoje = \Carbon\Carbon::now();
 $dataInicio = \Carbon\Carbon::parse($contratoAtual->data_inicio);
 $dataFim = \Carbon\Carbon::parse($contratoAtual->data_fim);
 if ($dataFim->gt($hoje)) {
 $diasRestantes = $hoje->diffInDays($dataFim);
 $diasTotais = $dataInicio->diffInDays($dataFim);
 $diasUsados = $dataInicio->diffInDays($hoje);
 if ($diasUsados == 0) {
 $valorProporcional = 0;
 } else {
 $valorDiarioAntigo = $valorAntigo / $diasTotais;
 $valorUsadoPlanoAntigo = $valorDiarioAntigo * $diasUsados;
 $valorPagoAntigo = $valorAntigo;
 $creditoOuDebito = $valorPagoAntigo - $valorUsadoPlanoAntigo;
 $valorProporcional = $creditoOuDebito;
 }
 $contratoAtual->update([
 'status' => 'cancelado',
 'data_cancelamento' => now(),
 'motivo_cancelamento' => 'Alteração de plano para ' . strtoupper($request->plano),
 'cancelado_por' => auth()->id()
 ]);
 \App\Models\Fatura::where('contrato_id', $contratoAtual->id)
 ->whereIn('status', ['pendente', 'vencido'])
 ->update([
 'status' => 'cancelado',
 'observacoes' => 'Fatura cancelada devido à alteração de plano',
 'updated_at' => now()
 ]);
 \App\Models\HistoricoContrato::create([
 'contrato_id' => $contratoAtual->id,
 'empresa_id' => $empresaId,
 'acao' => 'alterado',
 'descricao' => "Plano alterado de {$planoAntigo} para {$request->plano}",
 'dados_anteriores' => json_encode([
 'plano' => $planoAntigo,
 'valor_mensalidade' => $valorAntigo
 ]),
 'dados_novos' => json_encode([
 'plano' => $request->plano,
 'valor_mensalidade' => $request->valor_mensalidade
 ]),
 'usuario_id' => auth()->id(),
 'ip_address' => request()->ip()
 ]);
 }
 }
 $novoPlano = \App\Models\Plano::where('codigo', $request->plano)->first();
 $numeroContrato = \App\Models\Contrato::gerarNumeroContrato();
 $dataInicio = now();
 $dataFim = now()->addDays($diasRestantes > 0 ? $diasRestantes : 30);
 $novoContrato = \App\Models\Contrato::create([
 'empresa_id' => $empresaId,
 'plano_id' => $novoPlano->id,
 'numero_contrato' => $numeroContrato,
 'data_inicio' => $dataInicio,
 'data_fim' => $dataFim,
 'tipo_pagamento' => 'mensal',
 'valor_contratado' => $request->valor_mensalidade,
 'valor_desconto' => 0,
 'valor_final' => $request->valor_mensalidade,
 'status' => 'ativo',
 'criado_por' => auth()->id(),
 'max_usuarios_customizado' => $request->max_usuarios,
 'max_produtos_customizado' => $request->max_produtos,
 'max_pedidos_mes_customizado' => $request->max_pedidos_mes,
 'max_filiais_customizado' => $request->max_filiais
 ]);
 \App\Models\HistoricoContrato::create([
 'contrato_id' => $novoContrato->id,
 'empresa_id' => $empresaId,
 'acao' => 'criado',
 'descricao' => "Contrato criado por alteração de plano",
 'dados_anteriores' => null,
 'dados_novos' => json_encode($novoContrato->toArray()),
 'usuario_id' => auth()->id(),
 'ip_address' => request()->ip()
 ]);
 $numeroFatura = 'FAT-' . date('Y') . '-' . str_pad(\App\Models\Fatura::count() + 1, 5, '0', STR_PAD_LEFT);
 $valorPlano = $request->valor_mensalidade;
 $valorAdicional = 0;
 $valorDesconto = 0;
 if ($valorProporcional > 0) {
 $valorDesconto = $valorProporcional;
 }
 $valorTotal = $valorPlano + $valorAdicional - $valorDesconto;
 $descricaoFatura = "Fatura do plano {$request->plano} - R$ " . number_format($request->valor_mensalidade, 2, ',', '.');
 if (isset($planoAntigo) && $planoAntigo != $request->plano) {
 $descricaoFatura = "Alteração de plano de {$planoAntigo} (R$ " . number_format($valorAntigo, 2, ',', '.') . 
 ") para {$request->plano} (R$ " . number_format($request->valor_mensalidade, 2, ',', '.') . ").";
 if (isset($diasUsados) && $diasUsados == 0) {
 $descricaoFatura .= " Alteração realizada no mesmo dia do início do contrato, sem dias consumidos.";
 } elseif ($valorProporcional > 0) {
 $descricaoFatura .= " Crédito de R$ " . number_format($valorProporcional, 2, ',', '.') . 
 " aplicado como desconto (dias restantes: {$diasRestantes}).";
 }
 }
 \App\Models\Fatura::create([
 'contrato_id' => $novoContrato->id,
 'empresa_id' => $empresaId,
 'numero_fatura' => $numeroFatura,
 'data_referencia' => now()->format('Y-m-01'),
 'data_vencimento' => now()->addDays(5),
 'data_emissao' => now(),
 'valor_plano' => $valorPlano,
 'valor_adicional' => $valorAdicional,
 'valor_desconto' => $valorDesconto,
 'valor_total' => max(0, $valorTotal),
 'status' => 'pendente',
 'descricao' => $descricaoFatura
 ]);
 $empresa->update([
 'plano' => $request->plano,
 'valor_mensalidade' => $request->valor_mensalidade,
 'max_usuarios' => $request->max_usuarios,
 'max_produtos' => $request->max_produtos,
 'max_pedidos_mes' => $request->max_pedidos_mes,
 'max_filiais' => $request->max_filiais,
 'data_fim_contrato' => $dataFim
 ]);
 DB::commit();
 
 // Limpa qualquer output buffer antes de retornar JSON
 while (ob_get_level() > 0) {
 ob_end_clean();
 }
 
 return response()->json([
 'success' => true,
 'message' => 'Plano alterado com sucesso!',
 'detalhes' => [
 'novo_contrato' => $numeroContrato,
 'dias_restantes' => $diasRestantes,
 'ajuste_valor' => number_format($valorProporcional, 2, ',', '.'),
 'tipo_ajuste' => $valorProporcional > 0 ? 'cobranca' : 'credito'
 ]
 ]);
 } catch (\Illuminate\Validation\ValidationException $e) {
 DB::rollBack();
 \Log::error('Erro de validação: ' . json_encode($e->errors()));
 return response()->json([
 'success' => false,
 'message' => 'Dados inválidos',
 'errors' => $e->errors()
 ], 422);
 } catch (\Exception $e) {
 DB::rollBack();
 \Log::error('Erro ao alterar plano: ' . $e->getMessage());
 \Log::error('Stack trace: ' . $e->getTraceAsString());
 return response()->json([
 'success' => false,
 'message' => 'Erro ao alterar plano: ' . $e->getMessage()
 ], 500);
 }
 }
 public function getPlanDetails($codigo)
 {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado.');
 }
 $plano = \App\Models\Plano::where('codigo', $codigo)->first();
 if (!$plano) {
 return response()->json(['error' => 'Plano não encontrado'], 404);
 }
 return response()->json([
 'nome' => $plano->nome,
 'valor_sugerido' => $plano->valor_mensal,
 'max_usuarios' => $plano->max_usuarios,
 'max_produtos' => $plano->max_produtos,
 'max_pedidos_mes' => $plano->max_pedidos_mes,
 'max_filiais' => $plano->max_filiais,
 'recursos' => $plano->recursos,
 ]);
 }
 private function isMaster()
 {
 if (auth()->guard('admin')->check()) {
 $user = auth()->guard('admin')->user();
 // Usuário com tenant_code EATSFOOD é Master
 return $user->tenant_code === 'EATSFOOD';
 }
 return false;
 }
}