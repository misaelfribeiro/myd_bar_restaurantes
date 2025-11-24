<?php
namespace App\Http\Controllers;
use App\Models\Delivery;
use App\Models\Pedido;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class DeliveryController extends Controller
{
 public function index(Request $request)
 {
 // Lista pedidos com delivery que estão em preparo ou posterior
 $query = Pedido::with(['delivery', 'cliente', 'itens.produto', 'itens.combo', 'entregador'])
 ->whereHas('delivery')
 ->whereIn('status', ['em_preparo', 'pronto', 'entregue']);
 
 if ($request->filled('status')) {
 $query->where('status', $request->status);
 }
 
 if ($request->filled('cliente')) {
 $query->whereHas('delivery', function($q) use ($request) {
 $q->where('cliente_nome', 'like', '%' . $request->cliente . '%')
 ->orWhere('cliente_telefone', 'like', '%' . $request->cliente . '%');
 });
 }
 
 if ($request->filled('data_inicio')) {
 $query->whereDate('created_at', '>=', $request->data_inicio);
 }
 if ($request->filled('data_fim')) {
 $query->whereDate('created_at', '<=', $request->data_fim);
 }
 
 $pedidos = $query->orderBy('created_at', 'desc')->limit(50)->get();
 
 // Compatibilidade: mapear pedidos para formato delivery
 $deliveries = $pedidos;
 $estatisticas = [
 'em_preparo' => Pedido::whereHas('delivery')->where('status', 'em_preparo')->count(),
 'aguardando_entregador' => Pedido::whereHas('delivery')->where('status', 'em_preparo')->whereNull('entregador_id')->count(),
 'prontos' => Pedido::whereHas('delivery')->where('status', 'pronto')->count(),
 'em_rota' => Delivery::where('status', 'saiu_entrega')->count(),
 'entregues' => Pedido::whereHas('delivery')->where('status', 'entregue')->whereDate('updated_at', today())->count(),
 'faturamento' => Pedido::whereHas('delivery')->where('status', 'entregue')
 ->whereDate('updated_at', today())
 ->sum('total') ?? 0
 ];
 // Processar informações adicionais nos pedidos
 $deliveries->each(function($pedido) {
 $pedido->status_color = $this->getStatusColor($pedido->status);
 $pedido->status_label = $this->getStatusLabel($pedido->status);
 });
 if ($request->expectsJson() || $request->is('api/*')) {
 return response()->json([
 'pedidos' => $deliveries,
 'estatisticas' => $estatisticas
 ]);
 }
 return view('deliveries.index', compact('deliveries', 'estatisticas'));
 }

 public function create()
 {
 try {
 $pedidos = Pedido::with('mesa')
 ->whereDoesntHave('delivery')
 ->where('status', '!=', 'cancelado')
 ->orderBy('created_at', 'desc')
 ->get();
 return view('deliveries.create', compact('pedidos'));
 } catch (\Exception $e) {
 \Log::error('Erro no create do DeliveryController: ' . $e->getMessage());
 $pedidos = collect();
 return view('deliveries.create', compact('pedidos'))
 ->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
 }
 }
 public function store(Request $request)
 {
 \Log::info('DeliveryController@store - Dados recebidos:', $request->all());
 $validated = $request->validate([
 'cliente_id' => 'required|integer|exists:clientes,id',
 'pedido_id' => 'nullable|exists:pedidos,id',
 'taxa_entrega' => 'required|numeric|min:0',
 'tempo_estimado' => 'required|integer|min:10',
 'observacoes' => 'nullable|string',
 ], [
 'cliente_id.required' => 'É necessário selecionar um cliente.',
 'cliente_id.exists' => 'Cliente selecionado não é válido.',
 'taxa_entrega.required' => 'A taxa de entrega é obrigatória.',
 'tempo_estimado.required' => 'O tempo estimado é obrigatório.',
 ]);
 \Log::info('DeliveryController@store - Dados validados:', $validated);
 try {
 $cliente = Cliente::findOrFail($validated['cliente_id']);
 \Log::info('DeliveryController@store - Cliente encontrado:', $cliente->toArray());
 $deliveryData = [
 'cliente_id' => $cliente->id,
 'cliente_nome' => $cliente->nome,
 'cliente_telefone' => $cliente->telefone,
 'cliente_email' => $cliente->email,
 'endereco_rua' => $cliente->endereco_rua ?? '',
 'endereco_numero' => $cliente->endereco_numero ?? '',
 'endereco_complemento' => $cliente->endereco_complemento ?? '',
 'endereco_bairro' => $cliente->endereco_bairro ?? '',
 'endereco_cidade' => $cliente->endereco_cidade ?? '',
 'endereco_cep' => $cliente->endereco_cep ?? '',
 'endereco_referencia' => '',
 'taxa_entrega' => $validated['taxa_entrega'],
 'tempo_estimado' => $validated['tempo_estimado'],
 'pedido_id' => $validated['pedido_id'] ?? null,
 'observacoes' => $validated['observacoes'] ?? '',
 'status' => 'pendente',
 'tenant_code' => auth('admin')->check() ? auth('admin')->user()->tenant_code : (auth()->check() ? auth()->user()->tenant_code : null)
 ];
 \Log::info('DeliveryController@store - Dados para criar delivery:', $deliveryData);
 $delivery = Delivery::create($deliveryData);
 \Log::info('DeliveryController@store - Delivery criado com sucesso:', $delivery->toArray());
 if ($request->expectsJson()) {
 return response()->json([
 'success' => true,
 'message' => 'Delivery criado com sucesso!',
 'delivery' => $delivery,
 ], 201);
 }
 return redirect()
 ->route('deliveries.index')
 ->with('success', 'Delivery criado com sucesso!');
 } catch (\Exception $e) {
 \Log::error('Erro ao criar delivery: ' . $e->getMessage());
 \Log::error('Stack trace: ' . $e->getTraceAsString());
 if ($request->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao criar delivery: ' . $e->getMessage(),
 ], 500);
 }
 return back()
 ->withInput()
 ->withErrors(['erro' => 'Erro ao criar delivery: ' . $e->getMessage()]);
 }
 }
 public function show(Delivery $delivery)
 {
 $delivery->load(['pedido.itens.produto', 'pedido.entregador', 'entregador']);
 $entregadoresDisponiveis = \App\Models\Entregador::where('status', 'ativo')
 ->where('disponivel', 1)
 ->orderBy('avaliacao_media', 'desc')
 ->get();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json(['delivery' => $delivery, 'entregadores' => $entregadoresDisponiveis]);
 }
 return view('deliveries.show', compact('delivery', 'entregadoresDisponiveis'));
 }

 public function edit(Delivery $delivery)
 {
 $pedidos = Pedido::with('mesa', 'itens.produto')
 ->where(function($q) use ($delivery) {
 $q->whereDoesntHave('delivery')
 ->orWhere('id', $delivery->pedido_id);
 })
 ->orderBy('created_at', 'desc')
 ->get();
 return view('deliveries.edit', compact('delivery', 'pedidos'));
 }
 public function update(Request $request, Delivery $delivery)
 {
 $validated = $request->validate([
 'cliente_nome' => 'required|string|max:255',
 'cliente_telefone' => 'required|string|max:20',
 'cliente_email' => 'nullable|email|max:255',
 'endereco_rua' => 'required|string|max:255',
 'endereco_numero' => 'required|string|max:20',
 'endereco_complemento' => 'nullable|string|max:255',
 'endereco_bairro' => 'required|string|max:100',
 'endereco_cidade' => 'required|string|max:100',
 'endereco_cep' => 'required|string|max:9',
 'endereco_referencia' => 'nullable|string',
 'taxa_entrega' => 'required|numeric|min:0',
 'tempo_estimado' => 'required|integer|min:10',
 'distancia_km' => 'nullable|numeric|min:0',
 'status' => 'required|in:pendente,confirmado,preparando,pronto,saiu_entrega,entregue,cancelado',
 'pedido_id' => 'nullable|exists:pedidos,id',
 'entregador_nome' => 'nullable|string|max:255',
 'entregador_telefone' => 'nullable|string|max:20',
 'observacoes' => 'nullable|string',
 'observacoes_internas' => 'nullable|string',
 ]);
 $delivery->update($validated);
 if ($request->expectsJson() || $request->is('api/*')) {
 return response()->json($delivery);
 }
 return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery atualizado com sucesso!');
 }

 public function destroy(Delivery $delivery)
 {
 $delivery->delete();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json(['message' => 'Delivery excluído com sucesso!']);
 }
 return redirect()->route('deliveries.index')->with('success', 'Delivery excluído com sucesso!');
 }

 public function confirmar(Delivery $delivery)
 {
 $delivery->confirmar();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json(['message' => 'Delivery confirmado com sucesso!', 'delivery' => $delivery]);
 }
 return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery confirmado com sucesso!');
 }

 public function relatorio(Request $request)
 {
 $dataInicio = $request->input('data_inicio', today()->subDays(7)->format('Y-m-d'));
 $dataFim = $request->input('data_fim', today()->format('Y-m-d'));
 $deliveries = Delivery::whereBetween('data_pedido', [$dataInicio, $dataFim])
 ->with('pedido')
 ->get();
 $stats = [
 'total_deliveries' => $deliveries->count(),
 'total_faturamento' => $deliveries->sum('taxa_entrega'),
 'media_tempo' => $deliveries->where('status', 'entregue')->avg('tempo_estimado'),
 'taxa_cancelamento' => $deliveries->count() > 0 ? 
 ($deliveries->where('status', 'cancelado')->count() / $deliveries->count()) * 100 : 0,
 ];
 $bairrosMaisAtendidos = $deliveries->groupBy('endereco_bairro')
 ->map(function($items, $bairro) {
 return [
 'bairro' => $bairro,
 'total' => $items->count(),
 'faturamento' => $items->sum('taxa_entrega')
 ];
 })
 ->sortByDesc('total')
 ->take(10);
 if ($request->expectsJson() || $request->is('api/*')) {
 return response()->json(['stats' => $stats, 'deliveries' => $deliveries, 'top_bairros' => $topBairros]);
 }
 return view('deliveries.relatorio', compact('stats', 'deliveries', 'topBairros', 'dataInicio', 'dataFim'));
 }

 public function statsHoje()
 {
 $hoje = today();
 $stats = [
 'pendentes' => Delivery::where('status', 'pendente')->count(),
 'confirmados' => Delivery::where('status', 'confirmado')->count(), 
 'preparando' => Delivery::where('status', 'preparando')->count(),
 'prontos' => Delivery::where('status', 'pronto')->count(),
 'saiu_entrega' => Delivery::where('status', 'saiu_entrega')->count(),
 'entregues_hoje' => Delivery::where('status', 'entregue')->whereDate('data_entrega', $hoje)->count(),
 'cancelados_hoje' => Delivery::where('status', 'cancelado')->whereDate('updated_at', $hoje)->count(),
 ];
 $ativos = Delivery::whereNotIn('status', ['entregue', 'cancelado'])
 ->with('pedido')
 ->orderBy('created_at', 'desc')
 ->get()
 ->map(function($delivery) {
 return [
 'id' => $delivery->id,
 'nome_cliente' => $delivery->nome_cliente,
 'status' => $delivery->status,
 'tempo_estimado_entrega' => $delivery->tempo_estimado_entrega,
 'taxa_entrega' => $delivery->taxa_entrega,
 'pedido_id' => $delivery->pedido_id,
 'created_at' => $delivery->created_at->format('H:i')
 ];
 });
 $stats['ativos'] = $ativos;
 return response()->json($stats);
 }
 private function getStatusColor($status)
 {
 $colors = [
 'pendente' => 'warning',
 'confirmado' => 'info',
 'preparando' => 'primary',
 'pronto' => 'success',
 'saiu_entrega' => 'dark',
 'entregue' => 'success',
 'cancelado' => 'danger'
 ];
 return $colors[$status] ?? 'secondary';
 }
 private function getStatusLabel($status)
 {
 $labels = [
 'pendente' => 'Pendente',
 'confirmado' => 'Confirmado', 
 'preparando' => 'Preparando',
 'pronto' => 'Pronto',
 'saiu_entrega' => 'Saiu para Entrega',
 'entregue' => 'Entregue',
 'cancelado' => 'Cancelado'
 ];
 return $labels[$status] ?? $status;
 }
 public function atribuirEntregador(Request $request, Delivery $delivery)
 {
 try {
 $request->validate([
 'entregador_id' => 'required|exists:entregadores,id'
 ]);
 if (!in_array($delivery->status, ['confirmado', 'preparando', 'pronto'])) {
 return response()->json([
 'success' => false,
 'message' => 'A entrega precisa estar confirmada, preparando ou pronta para atribuir entregador.'
 ], 400);
 }
 $entregador = \App\Models\Entregador::findOrFail($request->entregador_id);
 if ($entregador->status !== 'ativo') {
 return response()->json([
 'success' => false,
 'message' => 'Este entregador não está ativo no momento.'
 ], 400);
 }
 if (!$entregador->disponivel) {
 return response()->json([
 'success' => false,
 'message' => 'Este entregador não está disponível no momento.'
 ], 400);
 }
 $delivery->pedido->update([
 'entregador_id' => $entregador->id
 ]);
 Log::info('Entregador atribuído à entrega', [
 'delivery_id' => $delivery->id,
 'pedido_id' => $delivery->pedido->id,
 'entregador_id' => $entregador->id,
 'entregador_nome' => $entregador->nome
 ]);
 return response()->json([
 'success' => true,
 'message' => 'Entregador atribuído com sucesso!',
 'entregador' => [
 'id' => $entregador->id,
 'nome' => $entregador->nome,
 'telefone' => $entregador->telefone,
 'tipo_veiculo' => $entregador->tipo_veiculo
 ]
 ]);
 } catch (\Exception $e) {
 Log::error('Erro ao atribuir entregador: ' . $e->getMessage());
 return response()->json([
 'success' => false,
 'message' => 'Erro ao atribuir entregador: ' . $e->getMessage()
 ], 500);
 }
 }
 public function removerEntregador(Delivery $delivery)
 {
 try {
 if (!$delivery->pedido->entregador_id) {
 return response()->json([
 'success' => false,
 'message' => 'Esta entrega não possui entregador atribuído.'
 ], 400);
 }
 if (in_array($delivery->status, ['saiu_entrega', 'entregue'])) {
 return response()->json([
 'success' => false,
 'message' => 'Não é possível remover o entregador depois que a entrega já saiu.'
 ], 400);
 }
 $entregadorNome = $delivery->pedido->entregador->nome;
 $delivery->pedido->update([
 'entregador_id' => null
 ]);
 Log::info('Entregador removido da entrega', [
 'delivery_id' => $delivery->id,
 'pedido_id' => $delivery->pedido->id,
 'entregador_nome' => $entregadorNome
 ]);
 return response()->json([
 'success' => true,
 'message' => 'Entregador removido com sucesso!'
 ]);
 } catch (\Exception $e) {
 Log::error('Erro ao remover entregador: ' . $e->getMessage());
 return response()->json([
 'success' => false,
 'message' => 'Erro ao remover entregador: ' . $e->getMessage()
 ], 500);
 }
 }
 public function tracking(Delivery $delivery)
 {
 try {
 $delivery->load(['entregador', 'pedido']);
 return response()->json([
 'success' => true,
 'delivery' => [
 'id' => $delivery->id,
 'status' => $delivery->status,
 'status_label' => $this->getStatusLabel($delivery->status),
 'cliente_nome' => $delivery->cliente_nome,
 'cliente_telefone' => $delivery->cliente_telefone,
 'endereco_rua' => $delivery->endereco_rua,
 'endereco_numero' => $delivery->endereco_numero,
 'endereco_complemento' => $delivery->endereco_complemento,
 'endereco_bairro' => $delivery->endereco_bairro,
 'endereco_cidade' => $delivery->endereco_cidade,
 'endereco_cep' => $delivery->endereco_cep,
 'destino_latitude' => $delivery->destino_latitude,
 'destino_longitude' => $delivery->destino_longitude,
 'entregador_id' => $delivery->entregador_id,
 'entregador_nome' => $delivery->entregador_nome,
 'entregador_telefone' => $delivery->entregador_telefone,
 'entregador_veiculo' => $delivery->entregador ? $delivery->entregador->tipo_veiculo : null,
 'entregador_latitude' => $delivery->entregador_latitude,
 'entregador_longitude' => $delivery->entregador_longitude,
 'entregador_localizacao_atualizada_em' => $delivery->entregador_localizacao_atualizada_em,
 'taxa_entrega' => $delivery->taxa_entrega,
 'tempo_estimado' => $delivery->tempo_estimado,
 'distancia_km' => $delivery->distancia_km,
 'data_pedido' => $delivery->data_pedido,
 'data_saida' => $delivery->data_saida,
 'data_entrega' => $delivery->data_entrega,
 ]
 ]);
 } catch (\Exception $e) {
 Log::error('Erro ao buscar tracking do delivery: ' . $e->getMessage());
 return response()->json([
 'success' => false,
 'message' => 'Erro ao buscar dados de rastreamento: ' . $e->getMessage()
 ], 500);
 }
 }
 public function updateLocation(Request $request, Delivery $delivery)
 {
 $validated = $request->validate([
 'latitude' => 'required|numeric|between:-90,90',
 'longitude' => 'required|numeric|between:-180,180',
 ]);
 try {
 $delivery->update([
 'entregador_latitude' => $validated['latitude'],
 'entregador_longitude' => $validated['longitude'],
 'entregador_localizacao_atualizada_em' => now(),
 ]);
 return response()->json([
 'success' => true,
 'message' => 'Localização atualizada com sucesso!'
 ]);
 } catch (\Exception $e) {
 Log::error('Erro ao atualizar localização: ' . $e->getMessage());
 return response()->json([
 'success' => false,
 'message' => 'Erro ao atualizar localização: ' . $e->getMessage()
 ], 500);
 }
 }
 
 /**
 * Disponibilizar entrega para entregadores da plataforma
 */
 public function disponibilizarParaPlataforma(Request $request, Delivery $delivery)
 {
 try {
 $valorEntregador = $request->input('valor_entregador');
 $delivery->disponibilizarParaPlataforma($valorEntregador);
 
 return redirect()->back()->with('success', 'Entrega disponibilizada para a plataforma com sucesso!');
 } catch (\Exception $e) {
 return redirect()->back()->with('error', 'Erro ao disponibilizar entrega: ' . $e->getMessage());
 }
 }
 
 /**
 * Atribuir entregador fixo
 */
 public function atribuirEntregadorFixo(Request $request, Delivery $delivery)
 {
 try {
 $validated = $request->validate([
 'entregador_id' => 'required|exists:entregadores,id'
 ]);
 
 $delivery->atribuirEntregadorFixo($validated['entregador_id']);
 
 return redirect()->back()->with('success', 'Entregador atribuído com sucesso!');
 } catch (\Exception $e) {
 return redirect()->back()->with('error', 'Erro ao atribuir entregador: ' . $e->getMessage());
 }
 }
 
 /**
 * API para entregadores da plataforma verem entregas disponíveis
 */
 public function entregasDisponiveis(Request $request)
 {
 try {
 $entregas = Delivery::disponiveis()
 ->with(['pedido', 'cliente'])
 ->orderBy('disponibilizado_em', 'asc')
 ->get()
 ->map(function($delivery) {
 return [
 'id' => $delivery->id,
 'cliente_nome' => $delivery->cliente_nome,
 'endereco_completo' => $delivery->endereco_completo,
 'endereco_bairro' => $delivery->endereco_bairro,
 'endereco_cidade' => $delivery->endereco_cidade,
 'destino_latitude' => $delivery->destino_latitude,
 'destino_longitude' => $delivery->destino_longitude,
 'distancia_km' => $delivery->distancia_km,
 'valor_entregador' => $delivery->valor_entregador,
 'taxa_entrega' => $delivery->taxa_entrega,
 'tempo_estimado' => $delivery->tempo_estimado,
 'disponibilizado_em' => $delivery->disponibilizado_em->diffForHumans(),
 'valor_pedido' => $delivery->pedido ? $delivery->pedido->total : 0,
 ];
 });
 
 return response()->json([
 'success' => true,
 'data' => $entregas
 ]);
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao buscar entregas: ' . $e->getMessage()
 ], 500);
 }
 }
 
 /**
 * Entregador aceita uma entrega da plataforma
 */
 public function aceitarEntrega(Request $request, Delivery $delivery)
 {
 try {
 $entregadorId = $request->input('entregador_id');
 
 if ($delivery->aceitarPorEntregador($entregadorId)) {
 return response()->json([
 'success' => true,
 'message' => 'Entrega aceita com sucesso!',
 'data' => $delivery
 ]);
 }
 
 return response()->json([
 'success' => false,
 'message' => 'Não foi possível aceitar esta entrega. Ela pode já ter sido aceita por outro entregador.'
 ], 400);
 
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao aceitar entrega: ' . $e->getMessage()
 ], 500);
 }
 }
 
 /**
 * Atualiza a localização do entregador durante uma entrega
 */
 public function atualizarLocalizacao(Request $request, Delivery $delivery)
 {
 try {
 $validated = $request->validate([
 'latitude' => 'required|numeric',
 'longitude' => 'required|numeric',
 'entregador_id' => 'required|exists:entregadores,id'
 ]);
 
 // Verifica se o entregador é o responsável pela entrega
 if ($delivery->entregador_id != $validated['entregador_id']) {
 return response()->json([
 'success' => false,
 'message' => 'Você não é o entregador responsável por esta entrega.'
 ], 403);
 }
 
        $delivery->update([
            'entregador_latitude' => $validated['latitude'],
            'entregador_longitude' => $validated['longitude'],
            'entregador_localizacao_atualizada_em' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Localização atualizada com sucesso!',
            'data' => [
                'latitude' => $delivery->entregador_latitude,
                'longitude' => $delivery->entregador_longitude,
                'atualizado_em' => $delivery->entregador_localizacao_atualizada_em
            ]
        ]); } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao atualizar localização: ' . $e->getMessage()
 ], 500);
 }
 }
 
 /**
 * Cancela disponibilização na plataforma
 */
 public function cancelarPlataforma(Delivery $delivery)
 {
 try {
 $delivery->update([
 'disponivel_plataforma' => false,
 'disponibilizado_em' => null,
 'tentativas_notificacao' => 0,
 'ultima_notificacao_em' => null,
 'entregadores_notificados' => null,
 'raio_busca_km' => 5
 ]);
 
 return response()->json([
 'success' => true,
 'message' => 'Disponibilização cancelada com sucesso!'
 ]);
 
 } catch (\Exception $e) {
 \Log::error('Erro ao cancelar plataforma: ' . $e->getMessage());
 return response()->json([
 'success' => false,
 'message' => 'Erro ao cancelar: ' . $e->getMessage()
 ], 500);
 }
 }
 
 /**
 * Inicia o preparo da entrega
 */
 public function iniciarPreparo(Delivery $delivery)
 {
 try {
 $delivery->update(['status' => 'preparando']);
 
 // Se está disponível na plataforma, já começa a buscar entregadores
 if ($delivery->disponivel_plataforma) {
 $delivery->notificarEntregadores();
 }
 
 return redirect()->back()->with('success', 'Preparo iniciado! Buscando entregadores...');
 } catch (\Exception $e) {
 return redirect()->back()->with('error', 'Erro ao iniciar preparo: ' . $e->getMessage());
 }
 }
 
 /**
 * Marca entrega como pronta
 */
 public function marcarPronto(Delivery $delivery)
 {
 try {
 $delivery->update(['status' => 'pronto']);
 
 return redirect()->back()->with('success', 'Entrega marcada como pronta!');
 } catch (\Exception $e) {
 return redirect()->back()->with('error', 'Erro: ' . $e->getMessage());
 }
 }
}