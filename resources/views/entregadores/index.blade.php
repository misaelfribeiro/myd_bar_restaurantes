@extends('layouts.app')
@section('title', 'Entregadores')
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1 class="h3 text-gray-800">
 <i class="fas fa-motorcycle mr-2"></i>Gestão de Entregadores
 </h1>
 <a href="{{ route('entregadores.create') }}" class="btn btn-primary">
 <i class="fas fa-plus mr-2"></i>Novo Entregador
 </a>
 </div>
 <!-- Filtros -->
 <div class="card shadow mb-4">
 <div class="card-header py-3">
 <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
 </div>
 <div class="card-body">
 <form method="GET" action="{{ route('entregadores.index') }}">
 <div class="row">
 <div class="col-md-3">
 <label for="busca" class="form-label">Buscar</label>
 <input type="text" class="form-control" id="busca" name="busca" 
 value="{{ request('busca') }}" placeholder="Nome, email, telefone ou CPF">
 </div>
 <div class="col-md-2">
 <label for="status" class="form-label">Status</label>
 <select class="form-control" id="status" name="status">
 <option value="">Todos</option>
 @foreach($statusOptions as $value => $label)
 <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
 {{ $label }}
 </option>
 @endforeach
 </select>
 </div>
 <div class="col-md-2">
 <label for="tipo" class="form-label">Tipo</label>
 <select class="form-control" id="tipo" name="tipo">
 <option value="">Todos</option>
 @foreach($tipoOptions as $value => $label)
 <option value="{{ $value }}" {{ request('tipo') == $value ? 'selected' : '' }}>
 {{ $label }}
 </option>
 @endforeach
 </select>
 </div>
 <div class="col-md-2">
 <label for="disponivel" class="form-label">Disponibilidade</label>
 <select class="form-control" id="disponivel" name="disponivel">
 <option value="">Todos</option>
 <option value="sim" {{ request('disponivel') == 'sim' ? 'selected' : '' }}>Disponível</option>
 <option value="nao" {{ request('disponivel') == 'nao' ? 'selected' : '' }}>Indisponível</option>
 </select>
 </div>
 <div class="col-md-3 d-flex align-items-end">
 <button type="submit" class="btn btn-primary mr-2">
 <i class="fas fa-search mr-1"></i>Filtrar
 </button>
 <a href="{{ route('entregadores.index') }}" class="btn btn-secondary">
 <i class="fas fa-times mr-1"></i>Limpar
 </a>
 </div>
 </div>
 </form>
 </div>
 </div>
 <!-- Lista de Entregadores -->
 <div class="card shadow">
 <div class="card-header py-3">
 <h6 class="m-0 font-weight-bold text-primary">Lista de Entregadores</h6>
 </div>
 <div class="card-body">
 @if($entregadores->count() > 0)
 <div class="table-responsive">
 <table class="table table-bordered">
 <thead>
 <tr>
 <th>Foto</th>
 <th>Nome</th>
 <th>Contato</th>
 <th>Tipo</th>
 <th>Veículo</th>
 <th>Status</th>
 <th>Disponível</th>
 <th>Estatísticas</th>
 <th>Ações</th>
 </tr>
 </thead>
 <tbody>
 @foreach($entregadores as $entregador)
 <tr>
 <td class="text-center">
 @if($entregador->foto_entregador)
 <img src="{{ asset('storage/' . $entregador->foto_entregador) }}" 
 class="rounded-circle" width="50" height="50" alt="Foto">
 @else
 <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
 style="width: 50px; height: 50px;">
 <i class="fas fa-user text-white"></i>
 </div>
 @endif
 </td>
 <td>
 <strong>{{ $entregador->nome }}</strong><br>
 <small class="text-muted">{{ $entregador->email }}</small>
 </td>
 <td>
 @if($entregador->whatsapp)
 <i class="fab fa-whatsapp text-success"></i> {{ $entregador->whatsapp }}<br>
 @endif
 @if($entregador->telefone)
 <i class="fas fa-phone text-info"></i> {{ $entregador->telefone }}
 @endif
 </td>
 <td>
 <span class="badge {{ $entregador->tipo == 'interno' ? 'bg-success' : 'bg-info' }} text-white">
 {{ $entregador->tipo == 'interno' ? 'Interno' : 'App Externo' }}
 </span>
 </td>
 <td>
 @if($entregador->tipo_veiculo)
 <i class="fas fa-{{ $entregador->tipo_veiculo == 'moto' ? 'motorcycle' : ($entregador->tipo_veiculo == 'carro' ? 'car' : ($entregador->tipo_veiculo == 'bicicleta' ? 'bicycle' : 'walking')) }}"></i>
 {{ ucfirst($entregador->tipo_veiculo) }}
 @if($entregador->placa_veiculo)
 <br><small class="text-muted">{{ $entregador->placa_veiculo }}</small>
 @endif
 @endif
 </td>
 <td>
 <span class="badge bg-{{ $entregador->status == 'ativo' ? 'success' : ($entregador->status == 'aprovado' ? 'primary' : ($entregador->status == 'pendente' ? 'warning' : ($entregador->status == 'suspenso' ? 'danger' : 'secondary'))) }} text-white">
 {{ ucfirst($entregador->status) }}
 </span>
 </td>
 <td class="text-center">
 @if($entregador->disponivel)
 <i class="fas fa-check-circle text-success" title="Disponível"></i>
 @else
 <i class="fas fa-times-circle text-danger" title="Indisponível"></i>
 @endif
 </td>
 <td>
 <small>
 📦 {{ $entregador->entregas_realizadas ?? 0 }} entregas<br>
 ⭐ {{ number_format($entregador->avaliacao_media ?? 0, 1) }}/5.0<br>
 📊 {{ number_format($entregador->taxa_sucesso ?? 0, 1) }}% sucesso
 </small>
 </td>
 <td>
 <div class="btn-group" role="group">
 <a href="{{ route('entregadores.show', $entregador) }}" 
 class="btn btn-sm btn-info" title="Visualizar">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('entregadores.edit', $entregador) }}" 
 class="btn btn-sm btn-primary" title="Editar">
 <i class="fas fa-edit"></i>
 </a>
 @if(auth()->check())
 @if($entregador->status == 'pendente')
 <button type="button" class="btn btn-sm btn-success" 
 data-toggle="modal" data-target="#aprovarModal{{ $entregador->id }}" title="Aprovar">
 <i class="fas fa-check"></i>
 </button>
 <button type="button" class="btn btn-sm btn-danger" 
 data-toggle="modal" data-target="#reprovarModal{{ $entregador->id }}" title="Reprovar">
 <i class="fas fa-times"></i>
 </button>
 @elseif($entregador->status == 'aprovado')
 <form method="POST" action="{{ route('entregadores.ativar', $entregador) }}" class="d-inline">
 @csrf
 <button type="submit" class="btn btn-sm btn-success" title="Ativar">
 <i class="fas fa-play"></i>
 </button>
 </form>
 @elseif($entregador->status == 'ativo')
 <form method="POST" action="{{ route('entregadores.desativar', $entregador) }}" class="d-inline">
 @csrf
 <button type="submit" class="btn btn-sm btn-warning" title="Desativar">
 <i class="fas fa-pause"></i>
 </button>
 </form>
 <button type="button" class="btn btn-sm btn-danger" 
 data-toggle="modal" data-target="#suspenderModal{{ $entregador->id }}" title="Suspender">
 <i class="fas fa-ban"></i>
 </button>
 @endif
 @endif
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 <!-- Paginação -->
 <div class="d-flex justify-content-center mt-4">
 {{ $entregadores->appends(request()->query())->links() }}
 </div>
 @else
 <div class="text-center py-4">
 <i class="fas fa-motorcycle fa-3x text-muted mb-3"></i>
 <h5 class="text-muted">Nenhum entregador encontrado</h5>
 <p class="text-muted">Cadastre o primeiro entregador para começar</p>
 <a href="{{ route('entregadores.create') }}" class="btn btn-primary">
 <i class="fas fa-plus mr-2"></i>Cadastrar Entregador
 </a>
 </div>
 @endif
 </div>
 </div>
