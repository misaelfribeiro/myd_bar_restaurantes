@extends('layouts.app')

@section('title', 'Editar Pedido #' . $pedido->id)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-edit me-2"></i>
                    Editar Pedido #{{ $pedido->id }}
                </h1>
                <p class="page-subtitle">
                    @if($pedido->mesa)
                        Mesa {{ $pedido->mesa->identificador }} -
                    @elseif($pedido->delivery)
                        Delivery ({{ $pedido->delivery->cliente_nome }}) -
                    @else
                        Balcão -
                    @endif
                    Status: <span class="badge bg-{{ $pedido->status === 'pendente' ? 'warning' : ($pedido->status === 'em_preparo' ? 'info' : ($pedido->status === 'pronto' ? 'success' : 'danger')) }}">
                        {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                    </span>
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list me-2"></i>
                    Lista de Pedidos
                </a>
                <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>
                    Visualizar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Formulário de Edição -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('pedidos.update', $pedido->id) }}" id="editPedidoForm">
                @csrf
                @method('PUT')
                
                <!-- Informações Básicas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informações Básicas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="mesa_id" class="form-label">
                                    <i class="fas fa-chair me-1"></i>
                                    Mesa
                                </label>
                                <select class="form-select @error('mesa_id') is-invalid @enderror" 
                                        id="mesa_id" 
                                        name="mesa_id"
                                        {{ $pedido->status !== 'pendente' ? 'disabled' : '' }}>
                                    @foreach($mesas as $mesa)
                                        <option value="{{ $mesa->id }}" 
                                                {{ $pedido->mesa_id == $mesa->id ? 'selected' : '' }}>
                                            Mesa {{ $mesa->identificador }} - {{ $mesa->lugares }} lugares
                                            @if($mesa->id != $pedido->mesa_id && $mesa->pedidos->count() > 0)
                                                (Ocupada)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('mesa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($pedido->status !== 'pendente')
                                    <small class="text-muted">Mesa não pode ser alterada após iniciar o preparo</small>
                                @endif
                            </div>
                            
                            <div class="col-md-6">
                                <label for="status" class="form-label">
                                    <i class="fas fa-flag me-1"></i>
                                    Status
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status"
                                        onchange="updateStatusInfo()">
                                    <option value="pendente" {{ $pedido->status === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="em_preparo" {{ $pedido->status === 'em_preparo' ? 'selected' : '' }}>Em Preparo</option>
                                    <option value="pronto" {{ $pedido->status === 'pronto' ? 'selected' : '' }}>Pronto</option>
                                    <option value="entregue" {{ $pedido->status === 'entregue' ? 'selected' : '' }}>Entregue</option>
                                    <option value="cancelado" {{ $pedido->status === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="statusInfo" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="observacoes" class="form-label">
                                    <i class="fas fa-comment me-1"></i>
                                    Observações
                                </label>
                                <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                          id="observacoes" 
                                          name="observacoes" 
                                          rows="3"
                                          placeholder="Observações especiais do pedido...">{{ old('observacoes', $pedido->observacoes) }}</textarea>
                                @error('observacoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Itens do Pedido -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Itens do Pedido
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="fas fa-plus me-2"></i>
                            Adicionar Item
                        </button>
                    </div>
                    <div class="card-body">
                        @if($errors->has('itens'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ $errors->first('itens') }}
                            </div>
                        @endif

                        <div id="itensList">
                            @forelse($pedido->itens as $index => $item)
                                <div class="item-row border rounded p-3 mb-3" data-index="{{ $index }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <h6 class="mb-1">{{ $item->produto->nome }}</h6>
                                            <small class="text-muted">{{ $item->produto->categoria->nome }}</small>
                                            <input type="hidden" name="itens[{{ $index }}][id]" value="{{ $item->id }}">
                                            <input type="hidden" name="itens[{{ $index }}][produto_id]" value="{{ $item->produto_id }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Preço Unit.</label>
                                            <input type="number" 
                                                   class="form-control form-control-sm" 
                                                   name="itens[{{ $index }}][preco_unitario]" 
                                                   value="{{ $item->preco_unitario }}"
                                                   step="0.01"
                                                   onchange="updateSubtotal({{ $index }})"
                                                   {{ $pedido->status !== 'pendente' ? 'readonly' : '' }}>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Quantidade</label>
                                            <input type="number" 
                                                   class="form-control form-control-sm" 
                                                   name="itens[{{ $index }}][quantidade]" 
                                                   value="{{ $item->quantidade }}"
                                                   min="1"
                                                   onchange="updateSubtotal({{ $index }})"
                                                   {{ $pedido->status !== 'pendente' ? 'readonly' : '' }}>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Subtotal</label>
                                            <div class="fw-bold subtotal" id="subtotal-{{ $index }}">
                                                R$ {{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            @if($pedido->status === 'pendente')
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm" 
                                                        onclick="removeItem({{ $index }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @elseif(isset($currentUser) && in_array($currentUser->role, ['admin', 'gerente']))
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm" 
                                                        onclick="removeItemAdmin({{ $item->id }}, {{ $index }})"
                                                        title="Excluir item (Admin/Gerente)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Observações do Item -->
                                    <div class="mt-2">
                                        <textarea class="form-control form-control-sm" 
                                                  name="itens[{{ $index }}][observacoes]" 
                                                  placeholder="Observações do item..."
                                                  rows="2"
                                                  {{ $pedido->status !== 'pendente' ? 'readonly' : '' }}>{{ $item->observacoes }}</textarea>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                                    <p>Nenhum item no pedido</p>
                                </div>
                            @endforelse
                        </div>
                        
                        <!-- Total -->
                        <div class="border-top pt-3 mt-3">
                            <div class="row">
                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Total:</strong>
                                        <h4 class="text-success mb-0" id="totalPedido">
                                            R$ {{ number_format($pedido->itens->sum(function($item) { return $item->preco_unitario * $item->quantidade; }), 2, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                @if($pedido->status === 'pendente')
                                    <button type="button" 
                                            class="btn btn-danger" 
                                            onclick="cancelarPedido()"
                                            data-bs-toggle="tooltip"
                                            title="Cancelar este pedido">
                                        <i class="fas fa-times me-2"></i>
                                        Cancelar Pedido
                                    </button>
                                @endif
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>
                                    Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Informações Laterais -->
        <div class="col-lg-4">
            <!-- Histórico de Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Histórico do Pedido
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Pedido Criado</h6>
                                <small class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        
                        @if($pedido->status !== 'pendente')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Em Preparo</h6>
                                    <small class="text-muted">{{ $pedido->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        @endif
                        
                        @if(in_array($pedido->status, ['pronto', 'entregue']))
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Pronto</h6>
                                    <small class="text-muted">{{ $pedido->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        @endif
                        
                        @if($pedido->status === 'entregue')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Entregue</h6>
                                    <small class="text-muted">{{ $pedido->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        @endif
                        
                        @if($pedido->status === 'cancelado')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Cancelado</h6>
                                    <small class="text-muted">{{ $pedido->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informações da Mesa/Delivery -->
            @if($pedido->mesa)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chair me-2"></i>
                        Informações da Mesa
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Identificador:</strong><br>
                        {{ $pedido->mesa->identificador }}
                    </div>
                    <div class="mb-3">
                        <strong>Capacidade:</strong><br>
                        {{ $pedido->mesa->lugares }} lugares
                    </div>
                    <div>
                        <strong>Status da Mesa:</strong><br>
                        <span class="badge bg-{{ $pedido->mesa->disponivel ? 'success' : 'warning' }}">
                            {{ $pedido->mesa->disponivel ? 'Disponível' : 'Ocupada' }}
                        </span>
                    </div>
                </div>
            </div>
            @elseif($pedido->delivery)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-motorcycle me-2"></i>
                        Informações do Delivery
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Cliente:</strong><br>
                        {{ $pedido->delivery->cliente_nome }}
                    </div>
                    <div class="mb-3">
                        <strong>Telefone:</strong><br>
                        {{ $pedido->delivery->cliente_telefone }}
                    </div>
                    <div>
                        <strong>Endereço:</strong><br>
                        {{ $pedido->delivery->endereco_completo }}
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-coffee me-2"></i>
                        Informações do Pedido
                    </h6>
                </div>
                <div class="card-body">
                    <div>
                        <strong>Tipo:</strong><br>
                        Balcão - Retirada no local
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Adicionar Item -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Adicionar Item ao Pedido
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($categorias as $categoria)
                        @if($categoria->produtos->count() > 0)
                            <div class="col-12 mb-4">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-tag me-2"></i>
                                    {{ $categoria->nome }}
                                </h6>
                                <div class="row">
                                    @foreach($categoria->produtos as $produto)
                                        @if($produto->ativo)
                                            <div class="col-md-6 mb-2">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <h6 class="card-title mb-1">{{ $produto->nome }}</h6>
                                                                <div class="text-success fw-bold">
                                                                    R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                                                </div>
                                                            </div>
                                                            <button type="button" 
                                                                    class="btn btn-outline-primary btn-sm" 
                                                                    onclick="addNewItem('{{ $produto->id }}', '{{ $produto->nome }}', '{{ $produto->preco }}', '{{ $categoria->nome }}')">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -15px;
    top: 25px;
    bottom: -20px;
    width: 2px;
    background: #dee2e6;
}

.timeline-item:last-child:before {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: -20px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.item-row {
    transition: all 0.3s ease;
}

.item-row:hover {
    background-color: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script>
let itemIndex = {{ $pedido->itens->count() }};

function updateSubtotal(index) {
    const preco = parseFloat(document.querySelector(`input[name="itens[${index}][preco_unitario]"]`).value) || 0;
    const quantidade = parseInt(document.querySelector(`input[name="itens[${index}][quantidade]"]`).value) || 0;
    const subtotal = preco * quantidade;
    
    document.getElementById(`subtotal-${index}`).textContent = 
        'R$ ' + subtotal.toFixed(2).replace('.', ',');
    
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal').forEach(el => {
        const value = el.textContent.replace('R$ ', '').replace(',', '.');
        total += parseFloat(value) || 0;
    });
    
    document.getElementById('totalPedido').textContent = 
        'R$ ' + total.toFixed(2).replace('.', ',');
}

function removeItem(index) {
    if (confirm('Tem certeza que deseja remover este item?')) {
        document.querySelector(`[data-index="${index}"]`).remove();
        updateTotal();
    }
}

function removeItemAdmin(itemId, index) {
    if (confirm('Tem certeza que deseja remover este item?\n\nEsta ação irá excluir permanentemente o item do pedido.')) {
        // Mostrar loading
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        fetch(`/pedidos/{{ $pedido->id }}/itens/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remover o item da interface
                document.querySelector(`[data-index="${index}"]`).remove();
                
                // Atualizar total
                document.getElementById('totalPedido').textContent = 
                    'R$ ' + data.novo_total.toFixed(2).replace('.', ',');
                
                // Mostrar mensagem de sucesso
                showToast('success', data.message);
            } else {
                // Mostrar mensagem de erro
                showToast('error', data.message);
                
                // Restaurar botão
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('error', 'Erro ao remover item. Tente novamente.');
            
            // Restaurar botão
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
}

function showToast(type, message) {
    // Criar toast simples
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Remove automaticamente após 5 segundos
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

function addNewItem(produtoId, nome, preco, categoria) {
    const container = document.getElementById('itensList');
    const newItem = document.createElement('div');
    newItem.className = 'item-row border rounded p-3 mb-3';
    newItem.setAttribute('data-index', itemIndex);
    
    newItem.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-5">
                <h6 class="mb-1">${nome}</h6>
                <small class="text-muted">${categoria}</small>
                <input type="hidden" name="itens[${itemIndex}][produto_id]" value="${produtoId}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Preço Unit.</label>
                <input type="number" 
                       class="form-control form-control-sm" 
                       name="itens[${itemIndex}][preco_unitario]" 
                       value="${preco}"
                       step="0.01"
                       onchange="updateSubtotal(${itemIndex})">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Quantidade</label>
                <input type="number" 
                       class="form-control form-control-sm" 
                       name="itens[${itemIndex}][quantidade]" 
                       value="1"
                       min="1"
                       onchange="updateSubtotal(${itemIndex})">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Subtotal</label>
                <div class="fw-bold subtotal" id="subtotal-${itemIndex}">
                    R$ ${parseFloat(preco).toFixed(2).replace('.', ',')}
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" 
                        class="btn btn-danger btn-sm" 
                        onclick="removeItem(${itemIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="mt-2">
            <textarea class="form-control form-control-sm" 
                      name="itens[${itemIndex}][observacoes]" 
                      placeholder="Observações do item..."
                      rows="2"></textarea>
        </div>
    `;
    
    container.appendChild(newItem);
    itemIndex++;
    updateTotal();
    
    // Fechar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('addItemModal'));
    modal.hide();
}

function updateStatusInfo() {
    const status = document.getElementById('status').value;
    const info = document.getElementById('statusInfo');
    
    const messages = {
        'pendente': '<small class="text-warning"><i class="fas fa-clock"></i> Aguardando preparo</small>',
        'em_preparo': '<small class="text-info"><i class="fas fa-fire"></i> Sendo preparado na cozinha</small>',
        'pronto': '<small class="text-success"><i class="fas fa-check"></i> Pronto para entrega</small>',
        'entregue': '<small class="text-success"><i class="fas fa-thumbs-up"></i> Entregue ao cliente</small>',
        'cancelado': '<small class="text-danger"><i class="fas fa-ban"></i> Pedido cancelado</small>'
    };
    
    info.innerHTML = messages[status] || '';
}

function cancelarPedido() {
    if (confirm('Tem certeza que deseja cancelar este pedido? Esta ação não pode ser desfeita.')) {
        document.getElementById('status').value = 'cancelado';
        document.getElementById('editPedidoForm').submit();
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    updateStatusInfo();
    
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush