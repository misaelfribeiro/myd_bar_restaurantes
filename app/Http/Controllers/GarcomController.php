<?php
namespace App\Http\Controllers;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\Usuario;
use App\Models\ItemPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class GarcomController extends Controller
{
 private function verificarCaixaAberto()
 {
 return \App\Models\Caixa::where('status', 'aberto')->first();
 }
 private function retornarErroCaixaFechado($request, $acao = 'realizar esta operação')
 {
 $mensagem = "Não é possível {$acao}. O caixa deve estar aberto para registrar vendas.";
 Log::warning('Tentativa de operação com caixa fechado', [
 'acao' => $acao,
 'user_id' => Auth::id(),
 'ip' => $request->ip()
 ]);
 if ($request->expectsJson() || $request->ajax()) {
 return response()->json([
 'success' => false,
 'message' => $mensagem
 ], 422);
 }
 return back()->with('error', $mensagem);
 }
 public function dashboard()
 {
 $userId = Auth::id() ?? 1;
 $meusPedidosHoje = Pedido::where('usuario_id', $userId)
 ->whereDate('created_at', today())
 ->whereDoesntHave('delivery')
 ->count();
 $minhaVendaHoje = Pedido::where('usuario_id', $userId)
 ->whereDate('created_at', today())
 ->whereDoesntHave('delivery')
 ->sum('total');
 $mesasDisponiveis = Mesa::count();
 $mesasOcupadas = Mesa::whereHas('pedidos', function($query) { 
 $query->where('status', 'aberto'); 
 })->count();
 $pedidosAbertos = Pedido::whereIn('status', ['aberto', 'entregue'])->count();
 $ultimosPedidos = Pedido::with(['mesa', 'itens.produto'])
 ->where('usuario_id', $userId)
 ->whereDoesntHave('delivery')
 ->latest()
 ->limit(5)
 ->get();
 $mesasOcupadasInfo = Mesa::with(['pedidos' => function($query) { 
 $query->whereIn('status', ['aberto', 'entregue'])->latest(); 
 }])->whereHas('pedidos', function($query) { 
 $query->whereIn('status', ['aberto', 'entregue']); 
 })->get();
 $mesasDisponiveisInfo = Mesa::whereDoesntHave('pedidos', function($query) { 
 $query->whereIn('status', ['aberto', 'entregue']); 
 })->limit(6)->get();
 $user = (object) ['nome' => 'Garçom Demo'];
 $caixaAberto = $this->verificarCaixaAberto();
 return view('garcom.dashboard', compact('meusPedidosHoje', 'minhaVendaHoje', 'mesasDisponiveis', 'mesasOcupadas', 'pedidosAbertos', 'ultimosPedidos', 'mesasOcupadasInfo', 'mesasDisponiveisInfo', 'user', 'caixaAberto'));
 }
 public function cardapio(Request $request)
 {
 $categorias = Categoria::with(['produtos' => function($query) { $query->where('ativo', true)->orderBy('nome'); }])->get();
 $produtosBusca = [];
 if ($request->has('busca') && !empty($request->busca)) {
 $produtosBusca = Produto::where('nome', 'like', '%' . $request->busca . '%')->where('ativo', true)->with('categoria')->get();
 }
 return view('garcom.cardapio', compact('categorias', 'produtosBusca'));
 }
 public function mesas()
 {
 $mesas = Mesa::with(['pedidos' => function($query) { $query->whereIn('status', ['aberto', 'entregue'])->with('usuario'); }])->orderBy('identificador')->get();
 return view('garcom.mesas', compact('mesas'));
 }
 public function buscarProdutos(Request $request)
 {
 $term = $request->get('q', '');
 if (empty($term)) {
 return response()->json([]);
 }
 $produtos = Produto::where('ativo', true)
 ->where(function($query) use ($term) {
 $query->where('nome', 'like', '%' . $term . '%')
 ->orWhere('codigo', 'like', '%' . $term . '%');
 })
 ->with('categoria')
 ->limit(20)
 ->get()
 ->map(function($produto) {
 return [
 'id' => $produto->id,
 'nome' => $produto->nome,
 'codigo' => $produto->codigo,
 'preco' => $produto->preco,
 'categoria' => $produto->categoria->nome,
 'tipo_preparo' => $produto->tipo_preparo,
 'descricao' => $produto->descricao
 ];
 });
 return response()->json($produtos);
 }
 public function criarPedidoRapido()
 {
 $mesas = Mesa::with(['pedidos' => function($query) { 
 $query->whereIn('status', ['aberto', 'entregue'])->with('usuario'); 
 }])->orderBy('identificador')->get();
 $mesas->each(function($mesa) {
 $mesa->ocupada = $mesa->pedidos->whereIn('status', ['aberto', 'entregue'])->count() > 0;
 $mesa->pedido_atual = $mesa->pedidos->whereIn('status', ['aberto', 'entregue'])->first();
 });
 $categorias = Categoria::with(['produtos' => function($query) { $query->where('ativo', true)->orderBy('nome'); }])->get();
 $caixaAberto = $this->verificarCaixaAberto();
 return view('garcom.pedido-rapido', compact('mesas', 'categorias', 'caixaAberto'));
 }
 public function meusPedidos(Request $request)
 {
 $userId = Auth::id() ?? 1;
 $query = Pedido::with(['mesa', 'itens.produto'])
 ->where('usuario_id', $userId)
 ->whereDoesntHave('delivery');
 if ($request->has('data') && !empty($request->data)) {
 $query->whereDate('created_at', $request->data);
 } else {
 $query->whereDate('created_at', today());
 }
 if ($request->has('status') && !empty($request->status)) {
 $query->where('status', $request->status);
 }
 $pedidos = $query->latest()->paginate(10);
 $estatisticas = [
 'total_pedidos' => Pedido::where('usuario_id', $userId)
 ->whereDate('created_at', today())
 ->whereDoesntHave('delivery')
 ->count(),
 'valor_total' => Pedido::where('usuario_id', $userId)
 ->whereDate('created_at', today())
 ->whereDoesntHave('delivery')
 ->sum('total'),
 'pedidos_abertos' => Pedido::where('usuario_id', $userId)
 ->where('status', 'aberto')
 ->whereDoesntHave('delivery')
 ->count(),
 'pedidos_finalizados' => Pedido::where('usuario_id', $userId)
 ->whereDate('created_at', today())
 ->where('status', 'finalizado')
 ->whereDoesntHave('delivery')
 ->count()
 ];
 return view('garcom.meus-pedidos', compact('pedidos', 'estatisticas'));
 }
 public function storePedidoRapido(Request $request)
 {
 Log::info('=== INÍCIO CRIAÇÃO PEDIDO VIA API ===');
 Log::info('Request data:', $request->all());
 Log::info('Content-Type:', [$request->header('Content-Type')]);
 Log::info('User ID:', [Auth::id()]);
 $request->validate([
 'mesa_id' => 'required|exists:mesas,id',
 'itens' => 'required|array|min:1',
 'itens.*.produto_id' => 'required|exists:produtos,id',
 'itens.*.quantidade' => 'required|integer|min:1',
 ]);
 try {
 $caixaAberto = $this->verificarCaixaAberto();
 if (!$caixaAberto) {
 return $this->retornarErroCaixaFechado($request, 'criar pedidos');
 }
 $pedidosAbertos = Pedido::where('mesa_id', $request->mesa_id)
 ->where('status', 'aberto')
 ->count();
 if ($pedidosAbertos > 0) {
 Log::warning('Tentativa de criar pedido em mesa ocupada', [
 'mesa_id' => $request->mesa_id,
 'pedidos_abertos' => $pedidosAbertos
 ]);
 if ($request->expectsJson() || $request->ajax()) {
 return response()->json([
 'success' => false,
 'message' => 'Esta mesa já possui um pedido em andamento. Finalize o pedido atual ou adicione itens ao pedido existente.'
 ], 422);
 }
 return back()->with('error', 'Esta mesa já possui um pedido em andamento.');
 }
 $dadosAPI = [
 'mesa_id' => $request->mesa_id,
 'observacoes' => $request->observacoes ?? '',
 'itens' => collect($request->itens)->map(function($item) {
 $produto = Produto::find($item['produto_id']);
 return [
 'produto_id' => (int) $item['produto_id'],
 'quantidade' => (int) $item['quantidade'],
 'preco_unitario' => (float) $produto->preco,
 'observacoes' => $item['observacoes'] ?? null
 ];
 })->toArray()
 ];
 $pedidoController = new \App\Http\Controllers\PedidoController();
 $apiRequest = new Request($dadosAPI);
 $apiRequest->setMethod('POST');
 app()->instance('request', $apiRequest);
 \Auth::setUser(\App\Models\Usuario::find(Auth::id() ?? 1));
 $response = $pedidoController->syncOffline($apiRequest);
 $responseData = $response->getData(true);
 if ($response->getStatusCode() === 201) {
 Log::info('Pedido criado com sucesso via API', [
 'pedido_id' => $responseData['pedido']['id'],
 'total' => $responseData['pedido']['total']
 ]);
 if ($request->expectsJson() || $request->ajax()) {
 return response()->json([
 'success' => true,
 'message' => 'Pedido criado com sucesso!',
 'pedido' => $responseData['pedido']
 ]);
 }
 return redirect()->route('garcom.dashboard')
 ->with('success', 'Pedido criado com sucesso!');
 } else {
 Log::error('Erro ao criar pedido via API', $responseData);
 if ($request->expectsJson() || $request->ajax()) {
 return $response;
 }
 return back()->with('error', $responseData['message'] ?? 'Erro ao criar pedido.');
 }
 } catch (\Exception $e) {
 Log::error('Erro ao processar pedido via API', [
 'message' => $e->getMessage(),
 'file' => $e->getFile(),
 'line' => $e->getLine()
 ]);
 if ($request->expectsJson() || $request->ajax()) {
 return response()->json([
 'success' => false,
 'message' => 'Erro interno do servidor. Tente novamente.'
 ], 500);
 }
 return back()->with('error', 'Erro ao criar pedido. Tente novamente.');
 }
 }
 public function finalizarMesa(Request $request, $mesaId)
 {
 try {
 DB::beginTransaction();
 $pedidos = Pedido::where('mesa_id', $mesaId)->where('status', 'aberto')->get();
 foreach ($pedidos as $pedido) {
 $pedido->update(['status' => 'finalizado']);
 }
 DB::commit();
 return response()->json(['success' => true, 'message' => 'Mesa finalizada com sucesso!']);
 } catch (\Exception $e) {
 DB::rollback();
 return response()->json(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
 }    }
 public function verPedido(Pedido $pedido)
 {
 $pedido->load(['mesa', 'usuario', 'itens.produto']);
 return view('garcom.pedido', compact('pedido'));
 }
 public function atualizarStatusPedido(Request $request, Pedido $pedido)
 {
 try {
 $status = $request->input('status');
 
 // Status válidos do sistema
 $statusValidos = ['aberto', 'em_preparo', 'pronto', 'entregue', 'finalizado', 'cancelado'];
 
 if (!in_array($status, $statusValidos)) {
 if ($request->expectsJson()) {
 return response()->json(['success' => false, 'message' => 'Status inválido']);
 }
 return redirect()->back()->with('error', 'Status inválido');
 }
 
 $pedido->update(['status' => $status]);
 
 // Sincroniza status da delivery se existir
 if ($pedido->delivery) {
 $deliveryStatus = $pedido->delivery->status; // mantém o atual por padrão
 
 switch($status) {
 case 'em_preparo':
 $deliveryStatus = 'preparando';
 break;
 case 'pronto':
 $deliveryStatus = 'pronto';
 break;
 case 'entregue':
 $deliveryStatus = 'entregue';
 break;
 case 'finalizado':
 $deliveryStatus = 'finalizado';
 break;
 case 'cancelado':
 $deliveryStatus = 'cancelado';
 break;
 }
 
 $pedido->delivery->update(['status' => $deliveryStatus]);
 }
 
 if ($request->expectsJson()) {
 return response()->json([
 'success' => true, 
 'message' => "Pedido atualizado para {$status} com sucesso!"
 ]);
 }
 
 return redirect()->back()->with('success', "Pedido atualizado para {$status} com sucesso!");
 
 } catch (\Exception $e) {
 if ($request->expectsJson()) {
 return response()->json([
 'success' => false, 
 'message' => 'Erro ao atualizar status: ' . $e->getMessage()
 ]);
 }
 return redirect()->back()->with('error', 'Erro ao atualizar status: ' . $e->getMessage());
 }
 }
 public function adicionarItensPedido(Request $request)
 {
 $mesaId = $request->get('mesa');
 $pedidoId = $request->get('pedido');
 $mesa = Mesa::findOrFail($mesaId);
 $pedido = Pedido::findOrFail($pedidoId);
 if ($pedido->status !== 'aberto') {
 return redirect()->route('garcom.mesas')->with('error', 'Este pedido já foi finalizado.');
 }
 $categorias = Categoria::with(['produtos' => function($query) { 
 $query->where('ativo', true)->orderBy('nome');
 }])->get();
 $mesas = Mesa::orderBy('identificador')->get();
 return view('garcom.adicionar-itens', compact('categorias', 'mesas', 'mesa', 'pedido'));
 }
 public function storeItensPedido(Request $request)
 {
 try {
 Log::info('🛒 Recebendo adição de itens ao pedido:', $request->all());
 $request->validate([
 'pedido_id' => 'required|exists:pedidos,id',
 'itens' => 'required|array',
 'itens.*.produto_id' => 'required|exists:produtos,id',
 'itens.*.quantidade' => 'required|integer|min:1|max:10'
 ]);
 $pedido = Pedido::findOrFail($request->pedido_id);
 $caixaAberto = $this->verificarCaixaAberto();
 if (!$caixaAberto) {
 return $this->retornarErroCaixaFechado($request, 'adicionar itens ao pedido');
 }
 if ($pedido->status !== 'aberto') {
 return response()->json([
 'success' => false,
 'message' => 'Este pedido já foi finalizado e não pode ser modificado.'
 ], 422);
 }
 DB::beginTransaction();
 $totalAdicional = 0;
 foreach ($request->itens as $itemData) {
 $produto = Produto::findOrFail($itemData['produto_id']);
 $itemExistente = ItemPedido::where('pedido_id', $pedido->id)
 ->where('produto_id', $produto->id)
 ->first();                if ($itemExistente) {
 $itemExistente->quantidade += $itemData['quantidade'];
 $itemExistente->subtotal = $itemExistente->preco_unitario * $itemExistente->quantidade;
 if (!empty($itemData['observacoes'])) {
 $observacoesExistentes = $itemExistente->observacoes ?: '';
 $novasObservacoes = $itemData['observacoes'];
 $itemExistente->observacoes = $observacoesExistentes 
 ? $observacoesExistentes . ' | ' . $novasObservacoes 
 : $novasObservacoes;
 }
 $itemExistente->save();
 Log::info("📝 Item existente atualizado:", [
 'produto' => $produto->nome,
 'quantidade_anterior' => $itemExistente->quantidade - $itemData['quantidade'],
 'quantidade_adicionada' => $itemData['quantidade'],
 'quantidade_nova' => $itemExistente->quantidade,
 'subtotal_novo' => $itemExistente->subtotal,
 'observacoes' => $itemExistente->observacoes
 ]);
 } else {
 $subtotal = $produto->preco * $itemData['quantidade'];
 ItemPedido::create([
 'pedido_id' => $pedido->id,
 'produto_id' => $produto->id,
 'quantidade' => $itemData['quantidade'],
 'preco_unitario' => $produto->preco,
 'subtotal' => $subtotal,
 'observacoes' => $itemData['observacoes'] ?? ''
 ]);
 Log::info("🆕 Novo item adicionado:", [
 'produto' => $produto->nome,
 'quantidade' => $itemData['quantidade'],
 'preco' => $produto->preco,
 'subtotal' => $subtotal,
 'observacoes' => $itemData['observacoes'] ?? ''
 ]);
 }
 $totalAdicional += $produto->preco * $itemData['quantidade'];
 }
 $pedido->total += $totalAdicional;
 $pedido->save();
 Log::info("💰 Total do pedido atualizado:", [
 'total_anterior' => $pedido->total - $totalAdicional,
 'total_adicional' => $totalAdicional,
 'total_novo' => $pedido->total
 ]);
 DB::commit();
 return response()->json([
 'success' => true,
 'message' => 'Itens adicionados com sucesso ao pedido!',
 'pedido_id' => $pedido->id,
 'total_novo' => $pedido->total,
 'total_adicional' => $totalAdicional
 ]);
 } catch (\Exception $e) {
 DB::rollback();
 Log::error('❌ Erro ao adicionar itens ao pedido:', [
 'message' => $e->getMessage(),
 'trace' => $e->getTraceAsString()
 ]);
 return response()->json([
 'success' => false,
 'message' => 'Erro ao adicionar itens: ' . $e->getMessage()
 ], 500);
 }
 }    public function dashboardData()
 {
 $userId = Auth::id() ?? 1;
 $meusPedidosHoje = Pedido::where('usuario_id', $userId)
 ->whereDate('created_at', today())
 ->whereDoesntHave('delivery')
 ->count();
 $minhaVendaHoje = Pedido::where('usuario_id', $userId)
 ->whereDate('created_at', today())
 ->whereDoesntHave('delivery')
 ->sum('total');
 $mesasDisponiveis = Mesa::count();
 $mesasOcupadas = Mesa::whereHas('pedidos', function($query) { 
 $query->where('status', 'aberto'); 
 })->count();
 $mesasOcupadasInfo = Mesa::with(['pedidos' => function($query) { 
 $query->where('status', 'aberto')->latest(); 
 }])->whereHas('pedidos', function($query) { 
 $query->where('status', 'aberto');        })->get()->map(function($mesa) {
 $pedidoAtual = $mesa->pedidos->first();
 return [
 'id' => $mesa->id,
 'numero' => $mesa->numero,
 'identificador' => $mesa->identificador,
 'pedido_id' => $pedidoAtual ? $pedidoAtual->id : null,
 'valor_total' => $pedidoAtual ? number_format($pedidoAtual->total, 2, ',', '.') : 'R$ 0,00'
 ];
 });
 $mesasDisponiveisInfo = Mesa::whereDoesntHave('pedidos', function($query) { 
 $query->where('status', 'aberto');
 })->limit(6)->get()->map(function($mesa) {
 return [
 'id' => $mesa->id,
 'numero' => $mesa->numero,
 'identificador' => $mesa->identificador
 ];
 });
 $ultimosPedidos = Pedido::with(['mesa', 'itens.produto'])
 ->where('usuario_id', $userId)
 ->whereDate('created_at', today())
 ->whereDoesntHave('delivery')
 ->latest()
 ->limit(5)
 ->get()
 ->map(function($pedido) {
 $mesaInfo = 'Balcão';
 if ($pedido->mesa) {
 $mesaInfo = $pedido->mesa->identificador ?? 'Mesa ' . $pedido->mesa->numero;
 }
 return [
 'id' => $pedido->id,
 'mesa_identificador' => $mesaInfo,
 'itens_count' => $pedido->itens->count(),
 'primeiro_item' => $pedido->itens->first() ? $pedido->itens->first()->produto->nome : '',
 'valor_total' => number_format($pedido->total, 2, ',', '.'),
 'status' => $pedido->status,
 'horario' => $pedido->created_at->format('H:i')
 ];
 });
 $data = [
 'meusPedidosHoje' => $meusPedidosHoje,
 'minhaVendaHoje' => number_format($minhaVendaHoje, 2, ',', '.'),
 'mesasDisponiveis' => $mesasDisponiveis,
 'mesasOcupadas' => $mesasOcupadas,
 'mesasOcupadasInfo' => $mesasOcupadasInfo,
 'mesasDisponiveisInfo' => $mesasDisponiveisInfo,
 'ultimosPedidos' => $ultimosPedidos,
 'timestamp' => now()->format('H:i:s')
 ];
 return response()->json($data);
 }
 public function trocarMesa(Request $request)
 {
 try {
 $request->validate([
 'pedido_id' => 'required|exists:pedidos,id',
 'nova_mesa_id' => 'required|exists:mesas,id',
 'motivo' => 'nullable|string|max:500'
 ]);
 $pedido = Pedido::findOrFail($request->pedido_id);
 $novaMesa = Mesa::findOrFail($request->nova_mesa_id);
 $mesaAnterior = $pedido->mesa;
 $mesaOcupada = Mesa::whereHas('pedidos', function($query) {
 $query->where('status', 'aberto');
 })->where('id', $request->nova_mesa_id)->exists();
 if ($mesaOcupada) {
 return response()->json([
 'success' => false,
 'message' => 'A mesa selecionada já está ocupada.'
 ]);
 }
 $userId = Auth::id() ?? 1;
 if ($pedido->usuario_id != $userId) {
 return response()->json([
 'success' => false,
 'message' => 'Você só pode alterar seus próprios pedidos.'
 ]);
 }
 if ($pedido->status != 'aberto') {
 return response()->json([
 'success' => false,
 'message' => 'Só é possível trocar mesa de pedidos em andamento.'
 ]);
 }
 $motivoTexto = $request->motivo ? " - Motivo: " . $request->motivo : "";
 $nomeAnterior = $mesaAnterior->identificador ?: 'Mesa ' . $mesaAnterior->numero;
 $nomeNovo = $novaMesa->identificador ?: 'Mesa ' . $novaMesa->numero;
 $observacaoTroca = "Mesa alterada de {$nomeAnterior} para {$nomeNovo}{$motivoTexto}";
 $observacaoAtual = $pedido->observacoes ? $pedido->observacoes . "\n" : "";
 $pedido->update([
 'mesa_id' => $request->nova_mesa_id,
 'observacoes' => $observacaoAtual . $observacaoTroca
 ]);
 return response()->json([
 'success' => true,
 'message' => "Mesa alterada com sucesso para {$nomeNovo}",
 'nova_mesa' => [
 'id' => $novaMesa->id,
 'nome' => $nomeNovo
 ]
 ]);
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao trocar mesa: ' . $e->getMessage()
 ]);
 }
 }
 public function infoParaPagamento(Mesa $mesa)
 {
 try {
 $pedidos = $mesa->pedidos()
 ->where('status', 'finalizado')
 ->with('itens.produto')
 ->whereDoesntHave('pagamentos', function($query) {
 $query->where('status', 'confirmado');
 })
 ->get();
 if ($pedidos->isEmpty()) {
 $pedidos = $mesa->pedidos()->where('status', 'aberto')->with('itens.produto')->get();
 if ($pedidos->isEmpty()) {
 return response()->json([
 'success' => false,
 'message' => 'Não há pedidos nesta mesa para finalizar.'
 ]);
 }
 }
 $total = $pedidos->sum('total');
 return response()->json([
 'success' => true,
 'mesa' => [
 'id' => $mesa->id,
 'identificador' => $mesa->identificador,
 'numero' => $mesa->numero
 ],
 'pedidos' => $pedidos->map(function($pedido) {
 return [
 'id' => $pedido->id,
 'total' => $pedido->total,
 'itens_count' => $pedido->itens->count(),
 'created_at' => $pedido->created_at->format('H:i'),
 'status' => $pedido->status
 ];
 }),
 'total' => $total
 ]);
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao buscar informações da mesa: ' . $e->getMessage()
 ]);
 }
 }
 public function processarPagamentoMesa(Request $request, Mesa $mesa)
 {
 try {
 $request->validate([
 'forma_pagamento' => 'required|in:dinheiro,cartao_credito,cartao_debito,pix,vale_refeicao',
 'valor_pagamento' => 'required|numeric|min:0',
 'valor_recebido' => 'nullable|numeric|min:0',
 'observacoes' => 'nullable|string|max:500'
 ]);
 $pedidosAbertos = $mesa->pedidos()->where('status', 'aberto')->get();
 $pedidosFinalizados = $mesa->pedidos()
 ->where('status', 'finalizado')
 ->whereDoesntHave('pagamentos', function($query) {
 $query->where('status', 'confirmado');
 })
 ->get();
 $pedidos = $pedidosAbertos->merge($pedidosFinalizados);
 if ($pedidos->isEmpty()) {
 return response()->json([
 'success' => false,
 'message' => 'Não há pedidos nesta mesa para processar pagamento.'
 ]);
 }
 $valorTotal = $pedidos->sum('total');
 $valorPagamento = $request->valor_pagamento;
 if (abs($valorTotal - $valorPagamento) > 0.01) {
 return response()->json([
 'success' => false,
 'message' => 'Valor do pagamento não confere com o total dos pedidos.'
 ]);
 }
 DB::beginTransaction();
 $troco = 0;
 $valorRecebido = $valorPagamento;
 if ($request->forma_pagamento === 'dinheiro') {
 $valorRecebido = $request->valor_recebido ?? $valorPagamento;
 $troco = $valorRecebido - $valorPagamento;
 if ($troco < 0) {
 DB::rollBack();
 return response()->json([
 'success' => false,
 'message' => 'Valor recebido insuficiente.'
 ]);
 }
 }
 foreach ($pedidos as $pedido) {
 $proporcaoPedido = $pedido->total / $valorTotal;
 $valorPedido = $valorPagamento * $proporcaoPedido;
 $valorRecebidoPedido = $valorRecebido * $proporcaoPedido;
 $trocoPedido = $troco * $proporcaoPedido;
 \App\Models\Pagamento::create([
 'pedido_id' => $pedido->id,
 'forma_pagamento' => $request->forma_pagamento,
 'valor' => $valorPedido,
 'valor_recebido' => $valorRecebidoPedido,
 'troco' => $trocoPedido,
 'status' => 'confirmado',
 'observacoes' => $request->observacoes . " (Mesa finalizada)",
 'usuario_id' => Auth::id() ?? 1,
 'data_pagamento' => now()
 ]);
 $pedido->update(['status' => 'finalizado']);
 }
 $caixa = \App\Models\Caixa::where('status', 'aberto')->first();
 if ($caixa) {
 $caixa->increment('total_vendas', $valorPagamento);
 switch ($request->forma_pagamento) {
 case 'dinheiro':
 $caixa->increment('total_dinheiro', $valorPagamento);
 break;
 case 'cartao_credito':
 case 'cartao_debito':
 $caixa->increment('total_cartao', $valorPagamento);
 break;
 case 'pix':
 $caixa->increment('total_pix', $valorPagamento);
 break;
 case 'vale_refeicao':
 $caixa->increment('total_vale', $valorPagamento);
 break;
 }
 }
 DB::commit();
 return response()->json([
 'success' => true,
 'message' => 'Mesa finalizada e pagamento processado com sucesso!',
 'pagamento' => [
 'valor' => $valorPagamento,
 'forma' => $request->forma_pagamento,
 'troco' => $troco,
 'pedidos_processados' => $pedidos->count()
 ]
 ]);
 } catch (\Illuminate\Validation\ValidationException $e) {
 return response()->json([
 'success' => false,
 'message' => 'Dados inválidos: ' . implode(', ', $e->validator->errors()->all())
 ]);
 } catch (\Exception $e) {
 DB::rollback();
 return response()->json([
 'success' => false,
 'message' => 'Erro ao processar pagamento: ' . $e->getMessage()
 ]);
 }
 }
 public function processarPagamento(Request $request, Pedido $pedido)
 {
 try {
 $request->validate([
 'forma_pagamento' => 'required|in:dinheiro,cartao_credito,cartao_debito,pix,vale_refeicao',
 'valor' => 'required|numeric|min:0',
 'valor_recebido' => 'nullable|numeric|min:0',
 'observacoes' => 'nullable|string|max:500'
 ]);
 if ($pedido->status !== 'finalizado') {
 return response()->json([
 'success' => false,
 'message' => 'Apenas pedidos finalizados podem ser pagos.'
 ]);
 }
 $totalJaPago = $pedido->pagamentos()->where('status', 'confirmado')->sum('valor');
 if ($totalJaPago >= $pedido->total) {
 return response()->json([
 'success' => false,
 'message' => 'Este pedido já foi pago.'
 ]);
 }
 $valorPagamento = $request->valor;
 $saldoRestante = $pedido->total - $totalJaPago;
 if ($valorPagamento > $saldoRestante + 0.01) {
 return response()->json([
 'success' => false,
 'message' => 'Valor do pagamento é maior que o saldo restante do pedido.'
 ]);
 }
 DB::beginTransaction();
 $troco = 0;
 $valorRecebido = $valorPagamento;
 if ($request->forma_pagamento === 'dinheiro') {
 $valorRecebido = $request->valor_recebido ?? $valorPagamento;
 $troco = $valorRecebido - $valorPagamento;
 if ($troco < 0) {
 return response()->json([
 'success' => false,
 'message' => 'Valor recebido insuficiente.'
 ]);
 }
 }
 $pagamento = \App\Models\Pagamento::create([
 'pedido_id' => $pedido->id,
 'forma_pagamento' => $request->forma_pagamento,
 'valor' => $valorPagamento,
 'valor_recebido' => $valorRecebido,
 'troco' => $troco,
 'status' => 'confirmado',
 'observacoes' => $request->observacoes,
 'usuario_id' => Auth::id() ?? 1,
 'data_pagamento' => now()
 ]);
 $caixa = \App\Models\Caixa::where('status', 'aberto')->first();
 if ($caixa) {
 $caixa->increment('total_vendas', $valorPagamento);
 switch ($request->forma_pagamento) {
 case 'dinheiro':
 $caixa->increment('total_dinheiro', $valorPagamento);
 break;
 case 'cartao_credito':
 case 'cartao_debito':
 $caixa->increment('total_cartao', $valorPagamento);
 break;
 case 'pix':
 $caixa->increment('total_pix', $valorPagamento);
 break;
 case 'vale_refeicao':
 $caixa->increment('total_vale', $valorPagamento);
 break;
 }
 }
 DB::commit();
 return response()->json([
 'success' => true,
 'message' => 'Pagamento processado com sucesso!',
 'pagamento' => [
 'id' => $pagamento->id,
 'valor' => $valorPagamento,
 'forma' => $request->forma_pagamento,
 'troco' => $troco
 ]
 ]);
 } catch (\Illuminate\Validation\ValidationException $e) {
 return response()->json([
 'success' => false,
 'message' => 'Dados inválidos: ' . implode(', ', $e->validator->errors()->all())
 ]);
 } catch (\Exception $e) {
 DB::rollback();
 return response()->json([
 'success' => false,
 'message' => 'Erro ao processar pagamento: ' . $e->getMessage()
 ]);
 }
 }
}