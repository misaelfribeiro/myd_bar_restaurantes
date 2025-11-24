@extends('layouts.app')

@section('title', 'Dashboard do Caixa')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-cash-register me-2"></i>
                    Dashboard do Caixa
                </h1>
                <p class="page-subtitle">Gerencie pagamentos e movimentações financeiras</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="atualizarDados()">
                    <i class="fas fa-sync-alt me-2" id="refresh-icon"></i>
                    Atualizar
                </button>
                @if($caixaAberto)
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalFecharCaixa">
                    <i class="fas fa-lock me-2"></i>
                    Fechar Caixa
                </button>
                @else
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAbrirCaixa">
                    <i class="fas fa-unlock me-2"></i>
                    Abrir Caixa
                </button>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($caixaAberto)
    <!-- Informações do Caixa Aberto -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="stat-icon-large bg-success">
                        <i class="fas fa-cash-register"></i>
                    </div>
                </div>
                <div class="col">
                    <h5 class="mb-1">
                        <span class="badge bg-success">
                            <i class="fas fa-unlock me-1"></i>
                            Caixa Aberto
                        </span>
                    </h5>
                    <p class="text-muted mb-0">
                        Operador: <strong>{{ $caixaAberto->usuario->nome ?? 'Sistema' }}</strong> |
                        Abertura: <strong>{{ $caixaAberto->data_abertura->format('d/m/Y H:i') }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-value" id="total-vendas">
                        R$ {{ number_format($totaisCaixa['total_vendas'], 2, ',', '.') }}
                    </div>
                    <div class="stats-label">Total de Vendas</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-value" id="quantidade-vendas">
                        {{ $totaisCaixa['quantidade_vendas'] }}
                    </div>
                    <div class="stats-label">Vendas Realizadas</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-value" id="total-troco">
                        R$ {{ number_format($totaisCaixa['total_troco'], 2, ',', '.') }}
                    </div>
                    <div class="stats-label">Troco Dado</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon bg-danger">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-value" id="pedidos-pendentes">
                        {{ $pedidosPendentes->count() }}
                    </div>
                    <div class="stats-label">Pedidos Pendentes</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formas de Pagamento -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-credit-card me-2"></i>
                Resumo por Forma de Pagamento
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3" id="formas-pagamento">
                @foreach(['dinheiro' => 'Dinheiro', 'cartao_credito' => 'Cartão Crédito', 'cartao_debito' => 'Cartão Débito', 'pix' => 'PIX', 'vale_refeicao' => 'Vale Refeição'] as $forma => $nome)
                    @php
                        $dados = $totaisCaixa['por_forma_pagamento'][$forma] ?? ['quantidade' => 0, 'total' => 0];
                    @endphp
                    <div class="col-md-2 col-6">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <h6 class="mb-2">{{ $nome }}</h6>
                                <p class="mb-1"><strong>{{ $dados['quantidade'] }}</strong> vendas</p>
                                <p class="mb-0 text-success">
                                    <strong>R$ {{ number_format($dados['total'], 2, ',', '.') }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Pedidos Pendentes -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-hourglass-half me-2"></i>
                Pedidos Aguardando Pagamento ({{ $pedidosPendentes->count() }})
            </h5>
        </div>
        <div class="card-body">
            @if($pedidosPendentes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Local</th>
                                <th>Itens</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="pedidos-pendentes-tbody">
                            @foreach($pedidosPendentes as $pedido)
                            <tr>
                                <td>
                                    <strong>#{{ $pedido->id }}</strong><br>
                                    <small class="text-muted">{{ $pedido->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($pedido->mesa)
                                        <span class="badge bg-info">
                                            {{ $pedido->mesa->identificador }}
                                        </span>
                                    @elseif($pedido->delivery)
                                        <span class="badge bg-success">
                                            <i class="fas fa-truck me-1"></i>
                                            Delivery
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-takeout-box me-1"></i>
                                            Balcão
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        @foreach($pedido->itens->take(2) as $item)
                                            {{ $item->quantidade }}x {{ $item->produto->nome }}<br>
                                        @endforeach
                                        @if($pedido->itens->count() > 2)
                                            <span class="text-muted">+{{ $pedido->itens->count() - 2 }} mais...</span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <strong class="text-success">
                                        R$ {{ number_format($pedido->total, 2, ',', '.') }}
                                    </strong>
                                </td>
                                <td>
                                    @php
                                        $badges = [
                                            'finalizado' => 'success',
                                            'entregue' => 'success',
                                            'pronto' => 'info',
                                            'em_preparo' => 'warning',
                                            'pendente' => 'secondary',
                                            'aberto' => 'primary'
                                        ];
                                        $badge = $badges[$pedido->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('caixa.recebimento', $pedido) }}" 
                                       class="btn btn-sm {{ in_array($pedido->status, ['finalizado', 'entregue']) ? 'btn-success' : 'btn-primary' }}">
                                        <i class="fas fa-money-bill me-1"></i>
                                        {{ in_array($pedido->status, ['finalizado', 'entregue']) ? 'Receber Pagamento' : 'Finalizar e Receber' }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">Nenhum pedido pendente no momento</p>
                </div>
            @endif
        </div>
    </div>

    @else
    <!-- Caixa Fechado -->
    <div class="card shadow-sm text-center">
        <div class="card-body py-5">
            <i class="fas fa-lock text-danger" style="font-size: 4rem;"></i>
            <h3 class="mt-4">Caixa Fechado</h3>
            <p class="text-muted">Abra o caixa para começar a receber pagamentos</p>
            <button class="btn btn-success btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#modalAbrirCaixa">
                <i class="fas fa-unlock me-2"></i>
                Abrir Caixa
            </button>
        </div>
    </div>
    @endif
</div>

<!-- Modal Fechar Caixa -->
<div class="modal fade" id="modalFecharCaixa" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-lock me-2"></i>
                    Fechamento de Caixa - Resumo Detalhado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('caixa.fechar') }}" method="POST" id="formFecharCaixa">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção!</strong> Confira todos os valores antes de confirmar o fechamento do caixa.
                    </div>

                    <!-- Informações do Caixa -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações do Caixa</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Operador:</strong> {{ $caixaAberto->usuario->nome ?? 'Sistema' }}</p>
                                    <p class="mb-2"><strong>Data/Hora Abertura:</strong> {{ $caixaAberto->data_abertura->format('d/m/Y H:i:s') }}</p>
                                    <p class="mb-0"><strong>Saldo Inicial:</strong> R$ {{ number_format($caixaAberto->saldo_inicial, 2, ',', '.') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Data/Hora Fechamento:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
                                    <p class="mb-0"><strong>Tempo Aberto:</strong> {{ $caixaAberto->data_abertura->diffForHumans(null, true) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumo de Vendas -->
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Resumo de Vendas</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-primary mb-1">{{ $totaisCaixa['quantidade_vendas'] }}</h4>
                                        <small class="text-muted">Vendas Realizadas</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-success mb-1">R$ {{ number_format($totaisCaixa['total_vendas'], 2, ',', '.') }}</h4>
                                        <small class="text-muted">Total de Vendas</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-warning mb-1">R$ {{ number_format($totaisCaixa['total_troco'], 2, ',', '.') }}</h4>
                                        <small class="text-muted">Troco Dado</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-info mb-1">R$ {{ number_format($totaisCaixa['total_recebido'], 2, ',', '.') }}</h4>
                                        <small class="text-muted">Total Recebido</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formas de Pagamento -->
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Detalhamento por Forma de Pagamento</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Forma de Pagamento</th>
                                            <th class="text-center">Quantidade</th>
                                            <th class="text-end">Valor Total</th>
                                            <th class="text-end">Percentual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $formasPagamento = [
                                                'dinheiro' => ['nome' => 'Dinheiro', 'icone' => 'fa-money-bill-wave', 'cor' => 'success'],
                                                'cartao_credito' => ['nome' => 'Cartão de Crédito', 'icone' => 'fa-credit-card', 'cor' => 'primary'],
                                                'cartao_debito' => ['nome' => 'Cartão de Débito', 'icone' => 'fa-credit-card', 'cor' => 'info'],
                                                'pix' => ['nome' => 'PIX', 'icone' => 'fa-qrcode', 'cor' => 'warning'],
                                                'vale_refeicao' => ['nome' => 'Vale Refeição', 'icone' => 'fa-ticket-alt', 'cor' => 'secondary']
                                            ];
                                        @endphp
                                        @foreach($formasPagamento as $forma => $info)
                                            @php
                                                $dados = $totaisCaixa['por_forma_pagamento'][$forma] ?? ['quantidade' => 0, 'total' => 0];
                                                $percentual = $totaisCaixa['total_vendas'] > 0 ? ($dados['total'] / $totaisCaixa['total_vendas']) * 100 : 0;
                                            @endphp
                                            @if($dados['quantidade'] > 0)
                                            <tr>
                                                <td>
                                                    <i class="fas {{ $info['icone'] }} text-{{ $info['cor'] }} me-2"></i>
                                                    {{ $info['nome'] }}
                                                </td>
                                                <td class="text-center">{{ $dados['quantidade'] }}</td>
                                                <td class="text-end">R$ {{ number_format($dados['total'], 2, ',', '.') }}</td>
                                                <td class="text-end">
                                                    <span class="badge bg-{{ $info['cor'] }}">{{ number_format($percentual, 1) }}%</span>
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td>TOTAL</td>
                                            <td class="text-center">{{ $totaisCaixa['quantidade_vendas'] }}</td>
                                            <td class="text-end">R$ {{ number_format($totaisCaixa['total_vendas'], 2, ',', '.') }}</td>
                                            <td class="text-end">100%</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Saldo Final -->
                    <div class="card mb-3">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Saldo Final do Caixa</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span>Saldo Inicial:</span>
                                        <strong class="text-primary">R$ {{ number_format($caixaAberto->saldo_inicial, 2, ',', '.') }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span>+ Entradas (Dinheiro):</span>
                                        <strong class="text-success">R$ {{ number_format($totaisCaixa['por_forma_pagamento']['dinheiro']['total'] ?? 0, 2, ',', '.') }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span>- Troco Dado:</span>
                                        <strong class="text-danger">R$ {{ number_format($totaisCaixa['total_troco'], 2, ',', '.') }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2">
                                        <span class="fs-5"><strong>Saldo em Dinheiro:</strong></span>
                                        <strong class="fs-4 text-success">
                                            R$ {{ number_format($caixaAberto->saldo_inicial + ($totaisCaixa['por_forma_pagamento']['dinheiro']['total'] ?? 0) - $totaisCaixa['total_troco'], 2, ',', '.') }}
                                        </strong>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="mb-3">Conferência de Valores:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Forma</th>
                                                    <th class="text-end">Valor a Conferir</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-success">
                                                    <td><i class="fas fa-money-bill-wave me-2"></i>Dinheiro em Caixa</td>
                                                    <td class="text-end fw-bold">
                                                        R$ {{ number_format($caixaAberto->saldo_inicial + ($totaisCaixa['por_forma_pagamento']['dinheiro']['total'] ?? 0) - $totaisCaixa['total_troco'], 2, ',', '.') }}
                                                    </td>
                                                </tr>
                                                @if(($totaisCaixa['por_forma_pagamento']['cartao_credito']['total'] ?? 0) > 0)
                                                <tr>
                                                    <td><i class="fas fa-credit-card me-2"></i>Cartão Crédito (comprovantes)</td>
                                                    <td class="text-end">R$ {{ number_format($totaisCaixa['por_forma_pagamento']['cartao_credito']['total'] ?? 0, 2, ',', '.') }}</td>
                                                </tr>
                                                @endif
                                                @if(($totaisCaixa['por_forma_pagamento']['cartao_debito']['total'] ?? 0) > 0)
                                                <tr>
                                                    <td><i class="fas fa-credit-card me-2"></i>Cartão Débito (comprovantes)</td>
                                                    <td class="text-end">R$ {{ number_format($totaisCaixa['por_forma_pagamento']['cartao_debito']['total'] ?? 0, 2, ',', '.') }}</td>
                                                </tr>
                                                @endif
                                                @if(($totaisCaixa['por_forma_pagamento']['pix']['total'] ?? 0) > 0)
                                                <tr>
                                                    <td><i class="fas fa-qrcode me-2"></i>PIX (confirmações)</td>
                                                    <td class="text-end">R$ {{ number_format($totaisCaixa['por_forma_pagamento']['pix']['total'] ?? 0, 2, ',', '.') }}</td>
                                                </tr>
                                                @endif
                                                @if(($totaisCaixa['por_forma_pagamento']['vale_refeicao']['total'] ?? 0) > 0)
                                                <tr>
                                                    <td><i class="fas fa-ticket-alt me-2"></i>Vale Refeição (cupons)</td>
                                                    <td class="text-end">R$ {{ number_format($totaisCaixa['por_forma_pagamento']['vale_refeicao']['total'] ?? 0, 2, ',', '.') }}</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-comment me-2"></i>Observações do Fechamento</h6>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" name="observacoes" rows="3" placeholder="Digite observações sobre o fechamento (opcional)..."></textarea>
                            <small class="text-muted">Ex: Diferenças encontradas, ocorrências, etc.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimir Resumo
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-lock me-2"></i>
                        Confirmar Fechamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Abrir Caixa -->
<div class="modal fade" id="modalAbrirCaixa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-unlock me-2"></i>
                    Abrir Caixa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('caixa.abrir') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="valor_abertura" class="form-label">Valor Inicial</label>
                        <input type="number" 
                               class="form-control" 
                               id="valor_abertura" 
                               name="valor_abertura" 
                               step="0.01" 
                               min="0"
                               value="0"
                               required>
                        <small class="text-muted">Valor em caixa para troco</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-unlock me-2"></i>
                        Abrir Caixa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-icon-large {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .spin {
        animation: spin 1s linear infinite;
    }
</style>
@endpush

@push('scripts')
<script>
    function atualizarDados() {
        const icon = document.getElementById('refresh-icon');
        icon.classList.add('spin');
        
        fetch('{{ route('caixa.api.totais') }}')
            .then(response => response.json())
            .then(data => {
                // Atualizar totais
                document.getElementById('total-vendas').textContent = 
                    'R$ ' + data.total_vendas.toFixed(2).replace('.', ',');
                document.getElementById('quantidade-vendas').textContent = data.quantidade_vendas;
                document.getElementById('total-troco').textContent = 
                    'R$ ' + data.total_troco.toFixed(2).replace('.', ',');
                document.getElementById('pedidos-pendentes').textContent = data.pedidos_pendentes;
                
                icon.classList.remove('spin');
            })
            .catch(error => {
                console.error('Erro ao atualizar dados:', error);
                icon.classList.remove('spin');
            });
    }
    
    // Auto-refresh a cada 30 segundos
    setInterval(atualizarDados, 30000);
</script>
@endpush

@push('styles')
<style>
    /* Estilos para impressão do modal de fechamento */
    @media print {
        body * {
            visibility: hidden;
        }
        #modalFecharCaixa,
        #modalFecharCaixa * {
            visibility: visible;
        }
        #modalFecharCaixa {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: auto;
        }
        #modalFecharCaixa .modal-dialog {
            max-width: 100%;
            margin: 0;
        }
        #modalFecharCaixa .modal-footer {
            display: none !important;
        }
        #modalFecharCaixa .btn-close {
            display: none !important;
        }
        #modalFecharCaixa .modal-header {
            background-color: #dc3545 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        #modalFecharCaixa .card-header {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        #modalFecharCaixa .badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        #modalFecharCaixa table {
            page-break-inside: avoid;
        }
    }

    /* Destaque de valores importantes */
    .fs-4.text-success {
        font-weight: 700;
    }

    /* Animação de carregamento */
    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush
