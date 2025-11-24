<?php
namespace App\Http\Controllers;
use App\Models\Pedido;
use App\Models\ItemExclusionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class PedidoController extends Controller
{
 public function index()
 {
 if (auth('sanctum')->check() && auth('sanctum')->user() instanceof \App\Models\Cliente) {
 $cliente = auth('sanctum')->user();
 $pedidos = Pedido::whereHas('delivery', function($query) use ($cliente) {
 $query->where('cliente_id', $cliente->id);
 })
 ->with(['itens.produto', 'delivery'])
 ->orderBy('created_at', 'desc')
 ->get();
 return response()->json($pedidos);
 }
 $pedidos = Pedido::with(['mesa', 'usuario', 'itens.produto', 'delivery', 'pagamentos', 'entregador'])->orderBy('created_at', 'desc')->get();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($pedidos);
 }
 return view('pedidos.index', compact('pedidos'));
 }

 public function create()
 {
 $mesas = \App\Models\Mesa::orderBy('identificador')->get();
 $usuarios = \App\Models\Usuario::orderBy('nome')->get();
 $categorias = \App\Models\Categoria::with('produtos')->orderBy('nome')->get();
 $combos = \App\Models\Combo::where('ativo', true)->with('produtos')->orderBy('nome')->get();
 return view('pedidos.create', compact('mesas', 'usuarios', 'categorias', 'combos'));
 }
 public function store(Request $request)
 {        $request->validate([
 'mesa_id' => 'required|exists:mesas,id',
 'usuario_id' => 'required|exists:usuarios,id',
 'status' => 'required|string|in:pendente,em_preparo,pronto,entregue,finalizado,cancelado'
 ], [
 'mesa_id.required' => 'A mesa é obrigatória.',
 'mesa_id.exists' => 'A mesa selecionada não existe.',
 'usuario_id.required' => 'O usuário (garçom) é obrigatório.',
 'usuario_id.exists' => 'O usuário selecionado não existe.',
 'status.required' => 'O status é obrigatório.',
 'status.in' => 'O status deve ser: pendente, em_preparo, pronto, entregue, finalizado ou cancelado.',
 ]);
 $pedido = Pedido::create([
 'mesa_id' => $request->mesa_id,
 'usuario_id' => $request->usuario_id,
 'status' => $request->status,
 'total' => 0,
 'tenant_code' => auth('admin')->check() ? auth('admin')->user()->tenant_code : (auth()->check() ? auth()->user()->tenant_code : null)
 ]);
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($pedido, 201);
 }
 return redirect()->route('pedidos.show', $pedido->id)->with('success', 'Pedido criado com sucesso!');
 }

 public function show(Pedido $pedido)
 {
 $pedido->load(['mesa', 'usuario', 'itens.produto', 'delivery.entregador', 'entregador']);
 $entregadores = [];
 if (!$pedido->mesa && !$pedido->entregador_id) {
 $entregadores = \App\Models\Entregador::where('status', 'ativo')
 ->where('disponivel', 1)
 ->orderBy('avaliacao_media', 'desc')
 ->get();
 }
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($pedido);
 }
 return view('pedidos.detalhes', compact('pedido', 'entregadores'));
 }

 public function edit(Pedido $pedido)
 {
 $mesas = \App\Models\Mesa::orderBy('identificador')->get();
 $usuarios = \App\Models\Usuario::orderBy('nome')->get();
 $categorias = \App\Models\Categoria::with('produtos')->orderBy('nome')->get();
 $currentUser = auth()->user();
 return view('pedidos.edit', compact('pedido', 'mesas', 'usuarios', 'categorias', 'currentUser'));
 }
 public function update(Request $request, Pedido $pedido)
 {        $request->validate([
 'mesa_id' => 'sometimes|exists:mesas,id',
 'usuario_id' => 'sometimes|exists:usuarios,id',
 'total' => 'sometimes|numeric|min:0',
 'status' => 'sometimes|string|in:pendente,em_preparo,pronto,entregue,finalizado,cancelado'
 ], [
 'mesa_id.exists' => 'A mesa selecionada não existe.',
 'usuario_id.exists' => 'O usuário selecionado não existe.',
 'total.numeric' => 'O total deve ser um número.',
 'total.min' => 'O total não pode ser negativo.',
 'status.in' => 'O status deve ser: pendente, em_preparo, pronto, entregue, finalizado ou cancelado.',
 ]);
 $pedido->update($request->all());
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($pedido);
 }
 return redirect()->route('pedidos.show', $pedido->id)->with('success', 'Pedido atualizado com sucesso!');
 }

 public function destroy(Pedido $pedido)
 {
 $pedido->delete();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json(['message' => 'Pedido excluído com sucesso!']);
 }
 return redirect()->route('pedidos.index')->with('success', 'Pedido excluído com sucesso!');
 }

 public function removeItem(Request $request, Pedido $pedido, $itemId)
 {
 $user = auth()->user();
 if (!$user || !in_array($user->role, ['admin', 'gerente'])) {
 if ($request->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Acesso negado. Apenas administradores e gerentes podem excluir itens.'
 ], 403);
 }
 return redirect()->back()->with('error', 'Acesso negado. Apenas administradores e gerentes podem excluir itens.');
 }
 $item = \App\Models\ItemPedido::where('id', $itemId)
 ->where('pedido_id', $pedido->id)
 ->first();
 if (!$item) {
 if ($request->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Item não encontrado.'
 ], 404);
 }
 return redirect()->back()->with('error', 'Item não encontrado.');
 }
 if (in_array($pedido->status, ['entregue', 'cancelado'])) {
 if ($request->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Não é possível modificar pedido com status: ' . $pedido->status
 ], 400);
 }
 return redirect()->back()->with('error', 'Não é possível modificar pedido com status: ' . $pedido->status);
 }
 if ($pedido->itens()->count() <= 1) {
 if ($request->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Não é possível excluir o último item do pedido. Cancele o pedido inteiro se necessário.'
 ], 400);
 }
 return redirect()->back()->with('error', 'Não é possível excluir o último item do pedido.');
 }
 try {
 $request->validate([
 'motivo' => 'nullable|string|max:500',
 'quantidade' => 'nullable|integer|min:1'
 ]);
 $quantidadeRemover = $request->quantidade ?? $item->quantidade;
 $isExclusaoCompleta = $quantidadeRemover >= $item->quantidade;
 if ($quantidadeRemover > $item->quantidade) {
 if ($request->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Quantidade a remover não pode ser maior que a quantidade do item.'
 ], 400);
 }
 return redirect()->back()->with('error', 'Quantidade inválida.');
 }
 $logData = clone $item;
 $logData->quantidade = $quantidadeRemover;
 ItemExclusionLog::logExclusion($logData, $request->motivo);
 if ($isExclusaoCompleta) {
 $item->delete();
 $message = 'Item removido completamente do pedido!';
 } else {
 $novaQuantidade = $item->quantidade - $quantidadeRemover;
 $item->update(['quantidade' => $novaQuantidade]);
 $message = "Quantidade reduzida! Removido: {$quantidadeRemover}, Restante: {$novaQuantidade}";
 }
 $novoTotal = $pedido->itens()->sum(DB::raw('quantidade * preco_unitario'));
 $pedido->update(['total' => $novoTotal]);
 if ($request->expectsJson()) {
 return response()->json([
 'success' => true,
 'message' => $message,
 'novo_total' => $novoTotal,
 'exclusao_completa' => $isExclusaoCompleta,
 'nova_quantidade' => $isExclusaoCompleta ? 0 : $item->quantidade
 ]);
 }
 return redirect()->back()->with('success', $message);
 } catch (\Exception $e) {
 Log::error('Erro ao remover/reduzir item do pedido', [
 'pedido_id' => $pedido->id,
 'item_id' => $itemId,
 'usuario_id' => auth()->id(),
 'quantidade_solicitada' => $request->quantidade,
 'error' => $e->getMessage()
 ]);
 if ($request->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao processar solicitação: ' . $e->getMessage()
 ], 500);
 }
 return redirect()->back()->with('error', 'Erro ao processar solicitação.');
 }
 }
 public function syncOffline(Request $request)
 {
 try {
 $isClienteApp = auth('sanctum')->check() && auth('sanctum')->user() instanceof \App\Models\Cliente;
 if ($isClienteApp || ($request->has('tipo_pedido') && $request->tipo_pedido === 'delivery')) {
 $validated = $request->validate([
 'tipo_pedido' => 'sometimes|string',
 'cliente_id' => 'nullable|exists:clientes,id',
 'cliente_nome' => 'required_without:cliente_id|string|max:255',
 'cliente_telefone' => 'required_without:cliente_id|string|max:20',
 'cliente_endereco' => 'required|filled|string|min:5|max:255',
 'cliente_bairro' => 'required|filled|string|min:0|max:100',
 'cliente_cidade' => 'sometimes|string|max:100',
 'cliente_cep' => 'sometimes|string|max:10',
 'endereco_numero' => 'sometimes|string|max:20',
 'taxa_entrega' => 'sometimes|numeric|min:0',
 'itens' => 'required|array|min:1',
 'itens.*.tipo_item' => 'nullable|string|in:produto,combo',
 'itens.*.produto_id' => 'nullable|exists:produtos,id',
 'itens.*.combo_id' => 'nullable|exists:combos,id',
 'itens.*.quantidade' => 'required|integer|min:1',
 'itens.*.preco_unitario' => 'required|numeric|min:0',
 'itens.*.observacoes' => 'nullable|string',
 'observacoes' => 'nullable|string'
 ], [
 'cliente_endereco.required' => 'O endereço é obrigatório para delivery',
 'cliente_endereco.filled' => 'O endereço não pode estar vazio',
 'cliente_endereco.min' => 'O endereço deve ter no mínimo 5 caracteres',
 'cliente_bairro.required' => 'O bairro é obrigatório para delivery',
 'cliente_bairro.filled' => 'O bairro não pode estar vazio',
 'cliente_bairro.min' => 'O bairro deve ter no mínimo 3 caracteres',
 'itens.required' => 'Adicione pelo menos um item ao pedido',
 'itens.min' => 'Adicione pelo menos um item ao pedido'
 ]);
 
 // Valida que cada item tem produto_id OU combo_id
 foreach ($validated['itens'] as $index => $item) {
 if (empty($item['produto_id']) && empty($item['combo_id'])) {
 return response()->json([
 'success' => false,
 'message' => 'Cada item deve ter um produto_id ou combo_id',
 'errors' => ["itens.{$index}" => ['Item inválido']]
 ], 422);
 }
 }
 
 if ($isClienteApp) {
 $cliente = auth('sanctum')->user();
 $validated['cliente_id'] = $cliente->id;
 $validated['cliente_nome'] = $validated['cliente_nome'] ?? $cliente->nome;
 $validated['cliente_telefone'] = $validated['cliente_telefone'] ?? $cliente->telefone;
 if (empty($validated['cliente_endereco']) && empty($cliente->endereco_rua)) {
 return response()->json([
 'success' => false,
 'message' => 'Por favor, cadastre seu endereço antes de fazer um pedido',
 'requires_address' => true
 ], 400);
 }
 }
 } else {
 $validated = $request->validate([
 'mesa_id' => 'required|exists:mesas,id',
 'itens' => 'required|array|min:1',
 'itens.*.tipo_item' => 'nullable|string|in:produto,combo',
 'itens.*.produto_id' => 'nullable|exists:produtos,id',
 'itens.*.combo_id' => 'nullable|exists:combos,id',
 'itens.*.quantidade' => 'required|integer|min:1',
 'itens.*.preco_unitario' => 'required|numeric|min:0',
 'itens.*.observacoes' => 'nullable|string',
 'observacoes' => 'nullable|string'
 ]);
 
 // Valida que cada item tem produto_id OU combo_id
 foreach ($validated['itens'] as $index => $item) {
 if (empty($item['produto_id']) && empty($item['combo_id'])) {
 return response()->json([
 'success' => false,
 'message' => 'Cada item deve ter um produto_id ou combo_id',
 'errors' => ["itens.{$index}" => ['Item inválido']]
 ], 422);
 }
 }
 }
 DB::beginTransaction();
 $totalPedido = 0;
 foreach ($validated['itens'] as $item) {
 $totalPedido += $item['quantidade'] * $item['preco_unitario'];
 }
 if ($isClienteApp || ($request->has('tipo_pedido') && $request->tipo_pedido === 'delivery')) {
 $usuarioId = $isClienteApp ? 1 : (auth()->id() ?? 1);
 $pedido = Pedido::create([
 'mesa_id' => null,
 'usuario_id' => $usuarioId,
 'cliente_id' => $validated['cliente_id'] ?? null,
 'status' => 'aberto',
 'total' => $totalPedido,
 'observacoes' => $validated['observacoes'] ?? null,
 'tenant_code' => auth('admin')->check() ? auth('admin')->user()->tenant_code : (auth()->check() ? auth()->user()->tenant_code : null)
 ]);
 foreach ($validated['itens'] as $item) {
 $subtotal = $item['quantidade'] * $item['preco_unitario'];
 \App\Models\ItemPedido::create([
 'pedido_id' => $pedido->id,
 'produto_id' => $item['produto_id'] ?? null,
 'combo_id' => $item['combo_id'] ?? null,
 'tipo_item' => $item['tipo_item'] ?? 'produto',
 'quantidade' => $item['quantidade'],
 'preco_unitario' => $item['preco_unitario'],
 'subtotal' => $subtotal,
 'observacoes' => $item['observacoes'] ?? null
 ]);
 }
 $delivery = \App\Models\Delivery::create([
 'pedido_id' => $pedido->id,
 'cliente_id' => $validated['cliente_id'] ?? null,
 'cliente_nome' => $validated['cliente_nome'],
 'cliente_telefone' => $validated['cliente_telefone'],
 'endereco_rua' => $validated['cliente_endereco'],
 'endereco_numero' => $validated['endereco_numero'] ?? 'S/N',
 'endereco_bairro' => $validated['cliente_bairro'],
 'endereco_cidade' => $validated['cliente_cidade'] ?? 'Cidade',
 'endereco_cep' => $validated['cliente_cep'] ?? '00000-000',
 'taxa_entrega' => $validated['taxa_entrega'] ?? 5.00,
 'tempo_estimado' => 45,
 'observacoes' => $validated['observacoes'] ?? null,
 'status' => 'pendente',
 'tenant_code' => auth('admin')->check() ? auth('admin')->user()->tenant_code : (auth()->check() ? auth()->user()->tenant_code : null)
 ]);
 DB::commit();
 
 // Prepara resposta
 $response = [
 'success' => true,
 'message' => 'Pedido de delivery criado com sucesso!',
 'pedido' => $pedido->load('itens.produto'),
 'delivery' => $delivery
 ];
 
 // Se solicitado, adiciona URL da comanda
 if ($request->has('imprimir_comanda') && $request->imprimir_comanda) {
 $this->imprimirComanda($pedido);
 $response['comanda_url'] = route('pedidos.comanda', $pedido->id);
 }
 
 return response()->json($response, 201);
 } else {
 $usuarioId = auth('sanctum')->check() ? 1 : (auth()->id() ?? 1);
 $pedido = Pedido::create([
 'mesa_id' => $validated['mesa_id'],
 'usuario_id' => $usuarioId,
 'status' => 'aberto',
 'total' => $totalPedido,
 'observacoes' => $validated['observacoes'] ?? null,
 'tenant_code' => auth('admin')->check() ? auth('admin')->user()->tenant_code : (auth()->check() ? auth()->user()->tenant_code : null)
 ]);
 foreach ($validated['itens'] as $item) {
 $subtotal = $item['quantidade'] * $item['preco_unitario'];
 \App\Models\ItemPedido::create([
 'pedido_id' => $pedido->id,
 'produto_id' => $item['produto_id'] ?? null,
 'combo_id' => $item['combo_id'] ?? null,
 'tipo_item' => $item['tipo_item'] ?? 'produto',
 'quantidade' => $item['quantidade'],
 'preco_unitario' => $item['preco_unitario'],
 'subtotal' => $subtotal,
 'observacoes' => $item['observacoes'] ?? null
 ]);
 }
 $mesa = \App\Models\Mesa::find($validated['mesa_id']);
 if ($mesa && $mesa->status === 'disponivel') {
 $mesa->update(['status' => 'ocupada']);
 }
 DB::commit();
 
 // Prepara resposta
 $response = [
 'success' => true,
 'message' => 'Pedido criado com sucesso!',
 'pedido' => $pedido->load('itens.produto', 'mesa')
 ];
 
 // Se solicitado, adiciona URL da comanda
 if ($request->has('imprimir_comanda') && $request->imprimir_comanda) {
 $this->imprimirComanda($pedido);
 $response['comanda_url'] = route('pedidos.comanda', $pedido->id);
 }
 
 return response()->json($response, 201);
 }
 DB::commit();
 return response()->json([
 'success' => true,
 'message' => 'Pedido sincronizado com sucesso',
 'pedido' => $pedido->load('itens.produto')
 ], 201);
 } catch (\Illuminate\Validation\ValidationException $e) {
 DB::rollBack();
 return response()->json([
 'success' => false,
 'message' => 'Dados inválidos',
 'errors' => $e->errors()
 ], 422);
 } catch (\Exception $e) {
 DB::rollBack();
 \Log::error('Erro ao sincronizar pedido offline: ' . $e->getMessage());
 return response()->json([
 'success' => false,
 'message' => 'Erro ao sincronizar pedido',
 'error' => $e->getMessage()
 ], 500);
 }
 }
 public function detalhes(Pedido $pedido)
 {
 $pedido->load(['mesa', 'usuario', 'itens.produto.categoria', 'delivery']);
 $currentUser = auth()->user();
 return view('pedidos.detalhes', compact('pedido', 'currentUser'));
 }
 
 public function verComanda(Pedido $pedido)
 {
 $pedido->load(['itens.produto.categoria', 'mesa', 'usuario', 'delivery']);
 return view('pedidos.comanda', compact('pedido'));
 }
 
 public function atribuirEntregador(Request $request, Pedido $pedido)
 {
 $request->validate([
 'entregador_id' => 'required|exists:entregadores,id'
 ]);
 try {
 $entregador = \App\Models\Entregador::findOrFail($request->entregador_id);
 if ($entregador->status !== 'ativo') {
 return response()->json([
 'success' => false,
 'message' => 'Entregador não está ativo no momento'
 ], 400);
 }
 if (!$entregador->disponivel) {
 return response()->json([
 'success' => false,
 'message' => 'Entregador não está disponível no momento'
 ], 400);
 }
 if (!$pedido->delivery) {
 return response()->json([
 'success' => false,
 'message' => 'Este pedido não é delivery'
 ], 400);
 }
 if ($pedido->entregador_id) {
 return response()->json([
 'success' => false,
 'message' => 'Pedido já possui entregador atribuído'
 ], 400);
 }
 $pedido->update([
 'entregador_id' => $entregador->id
 ]);
 Log::info('Entregador atribuído ao pedido', [
 'pedido_id' => $pedido->id,
 'entregador_id' => $entregador->id,
 'entregador_nome' => $entregador->nome,
 'usuario_responsavel' => auth()->user()->nome
 ]);
 return response()->json([
 'success' => true,
 'message' => "Entregador {$entregador->nome} atribuído com sucesso",
 'entregador' => $entregador->only(['id', 'nome', 'tipo_veiculo'])
 ]);
 } catch (\Exception $e) {
 Log::error('Erro ao atribuir entregador', [
 'pedido_id' => $pedido->id,
 'entregador_id' => $request->entregador_id,
 'error' => $e->getMessage()
 ]);
 return response()->json([
 'success' => false,
 'message' => 'Erro interno do servidor'
 ], 500);
 }
 }
 public function removerEntregador(Pedido $pedido)
 {
 try {
 if (!$pedido->entregador_id) {
 return response()->json([
 'success' => false,
 'message' => 'Este pedido não possui entregador atribuído'
 ], 400);
 }
 $entregadorNome = $pedido->entregador->nome;
 $pedido->update([
 'entregador_id' => null
 ]);
 \Log::info('Entregador removido do pedido', [
 'pedido_id' => $pedido->id,
 'entregador_nome' => $entregadorNome,
 'usuario_responsavel' => auth()->user()->nome
 ]);
 return response()->json([
 'success' => true,
 'message' => 'Entregador removido com sucesso'
 ]);
 } catch (\Exception $e) {
 \Log::error('Erro ao remover entregador', [
 'pedido_id' => $pedido->id,
 'error' => $e->getMessage()
 ]);
 return response()->json([
 'success' => false,
 'message' => 'Erro interno do servidor'
 ], 500);
 }
 }
 public function cancelar(Request $request, Pedido $pedido)
 {
 try {
 if (auth('sanctum')->check() && auth('sanctum')->user() instanceof \App\Models\Cliente) {
 $cliente = auth('sanctum')->user();
 if (!$pedido->delivery || $pedido->delivery->cliente_id != $cliente->id) {
 return response()->json([
 'success' => false,
 'message' => 'Você não tem permissão para cancelar este pedido'
 ], 403);
 }
 if ($pedido->delivery && in_array($pedido->delivery->status, ['saiu_para_entrega', 'entregue', 'cancelado'])) {
 return response()->json([
 'success' => false,
 'message' => 'Não é possível cancelar pedido em andamento ou já entregue'
 ], 400);
 }
 }
 $pedido->update(['status' => 'cancelado']);
 if ($pedido->delivery) {
 $pedido->delivery->update(['status' => 'cancelado']);
 }
 \Log::info('Pedido cancelado', [
 'pedido_id' => $pedido->id,
 'cancelado_por' => auth('sanctum')->check() ? 'Cliente' : 'Sistema'
 ]);
 return response()->json([
 'success' => true,
 'message' => 'Pedido cancelado com sucesso'
 ]);
 } catch (\Exception $e) {
 \Log::error('Erro ao cancelar pedido', [
 'pedido_id' => $pedido->id,
 'error' => $e->getMessage()
 ]);
 return response()->json([
 'success' => false,
 'message' => 'Erro ao cancelar pedido'
 ], 500);
 }
 }
 
 /**
 * Imprime comanda para a cozinha
 */
 private function imprimirComanda(Pedido $pedido)
 {
 try {
 // Carrega os dados necessários
 $pedido->load(['itens.produto.categoria', 'mesa', 'delivery.cliente']);
 
 // Loga a impressão
 \Log::info('Comanda impressa para pedido #' . $pedido->id, [
 'pedido_id' => $pedido->id,
 'mesa' => $pedido->mesa ? $pedido->mesa->identificador : null,
 'tipo' => $pedido->mesa ? 'mesa' : 'delivery',
 'itens' => $pedido->itens->count()
 ]);
 
 return true;
 } catch (\Exception $e) {
 \Log::error('Erro ao imprimir comanda: ' . $e->getMessage());
 return false;
 }
 }
 
    public function pedidosAtivos()
    {
        try {
            $pedidos = Pedido::whereIn('status', ['aberto', 'pendente', 'em_preparo', 'pronto'])
                ->with([
                    'mesa',
                    'usuario',
                    'itens' => function($query) {
                        $query->with(['produto.categoria', 'combo.produtos']);
                    },
                    'delivery.cliente'
                ])
                ->orderByRaw("FIELD(status, 'aberto', 'pendente', 'em_preparo', 'pronto')")
                ->orderBy('created_at', 'asc')
                ->get(); // Adicionar atributos calculados aos itens
 $pedidos->each(function($pedido) {
 $pedido->itens->each(function($item) {
 $item->append('nome_item');
 });
 });
 
 return response()->json([
 'success' => true,
 'pedidos' => $pedidos
 ]);
 } catch (\Exception $e) {
 \Log::error('Erro ao buscar pedidos ativos: ' . $e->getMessage());
 return response()->json([
 'success' => false,
 'message' => 'Erro ao buscar pedidos'
 ], 500);
 }
 }
}
 
 