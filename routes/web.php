<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\GarcomController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Dashboard routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/dashboard/stats', [DashboardController::class, 'stats']);
Route::get('/api/dashboard/pedidos-status', [DashboardController::class, 'pedidosPorStatus']);
Route::get('/api/dashboard/produtos-vendidos', [DashboardController::class, 'produtosMaisVendidos']);

// Interface de teste de autorização
Route::get('/autorizacao', function () {
    return view('autorizacao');
});

// Interface de gestão de usuários
Route::get('/usuarios', [App\Http\Controllers\UserManagementController::class, 'index'])->name('users.index');
Route::get('/user-management/users', [App\Http\Controllers\UserManagementController::class, 'getUsers']);
Route::post('/user-management/users', [App\Http\Controllers\UserManagementController::class, 'store']);
Route::get('/user-management/users/{usuario}', [App\Http\Controllers\UserManagementController::class, 'show']);
Route::put('/user-management/users/{usuario}', [App\Http\Controllers\UserManagementController::class, 'update']);
Route::delete('/user-management/users/{usuario}', [App\Http\Controllers\UserManagementController::class, 'destroy']);
Route::get('/user-management/stats', [App\Http\Controllers\UserManagementController::class, 'getRoleStats']);

// Interface de logs de acesso
Route::get('/logs', [App\Http\Controllers\LogsController::class, 'index'])->name('logs.index');
Route::get('/logs/data', [App\Http\Controllers\LogsController::class, 'getLogs']);
Route::get('/logs/stats', [App\Http\Controllers\LogsController::class, 'getStats']);
Route::get('/logs/security-events', [App\Http\Controllers\LogsController::class, 'getSecurityEvents']);
Route::delete('/logs/clear-old', [App\Http\Controllers\LogsController::class, 'clearOldLogs']);

// Rotas de relatórios
Route::get('/api/relatorios/vendas', [RelatorioController::class, 'vendas']);
Route::get('/api/relatorios/mesas-populares', [RelatorioController::class, 'mesasPopulares']);
Route::get('/api/relatorios/horarios-movimento', [RelatorioController::class, 'horariosMovimento']);
Route::get('/api/relatorios/status-pedidos', [RelatorioController::class, 'statusPedidos']);

// Página de login para testar autenticação
Route::get('/login', function () {
    return view('login');
});

// Rota de logout
Route::post('/logout', function () {
    // Simular logout para demonstração
    return redirect('/')->with('success', 'Logout realizado com sucesso!');
})->name('logout');

// Produtos
Route::resource('produtos', ProdutoController::class);
Route::patch('produtos/{produto}/toggle-status', [ProdutoController::class, 'toggleStatus'])->name('produtos.toggle-status');
// Categorias
Route::resource('categorias', CategoriaController::class);
// Pedidos
Route::resource('pedidos', PedidoController::class);
// Detalhes do pedido com itens
Route::get('/pedidos/{pedido}/detalhes', function ($pedidoId) {
    return view('pedidos.detalhes', ['pedidoId' => $pedidoId]);
})->name('pedidos.detalhes');
// Mesas
Route::resource('mesas', MesaController::class);

// 🍽️ MODO GARÇOM - Interface otimizada para operações diárias
Route::prefix('garcom')->name('garcom.')->group(function () {
    Route::get('/dashboard', [GarcomController::class, 'dashboard'])->name('dashboard');
    Route::get('/cardapio', [GarcomController::class, 'cardapio'])->name('cardapio');
    Route::get('/mesas', [GarcomController::class, 'mesas'])->name('mesas');    Route::get('/pedido-rapido', [GarcomController::class, 'criarPedidoRapido'])->name('pedido-rapido');
    Route::post('/pedido-rapido', [GarcomController::class, 'storePedidoRapido'])->name('pedido-rapido.store');
    Route::get('/pedido-rapido/adicionar', [GarcomController::class, 'adicionarItensPedido'])->name('pedido-rapido.adicionar');
    Route::post('/pedido-rapido/adicionar', [GarcomController::class, 'storeItensPedido'])->name('pedido-rapido.adicionar.store');Route::get('/meus-pedidos', [GarcomController::class, 'meusPedidos'])->name('meus-pedidos');
    Route::get('/pedidos/{pedido}', [GarcomController::class, 'verPedido'])->name('pedidos.show');
    Route::patch('/pedidos/{pedido}/status', [GarcomController::class, 'atualizarStatusPedido'])->name('pedidos.atualizar-status');
    Route::post('/mesas/{mesa}/finalizar', [GarcomController::class, 'finalizarMesa'])->name('mesas.finalizar');
    
    // API endpoints para o modo garçom
    Route::get('/dashboard-data', [GarcomController::class, 'dashboardData'])->name('dashboard-data');
    Route::get('/buscar-produtos', [GarcomController::class, 'buscarProdutos'])->name('buscar-produtos');
    
    // Rota de debug para pedido rápido
    Route::get('/garcom/pedido-rapido-debug', function () {
        $mesas = App\Models\Mesa::orderBy('identificador')->get();
        $categorias = App\Models\Categoria::with(['produtos' => function($query) { 
            $query->where('ativo', true)->orderBy('nome'); 
        }])->get();
        
        return view('garcom.pedido-rapido-debug', compact('mesas', 'categorias'));
    })->name('garcom.pedido-rapido-debug');
});