</div>
<!-- Modais de Aprovação/Reprovação/Suspensão -->
@foreach($entregadores as $entregador)
 @if(auth()->check())
 <!-- Modal Aprovação -->
 <div class="modal fade" id="aprovarModal{{ $entregador->id }}" tabindex="-1">
 <div class="modal-dialog">
 <div class="modal-content">
 <form method="POST" action="{{ route('entregadores.aprovar', $entregador) }}">
 @csrf
 <div class="modal-header">
 <h5 class="modal-title">Aprovar Entregador</h5>
 <button type="button" class="close" data-dismiss="modal">&times;</button>
 </div>
 <div class="modal-body">
 <p>Deseja aprovar o entregador <strong>{{ $entregador->nome }}</strong>?</p>
 <div class="form-group">
 <label for="observacoes">Observações (opcional)</label>
 <textarea name="observacoes" class="form-control" rows="3" 
 placeholder="Observações sobre a aprovação..."></textarea>
 </div>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
 <button type="submit" class="btn btn-success">Aprovar</button>
 </div>
 </form>
 </div>
 </div>
 </div>
 <!-- Modal Reprovação -->
 <div class="modal fade" id="reprovarModal{{ $entregador->id }}" tabindex="-1">
 <div class="modal-dialog">
 <div class="modal-content">
 <form method="POST" action="{{ route('entregadores.reprovar', $entregador) }}">
 @csrf
 <div class="modal-header">
 <h5 class="modal-title">Reprovar Entregador</h5>
 <button type="button" class="close" data-dismiss="modal">&times;</button>
 </div>
 <div class="modal-body">
 <p>Deseja reprovar o entregador <strong>{{ $entregador->nome }}</strong>?</p>
 <div class="form-group">
 <label for="observacoes">Motivo da reprovação *</label>
 <textarea name="observacoes" class="form-control" rows="3" required
 placeholder="Descreva o motivo da reprovação..."></textarea>
 </div>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
 <button type="submit" class="btn btn-danger">Reprovar</button>
 </div>
 </form>
 </div>
 </div>
 </div>
 <!-- Modal Suspensão -->
 <div class="modal fade" id="suspenderModal{{ $entregador->id }}" tabindex="-1">
 <div class="modal-dialog">
 <div class="modal-content">
 <form method="POST" action="{{ route('entregadores.suspender', $entregador) }}">
 @csrf
 <div class="modal-header">
 <h5 class="modal-title">Suspender Entregador</h5>
 <button type="button" class="close" data-dismiss="modal">&times;</button>
 </div>
 <div class="modal-body">
 <p>Deseja suspender o entregador <strong>{{ $entregador->nome }}</strong>?</p>
 <div class="form-group">
 <label for="observacoes">Motivo da suspensão *</label>
 <textarea name="observacoes" class="form-control" rows="3" required
 placeholder="Descreva o motivo da suspensão..."></textarea>
 </div>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
 <button type="submit" class="btn btn-danger">Suspender</button>
 </div>
 </form>
 </div>
 </div>
 </div>
 @endif
@endforeach
@endsection