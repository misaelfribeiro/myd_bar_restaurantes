@extends('layouts.app')

@section('title', 'Recebimento de Pagamento')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Recebimento de Pagamento
                </h1>
                <p class="page-subtitle">
                    Pedido #{{ $pedido->id }} - 
                    @if($pedido->mesa)
                        Mesa {{ $pedido->mesa->identificador }}
                    @elseif($pedido->delivery)
                        Delivery ({{ $pedido->delivery->cliente_nome }})
                    @else
                        Balcão
                    @endif
                </p>
            </div>
            <a href="{{ route('caixa.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Informações do Pedido -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-3">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Itens do Pedido
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Unitário</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->itens as $item)
                                <tr>
                                    <td>{{ $item->produto->nome }}</td>
                                    <td class="text-center">{{ $item->quantidade }}</td>
                                    <td class="text-end">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                    <td class="text-end">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <th colspan="3">TOTAL</th>
                                    <th class="text-end">R$ {{ number_format($pedido->total, 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Valor Total</h6>
                            <h2 class="text-success mb-0">R$ {{ number_format($pedido->total, 2, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Pagamento -->
    <form id="form-pagamento" onsubmit="return false;">
        @csrf
        <div class="row">
            <!-- Coluna Esquerda - Formulário -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            Formas de Pagamento
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Forma de Pagamento -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Forma de Pagamento *</label>
                                <select name="forma_pagamento" class="form-select form-select-lg" id="forma-pagamento" required>
                                    <option value="">Selecione...</option>
                                    <option value="dinheiro">💵 Dinheiro</option>
                                    <option value="cartao_credito">💳 Cartão de Crédito</option>
                                    <option value="cartao_debito">💳 Cartão de Débito</option>
                                    <option value="pix">📱 PIX</option>
                                    <option value="vale_refeicao">🎫 Vale Refeição</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Valor do Pagamento *</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" name="valor" class="form-control" id="valor-pagamento" 
                                           step="0.01" min="0.01" max="{{ $pedido->total }}" 
                                           value="{{ $pedido->total }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Campos para Dinheiro -->
                        <div id="campos-dinheiro" class="row mb-3" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Valor Recebido</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" name="valor_recebido" class="form-control" 
                                           id="valor-recebido" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Troco</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control bg-light" id="troco" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Atalhos de Valores -->
                        <div id="atalhos-dinheiro" style="display: none;" class="mb-3">
                            <label class="form-label fw-bold">Atalhos de Valores</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary atalho-valor" data-valor="10">R$ 10</button>
                                <button type="button" class="btn btn-outline-primary atalho-valor" data-valor="20">R$ 20</button>
                                <button type="button" class="btn btn-outline-primary atalho-valor" data-valor="50">R$ 50</button>
                                <button type="button" class="btn btn-outline-primary atalho-valor" data-valor="100">R$ 100</button>
                                <button type="button" class="btn btn-outline-primary atalho-valor" data-valor="200">R$ 200</button>
                                <button type="button" class="btn btn-outline-success" id="valor-exato">Valor Exato</button>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações (Opcional)</label>
                            <textarea name="observacoes" class="form-control" rows="2" 
                                      placeholder="Ex: Cliente pagou com nota de R$ 100,00"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita - Resumo e Ações -->
            <div class="col-md-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Resumo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Valor do Pedido:</span>
                            <strong class="text-primary">R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Valor a Pagar:</span>
                            <strong class="text-success" id="resumo-valor">R$ 0,00</strong>
                        </div>
                        <div id="resumo-recebido" class="d-flex justify-content-between mb-2" style="display: none;">
                            <span>Valor Recebido:</span>
                            <strong class="text-info">R$ 0,00</strong>
                        </div>
                        <div id="resumo-troco" class="d-flex justify-content-between" style="display: none;">
                            <span>Troco:</span>
                            <strong class="text-warning">R$ 0,00</strong>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg" id="btn-processar">
                        <i class="fas fa-check me-2"></i>
                        Processar Pagamento
                    </button>
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalMultiplosPagamentos">
                        <i class="fas fa-layer-group me-2"></i>
                        Múltiplas Formas
                    </button>
                    <a href="{{ route('caixa.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </a>
                </div>

                <!-- Pagamentos Existentes -->
                @if($pedido->pagamentos->count() > 0)
                    <div class="card shadow-sm mt-3">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                Pagamentos Realizados
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach($pedido->pagamentos as $pagamento)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $pagamento->forma_pagamento)) }}</strong><br>
                                        <span class="text-muted">{{ $pagamento->created_at->format('H:i') }}</span>
                                    </small>
                                    <span class="badge bg-{{ $pagamento->status == 'confirmado' ? 'success' : 'warning' }}">
                                        R$ {{ number_format($pagamento->valor, 2, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Modal Múltiplos Pagamentos -->
<div class="modal fade" id="modalMultiplosPagamentos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-layer-group me-2"></i>
                    Múltiplas Formas de Pagamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">
                    Utilize quando o cliente quiser pagar com mais de uma forma (ex: parte em dinheiro e parte no cartão).
                </p>
                
                <div id="formas-multiplas"></div>
                
                <button type="button" class="btn btn-outline-primary mt-3" id="adicionar-forma">
                    <i class="fas fa-plus me-1"></i>
                    Adicionar Forma de Pagamento
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmar-multiplos">
                    <i class="fas fa-check me-1"></i>
                    Processar Pagamentos
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const pedidoId = {{ $pedido->id }};
const valorTotal = parseFloat('{{ $pedido->total }}');

// Exibir campos específicos por forma de pagamento
document.getElementById('forma-pagamento').addEventListener('change', function() {
    const forma = this.value;
    const camposDinheiro = document.getElementById('campos-dinheiro');
    const atalhosDinheiro = document.getElementById('atalhos-dinheiro');
    
    if (forma === 'dinheiro') {
        camposDinheiro.style.display = 'flex';
        atalhosDinheiro.style.display = 'block';
    } else {
        camposDinheiro.style.display = 'none';
        atalhosDinheiro.style.display = 'none';
    }
    
    atualizarResumo();
});

// Atualizar resumo
function atualizarResumo() {
    const valor = parseFloat(document.getElementById('valor-pagamento').value) || 0;
    const valorRecebido = parseFloat(document.getElementById('valor-recebido').value) || 0;
    const troco = valorRecebido - valor;
    
    document.getElementById('resumo-valor').textContent = 'R$ ' + valor.toFixed(2).replace('.', ',');
    document.getElementById('troco').value = troco >= 0 ? troco.toFixed(2) : '0.00';
}

document.getElementById('valor-pagamento').addEventListener('input', atualizarResumo);
document.getElementById('valor-recebido').addEventListener('input', atualizarResumo);

// Atalhos de valores
document.querySelectorAll('.atalho-valor').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('valor-recebido').value = this.dataset.valor;
        atualizarResumo();
    });
});

document.getElementById('valor-exato').addEventListener('click', function() {
    document.getElementById('valor-recebido').value = valorTotal.toFixed(2);
    atualizarResumo();
});

// Processar pagamento
document.getElementById('form-pagamento').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        forma_pagamento: formData.get('forma_pagamento'),
        valor: parseFloat(formData.get('valor')),
        valor_recebido: formData.get('valor_recebido') ? parseFloat(formData.get('valor_recebido')) : null,
        observacoes: formData.get('observacoes')
    };
    
    try {
        const response = await fetch(`/api/pagamentos-teste/pedido/${pedidoId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Pagamento processado com sucesso!');
            window.location.href = '{{ route('caixa.index') }}';
        } else {
            alert('❌ Erro: ' + (result.message || 'Erro ao processar pagamento'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro ao processar pagamento. Verifique sua conexão.');
    }
});

// Múltiplos pagamentos
let contadorFormas = 0;

document.getElementById('adicionar-forma').addEventListener('click', function() {
    contadorFormas++;
    const html = `
        <div class="card mb-3" id="forma-${contadorFormas}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Forma</label>
                        <select class="form-select forma-multipla">
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao_credito">Cartão Crédito</option>
                            <option value="cartao_debito">Cartão Débito</option>
                            <option value="pix">PIX</option>
                            <option value="vale_refeicao">Vale Refeição</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Valor</label>
                        <input type="number" class="form-control valor-multiplo" step="0.01" min="0">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger" onclick="this.closest('.card').remove()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.getElementById('formas-multiplas').insertAdjacentHTML('beforeend', html);
});

document.getElementById('confirmar-multiplos').addEventListener('click', async function() {
    const formas = [];
    document.querySelectorAll('#formas-multiplas .card').forEach(card => {
        formas.push({
            forma_pagamento: card.querySelector('.forma-multipla').value,
            valor: parseFloat(card.querySelector('.valor-multiplo').value)
        });
    });
    
    try {
        const response = await fetch(`/api/pagamentos-teste/pedido/${pedidoId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pagamentos: formas })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Pagamentos processados com sucesso!');
            window.location.href = '{{ route('caixa.index') }}';
        } else {
            alert('❌ Erro: ' + (result.message || 'Erro ao processar pagamentos'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro ao processar pagamentos. Verifique sua conexão.');
    }
});
</script>
@endpush
