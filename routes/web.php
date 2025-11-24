<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\GarcomController;
use App\Http\Controllers\CaixaController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ClienteController;

// ===== ROTAS ADMIN - Área Administrativa =====
use App\Http\Controllers\AdminAuthController;

// Rota de DEBUG de sessão
Route::get('/debug-sessao', function() {
    return view('debug-sessao');
});

// Rota de logout forçado
Route::get('/logout-forcado', function() {
    auth()->guard('web')->logout();
    auth()->guard('admin')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return view('logout-forcado');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // Rotas de autenticação (sem middleware)
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Dashboard Master - SOMENTE para EATSFOOD
    Route::get('/', function() {
        // Verifica se está logado como admin
        if (!auth()->guard('admin')->check()) {
            return redirect()->route('admin.login');
        }
        
        // CRÍTICO: Só permite acesso ao Dashboard Master se for EATSFOOD
        $user = auth()->guard('admin')->user();
        if ($user->tenant_code !== 'EATSFOOD') {
            // Usuários admin de outras empresas vão para dashboard normal
            return redirect('/')->with('error', 'Acesso negado ao Dashboard Master');
        }
        
        // Chama o dashboard Master (apenas para EATSFOOD)
        return app(App\Http\Controllers\DashboardController::class)->dashboardMaster();
    })->name('dashboard');
});

Route::get('/teste-plano', function() {
 return view('teste_plano');
});

