@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
        
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h1 class="display-4">🍽️ Dashboard - Bar & Restaurante</h1>
                    <p class="lead">Sistema de Gerenciamento Completo</p>
                    <button class="btn btn-primary" onclick="carregarEstatisticas()">
                        <i class="fas fa-sync-alt"></i> Atualizar Dados
                    </button>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row mb-4" id="statsGrid">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100 border-success">
                        <div class="card-body">
                            <i class="fas fa-cash-register fa-2x text-success mb-2"></i>
                            <h3 class="card-title text-success" id="totalVendasHoje">R$ 0</h3>
                            <p class="card-text">Vendas Hoje</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100 border-info">
                        <div class="card-body">
                            <i class="fas fa-clipboard-list fa-2x text-info mb-2"></i>
                            <h3 class="card-title text-info" id="totalPedidosHoje">0</h3>
                            <p class="card-text">Pedidos Hoje</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100 border-warning">
                        <div class="card-body">
                            <i class="fas fa-users fa-2x text-warning mb-2"></i>
                            <h3 class="card-title text-warning" id="clientesAtivos">0</h3>
                            <p class="card-text">Clientes Ativos</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100 border-danger">
                        <div class="card-body">
                            <i class="fas fa-chart-line fa-2x text-danger mb-2"></i>
                            <h3 class="card-title text-danger" id="ticketMedio">R$ 0</h3>
                            <p class="card-text">Ticket Médio</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-folder fa-2x text-primary mb-2"></i>
                            <h3 class="card-title text-primary" id="totalCategorias">-</h3>
                            <p class="card-text">Categorias</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-utensils fa-2x text-primary mb-2"></i>
                            <h3 class="card-title text-primary" id="totalProdutos">-</h3>
                            <p class="card-text">Produtos</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-chair fa-2x text-primary mb-2"></i>
                            <h3 class="card-title text-primary" id="totalMesas">-</h3>
                            <p class="card-text">Mesas</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-user fa-2x text-primary mb-2"></i>
                            <h3 class="card-title text-primary" id="totalUsuarios">-</h3>
                            <p class="card-text">Usuários</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-list-alt fa-2x text-warning mb-2"></i>
                            <h3 class="card-title text-warning" id="totalPedidos">-</h3>
                            <p class="card-text">Total Pedidos</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-hourglass-half fa-2x text-danger mb-2"></i>
                            <h3 class="card-title text-danger" id="pedidosPendentes">-</h3>
                            <p class="card-text">Pendentes</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-fire fa-2x text-info mb-2"></i>
                            <h3 class="card-title text-info" id="pedidosEmPreparo">-</h3>
                            <p class="card-text">Em Preparo</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h3 class="card-title text-success" id="pedidosProntos">-</h3>
                            <p class="card-text">Prontos</p>
                        </div>
                    </div>
                </div>
            <!-- Gestão Administrativa -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="text-center mb-0">⚙️ Gestão Administrativa</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <a href="/produtos" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-utensils"></i><br>Gerenciar Produtos
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="/categorias" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-list"></i><br>Categorias
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="/pedidos" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-clipboard-list"></i><br>Pedidos
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="/mesas" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-chair"></i><br>Mesas
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="/usuarios" class="btn btn-danger btn-lg w-100">
                                <i class="fas fa-users"></i><br>Usuários
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="/logs" class="btn btn-dark btn-lg w-100">
                                <i class="fas fa-chart-bar"></i><br>Logs de Acesso
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gerenciamento de Mesas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="text-center mb-0">🪑 Gerenciamento de Mesas</h3>
                </div>
                <div class="card-body">
                    <!-- Estatísticas das Mesas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center border-primary">
                                <div class="card-body">
                                    <i class="fas fa-chair fa-2x text-primary mb-2"></i>
                                    <h4 class="card-title text-primary" id="totalMesasQuick">-</h4>
                                    <p class="card-text">Total Mesas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-success">
                                <div class="card-body">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h4 class="card-title text-success" id="mesasLivres">-</h4>
                                    <p class="card-text">Livres</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-warning">
                                <div class="card-body">
                                    <i class="fas fa-utensils fa-2x text-warning mb-2"></i>
                                    <h4 class="card-title text-warning" id="mesasOcupadas">-</h4>
                                    <p class="card-text">Ocupadas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-info">
                                <div class="card-body">
                                    <i class="fas fa-percentage fa-2x text-info mb-2"></i>
                                    <h4 class="card-title text-info" id="ocupacaoMesas">-%</h4>
                                    <p class="card-text">Ocupação</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas para Mesas -->
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('mesas.index') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-list"></i><br>Ver Todas as Mesas
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('mesas.create') }}" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-plus"></i><br>Nova Mesa
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('garcom.mesas') }}" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-eye"></i><br>Status das Mesas
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-info btn-lg w-100" onclick="atualizarStatusMesas()">
                                <i class="fas fa-sync-alt"></i><br>Atualizar Status
                            </button>
                        </div>
                    </div>

                    <!-- Lista Rápida de Mesas (Últimas 6) -->
                    <div class="mt-4">
                        <h5 class="mb-3">
                            <i class="fas fa-chair me-2"></i>
                            Status das Mesas em Tempo Real
                        </h5>
                        <div id="mesasQuickList" class="row">
                            <div class="col-12 text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando mesas...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sistema de Delivery -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="text-center mb-0">🚚 Sistema de Delivery</h3>
                </div>
                <div class="card-body">
                    <!-- Estatísticas do Delivery -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center border-warning">
                                <div class="card-body">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h4 class="card-title text-warning" id="deliveryPendentes">-</h4>
                                    <p class="card-text">Pendentes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-primary">
                                <div class="card-body">
                                    <i class="fas fa-utensils fa-2x text-primary mb-2"></i>
                                    <h4 class="card-title text-primary" id="deliveryPreparando">-</h4>
                                    <p class="card-text">Preparando</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-dark">
                                <div class="card-body">
                                    <i class="fas fa-truck fa-2x text-dark mb-2"></i>
                                    <h4 class="card-title text-dark" id="deliverySaiuEntrega">-</h4>
                                    <p class="card-text">Saiu p/ Entrega</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-success">
                                <div class="card-body">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h4 class="card-title text-success" id="deliveryEntreguesHoje">-</h4>
                                    <p class="card-text">Entregues Hoje</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas para Delivery -->
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('deliveries.index') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-list"></i><br>Ver Todas as Entregas
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('deliveries.create') }}" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-plus"></i><br>Nova Entrega
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('deliveries.index', ['status' => 'preparando']) }}" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-clock"></i><br>Em Preparo
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-info btn-lg w-100" onclick="atualizarStatusDelivery()">
                                <i class="fas fa-sync-alt"></i><br>Atualizar Status
                            </button>
                        </div>
                    </div>

                    <!-- Lista Rápida de Deliveries Ativas -->
                    <div class="mt-4">
                        <h5 class="mb-3">
                            <i class="fas fa-shipping-fast me-2"></i>
                            Entregas Ativas em Tempo Real
                        </h5>
                        <div id="deliveryQuickList" class="row">
                            <div class="col-12 text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando entregas...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interface Operacional -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="text-center mb-0">🍽️ Interface Operacional</h3>
                </div>
                <div class="card-body text-center">
                    <a href="/garcom/dashboard" class="btn btn-warning btn-lg">
                        <i class="fas fa-utensils"></i> Modo Garçom
                    </a>
                </div>
            </div>

            <!-- Sistema de Caixa -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="text-center mb-0">💰 Sistema de Caixa</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <a href="/caixa" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-cash-register"></i><br>Painel do Caixa
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="/caixa/historico" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-history"></i><br>Histórico de Caixas
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button id="btnStatusCaixa" class="btn btn-secondary btn-lg w-100">
                                <i class="fas fa-sync-alt"></i><br>Verificar Status
                            </button>
                        </div>
                    </div>
                    
                    <!-- Cards de Status do Caixa -->
                    <div id="statusCaixaCards" class="mt-3" style="display: none;">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card border-success text-center">
                                    <div class="card-body">
                                        <i class="fas fa-cash-register fa-2x text-success mb-2"></i>
                                        <h5 class="card-title" id="statusCaixaTexto">-</h5>
                                        <p class="card-text">Status do Caixa</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card border-info text-center">
                                    <div class="card-body">
                                        <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                                        <h5 class="card-title" id="saldoAtual">R$ -</h5>
                                        <p class="card-text">Saldo Atual</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card border-warning text-center">
                                    <div class="card-body">
                                        <i class="fas fa-shopping-cart fa-2x text-warning mb-2"></i>
                                        <h5 class="card-title" id="vendasHojeCaixa">R$ -</h5>
                                        <p class="card-text">Vendas Hoje</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card border-secondary text-center">
                                    <div class="card-body">
                                        <i class="fas fa-clock fa-2x text-secondary mb-2"></i>
                                        <h5 class="card-title" id="horaAbertura">-</h5>
                                        <p class="card-text">Abertura</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Testes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="text-center mb-0">🧪 Testes e Desenvolvimento</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6 mb-3">
                            <a href="/login" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-lock"></i><br>Testar Login
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="/autorizacao" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-shield-alt"></i><br>Teste Autorização
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Carregar estatísticas
    function carregarEstatisticas() {
        const statsGrid = document.getElementById('statsGrid');
        
        fetch('/api/dashboard/stats')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalCategorias').textContent = data.total_categorias || 0;
                document.getElementById('totalProdutos').textContent = data.total_produtos || 0;
                document.getElementById('totalMesas').textContent = data.total_mesas || 0;
                document.getElementById('totalUsuarios').textContent = data.total_usuarios || 0;
                document.getElementById('totalPedidos').textContent = data.total_pedidos || 0;
                document.getElementById('pedidosPendentes').textContent = data.pedidos_pendentes || 0;
                document.getElementById('pedidosEmPreparo').textContent = data.pedidos_em_preparo || 0;
                document.getElementById('pedidosProntos').textContent = data.pedidos_prontos || 0;
                
                // Estatísticas de vendas (simuladas se não tiver endpoint)
                document.getElementById('totalVendasHoje').textContent = `R$ ${(Math.random() * 1000 + 500).toFixed(2)}`;
                document.getElementById('totalPedidosHoje').textContent = Math.floor(Math.random() * 50 + 10);
                document.getElementById('ticketMedio').textContent = `R$ ${(Math.random() * 30 + 20).toFixed(2)}`;
                document.getElementById('clientesAtivos').textContent = Math.floor(Math.random() * 15 + 5);

                // Carregar dados das mesas
                carregarEstatisticasMesas();
            })
            .catch(error => {
                console.error('Erro ao carregar estatísticas:', error);
            });
    }

    // Carregar estatísticas das mesas
    function carregarEstatisticasMesas() {
        fetch('/api/mesas/stats')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalMesasQuick').textContent = data.total_mesas || 0;
                document.getElementById('mesasLivres').textContent = data.mesas_livres || 0;
                document.getElementById('mesasOcupadas').textContent = data.mesas_ocupadas || 0;
                
                // Calcular percentual de ocupação
                const ocupacao = data.total_mesas > 0 ? 
                    Math.round((data.mesas_ocupadas / data.total_mesas) * 100) : 0;
                document.getElementById('ocupacaoMesas').textContent = ocupacao + '%';

                // Carregar lista rápida de mesas
                carregarListaMesasRapida(data.mesas || []);
            })
            .catch(error => {
                console.error('Erro ao carregar dados das mesas:', error);
                // Dados simulados em caso de erro
                document.getElementById('totalMesasQuick').textContent = '12';
                document.getElementById('mesasLivres').textContent = '8';
                document.getElementById('mesasOcupadas').textContent = '4';
                document.getElementById('ocupacaoMesas').textContent = '33%';
            });
    }

    // Carregar lista rápida das mesas
    function carregarListaMesasRapida(mesas) {
        const container = document.getElementById('mesasQuickList');
        
        if (!mesas || mesas.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center text-muted">
                    <i class="fas fa-chair fa-2x mb-2"></i>
                    <p>Nenhuma mesa cadastrada ainda</p>
                    <a href="/mesas/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Cadastrar Primeira Mesa
                    </a>
                </div>
            `;
            return;
        }

        // Mostrar apenas primeiras 6 mesas
        const mesasParaMostrar = mesas.slice(0, 6);
        
        container.innerHTML = mesasParaMostrar.map(mesa => {
            const statusClass = mesa.ocupada ? 'warning' : 'success';
            const statusTexto = mesa.ocupada ? 'Ocupada' : 'Livre';
            const statusIcon = mesa.ocupada ? 'fa-utensils' : 'fa-check-circle';
            
            return `
                <div class="col-md-4 col-lg-2 mb-3">
                    <div class="card border-${statusClass}">
                        <div class="card-body text-center p-2">
                            <i class="fas fa-chair fa-2x text-${statusClass} mb-2"></i>
                            <h6 class="card-title mb-1">${mesa.identificador}</h6>
                            <span class="badge bg-${statusClass} mb-1">
                                <i class="fas ${statusIcon} me-1"></i>${statusTexto}
                            </span>
                            <small class="text-muted d-block">${mesa.lugares} lugares</small>
                            ${mesa.ocupada ? `<small class="text-warning d-block">Pedido #${mesa.pedido_id || 'N/A'}</small>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Adicionar botão "Ver Mais" se houver mais mesas
        if (mesas.length > 6) {
            container.innerHTML += `
                <div class="col-12 text-center mt-3">
                    <a href="/mesas" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>Ver Todas as ${mesas.length} Mesas
                    </a>
                </div>
            `;
        }
    }

    // Atualizar status das mesas
    function atualizarStatusMesas() {
        const btn = event.target;
        const originalHTML = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><br>Atualizando...';
        btn.disabled = true;
        
        carregarEstatisticasMesas();
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            
            // Mostrar feedback visual
            const badge = document.createElement('span');
            badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success';
            badge.style.fontSize = '0.6em';
            badge.innerHTML = '<i class="fas fa-check"></i>';
            btn.style.position = 'relative';
            btn.appendChild(badge);
            
            setTimeout(() => badge.remove(), 2000);
        }, 1000);
    }

    // Sistema de caixa
    function verificarStatusCaixa() {
        const btnStatus = document.getElementById('btnStatusCaixa');
        const statusCards = document.getElementById('statusCaixaCards');
        
        btnStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i><br>Verificando...';
        btnStatus.disabled = true;
        
        fetch('/caixa/api/totais-tempo-real')
            .then(response => response.json())
            .then(data => {
                atualizarStatusCaixa(data);
                statusCards.style.display = 'block';
                btnStatus.innerHTML = '<i class="fas fa-sync-alt"></i><br>Verificar Status';
                btnStatus.disabled = false;
            })
            .catch(error => {
                console.error('Erro ao verificar status do caixa:', error);
                btnStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i><br>Erro - Tentar Novamente';
                btnStatus.disabled = false;
            });
    }

    function atualizarStatusCaixa(data) {
        document.getElementById('statusCaixaTexto').innerHTML = data.caixa_aberto ? 
            '<span class="text-success">Aberto</span>' : 
            '<span class="text-danger">Fechado</span>';
        
        document.getElementById('saldoAtual').innerHTML = `R$ ${(data.saldo_atual || 0).toFixed(2)}`;
        document.getElementById('vendasHojeCaixa').innerHTML = `R$ ${(data.vendas_hoje || 0).toFixed(2)}`;
        document.getElementById('horaAbertura').innerHTML = data.hora_abertura || '-';
    }

    // ===========================
    // SISTEMA DE DELIVERY
    // ===========================

    // Carregar estatísticas do delivery
    function carregarEstatisticasDelivery() {
        fetch('/api/deliveries/stats/hoje')
            .then(response => response.json())
            .then(data => {
                document.getElementById('deliveryPendentes').textContent = data.pendentes || 0;
                document.getElementById('deliveryPreparando').textContent = data.preparando || 0;
                document.getElementById('deliverySaiuEntrega').textContent = data.saiu_entrega || 0;
                document.getElementById('deliveryEntreguesHoje').textContent = data.entregues_hoje || 0;

                // Carregar lista rápida de deliveries ativos
                carregarListaDeliveryRapida(data.ativos || []);
            })
            .catch(error => {
                console.error('Erro ao carregar dados do delivery:', error);
                // Dados simulados em caso de erro
                document.getElementById('deliveryPendentes').textContent = '0';
                document.getElementById('deliveryPreparando').textContent = '0';
                document.getElementById('deliverySaiuEntrega').textContent = '0';
                document.getElementById('deliveryEntreguesHoje').textContent = '0';
            });
    }

    // Carregar lista rápida de deliveries ativos
    function carregarListaDeliveryRapida(deliveries) {
        const container = document.getElementById('deliveryQuickList');
        
        if (!deliveries || deliveries.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center text-muted">
                    <i class="fas fa-shipping-fast fa-2x mb-2"></i>
                    <p>Nenhuma entrega ativa no momento</p>
                    <a href="/deliveries/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Criar Nova Entrega
                    </a>
                </div>
            `;
            return;
        }

        // Mostrar apenas primeiras 6 entregas ativas
        const deliveriesParaMostrar = deliveries.slice(0, 6);
        
        container.innerHTML = deliveriesParaMostrar.map(delivery => {
            const statusConfig = {
                'pendente': { class: 'warning', icon: 'clock', text: 'Pendente' },
                'confirmado': { class: 'info', icon: 'check', text: 'Confirmado' },
                'preparando': { class: 'primary', icon: 'utensils', text: 'Preparando' },
                'pronto': { class: 'secondary', icon: 'check-circle', text: 'Pronto' },
                'saiu_entrega': { class: 'dark', icon: 'truck', text: 'Saiu Entrega' }
            };
            
            const status = statusConfig[delivery.status] || { class: 'secondary', icon: 'question', text: delivery.status };
            
            return `
                <div class="col-md-4 col-lg-2 mb-3">
                    <div class="card border-${status.class}">
                        <div class="card-body text-center p-2">
                            <i class="fas fa-${status.icon} fa-2x text-${status.class} mb-2"></i>
                            <h6 class="card-title mb-1">#${delivery.id}</h6>
                            <span class="badge bg-${status.class} mb-1">
                                ${status.text}
                            </span>
                            <small class="text-muted d-block">${delivery.nome_cliente}</small>
                            <small class="text-muted d-block">${delivery.tempo_estimado_entrega || 0} min</small>
                            <small class="text-success d-block">R$ ${(delivery.taxa_entrega || 0).toFixed(2)}</small>
                            ${delivery.pedido_id ? `<small class="text-info d-block">Pedido #${delivery.pedido_id}</small>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        // Adicionar link para ver mais se houver mais entregas
        if (deliveries.length > 6) {
            container.innerHTML += `
                <div class="col-12 text-center mt-2">
                    <a href="/deliveries" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Ver mais ${deliveries.length - 6} entregas
                    </a>
                </div>
            `;
        }
    }

    // Atualizar status do delivery (função para botão)
    function atualizarStatusDelivery() {
        const btn = document.querySelector('button[onclick="atualizarStatusDelivery()"]');
        const originalHtml = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><br>Atualizando...';
        btn.disabled = true;
        
        // Simular carregamento e recarregar dados
        setTimeout(() => {
            carregarEstatisticasDelivery();
            
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            
            // Feedback visual de sucesso
            const badge = document.createElement('span');
            badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success';
            badge.style.fontSize = '0.6em';
            badge.innerHTML = '<i class="fas fa-check"></i>';
            btn.style.position = 'relative';
            btn.appendChild(badge);
            
            setTimeout(() => badge.remove(), 2000);
        }, 1000);
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        carregarEstatisticas();
        carregarEstatisticasMesas();
        carregarEstatisticasDelivery();
        
        // Botão de status do caixa
        document.getElementById('btnStatusCaixa').addEventListener('click', verificarStatusCaixa);
        
        // Atualizar estatísticas a cada 30 segundos
        setInterval(() => {
            carregarEstatisticas();
            carregarEstatisticasMesas();
            carregarEstatisticasDelivery();
        }, 30000);
        
        console.log('✅ Dashboard carregado com sucesso!');
    });
</script>
@endpush
