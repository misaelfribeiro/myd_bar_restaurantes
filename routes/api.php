<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ItemPedidoController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PagamentoController as ApiPagamentoController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\ClienteAuthController;
use App\Http\Controllers\Api\EntregadorApiController;

// Rota de teste simples
Route::get('/test-json', function() {
    return response()->json(['success' => true, 'message' => 'OK']);
});

// Rota para monitor da cozinha
Route::get('/cozinha/pedidos-ativos', function() {
    try {
        $pedidos = \App\Models\Pedido::whereIn('status', ['aberto', 'pendente', 'em_preparo', 'pronto'])
            ->with([
                'mesa:id,identificador,numero',
                'usuario:id,nome',
                'itens:id,pedido_id,produto_id,combo_id,quantidade,observacoes',
                'itens.produto:id,nome',
                'itens.combo:id,nome'
            ])
            ->select('id', 'mesa_id', 'usuario_id', 'status', 'observacoes', 'created_at')
            ->orderByRaw("FIELD(status, 'aberto', 'pendente', 'em_preparo', 'pronto')")
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();
        
        // Simplificar dados
        $pedidosSimples = $pedidos->map(function($pedido) {
            return [
                'id' => $pedido->id,
                'mesa' => $pedido->mesa ? $pedido->mesa->identificador : null,
                'usuario' => $pedido->usuario ? $pedido->usuario->nome : null,
                'status' => $pedido->status,
                'observacoes' => $pedido->observacoes,
                'created_at' => $pedido->created_at->format('Y-m-d H:i:s'),
                'itens' => $pedido->itens->map(function($item) {
                    return [
                        'id' => $item->id,
                        'quantidade' => $item->quantidade,
                        'observacoes' => $item->observacoes,
                        'nome_item' => $item->produto ? $item->produto->nome : ($item->combo ? $item->combo->nome : 'Item')
                    ];
                })->toArray()
            ];
        })->toArray();
        
        return response()->json([
            'success' => true,
            'total' => count($pedidosSimples),
            'pedidos' => $pedidosSimples
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::prefix('app/auth')->group(function () {
 Route::post('/login', [ClienteAuthController::class, 'loginOrRegister']);
 Route::middleware('auth:sanctum')->group(function () {
 Route::get('/me', [ClienteAuthController::class, 'me']);
 Route::post('/logout', [ClienteAuthController::class, 'logout']);
 Route::put('/profile', [ClienteAuthController::class, 'updateProfile']);
 });
});
Route::prefix('clientes')->group(function () {
 Route::get('/', [ClienteApiController::class, 'index']);
 Route::get('/search', [ClienteApiController::class, 'search']);
 Route::get('/buscar', [ClienteApiController::class, 'buscar']);
 Route::get('/buscar-delivery', [ClienteApiController::class, 'buscarParaDelivery']);
 Route::post('/cadastrar', [ClienteApiController::class, 'store']);
 Route::post('/buscar-ou-criar', [ClienteApiController::class, 'buscarOuCriar']);
 Route::post('/buscar-ou-criar-delivery', [ClienteApiController::class, 'buscarOuCriarParaDelivery']);
 Route::get('/{cliente}', [ClienteApiController::class, 'show']);
});

// ================================
// ROTAS PÚBLICAS - ENTREGADORES
// ================================
Route::prefix('entregadores/auth')->group(function () {
    Route::post('/login', [EntregadorApiController::class, 'login']);
});

// Rotas protegidas para entregadores
Route::middleware('auth:sanctum')->prefix('entregadores')->group(function () {
    Route::post('/auth/logout', [EntregadorApiController::class, 'logout']);
    Route::get('/auth/me', [EntregadorApiController::class, 'me']);
    
    // Entregas
    Route::get('/entregas/disponiveis', [EntregadorApiController::class, 'entregasDisponiveis']);
    Route::get('/entregas/ativas', [EntregadorApiController::class, 'entregasAtivas']);
    Route::get('/entregas/historico', [EntregadorApiController::class, 'historico']);
    Route::get('/entregas/{id}', [EntregadorApiController::class, 'detalhesEntrega']);
    Route::post('/entregas/aceitar/{id}', [EntregadorApiController::class, 'aceitarEntrega']);
    Route::post('/entregas/status/{id}', [EntregadorApiController::class, 'atualizarStatus']);
    
    // Ganhos
    Route::get('/ganhos', [EntregadorApiController::class, 'ganhos']);
    
    // Localização
    Route::post('/localizacao', [EntregadorApiController::class, 'atualizarLocalizacao']);
    
    // Disponibilidade
    Route::post('/disponibilidade/toggle', [EntregadorApiController::class, 'toggleDisponibilidade']);
    
    // Perfil
    Route::get('/perfil', [EntregadorApiController::class, 'me']);
    Route::put('/perfil', [EntregadorApiController::class, 'atualizarPerfil']);
});

Route::middleware('auth:sanctum')->group(function () {
 Route::post('/auth/logout', [AuthController::class, 'logout']);
 Route::get('/auth/me', [AuthController::class, 'me']);
 Route::post('/auth/refresh', [AuthController::class, 'refresh']);
 Route::post('/auth/revoke-all', [AuthController::class, 'revokeAll']);
 Route::get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'getStats']);
 Route::get('/dashboard/vendas-hoje', [\App\Http\Controllers\DashboardController::class, 'getVendasHoje']);
 Route::get('/dashboard/performance', [\App\Http\Controllers\DashboardController::class, 'getPerformance']);
 Route::get('/dashboard/top-produtos', [\App\Http\Controllers\DashboardController::class, 'getTopProdutos']);
 Route::get('/dashboard/pagamentos-hoje', [\App\Http\Controllers\DashboardController::class, 'getPagamentosHoje']);
 Route::get('/dashboard/usuarios-atividade', [\App\Http\Controllers\DashboardController::class, 'getUsuariosAtividade']);
 Route::get('/dashboard/recursos-servidor', [\App\Http\Controllers\DashboardController::class, 'getRecursosServidor']);
 Route::get('/health/server', [\App\Http\Controllers\HealthController::class, 'server']);
 Route::get('/health/database', [\App\Http\Controllers\HealthController::class, 'database']);
 Route::get('/health/cache', [\App\Http\Controllers\HealthController::class, 'cache']);
 Route::get('/health/php', [\App\Http\Controllers\HealthController::class, 'php']);
 Route::get('/health/apache', [\App\Http\Controllers\HealthController::class, 'apache']);
 Route::get('/health/mysql', [\App\Http\Controllers\HealthController::class, 'mysql']);
 Route::get('/health/laravel', [\App\Http\Controllers\HealthController::class, 'laravel']);
 Route::get('/logs/laravel', [\App\Http\Controllers\LogController::class, 'laravel']);
 Route::get('/logs/apache', [\App\Http\Controllers\LogController::class, 'apache']);
 Route::get('/logs/requests', [\App\Http\Controllers\LogController::class, 'requests']);
 Route::post('/logs/clear', [\App\Http\Controllers\LogController::class, 'clear']);
});
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
 Route::apiResource('usuarios', UsuarioController::class);
 Route::get('/relatorios/vendas', [\App\Http\Controllers\RelatorioController::class, 'vendas']);
 Route::get('/relatorios/horarios-movimento', [\App\Http\Controllers\RelatorioController::class, 'horariosMovimento']);
});
Route::middleware(['auth:sanctum', 'role:admin,gerente'])->group(function () {
 Route::apiResource('produtos', ProdutoController::class)->except(['index', 'show']);
 Route::patch('produtos/{produto}/toggle-status', [ProdutoController::class, 'toggleStatus']);
 Route::apiResource('categorias', CategoriaController::class)->except(['index', 'show']);
 Route::apiResource('mesas', MesaController::class)->except(['index', 'show']);
 Route::get('/relatorios/mesas-populares', [\App\Http\Controllers\RelatorioController::class, 'mesasPopulares']);
 Route::get('/entregadores/disponiveis', [\App\Http\Controllers\EntregadorController::class, 'disponiveis']);
 Route::post('/pedidos/{pedido}/atribuir-entregador', [PedidoController::class, 'atribuirEntregador']);
 Route::prefix('admin/clientes')->group(function () {
 Route::get('/{cliente}', [ClienteApiController::class, 'show']);
 Route::put('/{cliente}', [ClienteApiController::class, 'update']);
 });
});
Route::middleware(['auth:sanctum', 'role:admin,gerente,garcom'])->group(function () {
 Route::apiResource('pedidos', PedidoController::class);
 Route::apiResource('deliveries', DeliveryController::class);
 Route::patch('/deliveries/{delivery}/confirmar', [DeliveryController::class, 'confirmar']);
 Route::patch('/deliveries/{delivery}/iniciar-preparo', [DeliveryController::class, 'iniciarPreparo']);
 Route::patch('/deliveries/{delivery}/marcar-pronto', [DeliveryController::class, 'marcarPronto']);
 Route::patch('/deliveries/{delivery}/sair-entrega', [DeliveryController::class, 'sairEntrega']);
 Route::patch('/deliveries/{delivery}/marcar-entregue', [DeliveryController::class, 'marcarEntregue']);
 Route::patch('/deliveries/{delivery}/cancelar', [DeliveryController::class, 'cancelar']);
 Route::get('/deliveries/relatorios/status', [DeliveryController::class, 'relatorioStatus']);
 Route::get('/deliveries/relatorios/periodo', [DeliveryController::class, 'relatorioPeriodo']);
 Route::get('/deliveries/stats/hoje', [DeliveryController::class, 'statsHoje']);
 Route::post('/pedidos/sync', [PedidoController::class, 'syncOffline']);
 Route::post('/pagamentos/sync', [ApiPagamentoController::class, 'syncOffline']);
 Route::get('/produtos/cache', [ProdutoController::class, 'getCacheData']);
 Route::get('/mesas/cache', [MesaController::class, 'getCacheData']);
 Route::get('/categorias/cache', [CategoriaController::class, 'getCacheData']);
 Route::apiResource('item-pedidos', ItemPedidoController::class);
 Route::get('/pedidos/{pedido}/itens', [ItemPedidoController::class, 'itensPorPedido']);
 Route::post('/item-pedidos/multiplos', [ItemPedidoController::class, 'adicionarMultiplos']);
 Route::get('/relatorios/itens-mais-vendidos', [ItemPedidoController::class, 'itensMaisVendidos']);
 Route::prefix('pagamentos')->group(function () {
 Route::post('/pedido/{pedido}', [ApiPagamentoController::class, 'processarPagamentoPedido']);
 Route::post('/mesa/{mesa}', [ApiPagamentoController::class, 'processarPagamentoMesa']);
 Route::get('/info/pedido/{pedido}', [ApiPagamentoController::class, 'infoParaPagamentoPedido']);
 Route::get('/info/mesa/{mesa}', [ApiPagamentoController::class, 'infoParaPagamentoMesa']);
 });
 Route::get('/produtos', [ProdutoController::class, 'index']);
 Route::get('/produtos/{produto}', [ProdutoController::class, 'show']);
 Route::get('/categorias', [CategoriaController::class, 'index']);
 Route::get('/categorias/{categoria}', [CategoriaController::class, 'show']);
 Route::get('/mesas', [MesaController::class, 'index']);
 Route::get('/mesas/stats', [MesaController::class, 'stats']);
 Route::get('/mesas/{mesa}', [MesaController::class, 'show']);
 Route::get('/relatorios/status-pedidos', [\App\Http\Controllers\RelatorioController::class, 'statusPedidos']);
 Route::get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'stats']);
 Route::get('/dashboard/pedidos-status', [\App\Http\Controllers\DashboardController::class, 'pedidosPorStatus']);
 Route::get('/dashboard/produtos-vendidos', [\App\Http\Controllers\DashboardController::class, 'produtosMaisVendidos']);
 Route::prefix('deliveries')->group(function () {
 Route::get('/', [DeliveryController::class, 'apiIndex']);
 Route::post('/', [DeliveryController::class, 'apiStore']);
 Route::get('/{delivery}', [DeliveryController::class, 'apiShow']);
 Route::put('/{delivery}', [DeliveryController::class, 'apiUpdate']);
 Route::delete('/{delivery}', [DeliveryController::class, 'apiDestroy']);
 Route::patch('/{delivery}/confirmar', [DeliveryController::class, 'apiConfirmar']);
 Route::patch('/{delivery}/iniciar-preparo', [DeliveryController::class, 'apiIniciarPreparo']);
 Route::patch('/{delivery}/marcar-pronto', [DeliveryController::class, 'apiMarcarPronto']);
 Route::patch('/{delivery}/sair-entrega', [DeliveryController::class, 'apiSairEntrega']);
 Route::patch('/{delivery}/marcar-entregue', [DeliveryController::class, 'apiMarcarEntregue']);
 Route::patch('/{delivery}/cancelar', [DeliveryController::class, 'apiCancelar']);
 Route::get('/stats/resumo', [DeliveryController::class, 'apiStats']);
 
 // Platform delivery routes
 Route::get('/disponiveis', [DeliveryController::class, 'entregasDisponiveis']);
 Route::post('/{delivery}/aceitar', [DeliveryController::class, 'aceitarEntrega']);
 Route::post('/{delivery}/atualizar-localizacao', [DeliveryController::class, 'atualizarLocalizacao']);
 });
});
Route::get('/test-simple', function() {
 return response()->json(['message' => 'API funcionando']);
});
Route::get('/test-itens/{pedido}', function($pedidoId) {
 $itens = \App\Models\ItemPedido::where('pedido_id', $pedidoId)
 ->with('produto')
 ->get();
 return response()->json([
 'success' => true,
 'itens' => $itens,
 'count' => $itens->count()
 ]);
});
Route::get('/debug-all', function() {
 $pedidos = \App\Models\Pedido::with('itens')->get();
 $itens = \App\Models\ItemPedido::with('produto', 'pedido')->get();
 return response()->json([
 'pedidos' => $pedidos,
 'total_pedidos' => $pedidos->count(),
 'itens' => $itens,
 'total_itens' => $itens->count()
 ]);
});
Route::get('/debug-pedido/{pedido}', function($pedidoId) {
 $pedido = \App\Models\Pedido::with(['itens.produto', 'mesa', 'usuario'])->find($pedidoId);
 if (!$pedido) {
 return response()->json(['error' => 'Pedido não encontrado'], 404);
 }
 return response()->json([
 'pedido' => $pedido,
 'itens_count' => $pedido->itens->count(),
 'raw_itens' => \App\Models\ItemPedido::where('pedido_id', $pedidoId)->get()
 ]);
});
Route::middleware('auth:sanctum')->prefix('app')->group(function () {
 Route::get('/pedidos', [PedidoController::class, 'index']);
 Route::get('/pedidos/{pedido}', [PedidoController::class, 'show']);
 Route::post('/pedidos', [PedidoController::class, 'syncOffline']);
 Route::post('/pedidos/{pedido}/cancelar', [PedidoController::class, 'cancelar']);
 Route::get('/deliveries/{delivery}/tracking', [DeliveryController::class, 'tracking']);
});
Route::get('/produtos-public', [ProdutoController::class, 'index']);
Route::get('/categorias-public', [CategoriaController::class, 'index']);
Route::get('/pedidos-public/{pedido}', [PedidoController::class, 'show']);
Route::post('/pedidos-public', [PedidoController::class, 'syncOffline']);
Route::get('/pedidos-public/{pedido}/itens', [ItemPedidoController::class, 'itensPorPedido']);
Route::patch('/pedidos-public/{pedido}', [PedidoController::class, 'update']);
Route::post('/item-pedidos-public', [ItemPedidoController::class, 'store']);
Route::get('/item-pedidos-public/{item_pedido}', [ItemPedidoController::class, 'show']);
Route::put('/item-pedidos-public/{item_pedido}', [ItemPedidoController::class, 'update']);
Route::delete('/item-pedidos-public/{item_pedido}', [ItemPedidoController::class, 'destroy']);
Route::prefix('pagamentos-teste')->group(function () {
 Route::get('/info/pedido/{pedido}', [ApiPagamentoController::class, 'infoParaPagamentoPedido']);
 Route::get('/info/mesa/{mesa}', [ApiPagamentoController::class, 'infoParaPagamentoMesa']);
 Route::post('/pedido/{pedido}', [ApiPagamentoController::class, 'processarPagamentoPedido']);
 Route::post('/mesa/{mesa}', [ApiPagamentoController::class, 'processarPagamentoMesa']);
});
Route::get('/pagamentos-status', function() {
 return response()->json([
 'success' => true,
 'message' => 'API Unificada de Pagamentos funcionando!',
 'timestamp' => now()->toDateTimeString(),
 'features' => [
 'pagamentos_unicos' => true,
 'pagamentos_multiplos' => true,
 'pagamento_mesa_completa' => true,
 'calculo_troco_automatico' => true,
 'validacao_caixa_aberto' => true,
 'atualizacao_automatica_totais' => true,
 'logs_detalhados' => true
 ],
 'endpoints' => [
 'GET /api/pagamentos-teste/info/pedido/{id}' => 'Informações para pagamento de pedido',
 'GET /api/pagamentos-teste/info/mesa/{id}' => 'Informações para pagamento de mesa',
 'POST /api/pagamentos-teste/pedido/{id}' => 'Processar pagamento de pedido',
 'POST /api/pagamentos-teste/mesa/{id}' => 'Processar pagamento de mesa'
 ]
 ]);
});
Route::post('/debug-pagamento/{pedido}', function(Request $request, $pedidoId) {
 try {
 $pedido = \App\Models\Pedido::find($pedidoId);
 if (!$pedido) {
 return response()->json(['error' => 'Pedido não encontrado'], 404);
 }
 $caixa = \App\Models\Caixa::where('status', 'aberto')->first();
 if (!$caixa) {
 return response()->json(['error' => 'Nenhum caixa aberto'], 400);
 }
 $pagamento = \App\Models\Pagamento::create([
 'pedido_id' => $pedido->id,
 'caixa_id' => $caixa->id,
 'usuario_id' => 1,
 'forma_pagamento' => $request->input('forma_pagamento', 'cartao_credito'),
 'valor' => $request->input('valor', 10.00),
 'valor_recebido' => $request->input('valor', 10.00),
 'troco' => 0,
 'status' => 'confirmado',
 'data_pagamento' => now(),
 'observacoes' => 'Teste debug direto'
 ]);
 return response()->json([
 'success' => true,
 'message' => 'Pagamento de teste criado com sucesso!',
 'data' => [
 'pagamento_id' => $pagamento->id,
 'pedido_id' => $pedido->id,
 'valor' => $pagamento->valor,
 'forma' => $pagamento->forma_pagamento
 ]
 ]);
 } catch (\Exception $e) {
 return response()->json([
 'error' => true,
 'message' => $e->getMessage(),
 'line' => $e->getLine(),
 'file' => basename($e->getFile()),
 'trace' => $e->getTraceAsString()
 ], 500);
 }
});
Route::post('/debug-controller-api/{pedido}', function(Request $request, $pedidoId) {
 try {
 $controller = new \App\Http\Controllers\Api\PagamentoController();
 $pedido = \App\Models\Pedido::find($pedidoId);
 if (!$pedido) {
 return response()->json(['error' => 'Pedido não encontrado'], 404);
 }
 $response = $controller->processarPagamentoPedido($request, $pedido);
 return $response;
 } catch (\Exception $e) {
 return response()->json([
 'error' => true,
 'controller_error' => true,
 'message' => $e->getMessage(),
 'line' => $e->getLine(),
 'file' => basename($e->getFile()),
 'trace' => explode("\n", $e->getTraceAsString())
 ], 500);
 }
});
Route::get('/teste-conexao', [\App\Http\Controllers\Api\TesteController::class, 'testarConexao']);
Route::get('/teste-models', [\App\Http\Controllers\Api\TesteController::class, 'testarModelsAPI']);
use App\Http\Controllers\Api\MonitoramentoController as ApiMonitoramentoController;
use App\Http\Controllers\Api\TesteController as ApiTesteController;
Route::prefix('monitoramento')->group(function () {
 Route::get('/dashboard', [ApiMonitoramentoController::class, 'dashboard']);
 Route::get('/logs', [ApiMonitoramentoController::class, 'logs']);
 Route::get('/metricas', [ApiMonitoramentoController::class, 'metricas']);
 Route::get('/health', [ApiMonitoramentoController::class, 'health']);
});
Route::get('/status', function () {
 return response()->json([
 'status' => 'online',
 'timestamp' => now()->toISOString(),
 'version' => '1.0.0',
 'api_unificada' => 'funcionando'
 ]);
});
Route::prefix('notificacao')->group(function () {
 Route::post('/salvar-token', [App\Http\Controllers\Api\NotificacaoController::class, 'salvarToken']);
 Route::post('/desativar-token', [App\Http\Controllers\Api\NotificacaoController::class, 'desativarToken']);
 Route::get('/tokens', [App\Http\Controllers\Api\NotificacaoController::class, 'listarTokens']);
 Route::post('/enviar', [App\Http\Controllers\Api\NotificacaoController::class, 'enviarNotificacao']);
 Route::post('/enviar-multipla', [App\Http\Controllers\Api\NotificacaoController::class, 'enviarNotificacaoMultipla']);
 Route::post('/pedido-pronto', [App\Http\Controllers\Api\NotificacaoController::class, 'notificarPedidoPronto']);
 Route::post('/delivery-aceito', [App\Http\Controllers\Api\NotificacaoController::class, 'notificarDeliveryAceito']);
 Route::post('/delivery-entregue', [App\Http\Controllers\Api\NotificacaoController::class, 'notificarDeliveryEntregue']);
 Route::post('/testar', [App\Http\Controllers\Api\NotificacaoController::class, 'testar']);
});