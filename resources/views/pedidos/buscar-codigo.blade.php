@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-search me-2"></i>
                    <span>Buscar Pedido por Código de Segurança</span>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Como usar:</strong> Digite o código de 6 dígitos que o entregador informou para localizar o pedido.
                    </div>

                    <!-- Formulário de Busca -->
                    <div id="formBusca">
                        <div class="mb-3">
                            <label for="codigoSeguranca" class="form-label">Código de Segurança</label>
                            <input type="text" 
                                   class="form-control form-control-lg text-center" 
                                   id="codigoSeguranca" 
                                   placeholder="000000"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   style="font-size: 2rem; font-weight: bold; letter-spacing: 5px; font-family: monospace;"
                                   autocomplete="off"
                                   autofocus>
                            <div class="invalid-feedback" id="codigoError"></div>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-primary btn-lg" id="btnBuscar">
                                <i class="fas fa-search me-2"></i>Buscar Pedido
                            </button>
                        </div>
                    </div>

                    <!-- Resultado da Busca -->
                    <div id="resultadoBusca" style="display: none;">
                        <hr class="my-4">
                        
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Pedido encontrado!</strong>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-receipt me-2"></i>
                                    Pedido #<span id="pedidoNumero"></span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Cliente:</strong>
                                        <div id="pedidoCliente"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Status:</strong>
                                        <div id="pedidoStatus"></div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Tipo de Preparo:</strong>
                                        <div id="pedidoTipo"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Forma de Pagamento:</strong>
                                        <div id="pedidoPagamento"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <strong>Total:</strong>
                                    <h4 class="text-primary mb-0" id="pedidoTotal"></h4>
                                </div>

                                <!-- Entregador -->
                                <div id="entregadorInfo" style="display: none;">
                                    <hr>
                                    <h6 class="fw-bold">
                                        <i class="fas fa-motorcycle me-2"></i>Entregador
                                    </h6>
                                    <div id="entregadorNome"></div>
                                </div>

                                <!-- Itens do Pedido -->
                                <hr>
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-list me-2"></i>Itens do Pedido
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-center">Qtd</th>
                                                <th class="text-end">Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pedidoItens"></tbody>
                                    </table>
                                </div>

                                <!-- Observações -->
                                <div id="observacoesDiv" style="display: none;">
                                    <hr>
                                    <h6 class="fw-bold">
                                        <i class="fas fa-comment me-2"></i>Observações
                                    </h6>
                                    <p id="pedidoObservacoes" class="text-muted mb-0"></p>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('pedidos.index') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>Confirmar Entrega
                            </a>
                            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                                <i class="fas fa-redo me-2"></i>Nova Busca
                            </button>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="loadingSpinner" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Buscando...</span>
                        </div>
                        <p class="mt-2 text-muted">Buscando pedido...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCodigo = document.getElementById('codigoSeguranca');
    const btnBuscar = document.getElementById('btnBuscar');
    const formBusca = document.getElementById('formBusca');
    const resultadoBusca = document.getElementById('resultadoBusca');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const codigoError = document.getElementById('codigoError');

    // Permitir apenas números
    inputCodigo.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        this.classList.remove('is-invalid');
    });

    // Buscar ao pressionar Enter
    inputCodigo.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarPedido();
        }
    });

    btnBuscar.addEventListener('click', buscarPedido);

    function buscarPedido() {
        const codigo = inputCodigo.value.trim();

        // Validação
        if (codigo.length !== 6) {
            inputCodigo.classList.add('is-invalid');
            codigoError.textContent = 'O código deve ter exatamente 6 dígitos';
            return;
        }

        // Mostrar loading
        formBusca.style.display = 'none';
        loadingSpinner.style.display = 'block';
        resultadoBusca.style.display = 'none';

        // Fazer requisição
        fetch('{{ route("pedidos.confirmar-codigo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                codigo_seguranca: codigo
            })
        })
        .then(response => response.json())
        .then(data => {
            loadingSpinner.style.display = 'none';

            if (data.success) {
                mostrarPedido(data.pedido);
            } else {
                formBusca.style.display = 'block';
                inputCodigo.classList.add('is-invalid');
                codigoError.textContent = data.message || 'Código inválido ou pedido não encontrado';
                inputCodigo.select();
            }
        })
        .catch(error => {
            console.error('Erro ao buscar pedido:', error);
            loadingSpinner.style.display = 'none';
            formBusca.style.display = 'block';
            inputCodigo.classList.add('is-invalid');
            codigoError.textContent = 'Erro ao buscar pedido. Tente novamente.';
        });
    }

    function mostrarPedido(pedido) {
        // Preencher dados do pedido
        document.getElementById('pedidoNumero').textContent = pedido.numero_pedido || pedido.id;
        document.getElementById('pedidoCliente').textContent = pedido.cliente ? pedido.cliente.nome : 'Cliente não identificado';
        
        // Status
        const statusBadges = {
            'pendente': '<span class="badge bg-warning">Pendente</span>',
            'em_preparo': '<span class="badge bg-info">Em Preparo</span>',
            'pronto': '<span class="badge bg-success">Pronto</span>',
            'em_rota': '<span class="badge bg-primary">Em Rota de Entrega</span>',
            'entregue': '<span class="badge bg-success">Entregue</span>',
            'cancelado': '<span class="badge bg-danger">Cancelado</span>'
        };
        document.getElementById('pedidoStatus').innerHTML = statusBadges[pedido.status] || pedido.status;

        // Tipo de preparo
        const tipoLabels = {
            'local': 'Para consumir no local',
            'viagem': 'Para viagem',
            'delivery': 'Delivery'
        };
        document.getElementById('pedidoTipo').textContent = tipoLabels[pedido.tipo_preparo] || pedido.tipo_preparo;

        // Forma de pagamento
        const pagamentoLabels = {
            'dinheiro': 'Dinheiro',
            'cartao_credito': 'Cartão de Crédito',
            'cartao_debito': 'Cartão de Débito',
            'pix': 'PIX'
        };
        document.getElementById('pedidoPagamento').textContent = pagamentoLabels[pedido.forma_pagamento] || pedido.forma_pagamento;

        // Total
        document.getElementById('pedidoTotal').textContent = 'R$ ' + parseFloat(pedido.total).toFixed(2);

        // Entregador
        if (pedido.entregador) {
            document.getElementById('entregadorInfo').style.display = 'block';
            document.getElementById('entregadorNome').innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle fa-2x me-3"></i>
                    <div>
                        <strong>${pedido.entregador.nome}</strong><br>
                        <small class="text-muted">${pedido.entregador.telefone || ''}</small>
                    </div>
                </div>
            `;
        }

        // Itens
        const itensHtml = pedido.itens.map(item => {
            const nome = item.produto ? item.produto.nome : (item.combo ? item.combo.nome : 'Item');
            const valor = parseFloat(item.preco_unitario * item.quantidade).toFixed(2);
            return `
                <tr>
                    <td>${nome}</td>
                    <td class="text-center">${item.quantidade}</td>
                    <td class="text-end">R$ ${valor}</td>
                </tr>
            `;
        }).join('');
        document.getElementById('pedidoItens').innerHTML = itensHtml;

        // Observações
        if (pedido.observacoes) {
            document.getElementById('observacoesDiv').style.display = 'block';
            document.getElementById('pedidoObservacoes').textContent = pedido.observacoes;
        }

        // Mostrar resultado
        resultadoBusca.style.display = 'block';
    }
});
</script>
@endpush
@endsection
