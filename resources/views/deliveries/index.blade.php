@extends('layouts.app')
@section('title', 'Delivery - Gestão de Entregas')
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1><i class="fas fa-shipping-fast me-2"></i>Gestão de Entregas</h1>
 <div>
 <a href="{{ route('pedidos.create') }}" class="btn btn-success me-2">
 <i class="fas fa-plus me-2"></i>Novo Pedido Delivery
 </a>
 <a href="{{ route('cozinha.monitor') }}" class="btn btn-info" target="_blank">
 <i class="fas fa-tv me-2"></i>Monitor Cozinha
 </a>
 </div>
 </div>
 <!-- Filtros -->
 <div class="row mb-4">
 <div class="col-md-12">
 <div class="card">
 <div class="card-body">
 <form method="GET" class="row g-3">
 <div class="col-md-3">
 <label class="form-label">Status</label>
 <select name="status" class="form-select">
 <option value="">Todos os Status</option>
 <option value="em_preparo" {{ request('status') == 'em_preparo' ? 'selected' : '' }}>Em Preparo</option>
 <option value="pronto" {{ request('status') == 'pronto' ? 'selected' : '' }}>Pronto</option>
 <option value="entregue" {{ request('status') == 'entregue' ? 'selected' : '' }}>Entregue</option>
 </select>
 </div>
 <div class="col-md-3">
 <label class="form-label">Cliente</label>
 <input type="text" name="cliente" class="form-control" value="{{ request('cliente') }}" placeholder="Nome do cliente">
 </div>
 <div class="col-md-3">
 <label class="form-label">Telefone</label>
 <input type="text" name="telefone" class="form-control" value="{{ request('telefone') }}" placeholder="Telefone">
 </div>
 <div class="col-md-3">
 <div class="d-flex align-items-end h-100">
 <button type="submit" class="btn btn-outline-primary me-2">
 <i class="fas fa-search me-2"></i>Filtrar
 </button>
 <a href="{{ route('deliveries.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-times"></i>
 </a>
 </div>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
 <!-- Cards de Status -->
 <div class="row mb-4">
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-warning">
 <div class="card-body text-center">
 <h6 class="card-title">Em Preparo</h6>
 <h3>{{ $estatisticas['em_preparo'] ?? 0 }}</h3>
 <small>Aguardando cozinha</small>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-danger">
 <div class="card-body text-center">
 <h6 class="card-title">Sem Entregador</h6>
 <h3>{{ $estatisticas['aguardando_entregador'] ?? 0 }}</h3>
 <small>Precisa atribuir</small>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-primary">
 <div class="card-body text-center">
 <h6 class="card-title">Prontos</h6>
 <h3>{{ $estatisticas['prontos'] ?? 0 }}</h3>
 <small>Aguardando entrega</small>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-info">
 <div class="card-body text-center">
 <h6 class="card-title">Em Rota</h6>
 <h3>{{ $estatisticas['em_rota'] ?? 0 }}</h3>
 <small>Saíram para entrega</small>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-success">
 <div class="card-body text-center">
 <h6 class="card-title">Entregues</h6>
 <h3>{{ $estatisticas['entregues'] ?? 0 }}</h3>
 <small>Hoje</small>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-dark">
 <div class="card-body text-center">
 <h6 class="card-title">Faturamento</h6>
 <h3>R$ {{ number_format($estatisticas['faturamento'] ?? 0, 0, ',', '.') }}</h3>
 <small>Hoje</small>
 </div>
 </div>
 </div>
 </div>
 <!-- Tabela de Entregas -->
 <div class="row">
 <div class="col-md-12">
 <div class="card">
 <div class="card-body">
 @if($deliveries->isEmpty())
 <div class="text-center py-5">
 <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
 <h5 class="text-muted">Nenhum pedido de delivery em preparo</h5>
 <p class="text-muted">Pedidos de delivery aparecerão aqui quando estiverem com status "Em Preparo".</p>
 <a href="{{ route('pedidos.create') }}" class="btn btn-success">
 <i class="fas fa-plus me-2"></i>Criar Novo Pedido Delivery
 </a>
 </div>
 @else
 <div class="table-responsive">
 <table class="table table-hover">
 <thead class="table-dark">
 <tr>
 <th>Pedido</th>
 <th>Cliente</th>
 <th>Telefone</th>
 <th>Endereço</th>
 <th>Itens</th>
 <th>Total</th>
 <th>Entregador</th>
 <th>Status</th>
 <th>Ações</th>
 </tr>
 </thead>
 <tbody>
 @foreach($deliveries as $pedido)
 <tr>
 <td>
 <strong>#{{ $pedido->id }}</strong>
 <br>
 <small class="text-muted">{{ $pedido->created_at->format('d/m H:i') }}</small>
 </td>
 <td>{{ $pedido->delivery->cliente_nome ?? 'N/A' }}</td>
 <td>{{ $pedido->delivery->cliente_telefone ?? 'N/A' }}</td>
 <td>
 <small>
 {{ $pedido->delivery->endereco_rua ?? '' }} 
 {{ $pedido->delivery->endereco_numero ?? '' }}<br>
 {{ $pedido->delivery->endereco_bairro ?? '' }}
 </small>
 </td>
 <td>{{ $pedido->itens->count() }} itens</td>
 <td><strong>R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong></td>
 <td>
 @if($pedido->entregador)
 <span class="badge bg-success">
 <i class="fas fa-user-check"></i> {{ $pedido->entregador->nome }}
 </span>
 @elseif($pedido->delivery && $pedido->delivery->disponivel_plataforma)
 <span class="badge bg-warning text-dark">
 <i class="fas fa-search"></i> Buscando na plataforma
 </span>
 @else
 <span class="badge bg-danger">
 <i class="fas fa-user-times"></i> Sem entregador
 </span>
 @endif
 </td>
 <td>
 @if($pedido->status == 'em_preparo')
 <span class="badge bg-warning">
 <i class="fas fa-clock"></i> Em Preparo
 </span>
 @elseif($pedido->status == 'pronto')
 <span class="badge bg-primary">
 <i class="fas fa-check-circle"></i> Pronto
 </span>
 @elseif($pedido->status == 'entregue')
 <span class="badge bg-success">
 <i class="fas fa-check-double"></i> Entregue
 </span>
 @endif
 </td>
 <td>
 <div class="btn-group" role="group">
 <a href="{{ route('deliveries.show', $pedido->delivery->id) }}" 
 class="btn btn-sm btn-outline-primary" title="Ver detalhes">
 <i class="fas fa-eye"></i>
 </a>
 
 @if($pedido->status == 'em_preparo' && !$pedido->entregador_id && (!$pedido->delivery || !$pedido->delivery->disponivel_plataforma))
 <button type="button" class="btn btn-sm btn-success" 
 data-bs-toggle="modal" 
 data-bs-target="#modalAtribuirEntregador{{ $pedido->id }}"
 title="Atribuir Entregador">
 <i class="fas fa-user-plus"></i>
 </button>
 @endif
 
 @if($pedido->entregador_id && $pedido->status == 'em_preparo')
 <form method="POST" action="{{ route('pedidos.remover-entregador', $pedido->id) }}" class="d-inline">
 @csrf
 <button type="submit" class="btn btn-sm btn-warning" title="Remover Entregador"
 onclick="return confirm('Remover entregador deste pedido?')">
 <i class="fas fa-user-times"></i>
 </button>
 </form>
 @endif
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
</div>

<!-- Modais para atribuir entregador -->
@foreach($deliveries->where('status', 'em_preparo')->whereNull('entregador_id') as $pedido)
<div class="modal fade" id="modalAtribuirEntregador{{ $pedido->id }}" tabindex="-1">
 <div class="modal-dialog">
 <div class="modal-content">
 <div class="modal-header bg-primary text-white">
 <h5 class="modal-title">
 <i class="fas fa-user-plus me-2"></i>Atribuir Entregador - Pedido #{{ $pedido->id }}
 </h5>
 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
 </div>
 <form method="POST" action="{{ route('pedidos.atribuir-entregador', $pedido->id) }}">
 @csrf
 <div class="modal-body">
 <div class="mb-3">
 <label class="form-label">Cliente</label>
 <input type="text" class="form-control" value="{{ $pedido->delivery->cliente_nome }}" readonly>
 </div>
 <div class="mb-3">
 <label class="form-label">Endereço</label>
 <textarea class="form-control" rows="2" readonly>{{ $pedido->delivery->endereco_rua }} {{ $pedido->delivery->endereco_numero }}, {{ $pedido->delivery->endereco_bairro }}</textarea>
 </div>
 <div class="mb-3">
 <label class="form-label">Total do Pedido</label>
 <input type="text" class="form-control" value="R$ {{ number_format($pedido->total, 2, ',', '.') }}" readonly>
 </div>
 <div class="mb-3">
 <label class="form-label">Selecionar Entregador *</label>
 <select name="entregador_id" class="form-select" required>
 <option value="">Escolha um entregador...</option>
 @php
 $entregadores = \App\Models\Entregador::where('status', 'ativo')
 ->where('disponivel', 1)
 ->where('tenant_code', auth('admin')->user()->tenant_code ?? auth()->user()->tenant_code)
 ->orderBy('nome')
 ->get();
 @endphp
 @foreach($entregadores as $entregador)
 <option value="{{ $entregador->id }}">
 {{ $entregador->nome }} - {{ $entregador->tipo_veiculo }}
 </option>
 @endforeach
 </select>
 <small class="text-muted">Apenas entregadores ativos e disponíveis</small>
 </div>
 <div class="alert alert-info">
 <i class="fas fa-info-circle me-2"></i>
 O entregador será notificado e deverá aceitar a entrega.
 </div>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
 <button type="submit" class="btn btn-success">
 <i class="fas fa-check me-2"></i>Atribuir Entregador
 </button>
 </div>
 </form>
 </div>
 </div>
</div>
@endforeach

@endsection