// Rota raiz - Dashboard para usuários normais
Route::get('/', function() {
 // Verifica se está logado em qualquer guard
 $webAuth = auth()->guard('web')->check();
 $adminAuth = auth()->guard('admin')->check();
 
 if (!$webAuth && !$adminAuth) {
     return redirect('/login');
 }
 
 // Se for admin com tenant EATSFOOD, vai para Dashboard Master
 if ($adminAuth) {
     $user = auth()->guard('admin')->user();
     if ($user->tenant_code === 'EATSFOOD') {
         return redirect('/admin');
     }
 }
 
 // Dashboard para usuários normais (empresas parceiras)
 return app(App\Http\Controllers\DashboardController::class)->index();
})->name('dashboard');
Route::get('/dashboard', function() {
 if (!auth()->guard('web')->check() && !auth()->guard('admin')->check()) {
 return redirect('/login');
 }
 return app(App\Http\Controllers\DashboardController::class)->index();
})->name('dashboard.main');
Route::get('/api/dashboard/stats', function() {
 \Log::info('=== /api/dashboard/stats ===');
 \Log::info('Guard web check: ' . (auth()->guard('web')->check() ? 'YES' : 'NO'));
 \Log::info('Guard admin check: ' . (auth()->guard('admin')->check() ? 'YES' : 'NO'));
 
 if (auth()->guard('web')->check()) {
 \Log::info('Autenticado como WEB: ' . auth()->guard('web')->user()->nome);
 } elseif (auth()->guard('admin')->check()) {
 \Log::info('Autenticado como ADMIN: ' . auth()->guard('admin')->user()->name);
 } else {
 \Log::warning('Nenhum guard autenticado!');
 \Log::info('Session ID: ' . session()->getId());
 \Log::info('Session data: ' . json_encode(session()->all()));
 }
 
 if (!auth()->guard('web')->check() && !auth()->guard('admin')->check()) {
 return response()->json(['error' => 'Não autenticado'], 401);
 }
 
 // Limpa output buffers
 while (ob_get_level() > 0) {
 ob_end_clean();
 }
 
 return app(App\Http\Controllers\DashboardController::class)->stats();
});
Route::get('/api/dashboard/pedidos-status', function() {
 if (!auth()->guard('web')->check() && !auth()->guard('admin')->check()) {
 return response()->json(['error' => 'Não autenticado'], 401);
 }
 return app(App\Http\Controllers\DashboardController::class)->pedidosPorStatus();
});
Route::get('/api/dashboard/produtos-vendidos', function() {
 if (!auth()->check()) {
 return response()->json(['error' => 'Não autenticado'], 401);
 }
 return app(App\Http\Controllers\DashboardController::class)->produtosMaisVendidos();
});
Route::get('/autorizacao', function () {
 return view('autorizacao');
});
Route::get('/login-admin-teste', function() {
 $admin = App\Models\Usuario::where('role', 'admin')->first();
 if ($admin) {
 auth()->login($admin, true);
 request()->session()->regenerate();
 return redirect('/pedidos/52/detalhes')->with('success', 'Logado como admin: ' . $admin->nome);
 }
 return redirect('/')->with('error', 'Admin não encontrado');
});
Route::get('/status-auth', function() {
 $user = auth()->user();
 return response()->json([
 'authenticated' => auth()->check(),
 'user' => $user ? [
 'id' => $user->id,
 'nome' => $user->nome,
 'email' => $user->email,
 'role' => $user->role
 ] : null,
 'session_id' => session()->getId()
 ]);
});
Route::get('/usuarios', [App\Http\Controllers\UserManagementController::class, 'index'])->name('users.index');
Route::get('/user-management/users', [App\Http\Controllers\UserManagementController::class, 'getUsers']);
Route::post('/user-management/users', [App\Http\Controllers\UserManagementController::class, 'store']);
Route::get('/user-management/users/{usuario}', [App\Http\Controllers\UserManagementController::class, 'show']);
Route::put('/user-management/users/{usuario}', [App\Http\Controllers\UserManagementController::class, 'update']);
Route::delete('/user-management/users/{usuario}', [App\Http\Controllers\UserManagementController::class, 'destroy']);
Route::get('/user-management/stats', [App\Http\Controllers\UserManagementController::class, 'getRoleStats']);
Route::get('/logs', [App\Http\Controllers\LogsController::class, 'index'])->name('logs.index');
Route::get('/logs/data', [App\Http\Controllers\LogsController::class, 'getLogs']);
Route::get('/logs/stats', [App\Http\Controllers\LogsController::class, 'getStats']);
Route::get('/logs/security-events', [App\Http\Controllers\LogsController::class, 'getSecurityEvents']);
Route::delete('/logs/clear-old', [App\Http\Controllers\LogsController::class, 'clearOldLogs']);
Route::get('/api/relatorios/vendas', [RelatorioController::class, 'vendas']);
Route::get('/api/relatorios/mesas-populares', [RelatorioController::class, 'mesasPopulares']);
Route::get('/api/relatorios/horarios-movimento', [RelatorioController::class, 'horariosMovimento']);
Route::get('/api/relatorios/status-pedidos', [RelatorioController::class, 'statusPedidos']);
Route::get('/login', function () {
 return view('login-niveis');
});

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $email = $request->input('email');
    $password = $request->input('password');

    // *** LIMPA TODAS AS SESSÕES ANTERIORES ***
    auth()->guard('web')->logout();
    auth()->guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Tenta primeiro usuários da tabela usuarios (guard web)
    $usuario = App\Models\Usuario::where('email', $email)->first();
    
    if ($usuario && Hash::check($password, $usuario->password)) {
        auth()->guard('web')->login($usuario, $request->boolean('remember', true));
        $request->session()->regenerate();
        
        $redirects = [
            'admin' => '/',
            'gerente' => '/',
            'garcom' => '/garcom/dashboard',
            'caixa' => '/caixa',
            'cliente' => '/'
        ];
        
        $redirectUrl = $redirects[$usuario->role] ?? '/';
        
        // Se for AJAX, retorna JSON
        if ($request->wantsJson() || $request->ajax()) {
            // Limpa output buffer
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'user' => [
                    'nome' => $usuario->nome,
                    'email' => $usuario->email,
                    'role' => $usuario->role
                ],
                'redirect' => $redirectUrl
            ]);
        }
        
        return redirect()->intended($redirectUrl);
    }

    // Tenta usuários da tabela users (guard admin) que NÃO sejam EATSFOOD
    $admin = App\Models\User::where('email', $email)
        ->where('tenant_code', '!=', 'EATSFOOD')
        ->first();
    
    if ($admin && Hash::check($password, $admin->password)) {
        auth()->guard('admin')->login($admin, $request->boolean('remember', true));
        $request->session()->regenerate();
        
        // Se for AJAX, retorna JSON
        if ($request->wantsJson() || $request->ajax()) {
            // Limpa output buffer
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'user' => [
                    'nome' => $admin->name,
                    'email' => $admin->email,
                    'role' => 'admin'
                ],
                'redirect' => '/'
            ]);
        }
        
        return redirect()->intended('/');
    }

    // Se for AJAX, retorna erro em JSON
    if ($request->wantsJson() || $request->ajax()) {
        // Limpa output buffer
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Email ou senha incorretos'
        ], 401);
    }

    return back()->withErrors([
        'email' => 'Credenciais inválidas.',
    ])->withInput($request->only('email'));
});

