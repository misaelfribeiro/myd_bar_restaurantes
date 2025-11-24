@extends('layouts.app')

@section('title', 'Mesa {{ $mesa->identificador }}')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-chair me-2"></i>
                    Mesa {{ $mesa->identificador }}
                </h1>
                <p class="page-subtitle">Visualização detalhada da mesa</p>
            </div>
            <div>
                <a href="{{ route('mesas.edit', $mesa->id) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-2"></i>
                    Editar Mesa
                </a>
                <a href="{{ route('mesas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
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

    <div class="row">
        <!-- Informações da Mesa -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações da Mesa
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Ícone da Mesa -->
                    <div class="text-center mb-4">
                        <i class="fas fa-chair display-1 text-primary"></i>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Identificador</label>
                                <div class="h5">{{ $mesa->identificador }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Número de Lugares</label>
                                <div class="h5">
                                    <i class="fas fa-users me-2"></i>
                                    {{ $mesa->lugares }} {{ $mesa->lugares == 1 ? 'lugar' : 'lugares' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Status Atual</label>
                                <div>
                                    @if($mesa->pedidos->count() > 0)
                                        <span class="badge bg-warning fs-6">
                                            <i class="fas fa-times-circle me-1"></i>
                                            Ocupada
                                        </span>
                                    @else
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Livre
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Pedidos Ativos</label>
                                <div class="h5">
                                    <i class="fas fa-clipboard-list me-2"></i>
                                    {{ $mesa->pedidos->count() }} {{ $mesa->pedidos->count() == 1 ? 'pedido' : 'pedidos' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Criado em</label>
                                <div>
                                    <i class="fas fa-calendar me-2"></i>
                                    {{ $mesa->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Última Atualização</label>
                                <div>
                                    <i class="fas fa-clock me-2"></i>
                                    {{ $mesa->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pedidos da Mesa -->
            @if($mesa->pedidos->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Pedidos da Mesa
                        </h3>
                    </div>
                    <div class="card-body">
                        @foreach($mesa->pedidos as $pedido)
                            <div class="border rounded p-3 mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <strong>Pedido #{{ $pedido->id }}</strong>
                                        <br><small class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="badge bg-{{ 
                                            $pedido->status == 'aberto' ? 'primary' : 
                                            ($pedido->status == 'finalizado' ? 'success' : 
                                            ($pedido->status == 'entregue' ? 'info' : 'warning')) 
                                        }}">
                                            {{ ucfirst($pedido->status) }}
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>
                                            Ver Detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card mt-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-clipboard display-1 text-muted mb-3"></i>
                        <h4>Nenhum pedido ativo</h4>
                        <p class="text-muted">Esta mesa não possui pedidos no momento</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Painel de Ações -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Ações
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('mesas.edit', $mesa->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>
                            Editar Mesa
                        </a>
                        
                        @if($mesa->pedidos->count() == 0)
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluir">
                                <i class="fas fa-trash me-2"></i>
                                Excluir Mesa
                            </button>
                        @else
                            <button type="button" class="btn btn-danger" disabled title="Não é possível excluir mesa com pedidos ativos">
                                <i class="fas fa-trash me-2"></i>
                                Excluir Mesa
                            </button>
                        @endif
                        
                        <a href="{{ route('mesas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Voltar às Mesas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Histórico -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Histórico
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span><i class="fas fa-plus text-success me-2"></i>Criada</span>
                                    <span class="text-muted">{{ $mesa->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        @if($mesa->updated_at != $mesa->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span><i class="fas fa-edit text-warning me-2"></i>Última Edição</span>
                                        <span class="text-muted">{{ $mesa->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($mesa->pedidos->count() > 0)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span><i class="fas fa-clipboard text-info me-2"></i>Pedidos Ativos</span>
                                        <span class="text-muted">{{ $mesa->pedidos->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-trash-alt text-danger display-1 mb-3"></i>
                <h4>Excluir Mesa {{ $mesa->identificador }}?</h4>
                <p class="text-muted">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Cancelar
                </button>
                <form id="formExcluir" method="POST" action="{{ route('mesas.destroy', $mesa->id) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fade out dos alertas
    setTimeout(function() {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    }, 3000);

    // Confirmar exclusão
    function confirmarExclusao() {
        return confirm('Tem certeza que deseja excluir esta mesa? Esta ação não pode ser desfeita.');
    }
</script>
@endpush