@extends('layouts.app')
@section('title', 'Detalhes da Entrega #' . $delivery->id)
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1><i class="fas fa-shipping-fast me-2"></i>Entrega #{{ $delivery->id }}</h1>
 <div>
 <a href="{{ route('deliveries.index') }}" class="btn btn-secondary me-2">
 <i class="fas fa-arrow-left me-2"></i>Voltar
 </a>
 @if(in_array($delivery->status, ['pendente', 'confirmado']))
 <a href="{{ route('deliveries.edit', $delivery->id) }}" class="btn btn-primary">
 <i class="fas fa-edit me-2"></i>Editar
 </a>
 @endif
 </div>
 </div>
 <div class="row">
 <!-- Informações Principais -->
 <div class="col-lg-8">
 <div class="card mb-4">
 <div class="card-header d-flex justify-content-between align-items-center">
 <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações da Entrega</h5>
 {!! $delivery->status_badge !!}
 </div>
 <div class="card-body">
 <div class="row">
 <!-- Dados do Cliente -->
 <div class="col-md-6">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-user me-2"></i>Cliente
 </h6>
 <table class="table table-borderless">
 <tr>
 <th width="120">Nome:</th>
 <td>{{ $delivery->cliente_nome }}</td>
 </tr>
 <tr>
 <th>Telefone:</th>
 <td>
 <a href="tel:{{ $delivery->cliente_telefone }}" class="text-decoration-none">
 <i class="fas fa-phone me-1"></i>{{ $delivery->cliente_telefone }}
 </a>
 </td>
 </tr>
 </table>
 </div>
 <!-- Endereço -->
 <div class="col-md-6">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-map-marker-alt me-2"></i>Endereço
 </h6>
 <table class="table table-borderless">
 <tr>
 <th width="120">Endereço:</th>
 <td>{{ $delivery->endereco_rua }}, {{ $delivery->endereco_numero }}</td>
 </tr>
 <tr>
 <th>Bairro:</th>
 <td>{{ $delivery->endereco_bairro }}</td>
 </tr>
 <tr>
 <th>Cidade:</th>
 <td>{{ $delivery->endereco_cidade }}</td>
 </tr>
 @if($delivery->endereco_cep)
 <tr>
 <th>CEP:</th>
 <td>{{ $delivery->endereco_cep }}</td>
 </tr>
 @endif
 @if($delivery->endereco_complemento)
 <tr>
 <th>Complemento:</th>
 <td>{{ $delivery->endereco_complemento }}</td>
 </tr>
 @endif
 </table>
 </div>
 </div>
 <!-- Detalhes da Entrega -->
 <div class="row mt-4">
 <div class="col-12">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-shipping-fast me-2"></i>Detalhes da Entrega
 </h6>
 </div>
 <div class="col-md-6">
 <table class="table table-borderless">
 <tr>
 <th width="150">Taxa de Entrega:</th>
 <td><strong>R$ {{ number_format($delivery->taxa_entrega, 2, ',', '.') }}</strong></td>
 </tr>
 <tr>
 <th>Tempo Estimado:</th>
 <td>{{ $delivery->tempo_estimado }} minutos</td>
 </tr>
 @if($delivery->pedido)
 <tr>
 <th>Pedido Vinculado:</th>
 <td>
 <a href="{{ route('pedidos.show', $delivery->pedido->id) }}" class="text-decoration-none">
 <i class="fas fa-receipt me-1"></i>Pedido #{{ $delivery->pedido->id }}
 </a>
 </td>
 </tr>
 @endif
 </table>
 </div>
 <div class="col-md-6">
 <table class="table table-borderless">
 <tr>
 <th width="150">Data de Criação:</th>
 <td>{{ $delivery->created_at->format('d/m/Y H:i') }}</td>
 </tr>
 @if($delivery->data_confirmacao)
 <tr>
 <th>Confirmado em:</th>
 <td>{{ $delivery->data_confirmacao->format('d/m/Y H:i') }}</td>
 </tr>
 @endif
 @if($delivery->data_entrega)
 <tr>
 <th>Entregue em:</th>
 <td>{{ $delivery->data_entrega->format('d/m/Y H:i') }}</td>
 </tr>
 @endif
 </table>
 </div>
 </div>
 <!-- Observações -->
 @if($delivery->observacoes)
 <div class="row mt-4">
 <div class="col-12">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-comment me-2"></i>Observações
 </h6>
 <p class="mb-0">{{ $delivery->observacoes }}</p>
 </div>
 </div>
 @endif
 </div>
 </div>
 <!-- Pedido Vinculado -->
 @if($delivery->pedido)
 <div class="card">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Pedido Vinculado #{{ $delivery->pedido->id }}</h5>
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-6">
 <strong>Status do Pedido:</strong>
 <span class="badge bg-{{ $delivery->pedido->status == 'finalizado' ? 'success' : 'warning' }} ms-2">
 {{ ucfirst($delivery->pedido->status) }}
 </span>
 </div>
 <div class="col-md-6">
 <strong>Total do Pedido:</strong>
 <span class="fs-5 fw-bold text-success ms-2">
 R$ {{ number_format($delivery->pedido->total, 2, ',', '.') }}
 </span>
 </div>
 </div>
 @if($delivery->pedido->itens->count() > 0)
 <div class="table-responsive">
 <table class="table table-sm">
 <thead class="table-light">
 <tr>
 <th>Produto</th>
 <th>Qtd</th>
 <th>Preço Unit.</th>
 <th>Subtotal</th>
 </tr>
 </thead>
 <tbody>
 @foreach($delivery->pedido->itens as $item)
 <tr>
 <td>{{ $item->produto ? $item->produto->nome : 'Produto não encontrado' }}</td>
 <td>{{ $item->quantidade }}</td>
 <td>R$ {{ number_format($item->preco, 2, ',', '.') }}</td>
 <td>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 @endif
 <div class="text-end">
 <a href="{{ route('pedidos.show', $delivery->pedido->id) }}" class="btn btn-outline-primary">
 <i class="fas fa-eye me-2"></i>Ver Pedido Completo
 </a>
 </div>
 </div>
 </div>
 @endif
 </div>
 <!-- Painel de Ações -->
 <div class="col-lg-4">
 <div class="card sticky-top" style="top: 20px;">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Ações da Entrega</h5>
 </div>
 <div class="card-body">
 <!-- Atribuir Entregador (quando entrega está confirmada, preparando, pronta ou em trânsito) -->
 @if(in_array($delivery->status, ['confirmado', 'preparando', 'pronto', 'saiu_entrega']))
 <div class="mb-4">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-motorcycle me-2"></i>Entregador
 </h6>
 @if($delivery->pedido && $delivery->pedido->entregador)
 <!-- Entregador já atribuído -->
 <div class="alert alert-success mb-2">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <strong>{{ $delivery->pedido->entregador->nome }}</strong>
 @if($delivery->tipo_entrega == 'plataforma')
 <span class="badge bg-success ms-2">Plataforma</span>
 @else
 <span class="badge bg-primary ms-2">Fixo</span>
 @endif
 <br>
 <small class="text-muted">
 <i class="fas fa-phone me-1"></i>{{ $delivery->pedido->entregador->telefone }}<br>
 <i class="fas fa-motorcycle me-1"></i>{{ ucfirst($delivery->pedido->entregador->tipo_veiculo) }}
 @if($delivery->pedido->entregador->placa_veiculo)
 - {{ $delivery->pedido->entregador->placa_veiculo }}
 @endif
 @if($delivery->valor_entregador)
 <br><i class="fas fa-dollar-sign me-1"></i>R$ {{ number_format($delivery->valor_entregador, 2, ',', '.') }}
 @endif
 </small>
 </div>
 @if($delivery->status == 'pronto' || $delivery->status == 'preparando')
 <button type="button" class="btn btn-sm btn-outline-danger" 
 onclick="removerEntregador()">
 <i class="fas fa-times"></i>
 </button>
 @endif
 </div>
 </div>
 @elseif($delivery->disponivel_plataforma && (!$delivery->pedido || !$delivery->pedido->entregador))
 <!-- Disponível na plataforma aguardando aceite -->
 <div class="alert alert-warning mb-2">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <strong><i class="fas fa-globe me-2"></i>Disponível na Plataforma</strong><br>
 <small class="text-muted">
 <i class="fas fa-search me-1"></i>Buscando entregadores automaticamente<br>
 <i class="fas fa-dollar-sign me-1"></i>Valor: R$ {{ number_format($delivery->valor_entregador, 2, ',', '.') }}<br>
 <i class="fas fa-clock me-1"></i>Desde: {{ $delivery->disponibilizado_em->format('d/m/Y H:i') }}<br>
 @if($delivery->tentativas_notificacao > 0)
 <i class="fas fa-bell me-1"></i>{{ $delivery->tentativas_notificacao }} tentativa(s) de notificação<br>
 <i class="fas fa-map-marked-alt me-1"></i>Raio de busca: {{ $delivery->raio_busca_km }}km
 @endif
 </small>
 </div>
 <button type="button" class="btn btn-sm btn-outline-danger" 
 onclick="cancelarDisponibilizacao()">
 <i class="fas fa-times"></i> Cancelar
 </button>
 </div>
 </div>
 @else
 <!-- Selecionar tipo de entrega -->
 <div class="alert alert-info mb-3">
 <i class="fas fa-info-circle me-2"></i>
 <strong>Pedido Confirmado!</strong><br>
 Escolha como será realizada a entrega.
 </div>
 
 <div class="mb-4">
 <label class="form-label"><strong>Tipo de Entrega:</strong></label>
 <div class="btn-group w-100 mb-3" role="group">
 <input type="radio" class="btn-check" name="tipo_entrega" id="tipo_fixo" value="fixo" checked>
 <label class="btn btn-outline-primary" for="tipo_fixo">
 <i class="fas fa-user-check me-2"></i>Entregador Fixo
 </label>
 
 <input type="radio" class="btn-check" name="tipo_entrega" id="tipo_plataforma" value="plataforma">
 <label class="btn btn-outline-success" for="tipo_plataforma">
 <i class="fas fa-globe me-2"></i>Plataforma
 </label>
 </div>
 </div>
 
 <!-- Seção Entregador Fixo -->
 <div id="secao_entregador_fixo" class="tipo-entrega-secao">
 <div class="mb-3">
 <label class="form-label"><strong>Selecionar Entregador Disponível:</strong></label>
 <select class="form-select form-select-lg" id="entregador_id">
 <option value="">Escolha um entregador...</option>
 @foreach($entregadoresDisponiveis as $entregador)
 <option value="{{ $entregador->id }}">
 {{ $entregador->nome }} - 
 {{ ucfirst($entregador->tipo_veiculo) }} - 
 ⭐ {{ number_format($entregador->avaliacao_media, 1) }}
 ({{ $entregador->entregas_realizadas }} entregas)
 </option>
 @endforeach
 </select>
 </div>
 <button type="button" class="btn btn-success btn-lg w-100 mb-3" id="btnAtribuirEntregador"
 onclick="atribuirEntregador()" disabled>
 <i class="fas fa-user-check me-2"></i>Atribuir Entregador Fixo
 </button>
 </div>
 
 <!-- Seção Plataforma -->
 <div id="secao_plataforma" class="tipo-entrega-secao" style="display: none;">
 <div class="alert alert-warning mb-3">
 <i class="fas fa-info-circle me-2"></i>
 A entrega ficará disponível no aplicativo para que entregadores da plataforma possam aceitá-la.
 </div>
 <div class="mb-3">
 <label class="form-label"><strong>Valor para o Entregador:</strong></label>
 <div class="input-group input-group-lg">
 <span class="input-group-text">R$</span>
 <input type="number" class="form-control" id="valor_entregador" 
 value="{{ number_format($delivery->taxa_entrega * 0.7, 2, '.', '') }}" 
 step="0.01" min="0">
 </div>
 <small class="text-muted">
 Taxa de entrega: R$ {{ number_format($delivery->taxa_entrega, 2, ',', '.') }} 
 (Sugestão: 70% = R$ {{ number_format($delivery->taxa_entrega * 0.7, 2, ',', '.') }})
 </small>
 </div>
 <button type="button" class="btn btn-success btn-lg w-100 mb-3" 
 onclick="disponibilizarParaPlataforma()">
 <i class="fas fa-globe me-2"></i>Disponibilizar na Plataforma
 </button>
 </div>
 @endif
 </div>
 @endif
 
 @if($delivery->status == 'confirmado' && !$delivery->disponivel_plataforma)
 <form method="POST" action="{{ route('deliveries.iniciar-preparo', $delivery->id) }}" class="mb-2">
 @csrf
 @method('PATCH')
 <button type="submit" class="btn btn-primary w-100">
 <i class="fas fa-play me-2"></i>Iniciar Preparo
 </button>
 </form>
 <form method="POST" action="{{ route('deliveries.cancelar', $delivery->id) }}" class="mb-2">
 @csrf
 @method('PATCH')
 <button type="submit" class="btn btn-danger w-100" 
 onclick="return confirm('Tem certeza que deseja cancelar esta entrega?')">
 <i class="fas fa-times me-2"></i>Cancelar Entrega
 </button>
 </form>
 @endif
 @if($delivery->status == 'preparando')
 <form method="POST" action="{{ route('deliveries.marcar-pronto', $delivery->id) }}" class="mb-2">
 @csrf
 @method('PATCH')
 <button type="submit" class="btn btn-info w-100">
 <i class="fas fa-check-circle me-2"></i>Marcar como Pronto
 </button>
 </form>
 @endif
 @if($delivery->status == 'pronto' && !$delivery->pedido->entregador_id)
 <!-- Saiu para Entrega - Apenas se NÃO houver entregador parceiro -->
 <form method="POST" action="{{ route('deliveries.sair-entrega', $delivery->id) }}" class="mb-2">
 @csrf
 @method('PATCH')
 <button type="submit" class="btn btn-warning w-100">
 <i class="fas fa-truck me-2"></i>Saiu para Entrega (Estabelecimento)
 </button>
 </form>
 @endif
 @if($delivery->status == 'saiu_entrega')
 <form method="POST" action="{{ route('deliveries.marcar-entregue', $delivery->id) }}" class="mb-2">
 @csrf
 @method('PATCH')
 <button type="submit" class="btn btn-success w-100">
 <i class="fas fa-flag-checkered me-2"></i>Marcar como Entregue
 </button>
 </form>
 @endif
 @if(in_array($delivery->status, ['entregue', 'cancelado']))
 <div class="alert alert-info text-center mb-0">
 <i class="fas fa-info-circle me-2"></i>
 @if($delivery->status == 'entregue')
 Entrega finalizada com sucesso!
 @else
 Esta entrega foi cancelada.
 @endif
 </div>
 @endif
 <!-- Informações de Tempo -->
 <hr>
 <h6 class="text-muted"><i class="fas fa-clock me-2"></i>Timeline</h6>
 <ul class="list-unstyled mb-0">
 <li class="mb-2">
 <small class="text-muted">Criado:</small><br>
 <strong>{{ $delivery->created_at->format('d/m/Y H:i') }}</strong>
 </li>
 @if($delivery->data_confirmacao)
 <li class="mb-2">
 <small class="text-muted">Confirmado:</small><br>
 <strong>{{ $delivery->data_confirmacao->format('d/m/Y H:i') }}</strong>
 </li>
 @endif
 @if($delivery->data_entrega)
 <li class="mb-2">
 <small class="text-muted">Entregue:</small><br>
 <strong>{{ $delivery->data_entrega->format('d/m/Y H:i') }}</strong>
 </li>
 @endif
 </ul>
 </div>
 </div>
 </div>
 </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectEntregador = document.getElementById('entregador_id');
    const btnAtribuir = document.getElementById('btnAtribuirEntregador');
    
    if (selectEntregador && btnAtribuir) {
        selectEntregador.addEventListener('change', function() {
            btnAtribuir.disabled = !this.value;
        });
    }
    
    // Controle dos tipos de entrega
    const radioFixo = document.getElementById('tipo_fixo');
    const radioPlataforma = document.getElementById('tipo_plataforma');
    const secaoFixo = document.getElementById('secao_entregador_fixo');
    const secaoPlataforma = document.getElementById('secao_plataforma');
    
    if (radioFixo && radioPlataforma) {
        radioFixo.addEventListener('change', function() {
            if (this.checked) {
                secaoFixo.style.display = 'block';
                secaoPlataforma.style.display = 'none';
            }
        });
        
        radioPlataforma.addEventListener('change', function() {
            if (this.checked) {
                secaoFixo.style.display = 'none';
                secaoPlataforma.style.display = 'block';
            }
        });
    }
});

function atribuirEntregador() {
    const entregadorId = document.getElementById('entregador_id').value;
    
    if (!entregadorId) {
        alert('Por favor, selecione um entregador.');
        return;
    }
    
    if (confirm('Deseja atribuir este entregador à entrega?')) {
        const url = '{{ route("deliveries.atribuir-fixo", $delivery->id) }}';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                entregador_id: entregadorId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Entregador atribuído com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Não foi possível atribuir o entregador'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao atribuir entregador: ' + error.message);
        });
    }
}

function disponibilizarParaPlataforma() {
    const valorEntregador = document.getElementById('valor_entregador').value;
    
    if (!valorEntregador || parseFloat(valorEntregador) <= 0) {
        alert('Por favor, informe um valor válido para o entregador.');
        return;
    }
    
    if (confirm('Deseja disponibilizar esta entrega na plataforma por R$ ' + parseFloat(valorEntregador).toFixed(2) + '?')) {
        const url = '{{ route("deliveries.disponibilizar-plataforma", $delivery->id) }}';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                valor_entregador: parseFloat(valorEntregador)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Entrega disponibilizada na plataforma com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Não foi possível disponibilizar a entrega'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao disponibilizar entrega: ' + error.message);
        });
    }
}

function cancelarDisponibilizacao() {
    if (!confirm('Tem certeza que deseja cancelar a disponibilização na plataforma?')) {
        return;
    }
    
    const url = '{{ route("deliveries.cancelar-plataforma", $delivery->id) }}';
    console.log('Cancelando em:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        console.log('Status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Erro HTTP ' + response.status + ': ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Resposta:', data);
        if (data.success) {
            alert('Disponibilização cancelada com sucesso!');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível cancelar'));
        }
    })
    .catch(error => {
        console.error('Erro completo:', error);
        alert('Erro ao cancelar disponibilização: ' + error.message);
    });
}

function removerEntregador() {
    if (!confirm('Tem certeza que deseja remover este entregador?')) {
        return;
    }
    
    fetch('{{ route("deliveries.remover-entregador", $delivery->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Entregador removido com sucesso!');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível remover o entregador'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao remover entregador');
    });
}
</script>
@endpush