Route::post('/logout', function (Request $request) {
 try {
 // Faz logout de AMBOS os guards
 auth()->guard('web')->logout();
 auth()->guard('admin')->logout();
 
 // Invalida a sessão completamente
 $request->session()->invalidate();
 $request->session()->regenerateToken();
 
 // Limpa cookies
 if ($request->hasCookie('laravel_session')) {
 cookie()->queue(cookie()->forget('laravel_session'));
 }
 
 if ($request->expectsJson() || $request->ajax()) {
 return response()->json([
 'success' => true,
 'message' => 'Logout realizado com sucesso!',
 'redirect' => '/login'
 ]);
 }
 return redirect('/login')->with('success', 'Logout realizado com sucesso!');
 } catch (\Exception $e) {
 \Log::error('Erro no logout: ' . $e->getMessage());
 if ($request->expectsJson() || $request->ajax()) {
 return response()->json([
 'success' => false,
 'message' => 'Erro no logout: ' . $e->getMessage()
 ], 500);
 }
 return redirect('/login')->with('error', 'Erro no logout. Tente novamente.');
 }
})->name('logout');
Route::resource('produtos', ProdutoController::class);
Route::patch('produtos/{produto}/toggle-status', [ProdutoController::class, 'toggleStatus'])->name('produtos.toggle-status');
Route::resource('categorias', CategoriaController::class);
Route::resource('combos', \App\Http\Controllers\ComboController::class);
Route::patch('combos/{combo}/toggle-status', [\App\Http\Controllers\ComboController::class, 'toggleStatus'])->name('combos.toggle-status');

// Rota para o monitor da cozinha
Route::get('/cozinha/monitor', function() {
    return view('cozinha.monitor');
})->name('cozinha.monitor');

