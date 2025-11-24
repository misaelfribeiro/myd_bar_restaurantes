@extends('layouts.app')
@section('content')
<div class="container">
 <div class="row">
 <div class="col-md-12">
 <div class="card">
 <div class="card-header d-flex justify-content-between align-items-center">
 <h5 class="mb-0"><i class="fas fa-users"></i> Clientes</h5>
 <a href="{{ route('clientes.create') }}" class="btn btn-primary">
 <i class="fas fa-plus"></i> Novo Cliente
 </a>
 </div>
 <div class="card-body">
 <!-- Filtros -->
 <form method="GET" action="{{ route('clientes.index') }}" class="mb-3">
 <div class="row">
 <div class="col-md-6">
 <div class="input-group">
 <input type="text" name="search" class="form-control" 
 placeholder="Buscar por nome, telefone ou email..." 
 value="{{ request('search') }}">
 <div class="input-group-append">
 <button class="btn btn-outline-secondary" type="submit">
 <i class="fas fa-search"></i>
 </button>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <select name="status" class="form-control">
 <option value="">Todos os status</option>
 <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
 <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
 </select>
 </div>
 <div class="col-md-3">
 @if(request()->hasAny(['search', 'status']))
 <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
 <i class="fas fa-times"></i> Limpar
 </a>
 @endif
 </div>
 </div>
 </form>
 @if($clientes->count() > 0)
 <div class="table-responsive">
 <table class="table table-hover">
 <thead>
 <tr>
 <th>Nome</th>
 <th>Telefone</th>
 <th>Email</th>
 <th>Endereço</th>
 <th>Pedidos</th>
 <th>Deliveries</th>
 <th>Status</th>
 <th>Ações</th>
 </tr>
 </thead>
 <tbody>
 @foreach($clientes as $cliente)
 <tr>
 <td>
 <strong>{{ $cliente->nome }}</strong>
 </td>
 <td>{{ $cliente->telefone }}</td>
 <td>{{ $cliente->email ?: '-' }}</td>
 <td>
 <small class="text-muted">
 @if($cliente->endereco_rua)
 {{ $cliente->endereco_completo }}
 @else
 <em>Não informado</em>
 @endif
 </small>
 </td>
 <td>
 <span class="badge badge-info">{{ $cliente->pedidos_count }}</span>
 </td>
 <td>
 <span class="badge badge-primary">{{ $cliente->deliveries_count }}</span>
 </td>
 <td>
 @if($cliente->ativo)
 <span class="badge badge-success">Ativo</span>
 @else
 <span class="badge badge-secondary">Inativo</span>
 @endif
 </td>
 <td>
 <div class="btn-group">
 <a href="{{ route('clientes.show', $cliente) }}" 
 class="btn btn-sm btn-info" title="Ver">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('clientes.edit', $cliente) }}" 
 class="btn btn-sm btn-warning" title="Editar">
 <i class="fas fa-edit"></i>
 </a>
 <form action="{{ route('clientes.toggle-status', $cliente) }}" 
 method="POST" style="display: inline;">
 @csrf
 @method('PATCH')
 <button type="submit" 
 class="btn btn-sm {{ $cliente->ativo ? 'btn-secondary' : 'btn-success' }}" 
 title="{{ $cliente->ativo ? 'Desativar' : 'Ativar' }}">
 <i class="fas {{ $cliente->ativo ? 'fa-pause' : 'fa-play' }}"></i>
 </button>
 </form>
 @if(!$cliente->pedidos_count && !$cliente->deliveries_count)
 <form action="{{ route('clientes.destroy', $cliente) }}" 
 method="POST" style="display: inline;"
 onsubmit="return confirm('Tem certeza que deseja excluir este cliente?')">
 @csrf
 @method('DELETE')
 <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
 <i class="fas fa-trash"></i>
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
 {{ $clientes->links() }}
 @else
 <div class="text-center py-4">
 <i class="fas fa-users fa-3x text-muted mb-3"></i>
 <p class="text-muted">Nenhum cliente encontrado.</p>
 <a href="{{ route('clientes.create') }}" class="btn btn-primary">
 <i class="fas fa-plus"></i> Cadastrar Primeiro Cliente
 </a>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
</div>
@endsection