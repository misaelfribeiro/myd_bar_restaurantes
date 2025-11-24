<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🪑 Mesas - Modo Garçom</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .mesas-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .mesa-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 3px solid;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .mesa-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, currentColor, transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .mesa-card:hover::before {
            opacity: 1;
        }
        
        .mesa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .mesa-card.disponivel {
            border-color: #10b981;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02));
        }
        
        .mesa-card.ocupada {
            border-color: #ef4444;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(239, 68, 68, 0.02));
        }
        
        .mesa-card.manutencao {
            border-color: #f59e0b;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(245, 158, 11, 0.02));
        }
        
        .mesa-numero {
            font-size: 2.5rem;
            font-weight: 900;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .mesa-numero.disponivel { color: #10b981; }
        .mesa-numero.ocupada { color: #ef4444; }
        .mesa-numero.manutencao { color: #f59e0b; }
        
        .mesa-status {
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .mesa-status.disponivel { color: #10b981; }
        .mesa-status.ocupada { color: #ef4444; }
        .mesa-status.manutencao { color: #f59e0b; }
        
        .mesa-info {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .pedido-info {
            border-left: 4px solid #6366f1;
            padding-left: 15px;
            margin: 10px 0;
        }
        
        .tempo-pedido {
            font-size: 0.85rem;
            color: #6b7280;
        }
        
        .valor-pedido {
            font-size: 1.2rem;
            font-weight: 700;
            color: #10b981;
        }
        
        .garcom-info {
            font-size: 0.9rem;
            color: #8b5cf6;
            font-weight: 600;
        }
        
        .mesa-actions {
            text-align: center;
            margin-top: 15px;
        }
        
        .action-btn {
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            margin: 5px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-ver {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        
        .btn-finalizar {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
          .btn-pedido {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        
        .btn-adicionar {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        .stats-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 600;
        }
        
        .stat-disponivel { color: #10b981; }
        .stat-ocupada { color: #ef4444; }
        .stat-manutencao { color: #f59e0b; }
        .stat-total { color: #6366f1; }
        
        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .filter-btn {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 20px;
            padding: 8px 16px;
            margin: 5px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .filter-btn.active,
        .filter-btn:hover {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            transform: translateY(-2px);
        }
        
        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .mesa-card {
                margin-bottom: 15px;
                padding: 15px;
            }
            
            .mesa-numero {
                font-size: 2rem;
            }
            
            .action-btn {
                font-size: 0.8rem;
                padding: 6px 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-glass">
        <div class="container">
            <a class="navbar-brand" href="{{ route('garcom.dashboard') }}">
                <i class="fas fa-utensils me-2"></i>
                Modo Garçom
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.cardapio') }}">
                            <i class="fas fa-book me-1"></i> Cardápio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('garcom.mesas') }}">
                            <i class="fas fa-chair me-1"></i> Mesas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.meus-pedidos') }}">
                            <i class="fas fa-receipt me-1"></i> Meus Pedidos
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.pedido-rapido') }}">
                            <i class="fas fa-plus-circle me-1"></i> Novo Pedido
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero -->
        <div class="hero-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-chair me-2"></i>
                        Gerenciamento de Mesas
                    </h2>
                    <p class="mb-0">Visualize o status e gerencie todas as mesas do restaurante</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('garcom.dashboard') }}" class="back-btn">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="stats-section">
            <h6 class="mb-3">📊 Status das Mesas</h6>
            <div class="row">                @php
                    $totalMesas = $mesas->count();
                    $mesasOcupadas = $mesas->filter(function($mesa) { return $mesa->pedidos->count() > 0; })->count();
                    $mesasDisponiveis = $mesas->filter(function($mesa) { return $mesa->pedidos->count() == 0; })->count();
                    $mesasManutencao = 0; // Não temos status de manutenção na estrutura atual
                @endphp
                
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number stat-total">{{ $totalMesas }}</div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number stat-disponivel">{{ $mesasDisponiveis }}</div>
                        <div class="stat-label">Disponíveis</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number stat-ocupada">{{ $mesasOcupadas }}</div>
                        <div class="stat-label">Ocupadas</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number stat-manutencao">{{ $mesasManutencao }}</div>
                        <div class="stat-label">Manutenção</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-section">
            <h6 class="mb-3">🔍 Filtrar Mesas</h6>
            <div class="text-center">
                <button class="filter-btn active" onclick="filtrarMesas('todas')">
                    <i class="fas fa-list me-1"></i> Todas
                </button>
                <button class="filter-btn" onclick="filtrarMesas('disponivel')">
                    <i class="fas fa-check-circle me-1"></i> Disponíveis
                </button>
                <button class="filter-btn" onclick="filtrarMesas('ocupada')">
                    <i class="fas fa-user-friends me-1"></i> Ocupadas
                </button>
                <button class="filter-btn" onclick="filtrarMesas('manutencao')">
                    <i class="fas fa-tools me-1"></i> Manutenção
                </button>
            </div>
        </div>

        <!-- Mesas -->
        <div class="mesas-container">
            <h5 class="mb-4">
                <i class="fas fa-chair me-2"></i>
                Layout do Restaurante
            </h5>
              <div class="row" id="mesas-grid">
                @forelse($mesas as $mesa)
                <div class="col-md-4 col-lg-3 mesa-item" data-status="@if($mesa->pedidos->count() > 0)ocupada @else disponivel @endif">
                    <div class="mesa-card @if($mesa->pedidos->count() > 0)ocupada @else disponivel @endif">
                        <div class="mesa-numero @if($mesa->pedidos->count() > 0)ocupada @else disponivel @endif">
                            {{ $mesa->identificador }}
                        </div>
                        
                        <div class="mesa-status @if($mesa->pedidos->count() > 0)ocupada @else disponivel @endif">
                            @if($mesa->pedidos->count() > 0)
                                <i class="fas fa-user-friends me-1"></i> Ocupada
                            @else
                                <i class="fas fa-check-circle me-1"></i> Disponível
                            @endif
                        </div>                        @if($mesa->pedidos->count() > 0)
                            @php $pedido = $mesa->pedidos->first(); @endphp
                            <div class="mesa-info">
                                <div class="pedido-info">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>Pedido #{{ $pedido->id }}</strong>
                                            <div class="tempo-pedido">
                                                <i class="fas fa-clock me-1"></i>
                                                Há {{ $pedido->created_at->diffForHumans(null, true) }}
                                            </div>
                                        </div>
                                        <div class="valor-pedido">
                                            R$ {{ number_format($pedido->total, 2, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="garcom-info mt-2">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $pedido->usuario->nome }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mesa-actions">
                            @if($mesa->pedidos->count() == 0)
                                <a href="{{ route('garcom.pedido-rapido') }}?mesa={{ $mesa->id }}" class="action-btn btn-pedido">
                                    <i class="fas fa-plus me-1"></i> Novo Pedido
                                </a>                            @else
                                <a href="{{ route('garcom.pedidos.show', $mesa->pedidos->first()->id) }}" class="action-btn btn-ver">
                                    <i class="fas fa-eye me-1"></i> Ver Pedido
                                </a>
                                
                                <a href="{{ route('garcom.pedido-rapido.adicionar') }}?mesa={{ $mesa->id }}&pedido={{ $mesa->pedidos->first()->id }}" class="action-btn btn-adicionar">
                                    <i class="fas fa-plus me-1"></i> Adicionar Itens
                                </a>
                                
                                @if($mesa->pedidos->first()->usuario_id == (Auth::id() ?? 1))
                                    <button class="action-btn btn-finalizar" data-mesa-id="{{ $mesa->id }}" onclick="finalizarMesa(this.dataset.mesaId)">
                                        <i class="fas fa-check me-1"></i> Finalizar
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-chair fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma mesa cadastrada</h5>
                        <p class="text-muted">Entre em contato com o administrador para cadastrar mesas.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function filtrarMesas(status) {
            const mesaItems = document.querySelectorAll('.mesa-item');
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            // Atualizar botões ativos
            filterButtons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filtrar mesas
            mesaItems.forEach(item => {
                if (status === 'todas' || item.dataset.status === status) {
                    item.style.display = 'block';
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.style.transition = 'opacity 0.3s ease';
                        item.style.opacity = '1';
                    }, 100);
                } else {
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 300);
                }
            });
        }

    // =============================
    // FUNÇÃO GLOBAL PARA FINALIZAR MESA
// =============================
// FUNÇÃO GLOBAL PARA FINALIZAR MESA
function finalizarMesa(mesaId) {
    // Buscar informações básicas da mesa via API
    fetch(`/api/pagamentos-teste/info/mesa/${mesaId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Mesa não encontrada ou sem pedidos para pagamento');
        }
        return response.json();
    })
    .then(data => {
        console.log('📊 Mesa info:', data);
        // SEMPRE abrir o modal, mesmo se não houver pedidos finalizados
        let mesa, pedidos, total;
        if (data.success && data.data) {
            mesa = {
                id: mesaId,
                identificador: data.data.mesa.identificador,
                numero: data.data.mesa.numero,
                capacidade: data.data.mesa.capacidade,
                lugares: data.data.mesa.lugares,
                status: data.data.mesa.status
            };
            pedidos = data.data.pedidos || [];
            total = data.data.total_mesa || 0;
        } else {
            // Dados mínimos se API falhar
            mesa = { id: mesaId, identificador: `Mesa ${mesaId}` };
            pedidos = [];
            total = 0;
        }
        abrirModalPagamento(mesa, pedidos, total);
    })
    .catch(error => {
        console.error('❌ Erro na requisição:', error);
        // Fallback: abrir modal com dados mínimos
        const mesa = { id: mesaId, identificador: `Mesa ${mesaId}` };
        const pedidos = [];
        const total = 0;
        abrirModalPagamento(mesa, pedidos, total);
    });
}
        
        // Função para processamento rápido via API
        function processarPagamentoRapido(mesaId, formaPagamento) {
            console.log('⚡ Processamento rápido:', mesaId, formaPagamento);
            
            let observacoes = 'Pagamento rápido via modo garçom';
            let dadosPagamento = {
                forma_pagamento: formaPagamento,
                observacoes: observacoes
            };
            
            // Para dinheiro, perguntar valor recebido
            if (formaPagamento === 'dinheiro') {
                const valorRecebido = prompt('💵 Digite o valor recebido em dinheiro:');
                if (valorRecebido && !isNaN(valorRecebido)) {
                    dadosPagamento.valor_recebido = parseFloat(valorRecebido);
                } else {
                    alert('❌ Valor inválido');
                    return;
                }
            }
            
            // Mostrar loading
            const btn = document.querySelector(`[data-mesa-id="${mesaId}"]`);
            const originalText = btn ? btn.innerHTML : '';
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processando...';
                btn.disabled = true;
            }
            
            // Chamar API diretamente
            fetch(`/api/pagamentos-teste/mesa/${mesaId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(dadosPagamento)
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Resultado do pagamento:', data);
                
                if (data.success) {
                    let mensagem = `🎉 Mesa finalizada com sucesso!\n\n`;
                    mensagem += `💰 Total processado: R$ ${data.data.total_processado.toFixed(2).replace('.', ',')}\n`;
                    mensagem += `📋 Pedidos pagos: ${data.data.pedidos_processados}\n`;
                    
                    if (data.data.pagamentos && data.data.pagamentos.length > 0) {
                        const pagamento = data.data.pagamentos[0];
                        if (pagamento.troco && pagamento.troco > 0) {
                            mensagem += `💵 Troco: R$ ${pagamento.troco.toFixed(2).replace('.', ',')}\n`;
                        }
                    }
                    
                    mensagem += `\n🚀 Processado via API Unificada`;
                    alert(mensagem);
                    
                    // Recarregar página para atualizar status
                    window.location.reload();
                } else {
                    alert('❌ Erro ao processar pagamento: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('❌ Erro no pagamento:', error);
                alert('❌ Erro ao processar pagamento. Tente novamente.');
            })
            .finally(() => {
                if (btn) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            });
        }
        
        let mesaAtualPagamento = null;
        let pedidosParaPagamento = [];
        
        function abrirModalPagamento(mesa, pedidos, total) {
            console.log('🎯 Abrindo modal de pagamento:', mesa, pedidos, total);
            console.log('🔍 Elementos do modal:', {
                modal: document.getElementById('modalPagamentoMesa'),
                infoMesa: document.getElementById('info-mesa-pagamento'),
                infoPedidos: document.getElementById('info-pedidos-count'),
                infoTotal: document.getElementById('info-total-pagamento')
            });
            
            mesaAtualPagamento = mesa;
            pedidosParaPagamento = pedidos;
            
            // Converter total para número
            const totalNumerico = parseFloat(total);
            
            // Preencher informações básicas da mesa
            document.getElementById('info-mesa-pagamento').textContent = mesa.identificador || `Mesa ${mesa.numero}`;
            document.getElementById('info-pedidos-count').textContent = pedidos.length;
            document.getElementById('info-total-pagamento').textContent = totalNumerico.toFixed(2).replace('.', ',');
            document.getElementById('resumo-valor-pagamento').textContent = totalNumerico.toFixed(2).replace('.', ',');
            
            // Preencher informações adicionais da mesa
            document.getElementById('info-capacidade-mesa').textContent = mesa.capacidade || mesa.lugares || '-';
            
            // Status da mesa baseado nos pedidos
            const statusElement = document.getElementById('info-status-mesa');
            if (pedidos.length > 0) {
                statusElement.textContent = '🔴 Ocupada';
                statusElement.className = 'badge bg-danger';
            } else {
                statusElement.textContent = '🟢 Disponível';
                statusElement.className = 'badge bg-success';
            }
            
            // Tempo desde o primeiro pedido
            const tempoElement = document.getElementById('info-tempo-mesa');
            if (pedidos.length > 0) {
                const primeiroPedido = pedidos.sort((a, b) => new Date(a.created_at) - new Date(b.created_at))[0];
                const tempoDecorrido = calcularTempoDecorrido(primeiroPedido.created_at);
                tempoElement.textContent = tempoDecorrido;
            } else {
                tempoElement.textContent = '-';
            }
            
            // Limpar formulário
            document.getElementById('formPagamentoMesa').reset();
            document.querySelector('input[name="valor_pagamento"]').value = totalNumerico.toFixed(2);
            document.getElementById('campos-dinheiro-mesa').style.display = 'none';
            document.getElementById('resumo-troco-row-mesa').style.display = 'none';
            document.getElementById('resumo-forma-mesa').textContent = '-';
            
            // Mostrar lista de pedidos com mais detalhes
            const listaPedidos = document.getElementById('lista-pedidos-pagamento');
            listaPedidos.innerHTML = '';
            pedidos.forEach(pedido => {
                const item = document.createElement('div');
                item.className = 'list-group-item d-flex justify-content-between align-items-start';
                const totalPedido = parseFloat(pedido.total);
                const tempoDecorrido = calcularTempoDecorrido(pedido.created_at);
                item.innerHTML = `
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">📋 Pedido #${pedido.id} <span class="badge bg-secondary ms-1">${pedido.status}</span></div>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>${tempoDecorrido}
                            <i class="fas fa-user ms-2 me-1"></i>${pedido.usuario ? pedido.usuario.nome : 'Usuário'}
                        </small>
                    </div>
                    <span class="badge bg-primary rounded-pill">R$ ${totalPedido.toFixed(2).replace('.', ',')}</span>
                `;
                listaPedidos.appendChild(item);
            });
            
            // Gerar cupom para impressão
            gerarCupomImpressao(mesa, pedidos, totalNumerico);
            
            // Chamar função para finalizar e abrir modal
            finalizarModalPagamento(mesa);
        }
        
        // Função para gerar cupom de impressão
        function gerarCupomImpressao(mesa, pedidos, total) {
            const cupomDiv = document.getElementById('cupom-impressao');
            const agora = new Date();
            
            let cupom = `
                <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 8px; margin-bottom: 8px;">
                    <strong>MyD BAR & RESTAURANTE</strong><br>
                    <small>Via do Cliente</small><br>
                    <small>${agora.toLocaleDateString('pt-BR')} ${agora.toLocaleTimeString('pt-BR')}</small>
                </div>
                
                <div style="margin-bottom: 8px;">
                    <strong>MESA:</strong> ${mesa.identificador || mesa.numero}<br>
                    <strong>CAPACIDADE:</strong> ${mesa.capacidade || mesa.lugares || '-'} pessoas<br>
                    <strong>GARCOM:</strong> ${pedidos.length > 0 ? (pedidos[0].usuario?.nome || 'N/A') : 'N/A'}<br>
                    ${pedidos.length > 0 ? `<strong>TEMPO:</strong> ${calcularTempoDecorrido(pedidos[0].created_at)}` : ''}
                </div>
                
                <div style="border-top: 1px dashed #000; padding-top: 8px; margin-top: 8px;">
                    <strong>ITENS CONSUMIDOS:</strong>
                </div>
            `;
            
            // Buscar itens detalhados dos pedidos
            buscarItensPedidos(pedidos).then(itensDetalhados => {
                let totalItens = 0;
                
                itensDetalhados.forEach(item => {
                    const subtotal = item.subtotal || (item.quantidade * item.preco_unitario);
                    totalItens += subtotal;
                    
                    cupom += `
                        <div style="display: flex; justify-content: space-between; align-items: start; margin: 4px 0; font-size: 10px;">
                            <div style="flex: 1;">
                                <strong>${item.produto_nome}</strong><br>
                                <small>${item.quantidade}x R$ ${item.preco_unitario.toFixed(2).replace('.', ',')}</small>
                                ${item.observacoes ? `<br><small style="font-style: italic;">Obs: ${item.observacoes}</small>` : ''}
                            </div>
                            <div style="text-align: right; white-space: nowrap; margin-left: 8px;">
                                <strong>R$ ${subtotal.toFixed(2).replace('.', ',')}</strong>
                            </div>
                        </div>
                    `;
                });
                
                cupom += `
                    <div style="border-top: 1px dashed #000; margin-top: 8px; padding-top: 8px;">
                        <div style="display: flex; justify-content: space-between; font-weight: bold;">
                            <span>TOTAL GERAL:</span>
                            <span>R$ ${total.toFixed(2).replace('.', ',')}</span>
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 12px; font-size: 10px;">
                        <div style="border-top: 1px dashed #000; padding-top: 8px;">
                            Obrigado pela preferência!<br>
                            <small>Volte sempre!</small>
                        </div>
                    </div>
                `;
                
                cupomDiv.innerHTML = cupom;
            }).catch(error => {
                console.error('Erro ao carregar itens:', error);
                cupom += `
                    <div style="color: #666; text-align: center; padding: 10px;">
                        <em>Itens não disponíveis para impressão</em>
                    </div>
                    
                    <div style="border-top: 1px dashed #000; margin-top: 8px; padding-top: 8px;">
                        <div style="display: flex; justify-content: space-between; font-weight: bold;">
                            <span>TOTAL GERAL:</span>
                            <span>R$ ${total.toFixed(2).replace('.', ',')}</span>
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 12px; font-size: 10px;">
                        <div style="border-top: 1px dashed #000; padding-top: 8px;">
                            Obrigado pela preferência!<br>
                            <small>Volte sempre!</small>
                        </div>
                    </div>
                `;
                cupomDiv.innerHTML = cupom;
            });
        }
        
        // Função para buscar itens detalhados dos pedidos
        async function buscarItensPedidos(pedidos) {
            const itens = [];
            
            for (const pedido of pedidos) {
                try {
                    const response = await fetch(`/api/pedidos/${pedido.id}/itens`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success && data.itens) {
                            // Transformar dados da API para formato esperado
                            const itensTransformados = data.itens.map(item => ({
                                produto_nome: item.produto.nome,
                                quantidade: item.quantidade,
                                preco_unitario: item.preco_unitario,
                                observacoes: item.observacoes,
                                subtotal: item.subtotal
                            }));
                            itens.push(...itensTransformados);
                        }
                    }
                } catch (error) {
                    console.error(`Erro ao buscar itens do pedido ${pedido.id}:`, error);
                }
            }
            
            return itens;
        }
        
        // Função para imprimir cupom
        function imprimirCupom() {
            const cupomContent = document.getElementById('cupom-impressao').innerHTML;
            
            const printWindow = window.open('', '', 'width=300,height=600');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Via do Cliente</title>
                    <style>
                        @media print {
                            body { 
                                font-family: 'Courier New', monospace; 
                                font-size: 12px; 
                                line-height: 1.2;
                                margin: 0; 
                                padding: 5px;
                                width: 280px;
                            }
                            @page { 
                                size: 80mm auto; 
                                margin: 0; 
                            }
                        }
                        body { 
                            font-family: 'Courier New', monospace; 
                            font-size: 12px; 
                            line-height: 1.2;
                            margin: 0; 
                            padding: 5px;
                            width: 280px;
                        }
                    </style>
                </head>
                <body>
                    ${cupomContent}
                </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.focus();
            
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }
        
        // Função auxiliar para calcular tempo decorrido
        function calcularTempoDecorrido(dataString) {
            const agora = new Date();
            const data = new Date(dataString);
            const diffMs = agora - data;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHoras = Math.floor(diffMins / 60);
            const diffDias = Math.floor(diffHoras / 24);
            
            if (diffDias > 0) {
                return `${diffDias} dia${diffDias > 1 ? 's' : ''}`;
            } else if (diffHoras > 0) {
                return `${diffHoras}h ${diffMins % 60}min`;
            } else if (diffMins > 0) {
                return `${diffMins} min`;
            } else {
                return 'agora';
            }
        }
        
        // Definir action do formulário e abrir modal
        function finalizarModalPagamento(mesa) {
            document.getElementById('formPagamentoMesa').action = '/garcom/processar-pagamento-mesa/' + mesa.id;
            
            // Abrir modal
            try {
                new bootstrap.Modal(document.getElementById('modalPagamentoMesa')).show();
            } catch (error) {
                console.error('Erro ao abrir modal:', error);
                alert('Erro ao abrir modal de pagamento.');
            }
        }

        // Atualização automática a cada minuto
        setInterval(() => {
            window.location.reload();
        }, 60000);

        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const mesaCards = document.querySelectorAll('.mesa-card');
            mesaCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });        });
    </script>

    <!-- Modal de Pagamento da Mesa -->
    <div class="modal fade" id="modalPagamentoMesa" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header bg-success text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title">
                        <i class="fas fa-cash-register me-2"></i>
                        Finalizar Mesa - Receber Pagamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Coluna Esquerda - Informações da Mesa e Pagamento -->
                        <div class="col-lg-7">
                            <form id="formPagamentoMesa" method="POST">
                                @csrf
                        
                        <!-- Informações da Mesa -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>🪑 Mesa:</strong> <span id="info-mesa-pagamento"></span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>👥 Capacidade:</strong> <span id="info-capacidade-mesa">-</span> lugares
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>📋 Pedidos:</strong> <span id="info-pedidos-count"></span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>💰 Total:</strong> R$ <span id="info-total-pagamento">0,00</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>📊 Status:</strong> <span id="info-status-mesa" class="badge">-</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>🕐 Tempo:</strong> <span id="info-tempo-mesa">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Pedidos -->
                        <div class="mb-3">
                            <h6>📋 Pedidos incluídos no pagamento:</h6>
                            <div class="list-group" id="lista-pedidos-pagamento">
                                <!-- Preenchido dinamicamente -->
                            </div>
                        </div>

                        <!-- Tipo de Pagamento -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Pagamento</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo_pagamento" id="pagamento_unico" value="unico" checked onchange="alterarTipoPagamento('unico')">
                                <label class="btn btn-outline-primary" for="pagamento_unico">
                                    <i class="fas fa-money-bill-wave me-1"></i> Pagamento Único
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipo_pagamento" id="pagamento_multiplo" value="multiplo" onchange="alterarTipoPagamento('multiplo')">
                                <label class="btn btn-outline-success" for="pagamento_multiplo">
                                    <i class="fas fa-credit-card me-1"></i> Pagamento Múltiplo
                                </label>
                            </div>
                        </div>

                        <!-- PAGAMENTO ÚNICO -->
                        <div id="area-pagamento-unico">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Forma de Pagamento *</label>
                                    <select name="forma_pagamento" id="forma_pagamento_unico" class="form-select" onchange="alterarFormaPagamentoMesa(this.value)">
                                        <option value="">Selecione a forma de pagamento...</option>
                                        <option value="dinheiro">💵 Dinheiro</option>
                                        <option value="cartao_credito">💳 Cartão de Crédito</option>
                                        <option value="cartao_debito">💳 Cartão de Débito</option>
                                        <option value="pix">📱 PIX</option>
                                        <option value="vale_refeicao">🍽️ Vale Refeição</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Valor Total</label>
                                    <input type="number" name="valor_pagamento" id="valor_pagamento_unico" class="form-control" step="0.01" min="0" readonly style="background-color: #e9ecef; font-weight: bold;">
                                </div>
                            </div>
                        </div>

                        <!-- PAGAMENTO MÚLTIPLO -->
                        <div id="area-pagamento-multiplo" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Pagamento Dividido:</strong> Adicione as formas de pagamento e os valores. O total deve corresponder ao valor da conta.
                            </div>
                            
                            <div id="lista-pagamentos-multiplos" class="mb-3">
                                <!-- Pagamentos múltiplos serão adicionados aqui -->
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold">Forma de Pagamento</label>
                                    <select id="forma_multiplo_select" class="form-select">
                                        <option value="">Selecione...</option>
                                        <option value="dinheiro">💵 Dinheiro</option>
                                        <option value="cartao_credito">💳 Cartão de Crédito</option>
                                        <option value="cartao_debito">💳 Cartão de Débito</option>
                                        <option value="pix">📱 PIX</option>
                                        <option value="vale_refeicao">🍽️ Vale Refeição</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold">Valor</label>
                                    <input type="number" id="valor_multiplo_input" class="form-control" step="0.01" min="0.01" placeholder="0,00">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" onclick="adicionarPagamentoMultiplo()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-secondary mb-0">
                                        <strong>Total da Conta:</strong> R$ <span id="total-conta-multiplo">0,00</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert mb-0" id="alert-total-multiplo">
                                        <strong>Total Pago:</strong> R$ <span id="total-pago-multiplo">0,00</span><br>
                                        <strong>Faltante:</strong> R$ <span id="total-faltante-multiplo">0,00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos específicos para Dinheiro -->
                        <div id="campos-dinheiro-mesa" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Valor Recebido *</label>
                                    <input type="number" name="valor_recebido" class="form-control" step="0.01" min="0" placeholder="Valor recebido do cliente" oninput="calcularTrocoMesa()">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Troco</label>
                                    <input type="text" id="troco-calculado-mesa" class="form-control" readonly style="background-color: #e9ecef; font-weight: bold; color: #dc3545;">
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" class="form-control" rows="2" placeholder="Observações sobre o pagamento da mesa (opcional)"></textarea>
                        </div>
                        </div>
                        
                        <!-- Coluna Direita - Cupom para Impressão -->
                        <div class="col-lg-5">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">🧾 Via do Cliente</h6>
                                    <button type="button" class="btn btn-light btn-sm" onclick="imprimirCupom()">
                                        <i class="fas fa-print me-1"></i> Imprimir
                                    </button>
                                </div>
                                <div class="card-body p-2" style="font-family: 'Courier New', monospace; font-size: 11px; background-color: #f8f9fa; max-height: 500px; overflow-y: auto;">
                                    <div id="cupom-impressao" style="width: 280px; margin: 0 auto;">
                                        <!-- Cupom será gerado aqui -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>
                    
                    <!-- Resumo do Pagamento -->
                    <div class="alert alert-light mt-3">
                        <h6 class="mb-2">💰 Resumo do Pagamento</h6>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Valor Total:</small>
                                <div class="fw-bold">R$ <span id="resumo-valor-pagamento">0,00</span></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Forma de Pagamento:</small>
                                <div class="fw-bold" id="resumo-forma-mesa">-</div>
                            </div>
                        </div>
                        <div class="row mt-2" id="resumo-troco-row-mesa" style="display: none;">
                            <div class="col-6">
                                <small class="text-muted">Valor Recebido:</small>
                                <div class="fw-bold">R$ <span id="resumo-recebido-mesa">0,00</span></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Troco:</small>
                                <div class="fw-bold text-danger">R$ <span id="resumo-troco-mesa">0,00</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 25px;">
                            <i class="fas fa-times me-1"></i>
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-success" onclick="confirmarPagamentoMesa()" style="border-radius: 25px;">
                            <i class="fas fa-check me-1"></i>
                            Finalizar e Receber
                        </button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>

    <script>
        // SISTEMA DE PAGAMENTO DA MESA
        let pagamentosMultiplos = [];
        
        // Alternar entre pagamento único e múltiplo
        function alterarTipoPagamento(tipo) {
            const areaUnico = document.getElementById('area-pagamento-unico');
            const areaMultiplo = document.getElementById('area-pagamento-multiplo');
            const camposDinheiro = document.getElementById('campos-dinheiro-mesa');
            
            if (tipo === 'unico') {
                areaUnico.style.display = 'block';
                areaMultiplo.style.display = 'none';
                camposDinheiro.style.display = 'none';
                pagamentosMultiplos = [];
            } else {
                areaUnico.style.display = 'none';
                areaMultiplo.style.display = 'block';
                camposDinheiro.style.display = 'none';
                
                // Atualizar total da conta no modo múltiplo
                const valorTotal = parseFloat(document.getElementById('valor_pagamento_unico').value) || 0;
                document.getElementById('total-conta-multiplo').textContent = valorTotal.toFixed(2).replace('.', ',');
                atualizarTotaisMultiplos();
            }
        }
        
        // Adicionar pagamento múltiplo
        function adicionarPagamentoMultiplo() {
            const formaSelect = document.getElementById('forma_multiplo_select');
            const valorInput = document.getElementById('valor_multiplo_input');
            
            const forma = formaSelect.value;
            const valor = parseFloat(valorInput.value);
            
            if (!forma) {
                alert('Selecione uma forma de pagamento');
                return;
            }
            
            if (!valor || valor <= 0) {
                alert('Digite um valor válido');
                return;
            }
            
            const formasTexto = {
                'dinheiro': '💵 Dinheiro',
                'cartao_credito': '💳 Cartão de Crédito',
                'cartao_debito': '💳 Cartão de Débito',
                'pix': '📱 PIX',
                'vale_refeicao': '🍽️ Vale Refeição'
            };
            
            pagamentosMultiplos.push({
                forma_pagamento: forma,
                valor: valor,
                texto: formasTexto[forma]
            });
            
            // Limpar campos
            formaSelect.value = '';
            valorInput.value = '';
            
            // Atualizar lista
            renderizarPagamentosMultiplos();
            atualizarTotaisMultiplos();
        }
        
        // Renderizar lista de pagamentos múltiplos
        function renderizarPagamentosMultiplos() {
            const lista = document.getElementById('lista-pagamentos-multiplos');
            
            if (pagamentosMultiplos.length === 0) {
                lista.innerHTML = '<div class="alert alert-secondary">Nenhum pagamento adicionado ainda.</div>';
                return;
            }
            
            let html = '<div class="list-group">';
            pagamentosMultiplos.forEach((pag, index) => {
                html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${pag.texto}</strong>
                        </div>
                        <div>
                            <span class="badge bg-success me-2">R$ ${pag.valor.toFixed(2).replace('.', ',')}</span>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removerPagamentoMultiplo(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            lista.innerHTML = html;
        }
        
        // Remover pagamento múltiplo
        function removerPagamentoMultiplo(index) {
            pagamentosMultiplos.splice(index, 1);
            renderizarPagamentosMultiplos();
            atualizarTotaisMultiplos();
        }
        
        // Atualizar totais de pagamentos múltiplos
        function atualizarTotaisMultiplos() {
            const totalConta = parseFloat(document.getElementById('total-conta-multiplo').textContent.replace(',', '.')) || 0;
            const totalPago = pagamentosMultiplos.reduce((sum, pag) => sum + pag.valor, 0);
            const faltante = totalConta - totalPago;
            
            document.getElementById('total-pago-multiplo').textContent = totalPago.toFixed(2).replace('.', ',');
            document.getElementById('total-faltante-multiplo').textContent = Math.abs(faltante).toFixed(2).replace('.', ',');
            
            const alertTotal = document.getElementById('alert-total-multiplo');
            if (faltante > 0.01) {
                alertTotal.className = 'alert alert-warning mb-0';
            } else if (faltante < -0.01) {
                alertTotal.className = 'alert alert-danger mb-0';
            } else {
                alertTotal.className = 'alert alert-success mb-0';
            }
        }
        
        function alterarFormaPagamentoMesa(forma) {
            const camposDinheiro = document.getElementById('campos-dinheiro-mesa');
            const resumoTrocoRow = document.getElementById('resumo-troco-row-mesa');
            const resumoForma = document.getElementById('resumo-forma-mesa');
            
            // Atualizar resumo da forma
            const formasTexto = {
                'dinheiro': '💵 Dinheiro',
                'cartao_credito': '💳 Cartão de Crédito',
                'cartao_debito': '💳 Cartão de Débito',
                'pix': '📱 PIX',
                'vale_refeicao': '🍽️ Vale Refeição'
            };
            resumoForma.textContent = formasTexto[forma] || '-';
            
            if (forma === 'dinheiro') {
                camposDinheiro.style.display = 'block';
                resumoTrocoRow.style.display = 'flex';
            } else {
                camposDinheiro.style.display = 'none';
                resumoTrocoRow.style.display = 'none';
                document.querySelector('input[name="valor_recebido"]').value = '';
                calcularTrocoMesa();
            }
        }
        
        function calcularTrocoMesa() {
            const valorRecebido = parseFloat(document.querySelector('input[name="valor_recebido"]').value) || 0;
            const valorTotal = parseFloat(document.querySelector('input[name="valor_pagamento"]').value) || 0;
            const troco = valorRecebido - valorTotal;
            
            const trocoCalculado = document.getElementById('troco-calculado-mesa');
            const resumoRecebido = document.getElementById('resumo-recebido-mesa');
            const resumoTroco = document.getElementById('resumo-troco-mesa');
            
            if (valorRecebido > 0) {
                resumoRecebido.textContent = valorRecebido.toFixed(2).replace('.', ',');
            } else {
                resumoRecebido.textContent = '0,00';
            }
            
            if (troco >= 0) {
                trocoCalculado.value = 'R$ ' + troco.toFixed(2).replace('.', ',');
                trocoCalculado.style.color = '#dc3545';
                resumoTroco.textContent = troco.toFixed(2).replace('.', ',');
            } else {
                trocoCalculado.value = 'Valor insuficiente!';
                trocoCalculado.style.color = '#dc3545';
                resumoTroco.textContent = '0,00';
            }
        }        function confirmarPagamentoMesa() {
            const form = document.getElementById('formPagamentoMesa');
            const formData = new FormData(form);
            const tipoPagamento = document.querySelector('input[name="tipo_pagamento"]:checked').value;
            
            let dadosAPI = {};
            let valorTotal = 0;
            let mensagemConfirmacao = '';
            
            if (tipoPagamento === 'multiplo') {
                // PAGAMENTO MÚLTIPLO
                if (pagamentosMultiplos.length === 0) {
                    alert('Adicione pelo menos uma forma de pagamento.');
                    return;
                }
                
                const totalConta = parseFloat(document.getElementById('total-conta-multiplo').textContent.replace(',', '.'));
                const totalPago = pagamentosMultiplos.reduce((sum, pag) => sum + pag.valor, 0);
                
                if (Math.abs(totalConta - totalPago) > 0.01) {
                    alert(`O total pago (R$ ${totalPago.toFixed(2).replace('.', ',')}) não confere com o total da conta (R$ ${totalConta.toFixed(2).replace('.', ',')}).`);
                    return;
                }
                
                valorTotal = totalConta;
                dadosAPI.multiplos_pagamentos = JSON.stringify(pagamentosMultiplos);
                dadosAPI.observacoes = formData.get('observacoes') || 'Pagamento múltiplo via modo garçom';
                
                mensagemConfirmacao = `Finalizar mesa com pagamento múltiplo?\n\nTotal: R$ ${valorTotal.toFixed(2).replace('.', ',')}\n`;
                pagamentosMultiplos.forEach(pag => {
                    mensagemConfirmacao += `${pag.texto}: R$ ${pag.valor.toFixed(2).replace('.', ',')}\n`;
                });
                
            } else {
                // PAGAMENTO ÚNICO
                const forma = formData.get('forma_pagamento');
                if (!forma) {
                    alert('Por favor, selecione uma forma de pagamento.');
                    return;
                }
                
                valorTotal = parseFloat(formData.get('valor_pagamento')) || 0;
                
                if (forma === 'dinheiro') {
                    const valorRecebido = parseFloat(formData.get('valor_recebido')) || 0;
                    
                    if (valorRecebido < valorTotal) {
                        alert('O valor recebido deve ser maior ou igual ao valor total.');
                        return;
                    }
                    dadosAPI.valor_recebido = valorRecebido;
                }
                
                dadosAPI.forma_pagamento = forma;
                dadosAPI.valor = valorTotal;
                dadosAPI.observacoes = formData.get('observacoes') || 'Pagamento via modo garçom';
                
                const formasTexto = {
                    'dinheiro': '💵 Dinheiro',
                    'cartao_credito': '💳 Cartão de Crédito',
                    'cartao_debito': '💳 Cartão de Débito',
                    'pix': '📱 PIX',
                    'vale_refeicao': '🍽️ Vale Refeição'
                };
                
                mensagemConfirmacao = `Finalizar mesa e confirmar pagamento de R$ ${valorTotal.toFixed(2).replace('.', ',')} via ${formasTexto[forma]}?\n\nEsta ação finalizará todos os pedidos da mesa.`;
            }
            
            if (!confirm(mensagemConfirmacao)) {
                return;
            }
            
            // Mostrar loading
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processando...';
            btn.disabled = true;
            
            // Chamar API unificada de pagamentos
            fetch(`/api/pagamentos-teste/mesa/${mesaAtualPagamento.id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(dadosAPI)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta da API');
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ [GARÇOM] Sucesso:', data);
                
                if (data.success) {
                    // Fechar modal
                    bootstrap.Modal.getInstance(document.getElementById('modalPagamentoMesa')).hide();
                    
                    // Mostrar mensagem de sucesso
                    let mensagem = '🎉 Mesa finalizada e pagamento processado com sucesso!';
                    
                    if (data.data) {
                        mensagem += `\n\n💰 Total: R$ ${data.data.total_processado.toFixed(2).replace('.', ',')}`;
                        mensagem += `\n📋 Pedidos: ${data.data.pedidos_processados}`;
                        
                        if (tipoPagamento === 'multiplo') {
                            mensagem += `\n\n✅ Pagamento Múltiplo processado com sucesso!`;
                        }
                    }
                    
                    alert(mensagem);
                    
                    // Recarregar página
                    window.location.reload();
                } else {
                    alert('Erro ao processar pagamento: ' + (data.message || 'Erro desconhecido'));
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('❌ [GARÇOM] Erro:', error);
                alert('Erro ao processar pagamento. Verifique sua conexão e tente novamente.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>

</body>
</html>
