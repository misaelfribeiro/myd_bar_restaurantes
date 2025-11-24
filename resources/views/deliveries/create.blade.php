@extends('layouts.app')

@section('title', 'Nova Entrega')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus me-2"></i>Nova Entrega</h1>
        <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Informações da Entrega</h5>
                </div>
                <div class="card-body">
                    <form id="formDelivery" action="{{ route('deliveries.store') }}" method="POST">
                        @csrf
                        
                        <!-- Busca de Cliente -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-search me-2"></i>Buscar Cliente
                                </h6>
                                
                                <div class="mb-3 position-relative">
                                    <label for="busca_cliente" class="form-label">Nome ou telefone do cliente *</label>
                                    <input type="text" class="form-control" id="busca_cliente" 
                                           placeholder="Digite pelo menos 3 caracteres...">
                                    
                                    <!-- Resultados -->
                                    <div id="resultados" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 300px; overflow-y: auto;"></div>
                                </div>
                                
                                <!-- Cliente selecionado -->
                                <div id="cliente_info" class="alert alert-success" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong id="cliente_nome"></strong><br>
                                            <small class="text-muted">
                                                <span id="cliente_telefone"></span> | 
                                                <span id="cliente_endereco"></span>
                                            </small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="limparCliente()">
                                            Trocar
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Campo hidden -->
                                <input type="hidden" id="cliente_id" name="cliente_id" required>
                            </div>
                        </div>

                        <!-- Seleção de Pedido -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-receipt me-2"></i>Selecionar Pedido
                                </h6>
                                
                                @if($pedidos->count() > 0)
                                    <div class="mb-3">
                                        <label for="pedido_id" class="form-label">Pedido para Entrega</label>
                                        <select class="form-select @error('pedido_id') is-invalid @enderror" 
                                                id="pedido_id" name="pedido_id">
                                            <option value="">Selecione um pedido (opcional)</option>
                                            @foreach($pedidos as $pedido)
                                                <option value="{{ $pedido->id }}" {{ old('pedido_id') == $pedido->id ? 'selected' : '' }}>
                                                    Pedido #{{ $pedido->id }} - 
                                                    @if($pedido->mesa_id)
                                                        Mesa {{ $pedido->mesa->numero ?? $pedido->mesa_id }} - 
                                                    @endif
                                                    R$ {{ number_format($pedido->valor_total, 2, ',', '.') }} - 
                                                    {{ $pedido->created_at->format('d/m/Y H:i') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('pedido_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Selecione um pedido existente ou deixe em branco para criar uma entrega sem pedido específico.
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Nenhum pedido disponível</strong><br>
                                        Não há pedidos sem entrega no momento. Você pode criar a entrega sem vincular a um pedido específico.
                                    </div>
                                    <input type="hidden" name="pedido_id" value="">
                                @endif
                            </div>
                        </div>

                        <!-- Informações da Entrega -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-shipping-fast me-2"></i>Detalhes da Entrega
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <label for="taxa_entrega" class="form-label">Taxa de Entrega (R$) *</label>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('taxa_entrega') is-invalid @enderror" 
                                       id="taxa_entrega" name="taxa_entrega" 
                                       value="{{ old('taxa_entrega', '5.00') }}" required>
                                @error('taxa_entrega')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tempo_estimado" class="form-label">Tempo Estimado (min) *</label>
                                <input type="number" min="10" max="180" 
                                       class="form-control @error('tempo_estimado') is-invalid @enderror" 
                                       id="tempo_estimado" name="tempo_estimado" 
                                       value="{{ old('tempo_estimado', '30') }}" required>
                                @error('tempo_estimado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                          id="observacoes" name="observacoes" rows="3" 
                                          placeholder="Observações sobre a entrega...">{{ old('observacoes') }}</textarea>
                                @error('observacoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Criar Entrega
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let debounceTimer = null;

// Buscar clientes
document.getElementById('busca_cliente').addEventListener('input', function() {
    const termo = this.value.trim();
    
    if (termo.length < 3) {
        document.getElementById('resultados').style.display = 'none';
        return;
    }
    
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => buscarClientes(termo), 300);
});

// Função de busca
async function buscarClientes(termo) {
    try {
        const response = await fetch(`/api/clientes/search?search=${encodeURIComponent(termo)}`);
        const data = await response.json();
        
        console.log('📡 Resposta da API:', data);
        
        const resultados = document.getElementById('resultados');
        resultados.innerHTML = '';
        
        if (data.success && data.data.length > 0) {
            data.data.forEach(cliente => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action';
                item.innerHTML = `
                    <strong>${cliente.nome}</strong><br>
                   
                    <small class="text-muted">${cliente.telefone} - ${cliente.endereco_completo || 'Sem endereço'}</small>
                `;
                item.onclick = () => selecionarCliente(cliente);
                resultados.appendChild(item);
            });
            resultados.style.display = 'block';
        } else {
            resultados.innerHTML = '<div class="list-group-item">Nenhum cliente encontrado</div>';
            resultados.style.display = 'block';
        }
    } catch (error) {
        console.error('❌ Erro na busca:', error);
    }
}

// Selecionar cliente
function selecionarCliente(cliente) {
    // Preencher campos
    document.getElementById('cliente_id').value = cliente.id;   
    document.getElementById('cliente_nome').textContent = cliente.nome;
    document.getElementById('cliente_telefone').textContent = cliente.telefone;
    document.getElementById('cliente_endereco').textContent = cliente.endereco_completo || 'Sem endereço';
    
    // Mostrar info do cliente
    document.getElementById('cliente_info').style.display = 'block';
    document.getElementById('resultados').style.display = 'none';
    document.getElementById('busca_cliente').value = '';
    
    console.log('✅ Cliente selecionado:', cliente.nome, 'ID:', cliente.id);
}

// Limpar cliente
function limparCliente() {
    document.getElementById('cliente_id').value = '';
    document.getElementById('cliente_info').style.display = 'none';
    document.getElementById('busca_cliente').value = '';
    document.getElementById('resultados').style.display = 'none';
    console.log('🗑️ Cliente limpo');
}

// Fechar resultados ao clicar fora
document.addEventListener('click', function(e) {
    if (!e.target.closest('#busca_cliente') && !e.target.closest('#resultados')) {
        document.getElementById('resultados').style.display = 'none';
    }
});

// Validar formulário antes de enviar
document.getElementById('formDelivery').addEventListener('submit', function(e) {
    const clienteId = document.getElementById('cliente_id').value;
    
    console.log('📋 Tentando enviar formulário...');
    console.log('   Cliente ID:', clienteId);
    
    if (!clienteId) {
        e.preventDefault();
        alert('Por favor, selecione um cliente antes de criar a entrega.');
        document.getElementById('busca_cliente').focus();
        console.log('❌ Formulário bloqueado: cliente_id vazio');
        return false;
    }
    
    console.log('✅ Formulário válido, enviando...');
    return true;
});


</script>
@endsection