Route::resource('pedidos', PedidoController::class);
Route::get('/pedidos/{pedido}/detalhes', [PedidoController::class, 'detalhes'])->name('pedidos.detalhes');
Route::get('/pedidos/{pedido}/comanda', [PedidoController::class, 'verComanda'])->name('pedidos.comanda');
Route::delete('/pedidos/{pedido}/itens/{item}', [PedidoController::class, 'removeItem'])->name('pedidos.removeItem');
Route::post('/pedidos/{pedido}/atribuir-entregador', [PedidoController::class, 'atribuirEntregador'])->name('pedidos.atribuir-entregador');
Route::post('/pedidos/{pedido}/remover-entregador', [PedidoController::class, 'removerEntregador'])->name('pedidos.remover-entregador');
Route::post('/pedidos/{pedido}/atribuir-entregador', [PedidoController::class, 'atribuirEntregador'])->name('pedidos.atribuir-entregador');
Route::post('/pedidos/{pedido}/remover-entregador', [PedidoController::class, 'removerEntregador'])->name('pedidos.remover-entregador');
Route::resource('mesas', MesaController::class);
Route::resource('entregadores', \App\Http\Controllers\EntregadorController::class)->parameters([
 'entregadores' => 'entregador'
]);
Route::post('entregadores/{entregador}/aprovar', [\App\Http\Controllers\EntregadorController::class, 'aprovar'])->name('entregadores.aprovar');
Route::post('entregadores/{entregador}/reprovar', [\App\Http\Controllers\EntregadorController::class, 'reprovar'])->name('entregadores.reprovar');
Route::post('entregadores/{entregador}/suspender', [\App\Http\Controllers\EntregadorController::class, 'suspender'])->name('entregadores.suspender');
Route::post('entregadores/{entregador}/ativar', [\App\Http\Controllers\EntregadorController::class, 'ativar'])->name('entregadores.ativar');
Route::post('entregadores/{entregador}/desativar', [\App\Http\Controllers\EntregadorController::class, 'desativar'])->name('entregadores.desativar');
Route::get('entregadores/{entregador}/documento/{tipo}', [\App\Http\Controllers\EntregadorController::class, 'downloadDocumento'])->name('entregadores.documento');
Route::get('entregadores-disponiveis', [\App\Http\Controllers\EntregadorController::class, 'disponiveis'])->name('entregadores.disponiveis');
Route::get('entregadores-pendentes', [\App\Http\Controllers\EntregadorController::class, 'pendentes'])->name('entregadores.pendentes');
Route::resource('clientes', \App\Http\Controllers\ClienteController::class);
Route::patch('clientes/{cliente}/toggle-status', [\App\Http\Controllers\ClienteController::class, 'toggleStatus'])->name('clientes.toggle-status');
Route::middleware(['admin.access'])->group(function () {
 Route::resource('empresas', \App\Http\Controllers\EmpresaController::class);
});
Route::prefix('admin')->name('admin.')->middleware(['admin.access'])->group(function () {
 Route::get('/tenants', [\App\Http\Controllers\Admin\TenantController::class, 'index'])->name('tenants.index');
 Route::get('/tenants/list', [\App\Http\Controllers\Admin\TenantController::class, 'tenants'])->name('tenants.list');
 Route::get('/tenants/{id}', [\App\Http\Controllers\Admin\TenantController::class, 'show'])->name('tenants.show');
 Route::post('/tenants/{id}/suspend', [\App\Http\Controllers\Admin\TenantController::class, 'suspend'])->name('tenants.suspend');
 Route::post('/tenants/{id}/activate', [\App\Http\Controllers\Admin\TenantController::class, 'activate'])->name('tenants.activate');
 Route::post('/tenants/{id}/change-plan', [\App\Http\Controllers\Admin\TenantController::class, 'changePlan'])->name('tenants.change-plan');
 Route::get('/financial', [\App\Http\Controllers\Admin\TenantController::class, 'financial'])->name('tenants.financial');
 Route::get('/users', [\App\Http\Controllers\Admin\AdminUsersController::class, 'index'])->name('users.index');
 Route::get('/users/create', [\App\Http\Controllers\Admin\AdminUsersController::class, 'create'])->name('users.create');
 Route::post('/users', [\App\Http\Controllers\Admin\AdminUsersController::class, 'store'])->name('users.store');
 Route::get('/users/{user}', [\App\Http\Controllers\Admin\AdminUsersController::class, 'show'])->name('users.show');
 Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\AdminUsersController::class, 'edit'])->name('users.edit');
 Route::put('/users/{user}', [\App\Http\Controllers\Admin\AdminUsersController::class, 'update'])->name('users.update');
 Route::delete('/users/{user}', [\App\Http\Controllers\Admin\AdminUsersController::class, 'destroy'])->name('users.destroy');
 Route::get('/users-stats', [\App\Http\Controllers\Admin\AdminUsersController::class, 'stats'])->name('users.stats');
 Route::get('/planos', [\App\Http\Controllers\Admin\PlanoController::class, 'index'])->name('planos.index');
 Route::post('/planos/change/{empresaId}', [\App\Http\Controllers\Admin\PlanoController::class, 'changePlan'])->name('planos.change');
 Route::get('/planos/details/{plano}', [\App\Http\Controllers\Admin\PlanoController::class, 'getPlanDetails'])->name('planos.details');
 Route::get('/contratos', [\App\Http\Controllers\Admin\ContratoController::class, 'index'])->name('contratos.index');
 Route::get('/contratos/create', [\App\Http\Controllers\Admin\ContratoController::class, 'create'])->name('contratos.create');
 Route::post('/contratos', [\App\Http\Controllers\Admin\ContratoController::class, 'store'])->name('contratos.store');
 Route::get('/contratos/{contrato}', [\App\Http\Controllers\Admin\ContratoController::class, 'show'])->name('contratos.show');
 Route::post('/contratos/{contrato}/renovar', [\App\Http\Controllers\Admin\ContratoController::class, 'renovar'])->name('contratos.renovar');
 Route::post('/contratos/{contrato}/suspender', [\App\Http\Controllers\Admin\ContratoController::class, 'suspender'])->name('contratos.suspender');
 Route::post('/contratos/{contrato}/cancelar', [\App\Http\Controllers\Admin\ContratoController::class, 'cancelar'])->name('contratos.cancelar');
 Route::post('/contratos/{contrato}/reativar', [\App\Http\Controllers\Admin\ContratoController::class, 'reativar'])->name('contratos.reativar');
 Route::get('/financeiro', [\App\Http\Controllers\Admin\FinanceiroController::class, 'index'])->name('financeiro.index');
 Route::get('/financeiro/create', [\App\Http\Controllers\Admin\FinanceiroController::class, 'create'])->name('financeiro.create');
 Route::post('/financeiro', [\App\Http\Controllers\Admin\FinanceiroController::class, 'store'])->name('financeiro.store');
 Route::get('/financeiro/contratos/{empresa}', [\App\Http\Controllers\Admin\FinanceiroController::class, 'getContratos'])->name('financeiro.contratos');
 Route::get('/financeiro/{fatura}', [\App\Http\Controllers\Admin\FinanceiroController::class, 'show'])->name('financeiro.show');
 Route::post('/financeiro/{fatura}/pagar', [\App\Http\Controllers\Admin\FinanceiroController::class, 'marcarPago'])->name('financeiro.pagar');
 Route::post('/financeiro/{fatura}/cancelar', [\App\Http\Controllers\Admin\FinanceiroController::class, 'cancelar'])->name('financeiro.cancelar');
 Route::get('/financeiro-relatorios', [\App\Http\Controllers\Admin\FinanceiroController::class, 'relatorios'])->name('financeiro.relatorios');
 Route::post('/financeiro/atualizar-vencidas', [\App\Http\Controllers\Admin\FinanceiroController::class, 'atualizarVencidas'])->name('financeiro.atualizar-vencidas');
 Route::resource('/cargos', \App\Http\Controllers\Admin\CargoController::class)->names('cargos');
 Route::resource('/funcionarios', \App\Http\Controllers\Admin\FuncionarioController::class)->names('funcionarios');
 Route::resource('/comissoes', \App\Http\Controllers\Admin\ComissaoController::class)->names('comissoes');
 Route::resource('/bonus', \App\Http\Controllers\Admin\BonusController::class)->names('bonus');
});
Route::prefix('caixa')->name('caixa.')->group(function () {
 Route::get('/', [CaixaController::class, 'index'])->name('index');
 Route::post('/abrir', [CaixaController::class, 'abrir'])->name('abrir');
 Route::post('/fechar', [CaixaController::class, 'fechar'])->name('fechar');
 Route::get('/fechamento/confirmacao/{id}', [CaixaController::class, 'confirmacaoFechamento'])->name('fechamento.confirmacao');
 Route::get('/historico', [CaixaController::class, 'historico'])->name('historico');
 Route::get('/relatorio/{caixa}', [CaixaController::class, 'relatorio'])->name('relatorio');
 Route::get('/recebimento/{pedido}', [CaixaController::class, 'recebimento'])->name('recebimento');
 Route::get('/api/totais', [CaixaController::class, 'totaisTempoReal'])->name('api.totais');
});
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
 Route::get('/mesas/{mesa}/info-pagamento', [GarcomController::class, 'infoParaPagamento'])->name('mesas.info-pagamento');
 Route::get('/dashboard-data', [GarcomController::class, 'dashboardData'])->name('dashboard-data');
 Route::get('/buscar-produtos', [GarcomController::class, 'buscarProdutos'])->name('buscar-produtos');
 Route::get('/garcom/pedido-rapido-debug', function () {
 $mesas = App\Models\Mesa::orderBy('identificador')->get();
 $categorias = App\Models\Categoria::with(['produtos' => function($query) { 
 $query->where('ativo', true)->orderBy('nome'); 
 }])->get();
 return view('garcom.pedido-rapido-debug', compact('mesas', 'categorias'));
 })->name('garcom.pedido-rapido-debug');
});
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
Route::get('/cleanup/duplicate-orders', function () {
 try {
 $cleaned = 0;
 $mesasProblema = App\Models\Mesa::whereHas('pedidos', function($query) {
 $query->where('status', 'aberto');
 })->withCount(['pedidos' => function($query) {
 $query->where('status', 'aberto');
 }])->having('pedidos_count', '>', 1)->get();
 foreach ($mesasProblema as $mesa) {
 $pedidosAbertos = $mesa->pedidos()->where('status', 'aberto')
 ->orderBy('created_at', 'desc')->get();
 $pedidoManter = $pedidosAbertos->first();
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
Route::get('/diagnostic/current-state', function () {
 try {
 $totalPedidos = App\Models\Pedido::count();
 $pedidosAbertos = App\Models\Pedido::where('status', 'aberto')->count();
 $pedidosFinalizados = App\Models\Pedido::whereIn('status', ['finalizado', 'entregue'])->count();
 $totalMesas = App\Models\Mesa::count();
 $mesasOcupadas = App\Models\Mesa::whereHas('pedidos', function($q) {
 $q->whereIn('status', ['aberto', 'entregue']);
 })->count();
 $pedidosDetalhados = App\Models\Pedido::with('mesa')
 ->where('status', 'aberto')
 ->orderBy('created_at', 'desc')
 ->get()
 ->map(function($pedido) {
 $mesaInfo = 'Balcão';
 $mesaId = null;
 if ($pedido->mesa) {
 $mesaInfo = $pedido->mesa->identificador;
 $mesaId = $pedido->mesa_id;
 } elseif ($pedido->delivery) {
 $mesaInfo = 'Delivery - ' . $pedido->delivery->cliente_nome;
 }
 return [
 'id' => $pedido->id,
 'mesa' => $mesaInfo,
 'mesa_id' => $mesaId,
 'total' => 'R$ ' . number_format($pedido->total, 2, ',', '.'),
 'criado' => $pedido->created_at->format('H:i:s'),
 'data' => $pedido->created_at->format('d/m/Y')
 ];
 });
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
Route::get('/criar-pedido-teste', function () {
 $mesa = \App\Models\Mesa::first();
 $produtos = \App\Models\Produto::take(3)->get();
 if (!$mesa || $produtos->count() == 0) {
 return 'Erro: Execute primeiro os scripts de dados básicos';
 }
 $pedidoExistente = \App\Models\Pedido::where('mesa_id', $mesa->id)
 ->where('status', 'aberto')
 ->first();
 if ($pedidoExistente) {
 return redirect("/garcom/mesas")
 ->with('info', 'Mesa já possui pedido ativo - redirecionado para visualização');
 }
 try {
 DB::beginTransaction();
 $pedido = \App\Models\Pedido::create([
 'usuario_id' => 1,
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
 $pedido->update(['total' => $total]);
 DB::commit();
 return redirect("/garcom/dashboard")
 ->with('success', "Pedido #{$pedido->id} criado com sucesso para {$mesa->identificador}! Total: R$ " . number_format($total, 2, ',', '.'));
 } catch (\Exception $e) {        DB::rollback();
 return 'Erro ao criar pedido: ' . $e->getMessage();
 }
});
Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.dashboard');
Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
Route::resource('deliveries', DeliveryController::class);
Route::patch('/deliveries/{delivery}/confirmar', [DeliveryController::class, 'confirmar'])->name('deliveries.confirmar');
Route::patch('/deliveries/{delivery}/iniciar-preparo', [DeliveryController::class, 'iniciarPreparo'])->name('deliveries.iniciar-preparo');
Route::patch('/deliveries/{delivery}/marcar-pronto', [DeliveryController::class, 'marcarPronto'])->name('deliveries.marcar-pronto');
Route::patch('/deliveries/{delivery}/sair-entrega', [DeliveryController::class, 'sairEntrega'])->name('deliveries.sair-entrega');
Route::patch('/deliveries/{delivery}/marcar-entregue', [DeliveryController::class, 'marcarEntregue'])->name('deliveries.marcar-entregue');
Route::patch('/deliveries/{delivery}/cancelar', [DeliveryController::class, 'cancelar'])->name('deliveries.cancelar');
Route::post('/deliveries/{delivery}/atribuir-entregador', [DeliveryController::class, 'atribuirEntregador'])->name('deliveries.atribuir-entregador');
Route::post('/deliveries/{delivery}/remover-entregador', [DeliveryController::class, 'removerEntregador'])->name('deliveries.remover-entregador');

// Rotas para sistema de entrega por plataforma
Route::post('/deliveries/{delivery}/disponibilizar-plataforma', [DeliveryController::class, 'disponibilizarParaPlataforma'])->name('deliveries.disponibilizar-plataforma');
Route::post('/deliveries/{delivery}/atribuir-fixo', [DeliveryController::class, 'atribuirEntregadorFixo'])->name('deliveries.atribuir-fixo');
Route::post('/deliveries/{delivery}/cancelar-plataforma', [DeliveryController::class, 'cancelarPlataforma'])->name('deliveries.cancelar-plataforma');
Route::get('/app-cliente/{path?}', function ($path = '') {
 $filePath = public_path('app-cliente/index.html');
 if (!file_exists($filePath)) {
 abort(404, 'App cliente não encontrado');
 }
 return response()->file($filePath, [
 'Content-Type' => 'text/html; charset=UTF-8',
 'Cache-Control' => 'no-cache, no-store, must-revalidate',
 ]);
})->where('path', '.*');