// Rota de teste para debug de pedidos
Route::get('/debug/test-pedido', function () {
    try {
        $mesa = App\Models\Mesa::first();
        $produto = App\Models\Produto::first();
        $usuario = App\Models\Usuario::first();
        
        if (!$mesa || !$produto || !$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Dados insuficientes',
                'debug' => [
                    'mesa' => $mesa ? 'OK' : 'MISSING',
                    'produto' => $produto ? 'OK' : 'MISSING', 
                    'usuario' => $usuario ? 'OK' : 'MISSING'
                ]
            ]);
        }
        
        // Testar criação direta
        DB::beginTransaction();
        
        $pedido = App\Models\Pedido::create([
            'usuario_id' => $usuario->id,
            'mesa_id' => $mesa->id,
            'total' => $produto->preco,
            'status' => 'aberto',
            'observacoes' => 'Teste via rota debug'
        ]);
        
        App\Models\ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 1,
            'preco_unitario' => $produto->preco,
            'subtotal' => $produto->preco
        ]);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Pedido criado via rota de debug!',
            'pedido_id' => $pedido->id,
            'debug' => [
                'mesa' => $mesa->identificador,
                'produto' => $produto->nome,
                'usuario' => $usuario->nome,
                'total' => $produto->preco
            ]
        ]);
        
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Erro: ' . $e->getMessage(),
            'debug' => [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ]);
    }
});

