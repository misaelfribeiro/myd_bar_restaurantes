@extends('layouts.app')
@section('title', 'Bônus')
@section('content')
<div class="container-fluid px-4">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">🎁 Bônus</h1>
 <p class="text-muted mb-0">Gerencie os bônus dos funcionários</p>
 </div>
 <a href="{{ route('admin.bonus.create') }}" class="btn btn-primary">
 <i class="fas fa-plus"></i> Novo Bônus
 </a>
 </div>
 @if(session('success'))
 <div class="alert alert-success alert-dismissible fade show">
 {{ session('success') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 <div class="row g-3 mb-4">
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-grow-1">
 <p class="text-muted mb-1 small">Total de Bônus</p>
 <h4 class="mb-0">{{ $bonus->count() }}</h4>
 </div>
 <div class="bg-primary bg-opacity-10 p-3 rounded">
 <i class="fas fa-gift text-primary fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-grow-1">
 <p class="text-muted mb-1 small">Pendentes</p>
 <h4 class="mb-0">R$ {{ number_format($bonus->where('status', 'pendente')->sum('valor'), 2, ',', '.') }}</h4>
 </div>
 <div class="bg-warning bg-opacity-10 p-3 rounded">
 <i class="fas fa-clock text-warning fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-grow-1">
 <p class="text-muted mb-1 small">Pagos</p>
 <h4 class="mb-0">R$ {{ number_format($bonus->where('status', 'pago')->sum('valor'), 2, ',', '.') }}</h4>
 </div>
 <div class="bg-success bg-opacity-10 p-3 rounded">
 <i class="fas fa-check-circle text-success fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-grow-1">
 <p class="text-muted mb-1 small">Total Geral</p>
 <h4 class="mb-0">R$ {{ number_format($bonus->sum('valor'), 2, ',', '.') }}</h4>
 </div>
 <div class="bg-info bg-opacity-10 p-3 rounded">
 <i class="fas fa-money-bill-wave text-info fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-body">
 <form method="GET" class="row g-3">
 <div class="col-md-4">
 <label class="form-label">Funcionário</label>
 <select name="funcionario_id" class="form-select">
 <option value="">Todos</option>
 @foreach($funcionarios as $func)
 <option value="{{ $func->id }}" {{ request('funcionario_id') == $func->id ? 'selected' : '' }}>
 {{ $func->nome_completo }}
 </option>
 @endforeach
 </select>
 </div>
 <div class="col-md-3">
 <label class="form-label">Tipo</label>
 <input type="text" name="tipo" class="form-control" value="{{ request('tipo') }}" placeholder="Ex: Desempenho">
 </div>
 <div class="col-md-3">
 <label class="form-label">Status</label>
 <select name="status" class="form-select">
 <option value="">Todos</option>
 <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
 <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pago</option>
 <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
 </select>
 </div>
 <div class="col-md-2">
 <label class="form-label">&nbsp;</label>
 <button type="submit" class="btn btn-primary w-100">
 <i class="fas fa-filter"></i> Filtrar
 </button>
 </div>
 </form>
 </div>
 </div>
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover align-middle">
 <thead>
 <tr>
 <th>Funcionário</th>
 <th>Tipo</th>
 <th>Descrição</th>
 <th>Data Ref.</th>
 <th>Valor</th>
 <th>Status</th>
 <th>Data Pagamento</th>
 <th width="150">Ações</th>
 </tr>
 </thead>
 <tbody>
 @forelse($bonus as $item)
 <tr>
 <td><strong>{{ $item->funcionario->nome_completo }}</strong></td>
 <td><span class="badge bg-info">{{ $item->tipo }}</span></td>
 <td>{{ Str::limit($item->descricao, 40) }}</td>
 <td>{{ $item->data_referencia ? $item->data_referencia->format('d/m/Y') : '-' }}</td>
 <td><strong class="text-success">R$ {{ number_format($item->valor, 2, ',', '.') }}</strong></td>
 <td>
 @if($item->status == 'pendente')
 <span class="badge bg-warning">Pendente</span>
 @elseif($item->status == 'pago')
 <span class="badge bg-success">Pago</span>
 @else
 <span class="badge bg-danger">Cancelado</span>
 @endif
 </td>
 <td>{{ $item->data_pagamento ? $item->data_pagamento->format('d/m/Y') : '-' }}</td>
 <td>
 <a href="{{ route('admin.bonus.edit', $item->id) }}" 
 class="btn btn-sm btn-warning" title="Editar">
 <i class="fas fa-edit"></i>
 </a>
 <form action="{{ route('admin.bonus.destroy', $item->id) }}" 
 method="POST" class="d-inline" 
 onsubmit="return confirm('Tem certeza que deseja excluir este bônus?')">
 @csrf
 @method('DELETE')
 <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
 <i class="fas fa-trash"></i>
 </button>
 </form>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="8" class="text-center py-4">
 <i class="fas fa-gift fa-3x text-muted mb-3"></i>
 <p class="text-muted">Nenhum bônus cadastrado ainda.</p>
 <a href="{{ route('admin.bonus.create') }}" class="btn btn-primary">
 <i class="fas fa-plus"></i> Cadastrar Primeiro Bônus
 </a>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 </div>
</div>
@endsection