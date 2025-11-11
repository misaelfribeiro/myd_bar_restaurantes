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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rotas de autenticação (públicas)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Rotas protegidas por autenticação básica
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::post('/auth/revoke-all', [AuthController::class, 'revokeAll']);
});

// ================================
// ROTAS COM AUTORIZAÇÃO POR PERFIL
// ================================

// 🔴 ADMIN APENAS - Gerenciamento de usuários e relatórios sensíveis
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('usuarios', UsuarioController::class);
    Route::get('/relatorios/vendas', [\App\Http\Controllers\RelatorioController::class, 'vendas']);
    Route::get('/relatorios/horarios-movimento', [\App\Http\Controllers\RelatorioController::class, 'horariosMovimento']);
});

// 🟠 ADMIN + GERENTE - Gestão de produtos, categorias e mesas
Route::middleware(['auth:sanctum', 'role:admin,gerente'])->group(function () {
    Route::apiResource('produtos', ProdutoController::class)->except(['index', 'show']);
    Route::patch('produtos/{produto}/toggle-status', [ProdutoController::class, 'toggleStatus']);
    Route::apiResource('categorias', CategoriaController::class)->except(['index', 'show']);
    Route::apiResource('mesas', MesaController::class)->except(['index', 'show']);
    Route::get('/relatorios/mesas-populares', [\App\Http\Controllers\RelatorioController::class, 'mesasPopulares']);
});

// 🟡 ADMIN + GERENTE + GARCOM - Gestão de pedidos e consultas
Route::middleware(['auth:sanctum', 'role:admin,gerente,garcom'])->group(function () {
    Route::apiResource('pedidos', PedidoController::class);
    
    // Rotas para itens do pedido
    Route::apiResource('item-pedidos', ItemPedidoController::class);
    Route::get('/pedidos/{pedido}/itens', [ItemPedidoController::class, 'itensPorPedido']);
    Route::post('/item-pedidos/multiplos', [ItemPedidoController::class, 'adicionarMultiplos']);
    Route::get('/relatorios/itens-mais-vendidos', [ItemPedidoController::class, 'itensMaisVendidos']);
    
    Route::get('/produtos', [ProdutoController::class, 'index']);
    Route::get('/produtos/{produto}', [ProdutoController::class, 'show']);
    Route::get('/categorias', [CategoriaController::class, 'index']);
    Route::get('/categorias/{categoria}', [CategoriaController::class, 'show']);
    Route::get('/mesas', [MesaController::class, 'index']);
    Route::get('/mesas/{mesa}', [MesaController::class, 'show']);
    Route::get('/relatorios/status-pedidos', [\App\Http\Controllers\RelatorioController::class, 'statusPedidos']);
    Route::get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'stats']);
    Route::get('/dashboard/pedidos-status', [\App\Http\Controllers\DashboardController::class, 'pedidosPorStatus']);
    Route::get('/dashboard/produtos-vendidos', [\App\Http\Controllers\DashboardController::class, 'produtosMaisVendidos']);
});

// ⭐ ROTAS PÚBLICAS PARA TESTE DA INTERFACE
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
Route::get('/pedidos-public/{pedido}', [PedidoController::class, 'show']);
Route::get('/pedidos-public/{pedido}/itens', [ItemPedidoController::class, 'itensPorPedido']);
Route::get('/produtos-public', [ProdutoController::class, 'index']);
Route::post('/item-pedidos-public', [ItemPedidoController::class, 'store']);
Route::get('/item-pedidos-public/{item_pedido}', [ItemPedidoController::class, 'show']);
Route::put('/item-pedidos-public/{item_pedido}', [ItemPedidoController::class, 'update']);
Route::delete('/item-pedidos-public/{item_pedido}', [ItemPedidoController::class, 'destroy']);

// Manter algumas rotas públicas para demonstração
Route::get('/categorias-public', [CategoriaController::class, 'index']);
Route::get('/produtos-public', [ProdutoController::class, 'index']);