// Rota de limpeza de pedidos duplicados
Route::get('/cleanup/duplicate-orders', function () {
    try {
        $cleaned = 0;
        
        // Encontrar mesas com múltiplos pedidos abertos
        $mesasProblema = App\Models\Mesa::whereHas('pedidos', function($query) {
            $query->where('status', 'aberto');
        })->withCount(['pedidos' => function($query) {
            $query->where('status', 'aberto');
        }])->having('pedidos_count', '>', 1)->get();
        
        foreach ($mesasProblema as $mesa) {
            // Pegar todos os pedidos abertos da mesa
            $pedidosAbertos = $mesa->pedidos()->where('status', 'aberto')
                                  ->orderBy('created_at', 'desc')->get();
            
            // Manter apenas o mais recente
            $pedidoManter = $pedidosAbertos->first();
            
            // Remover os demais
            foreach ($pedidosAbertos->slice(1) as $pedido) {
                $pedido->delete();
                $cleaned++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Limpeza concluída. $cleaned pedidos duplicados removidos.",
            'mesas_limpas' => $mesasProblema->count()
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Rota de diagnóstico do estado atual
Route::get('/diagnostic/current-state', function () {
    try {
        $totalPedidos = App\Models\Pedido::count();
        $pedidosAbertos = App\Models\Pedido::where('status', 'aberto')->count();
        $pedidosFinalizados = App\Models\Pedido::where('status', 'finalizado')->count();
        
        $totalMesas = App\Models\Mesa::count();
        $mesasOcupadas = App\Models\Mesa::whereHas('pedidos', function($q) {
            $q->where('status', 'aberto');
        })->count();
        
        $pedidosDetalhados = App\Models\Pedido::with('mesa')
            ->where('status', 'aberto')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($pedido) {
                return [
                    'id' => $pedido->id,
                    'mesa' => $pedido->mesa->identificador,
                    'mesa_id' => $pedido->mesa_id,
                    'total' => 'R$ ' . number_format($pedido->total, 2, ',', '.'),
                    'criado' => $pedido->created_at->format('H:i:s'),
                    'data' => $pedido->created_at->format('d/m/Y')
                ];
            });
        
        // Verificar mesas com múltiplos pedidos
        $mesasProblema = App\Models\Mesa::withCount(['pedidos' => function($query) {
            $query->where('status', 'aberto');
        }])->having('pedidos_count', '>', 1)->get();
        
        return response()->json([
            'timestamp' => now()->format('H:i:s d/m/Y'),
            'resumo' => [
                'total_pedidos' => $totalPedidos,
                'pedidos_abertos' => $pedidosAbertos,
                'pedidos_finalizados' => $pedidosFinalizados,
                'total_mesas' => $totalMesas,
                'mesas_ocupadas' => $mesasOcupadas,
                'mesas_livres' => $totalMesas - $mesasOcupadas
            ],
            'pedidos_abertos_detalhes' => $pedidosDetalhados,
            'problemas' => [
                'mesas_multiplos_pedidos' => $mesasProblema->count(),
                'detalhes_problemas' => $mesasProblema->map(function($mesa) {
                    return [
                        'mesa' => $mesa->identificador,
                        'pedidos_abertos' => $mesa->pedidos_count
                    ];
                })
            ]
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Rota temporária para criar produto de teste
Route::get('/criar-produto-teste', function () {
    $categoria = \App\Models\Categoria::first();
    
    if (!$categoria) {
        return 'Erro: Execute primeiro o seeder de categorias com: php artisan db:seed --class=CategoriaSeeder';
    }
    
    $existente = \App\Models\Produto::where('nome', 'Hambúrguer Artesanal')->first();
    
    if ($existente) {
        return redirect("/produtos/{$existente->id}")
               ->with('info', 'Produto já existia - redirecionado para visualização');
    }
    
    $produto = \App\Models\Produto::create([
        'nome' => 'Hambúrguer Artesanal',
        'descricao' => 'Delicioso hambúrguer com pão brioche, carne bovina 180g, queijo cheddar, alface americana, tomate, cebola roxa e molho especial da casa. Acompanha batata rústica.',
        'preco' => 28.90,
        'categoria_id' => $categoria->id,
        'ativo' => true
    ]);
    
    return redirect("/produtos/{$produto->id}")
           ->with('success', 'Produto de teste criado com sucesso!');
});

// Rota temporária para criar pedido de teste
Route::get('/criar-pedido-teste', function () {
    $mesa = \App\Models\Mesa::first();
    $produtos = \App\Models\Produto::take(3)->get();
    
    if (!$mesa || $produtos->count() == 0) {
        return 'Erro: Execute primeiro os scripts de dados básicos';
    }
    
    // Verificar se já existe pedido para esta mesa
    $pedidoExistente = \App\Models\Pedido::where('mesa_id', $mesa->id)
                                        ->where('status', 'aberto')
                                        ->first();
    
    if ($pedidoExistente) {
        return redirect("/garcom/mesas")
               ->with('info', 'Mesa já possui pedido ativo - redirecionado para visualização');
    }
    
    try {
        DB::beginTransaction();
        
        // Criar pedido
        $pedido = \App\Models\Pedido::create([
            'usuario_id' => 1, // Garçom demo
            'mesa_id' => $mesa->id,
            'status' => 'aberto',
            'observacoes' => 'Pedido de demonstração criado automaticamente',
            'total' => 0
        ]);
        
        $total = 0;
        foreach ($produtos as $produto) {
            $quantidade = rand(1, 2);
            $subtotal = $produto->preco * $quantidade;
            
            \App\Models\ItemPedido::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $produto->id,
                'quantidade' => $quantidade,
                'preco' => $produto->preco,
                'subtotal' => $subtotal
            ]);
            
            $total += $subtotal;
        }
        
        // Atualizar total do pedido
        $pedido->update(['total' => $total]);
        
        DB::commit();
        
        return redirect("/garcom/dashboard")
               ->with('success', "Pedido #{$pedido->id} criado com sucesso para {$mesa->identificador}! Total: R$ " . number_format($total, 2, ',', '.'));
               
    } catch (\Exception $e) {
        DB::rollback();
        return 'Erro ao criar pedido: ' . $e->getMessage();
    }
});
