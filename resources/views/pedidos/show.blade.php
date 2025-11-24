@extends('layouts.app')

@section('title', 'Pedido #{{ $pedido->id }}')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-receipt me-2"></i>
                    Pedido #{{ $pedido->id }}
                </h1>
                <p class="page-subtitle">Detalhes completos do pedido</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Editar
                </a>
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Status Timeline -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-clock me-2"></i>
                Status do Pedido
            </h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="badge bg-{{ $pedido->status == 'pendente' ? 'warning' : ($pedido->status == 'em_preparo' ? 'info' : ($pedido->status == 'pronto' ? 'success' : ($pedido->status == 'entregue' ? 'primary' : 'secondary'))) }} fs-6 px-3 py-2">
                        @switch($pedido->status)
                            @case('pendente')
                                <i class="fas fa-clock me-2"></i>Pendente
                                @break
                            @case('em_preparo')
                                <i class="fas fa-fire me-2"></i>Em Preparo
                                @break
                            @case('pronto')
                                <i class="fas fa-check me-2"></i>Pronto
                                @break
                            @case('entregue')
                                <i class="fas fa-thumbs-up me-2"></i>Entregue
                                @break
                            @case('finalizado')
                                <i class="fas fa-flag-checkered me-2"></i>Finalizado
                                @break
                            @default
                                <i class="fas fa-times me-2"></i>{{ ucfirst($pedido->status) }}
                        @endswitch
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted">
                        Criado em {{ $pedido->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informações do Pedido -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações do Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    @if($pedido->mesa)
                                        Mesa
                                    @else
                                        Tipo de Pedido
                                    @endif
                                </label>
                                <div class="h6">
                                    @if($pedido->mesa)
                                        <i class="fas fa-table me-2"></i>
                                        {{ $pedido->mesa->identificador ?? 'Mesa ' . $pedido->mesa->numero }} 
                                        ({{ $pedido->mesa->lugares }} lugares)
                                    @else
                                        <i class="fas fa-truck me-2"></i>
                                        Delivery
                                        @if($pedido->delivery)
                                            - {{ $pedido->delivery->cliente_nome }}
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Garçom Responsável</label>
                                <div class="h6">
                                    <i class="fas fa-user me-2"></i>
                                    {{ $pedido->usuario->nome }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Data e Hora</label>
                                <div>
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    {{ $pedido->created_at->format('d/m/Y \à\s H:i') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Total do Pedido</label>
                                <div class="h5 text-success">
                                    <i class="fas fa-calculator me-2"></i>
                                    R$ {{ number_format($pedido->total, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($pedido->observacoes)
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-comment me-2"></i>
                            Observações do Pedido
                        </h6>
                        <p class="mb-0">{{ $pedido->observacoes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informações do Delivery (se aplicável) -->
            @if($pedido->delivery)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-truck me-2"></i>
                        Informações da Entrega
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Cliente</label>
                                <div class="h6">
                                    <i class="fas fa-user me-2"></i>
                                    {{ $pedido->delivery->cliente_nome }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Telefone</label>
                                <div class="h6">
                                    <i class="fas fa-phone me-2"></i>
                                    {{ $pedido->delivery->cliente_telefone }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label text-muted">Endereço de Entrega</label>
                                <div>
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    {{ $pedido->delivery->endereco_completo }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Status da Entrega</label>
                                <div>
                                    <span class="badge bg-{{ $pedido->delivery->status_color }} fs-6">
                                        {{ $pedido->delivery->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Taxa de Entrega</label>
                                <div class="h6 text-info">
                                    <i class="fas fa-dollar-sign me-2"></i>
                                    R$ {{ number_format($pedido->delivery->taxa_entrega, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Tempo Estimado</label>
                                <div>
                                    <i class="fas fa-clock me-2"></i>
                                    {{ $pedido->delivery->tempo_estimado }} minutos
                                </div>
                            </div>
                        </div>
                        @if($pedido->delivery->distancia_km)
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Distância</label>
                                <div>
                                    <i class="fas fa-route me-2"></i>
                                    {{ number_format($pedido->delivery->distancia_km, 1, ',', '.') }} km
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($pedido->delivery->observacoes)
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            Observações da Entrega
                        </h6>
                        <p class="mb-0">{{ $pedido->delivery->observacoes }}</p>
                    </div>
                    @endif
                    
                    <div class="text-end">
                        <a href="{{ route('deliveries.show', $pedido->delivery->id) }}" class="btn btn-info">
                            <i class="fas fa-truck me-2"></i>
                            Ver Detalhes da Entrega
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Atribuir Entregador (se for delivery e estiver em preparo/pronto) -->
            @if(!$pedido->mesa && in_array($pedido->status, ['em_preparo', 'pronto']))
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-motorcycle me-2"></i>
                        Entregador
                    </h5>
                </div>
                <div class="card-body">
                    @if($pedido->entregador)
                        <!-- Entregador já atribuído -->
                        <div class="alert alert-success">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-2">
                                        <i class="fas fa-user-check me-2"></i>
                                        Entregador Atribuído
                                    </h6>
                                    <div class="mb-2">
                                        <strong>Nome:</strong> {{ $pedido->entregador->nome }}<br>
                                        <strong>Telefone:</strong> {{ $pedido->entregador->telefone }}<br>
                                        @if($pedido->entregador->tipo_veiculo)
                                            <strong>Veículo:</strong> {{ ucfirst($pedido->entregador->tipo_veiculo) }}
                                            @if($pedido->entregador->placa_veiculo)
                                                - {{ $pedido->entregador->placa_veiculo }}
                                            @endif
                                        @endif
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-info me-2">
                                            <i class="fas fa-star me-1"></i>
                                            Avaliação: {{ number_format($pedido->entregador->avaliacao_media, 1) }}
                                        </span>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-box me-1"></i>
                                            {{ $pedido->entregador->entregas_realizadas }} entregas
                                        </span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="removerEntregador({{ $pedido->id }})">
                                    <i class="fas fa-times me-1"></i>
                                    Remover
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Selecionar entregador -->
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Este pedido ainda não possui entregador atribuído
                        </div>
                        
                        <div class="mb-3">
                            <label for="entregador_select" class="form-label">
                                <i class="fas fa-search me-2"></i>
                                Selecionar Entregador Disponível
                            </label>
                            <select class="form-select" id="entregador_select">
                                <option value="">Escolha um entregador...</option>
                                @foreach($entregadores ?? [] as $entregador)
                                    <option value="{{ $entregador->id }}" 
                                            data-nome="{{ $entregador->nome }}"
                                            data-telefone="{{ $entregador->telefone }}"
                                            data-veiculo="{{ ucfirst($entregador->tipo_veiculo) }}"
                                            data-placa="{{ $entregador->placa_veiculo ?? '' }}"
                                            data-avaliacao="{{ $entregador->avaliacao_media }}"
                                            data-entregas="{{ $entregador->entregas_realizadas }}">
                                        {{ $entregador->nome }} 
                                        ({{ ucfirst($entregador->tipo_veiculo) }}) - 
                                        ⭐ {{ number_format($entregador->avaliacao_media, 1) }} - 
                                        {{ $entregador->entregas_realizadas }} entregas
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-success" id="btnAtribuirEntregador" 
                                    onclick="atribuirEntregador({{ $pedido->id }})" disabled>
                                <i class="fas fa-check me-2"></i>
                                Atribuir Entregador
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Itens do Pedido -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Itens do Pedido ({{ $pedido->itens->count() }} itens)
                    </h5>
                    <a href="{{ route('pedidos.detalhes', $pedido) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        Gerenciar Itens
                    </a>
                </div>
                <div class="card-body">
                    @forelse($pedido->itens as $item)
                        <div class="border rounded p-3 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $item->produto->nome }}</h6>
                                    <small class="text-muted">{{ $item->produto->categoria->nome ?? 'Sem categoria' }}</small>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="badge bg-secondary">
                                        Qtd: {{ $item->quantidade }}
                                    </span>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="fw-bold">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</div>
                                    <small class="text-muted">unit.</small>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="fw-bold text-primary">
                                        R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            @if($item->observacoes)
                                <div class="mt-2 p-2 bg-light rounded">
                                    <small class="text-muted">
                                        <i class="fas fa-comment me-1"></i>
                                        <strong>Obs:</strong> {{ $item->observacoes }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5>Nenhum item no pedido</h5>
                            <p class="text-muted">Adicione itens a este pedido usando o gerenciador.</p>
                            <a href="{{ route('pedidos.detalhes', $pedido) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Adicionar Itens
                            </a>
                        </div>
                    @endforelse
                </div>
                @if($pedido->itens->count() > 0)
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Total do Pedido:</strong>
                        <h4 class="text-success mb-0">R$ {{ number_format($pedido->total, 2, ',', '.') }}</h4>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Painel de Ações -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Ações do Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>
                            Editar Pedido
                        </a>
                        
                        <a href="{{ route('pedidos.detalhes', $pedido) }}" class="btn btn-info">
                            <i class="fas fa-list me-2"></i>
                            Gerenciar Itens
                        </a>
                        
                        @if($pedido->status !== 'finalizado' && $pedido->status !== 'cancelado' && $pedido->status !== 'entregue')
                            <button class="btn btn-success" onclick="alterarStatus('{{ $pedido->id }}', 'finalizado')">
                                <i class="fas fa-check me-2"></i>
                                Finalizar Pedido
                            </button>
                        @endif
                        
                        @if($pedido->status === 'pronto')
                            <button class="btn btn-primary" onclick="alterarStatus('{{ $pedido->id }}', 'entregue')">
                                <i class="fas fa-truck me-2"></i>
                                Marcar como Entregue
                            </button>
                        @endif
                        
                        @if($pedido->status === 'entregue')
                            <button class="btn btn-success" onclick="alterarStatus('{{ $pedido->id }}', 'finalizado')">
                                <i class="fas fa-flag-checkered me-2"></i>
                                Finalizar Pedido
                            </button>
                        @endif
                        
                        @if($pedido->status === 'pendente')
                            <button class="btn btn-danger" onclick="alterarStatus('{{ $pedido->id }}', 'cancelado')">
                                <i class="fas fa-times me-2"></i>
                                Cancelar Pedido
                            </button>
                        @endif
                        
                        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Voltar à Lista
                        </a>
                    </div>
                </div>
            </div>

            <!-- Resumo do Pedido -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Resumo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <div class="h4 text-primary mb-1">{{ $pedido->itens->count() }}</div>
                                <small class="text-muted">Itens</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 text-info mb-1">{{ $pedido->itens->sum('quantidade') }}</div>
                            <small class="text-muted">Quantidade</small>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Valor Total:</span>
                        <strong class="text-success">R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Habilitar botão de atribuir quando selecionar entregador
document.addEventListener('DOMContentLoaded', function() {
    const selectEntregador = document.getElementById('entregador_select');
    const btnAtribuir = document.getElementById('btnAtribuirEntregador');
    
    if (selectEntregador && btnAtribuir) {
        selectEntregador.addEventListener('change', function() {
            btnAtribuir.disabled = !this.value;
        });
    }
});

// Atribuir entregador ao pedido
function atribuirEntregador(pedidoId) {
    const selectEntregador = document.getElementById('entregador_select');
    const entregadorId = selectEntregador.value;
    
    if (!entregadorId) {
        alert('Por favor, selecione um entregador');
        return;
    }
    
    const option = selectEntregador.options[selectEntregador.selectedIndex];
    const nomeEntregador = option.getAttribute('data-nome');
    
    if (confirm(`Deseja atribuir o entregador ${nomeEntregador} a este pedido?`)) {
        fetch(`/pedidos/${pedidoId}/atribuir-entregador`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ entregador_id: entregadorId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Entregador atribuído com sucesso!');
                location.reload();
            } else {
                alert('Erro ao atribuir entregador: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao atribuir entregador');
        });
    }
}

// Remover entregador do pedido
function removerEntregador(pedidoId) {
    if (confirm('Deseja remover o entregador deste pedido?')) {
        fetch(`/pedidos/${pedidoId}/remover-entregador`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Entregador removido com sucesso!');
                location.reload();
            } else {
                alert('Erro ao remover entregador: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao remover entregador');
        });
    }
}

function alterarStatus(pedidoId, novoStatus) {
    let mensagem = '';
    switch(novoStatus) {
        case 'em_preparo':
            mensagem = 'iniciar o preparo deste pedido';
            break;
        case 'pronto':
            mensagem = 'marcar este pedido como pronto';
            break;
        case 'entregue':
            mensagem = 'marcar este pedido como entregue';
            break;
        case 'finalizado':
            mensagem = 'finalizar este pedido';
            break;
        case 'cancelado':
            mensagem = 'cancelar este pedido';
            break;
    }
    
    if (confirm(`Deseja ${mensagem}?`)) {
        // Implementar chamada AJAX para atualizar status
        fetch(`/api/pedidos/${pedidoId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: novoStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao alterar status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao alterar status do pedido');
        });
    }
}
</script>
@endpush