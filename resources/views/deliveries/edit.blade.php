@extends('layouts.app')

@section('title', 'Editar Entrega #' . $delivery->id)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit me-2"></i>Editar Entrega #{{ $delivery->id }}</h1>
        <a href="{{ route('deliveries.show', $delivery->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Informações da Entrega</h5>
                    {!! $delivery->status_badge !!}
                </div>
                <div class="card-body">
                    @if(!in_array($delivery->status, ['pendente', 'confirmado']))
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Atenção:</strong> Esta entrega está em andamento ({{ $delivery->status_texto }}). 
                            Algumas alterações podem não ser recomendadas.
                        </div>
                    @endif

                    <form action="{{ route('deliveries.update', $delivery->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Dados do Cliente -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-user me-2"></i>Dados do Cliente
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <label for="cliente_nome" class="form-label">Nome do Cliente *</label>
                                <input type="text" class="form-control @error('cliente_nome') is-invalid @enderror" 
                                       id="cliente_nome" name="cliente_nome" 
                                       value="{{ old('cliente_nome', $delivery->cliente_nome) }}" required>
                                @error('cliente_nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cliente_telefone" class="form-label">Telefone *</label>
                                <input type="text" class="form-control @error('cliente_telefone') is-invalid @enderror" 
                                       id="cliente_telefone" name="cliente_telefone" 
                                       value="{{ old('cliente_telefone', $delivery->cliente_telefone) }}" 
                                       placeholder="(00) 00000-0000" required>
                                @error('cliente_telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Endereço de Entrega -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Endereço de Entrega
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <label for="endereco_rua" class="form-label">Rua *</label>
                                <input type="text" class="form-control @error('endereco_rua') is-invalid @enderror" 
                                       id="endereco_rua" name="endereco_rua" 
                                       value="{{ old('endereco_rua', $delivery->endereco_rua) }}" 
                                       placeholder="Nome da rua" required>
                                @error('endereco_rua')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="endereco_numero" class="form-label">Número *</label>
                                <input type="text" class="form-control @error('endereco_numero') is-invalid @enderror" 
                                       id="endereco_numero" name="endereco_numero" 
                                       value="{{ old('endereco_numero', $delivery->endereco_numero) }}" required>
                                @error('endereco_numero')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="endereco_bairro" class="form-label">Bairro *</label>
                                <input type="text" class="form-control @error('endereco_bairro') is-invalid @enderror" 
                                       id="endereco_bairro" name="endereco_bairro" 
                                       value="{{ old('endereco_bairro', $delivery->endereco_bairro) }}" required>
                                @error('endereco_bairro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="endereco_cidade" class="form-label">Cidade *</label>
                                <input type="text" class="form-control @error('endereco_cidade') is-invalid @enderror" 
                                       id="endereco_cidade" name="endereco_cidade" 
                                       value="{{ old('endereco_cidade', $delivery->endereco_cidade) }}" required>
                                @error('endereco_cidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="endereco_cep" class="form-label">CEP</label>
                                <input type="text" class="form-control @error('endereco_cep') is-invalid @enderror" 
                                       id="endereco_cep" name="endereco_cep" 
                                       value="{{ old('endereco_cep', $delivery->endereco_cep) }}" 
                                       placeholder="00000-000">
                                @error('endereco_cep')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="endereco_complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control @error('endereco_complemento') is-invalid @enderror" 
                                       id="endereco_complemento" name="endereco_complemento" 
                                       value="{{ old('endereco_complemento', $delivery->endereco_complemento) }}" 
                                       placeholder="Apto, bloco, etc">
                                @error('endereco_complemento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informações da Entrega -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-shipping-fast me-2"></i>Detalhes da Entrega
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <label for="pedido_id" class="form-label">Pedido Vinculado</label>
                                <select class="form-select @error('pedido_id') is-invalid @enderror" 
                                        id="pedido_id" name="pedido_id">
                                    <option value="">Selecione um pedido (opcional)</option>
                                    @foreach($pedidos as $pedido)
                                        <option value="{{ $pedido->id }}" 
                                                {{ old('pedido_id', $delivery->pedido_id) == $pedido->id ? 'selected' : '' }}>
                                            Pedido #{{ $pedido->id }} - R$ {{ number_format($pedido->total, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pedido_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($delivery->pedido_id && !in_array($delivery->status, ['pendente', 'confirmado']))
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Recomendado não alterar o pedido com entrega em andamento.
                                    </small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="taxa_entrega" class="form-label">Taxa de Entrega (R$) *</label>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('taxa_entrega') is-invalid @enderror" 
                                       id="taxa_entrega" name="taxa_entrega" 
                                       value="{{ old('taxa_entrega', number_format($delivery->taxa_entrega, 2, '.', '')) }}" required>
                                @error('taxa_entrega')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="tempo_estimado_entrega" class="form-label">Tempo Estimado (min) *</label>
                                <input type="number" min="1" max="180" 
                                       class="form-control @error('tempo_estimado_entrega') is-invalid @enderror" 
                                       id="tempo_estimado_entrega" name="tempo_estimado_entrega" 
                                       value="{{ old('tempo_estimado_entrega', $delivery->tempo_estimado_entrega) }}" required>
                                @error('tempo_estimado_entrega')
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
                                          placeholder="Observações sobre a entrega...">{{ old('observacoes', $delivery->observacoes) }}</textarea>
                                @error('observacoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informações de Sistema (apenas visualização) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-secondary border-bottom pb-2 mb-3">
                                    <i class="fas fa-info me-2"></i>Informações do Sistema
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-muted">Status Atual</label>
                                    <div>{!! $delivery->status_badge !!}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-muted">Data de Criação</label>
                                    <div class="form-control-plaintext">{{ $delivery->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                            @if($delivery->data_confirmacao)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-muted">Data de Confirmação</label>
                                    <div class="form-control-plaintext">{{ $delivery->data_confirmacao->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                            @endif
                            @if($delivery->data_entrega)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-muted">Data de Entrega</label>
                                    <div class="form-control-plaintext">{{ $delivery->data_entrega->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Botões -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('deliveries.show', $delivery->id) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Atualizar Entrega
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
// Máscara para telefone
document.getElementById('telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 11) {
        value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
    } else if (value.length >= 7) {
        value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
    } else if (value.length >= 3) {
        value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
    } else if (value.length >= 1) {
        value = value.replace(/^(\d*)/, '($1');
    }
    e.target.value = value;
});

// Máscara para CEP
document.getElementById('cep').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 6) {
        value = value.replace(/^(\d{5})(\d{0,3}).*/, '$1-$2');
    }
    e.target.value = value;
});
</script>
@endsection