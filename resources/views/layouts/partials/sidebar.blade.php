<nav class="sidebar" id="sidebar">
 <button class="sidebar-toggle" id="sidebarToggleBtn">
 <i class="fas fa-chevron-left"></i>
 </button>
 <div class="sidebar-header">
 <div class="sidebar-title">Menu Principal</div>
 </div>
 <div class="menu">
 <!-- Dashboard -->
 <div class="menu-section">
 <div class="menu-section-title">Dashboard</div>
 <a href="{{ route('dashboard') }}" class="menu-item {{ request()->is('/') ? 'active' : '' }}">
 <i class="fas fa-tachometer-alt"></i>
 <span class="menu-text">Visão Geral</span>
 </a>
 </div>
 @php
 $isMaster = false;
 if (auth()->guard('admin')->check()) {
 $user = auth()->guard('admin')->user();
 if ($user->tenant_code === 'EATSFOOD') {
 $empresa = \App\Models\Empresa::where('tenant_code', 'EATSFOOD')->first();
 $isMaster = $empresa && $empresa->is_master;
 }
 }
 @endphp
 @if($isMaster)
 <!-- Menu Master: Administração da Plataforma -->
 <div class="menu-section" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
 <div class="menu-section-title" style="color: #667eea; font-weight: bold;">
 <i class="fas fa-crown me-2"></i>Painel Master
 </div>
 </div>
 <div class="menu-section">
 <div class="menu-section-title">🏢 Gestão de Empresas</div>
 <a href="{{ route('empresas.index') }}" class="menu-item {{ request()->is('empresas') || request()->is('empresas/index') ? 'active' : '' }}">
 <i class="fas fa-building"></i>
 <span class="menu-text">Todas as Empresas</span>
 </a>
 <a href="{{ route('empresas.create') }}" class="menu-item {{ request()->is('empresas/create') ? 'active' : '' }}">
 <i class="fas fa-plus-circle"></i>
 <span class="menu-text">Nova Empresa</span>
 </a>
 <a href="{{ route('admin.tenants.list') }}" class="menu-item {{ request()->is('admin/tenants/list') ? 'active' : '' }}">
 <i class="fas fa-list-alt"></i>
 <span class="menu-text">Listar Tenants</span>
 </a>
 </div>
 <div class="menu-section">
 <div class="menu-section-title">👥 Usuários e Acesso</div>
 <a href="{{ route('admin.users.index') }}" class="menu-item {{ request()->is('admin/users*') ? 'active' : '' }}">
 <i class="fas fa-users-cog"></i>
 <span class="menu-text">Admins das Empresas</span>
 </a>
 <a href="#" class="menu-item">
 <i class="fas fa-user-shield"></i>
 <span class="menu-text">Permissões</span>
 </a>
 </div>
 <div class="menu-section">
 <div class="menu-section-title">💎 Planos e Assinaturas</div>
 <a href="{{ route('admin.planos.index') }}" class="menu-item {{ request()->is('admin/planos*') ? 'active' : '' }}">
 <i class="fas fa-layer-group"></i>
 <span class="menu-text">Gerenciar Planos</span>
 </a>
 <a href="{{ route('admin.contratos.index') }}" class="menu-item {{ request()->is('admin/contratos*') ? 'active' : '' }}">
 <i class="fas fa-file-contract"></i>
 <span class="menu-text">Contratos</span>
 </a>
 <a href="{{ route('admin.financeiro.index') }}" class="menu-item {{ request()->is('admin/financeiro*') ? 'active' : '' }}">
 <i class="fas fa-file-invoice-dollar"></i>
 <span class="menu-text">Faturas</span>
 </a>
 <a href="#" class="menu-item">
 <i class="fas fa-tags"></i>
 <span class="menu-text">Pacotes e Ofertas</span>
 </a>
 </div>
 <div class="menu-section">
 <div class="menu-section-title">💰 Financeiro</div>
 <a href="{{ route('admin.financeiro.index') }}" class="menu-item {{ request()->is('admin/financeiro') || request()->is('admin/financeiro/*/show') ? 'active' : '' }}">
 <i class="fas fa-file-invoice-dollar"></i>
 <span class="menu-text">Faturas</span>
 </a>
 <a href="{{ route('admin.financeiro.pagamentos.dashboard') }}" class="menu-item {{ request()->is('admin/financeiro/pagamentos*') ? 'active' : '' }}">
 <i class="fas fa-credit-card"></i>
 <span class="menu-text">Pagamentos</span>
 @php
     $pendentes = \App\Models\Payment::where('status', 'pending')->count();
 @endphp
 @if($pendentes > 0)
     <span class="badge bg-warning text-dark ms-2">{{ $pendentes }}</span>
 @endif
 </a>
 <a href="{{ route('admin.financeiro.relatorios') }}" class="menu-item {{ request()->is('admin/financeiro-relatorios') ? 'active' : '' }}">
 <i class="fas fa-chart-line"></i>
 <span class="menu-text">Relatórios Caixa</span>
 </a>
 <a href="{{ route('admin.financeiro.create') }}" class="menu-item {{ request()->is('admin/financeiro/create') ? 'active' : '' }}">
 <i class="fas fa-plus-circle"></i>
 <span class="menu-text">Nova Fatura</span>
 </a>
 </div>
 <div class="menu-section">
 <div class="menu-section-title">👥 Recursos Humanos</div>
 <a href="{{ route('admin.funcionarios.index') }}" class="menu-item {{ request()->is('admin/funcionarios*') ? 'active' : '' }}">
 <i class="fas fa-users"></i>
 <span class="menu-text">Funcionários</span>
 </a>
 <a href="{{ route('admin.cargos.index') }}" class="menu-item {{ request()->is('admin/cargos*') ? 'active' : '' }}">
 <i class="fas fa-user-tie"></i>
 <span class="menu-text">Cargos</span>
 </a>
 <a href="{{ route('admin.comissoes.index') }}" class="menu-item {{ request()->is('admin/comissoes*') ? 'active' : '' }}">
 <i class="fas fa-percentage"></i>
 <span class="menu-text">Comissões</span>
 </a>
 <a href="{{ route('admin.bonus.index') }}" class="menu-item {{ request()->is('admin/bonus*') ? 'active' : '' }}">
 <i class="fas fa-gift"></i>
 <span class="menu-text">Bônus</span>
 </a>
 </div>
 <div class="menu-section">
 <div class="menu-section-title">📊 Plataforma</div>
 <a href="#" class="menu-item">
 <i class="fas fa-cog"></i>
 <span class="menu-text">Configurações</span>
 </a>
 <a href="#" class="menu-item">
 <i class="fas fa-bell"></i>
 <span class="menu-text">Notificações</span>
 </a>
 <a href="{{ route('logs.index') }}" class="menu-item {{ request()->is('logs*') ? 'active' : '' }}">
 <i class="fas fa-file-alt"></i>
 <span class="menu-text">Logs do Sistema</span>
 </a>
 </div>
 @else
 <!-- Menu Normal: Operações de Restaurante -->
 <!-- Operacional -->
 <div class="menu-section">
 <div class="menu-section-title">Operacional</div>
 <a href="{{ route('garcom.dashboard') }}" class="menu-item {{ request()->is('garcom*') ? 'active' : '' }}">
 <i class="fas fa-user-tie"></i>
 <span class="menu-text">Modo Garçom</span>
 </a>
 <a href="{{ route('caixa.index') }}" class="menu-item {{ request()->is('caixa') || request()->is('caixa/index') ? 'active' : '' }}">
 <i class="fas fa-cash-register"></i>
 <span class="menu-text">Painel do Caixa</span>
 </a>
 </div>
 <!-- Gestão -->
 <div class="menu-section">
 <div class="menu-section-title">Gestão</div>
 <a href="{{ route('produtos.index') }}" class="menu-item {{ request()->is('produtos*') ? 'active' : '' }}">
 <i class="fas fa-box"></i>
 <span class="menu-text">Produtos</span>
 </a>
 <a href="{{ route('categorias.index') }}" class="menu-item {{ request()->is('categorias*') ? 'active' : '' }}">
 <i class="fas fa-tags"></i>
 <span class="menu-text">Categorias</span>
 </a>
 <a href="{{ route('combos.index') }}" class="menu-item {{ request()->is('combos*') ? 'active' : '' }}">
 <i class="fas fa-fire"></i>
 <span class="menu-text">Combos</span>
 </a>
 <a href="{{ route('pedidos.index') }}" class="menu-item {{ request()->is('pedidos*') ? 'active' : '' }}">
 <i class="fas fa-receipt"></i>
 <span class="menu-text">Pedidos</span>
 <span class="menu-badge" id="pedidos-pendentes-badge">0</span>
 </a>
 <a href="{{ route('mesas.index') }}" class="menu-item {{ request()->is('mesas*') ? 'active' : '' }}">
 <i class="fas fa-chair"></i>
 <span class="menu-text">Mesas</span>
 </a>
 <a href="{{ route('deliveries.index') }}" class="menu-item {{ request()->is('deliveries*') ? 'active' : '' }}">
 <i class="fas fa-shipping-fast"></i>
 <span class="menu-text">Delivery</span>
 <span class="menu-badge" id="delivery-ativos-badge">0</span>
 </a>
 <a href="{{ route('entregadores.index') }}" class="menu-item {{ request()->is('entregadores*') ? 'active' : '' }}">
 <i class="fas fa-motorcycle"></i>
 <span class="menu-text">Entregadores</span>
 </a>
 <a href="{{ route('clientes.index') }}" class="menu-item {{ request()->is('clientes*') ? 'active' : '' }}">
 <i class="fas fa-users"></i>
 <span class="menu-text">Clientes</span>
 </a>
 </div>
 <!-- Administração -->
 <div class="menu-section">
 <div class="menu-section-title">Administração</div>
 <a href="{{ route('empresas.index') }}" class="menu-item {{ request()->is('empresas*') ? 'active' : '' }}">
 <i class="fas fa-building"></i>
 <span class="menu-text">Minha Empresa</span>
 </a>
 <a href="{{ route('users.index') }}" class="menu-item {{ request()->is('usuarios*') ? 'active' : '' }}">
 <i class="fas fa-users"></i>
 <span class="menu-text">Usuários</span>
 </a>
 </div>
 <!-- Financeiro -->
 <div class="menu-section">
 <div class="menu-section-title">Financeiro</div>
 <a href="/pagamentos" class="menu-item {{ request()->is('pagamentos*') ? 'active' : '' }}">
 <i class="fas fa-credit-card"></i>
 <span class="menu-text">Pagamentos</span>
 </a>
 <a href="/caixa/historico" class="menu-item {{ request()->is('caixa/historico*') ? 'active' : '' }}">
 <i class="fas fa-chart-line"></i>
 <span class="menu-text">Histórico Caixa</span>
 </a>
 </div>
 @endif
 <!-- Relatórios -->
 <div class="menu-section">
 <div class="menu-section-title">Relatórios</div>
 <a href="#" class="menu-item">
 <i class="fas fa-chart-bar"></i>
 <span class="menu-text">Vendas</span>
 </a>
 <a href="{{ route('logs.index') }}" class="menu-item {{ request()->is('logs*') ? 'active' : '' }}">
 <i class="fas fa-clipboard-list"></i>
 <span class="menu-text">Logs de Acesso</span>
 </a>
 </div>
 <!-- Sistema -->
 <div class="menu-section">
 <div class="menu-section-title">Sistema</div>
 <a href="#" class="menu-item" onclick="showMaintenanceMode()">
 <i class="fas fa-tools"></i>
 <span class="menu-text">Manutenção</span>
 </a>
 <a href="#" class="menu-item" onclick="showApiStatus()">
 <i class="fas fa-server"></i>
 <span class="menu-text">Status APIs</span>
 </a>
 </div>
 </div>
</nav>