@extends('layouts.app')
@section('title', 'Comissões')
@section('content')
<div class="container-fluid px-4">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">💰 Comissões</h1>
 <p class="text-muted mb-0">Gerencie as comissões dos funcionários</p>
 </div>
 <a href="{{ route('admin.comissoes.create') }}" class="btn btn-primary">
 <i class="fas fa-plus"></i> Nova Comissão
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
 <p class="text-muted mb-1 small">Total de Comissões</p>
 <h4 class="mb-0">{{ $comissoes->count() }}</h4>
 </div>
 <div class="bg-primary bg-opacity-10 p-3 rounded">
 <i class="fas fa-percent text-primary fa-2x"></i>
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
 <h4 class="mb-0">R$ {{ number_format($comissoes->where('status', 'pendente')->sum('valor_comissao'), 2, ',', '.') }}</h4>
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
 <p class="text-muted mb-1 small">Pagas</p>
 <h4 class="mb-0">R$ {{ number_format($comissoes->where('status', 'pago')->sum('valor_comissao'), 2, ',', '.') }}</h4>
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
 <h4 class="mb-0">R$ {{ number_format($comissoes->sum('valor_comissao'), 2, ',', '.') }}</h4>
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
 <label class="form-label">Status</label>
 <select name="status" class="form-select">
 <option value="">Todos</option>
 <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
 <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pago</option>
 <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
 </select>
 </div>
 <div class="col-md-3">
 <label class="form-label">Mês</label>
 <select name="mes" class="form-select">
 <option value="">Todos</option>
 @for($i = 1; $i <= 12; $i++)
 <option value="{{ $i }}" {{ request('mes') == $i ? 'selected' : '' }}>
 {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
 </option>
 @endfor
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
 <th>Data Ref.</th>
 <th>Valor Base</th>
 <th>Percentual</th>
 <th>Valor Comissão</th>
 <th>Status</th>
 <th>Data Pagamento</th>
 <th width="150">Ações</th>
 </tr>
 </thead>
 <tbody>
 @forelse($comissoes as $comissao)
 <tr>
 <td><strong>{{ $comissao->funcionario->nome_completo }}</strong></td>
 <td>{{ $comissao->data_referencia ? $comissao->data_referencia->format('d/m/Y') : '-' }}</td>
 <td>R$ {{ number_format($comissao->valor_base, 2, ',', '.') }}</td>
 <td>{{ number_format($comissao->percentual, 2, ',', '.') }}%</td>
 <td><strong class="text-success">R$ {{ number_format($comissao->valor_comissao, 2, ',', '.') }}</strong></td>
 <td>
 @if($comissao->status == 'pendente')
 <span class="badge bg-warning">Pendente</span>
 @elseif($comissao->status == 'pago')
 <span class="badge bg-success">Pago</span>
 @else
 <span class="badge bg-danger">Cancelado</span>
 @endif
 </td>
 <td>{{ $comissao->data_pagamento ? $comissao->data_pagamento->format('d/m/Y') : '-' }}</td>
 <td>
 <a href="{{ route('admin.comissoes.edit', $comissao->id) }}" 
 class="btn btn-sm btn-warning" title="Editar">
 <i class="fas fa-edit"></i>
 </a>
 <form action="{{ route('admin.comissoes.destroy', $comissao->id) }}" 
 method="POST" class="d-inline" 
 onsubmit="return confirm('Tem certeza que deseja excluir esta comissão?')">
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
 <i class="fas fa-percent fa-3x text-muted mb-3"></i>
 <p class="text-muted">Nenhuma comissão cadastrada ainda.</p>
 <a href="{{ route('admin.comissoes.create') }}" class="btn btn-primary">
 <i class="fas fa-plus"></i> Cadastrar Primeira Comissão
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