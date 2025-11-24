@extends('layouts.app')
@section('title', 'Financeiro - Faturas')
@section('content')
<div class="container-fluid px-4">
 <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">💰 Financeiro - Faturas</h1>
 <p class="text-muted mb-0">Gestão de faturas e pagamentos</p>
 </div>
 <div>
 <a href="{{ route('admin.financeiro.relatorios') }}" class="btn btn-outline-primary me-2">
 <i class="fas fa-chart-line"></i> Relatórios
 </a>
 <a href="{{ route('admin.financeiro.create') }}" class="btn btn-primary">
 <i class="fas fa-plus"></i> Nova Fatura
 </a>
 </div>
 </div>
 @if(session('success'))
 <div class="alert alert-success alert-dismissible fade show">
 {{ session('success') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 <!-- Estatísticas -->
 <div class="row g-3 mb-4">
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-grow-1">
 <p class="text-muted mb-1 small">Total Faturado</p>
 <h4 class="mb-0">R$ {{ number_format($estatisticas['total_faturado'], 2, ',', '.') }}</h4>
 </div>
 <div class="bg-primary bg-opacity-10 p-3 rounded">
 <i class="fas fa-file-invoice-dollar text-primary fa-2x"></i>
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
 <p class="text-muted mb-1 small">Total Pago</p>
 <h4 class="mb-0 text-success">R$ {{ number_format($estatisticas['total_pago'], 2, ',', '.') }}</h4>
 </div>
 <div class="bg-success bg-opacity-10 p-3 rounded">
 <i class="fas fa-check-circle text-success fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-2">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-grow-1">
 <p class="text-muted mb-1 small">Pendente</p>
 <h4 class="mb-0 text-warning">R$ {{ number_format($estatisticas['total_pendente'], 2, ',', '.') }}</h4>
 </div>
 <div class="bg-warning bg-opacity-10 p-3 rounded">
 <i class="fas fa-clock text-warning fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-2">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-grow-1">
 <p class="text-muted mb-1 small">Vencido</p>
 <h4 class="mb-0 text-danger">R$ {{ number_format($estatisticas['total_vencido'], 2, ',', '.') }}</h4>
 </div>
 <div class="bg-danger bg-opacity-10 p-3 rounded">
 <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-2">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-grow-1">
 <p class="text-muted mb-1 small">Cancelado</p>
 <h4 class="mb-0 text-secondary">R$ {{ number_format($estatisticas['total_cancelado'], 2, ',', '.') }}</h4>
 </div>
 <div class="bg-secondary bg-opacity-10 p-3 rounded">
 <i class="fas fa-times-circle text-secondary fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Filtros -->
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-body">
 <form method="GET" class="row g-3">
 <div class="col-md-3">
 <input type="text" class="form-control" name="search" placeholder="Buscar fatura ou empresa" value="{{ request('search') }}">
 </div>
 <div class="col-md-2">
 <select class="form-select" name="status">
 <option value="">Todos Status</option>
 <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
 <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pago</option>
 <option value="vencido" {{ request('status') == 'vencido' ? 'selected' : '' }}>Vencido</option>
 <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
 </select>
 </div>
 <div class="col-md-2">
 <select class="form-select" name="mes">
 <option value="">Todos Meses</option>
 @for($i = 1; $i <= 12; $i++)
 <option value="{{ $i }}" {{ request('mes') == $i ? 'selected' : '' }}>
 {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
 </option>
 @endfor
 </select>
 </div>
 <div class="col-md-2">
 <select class="form-select" name="ano">
 @for($y = now()->year; $y >= 2024; $y--)
 <option value="{{ $y }}" {{ request('ano', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
 @endfor
 </select>
 </div>
 <div class="col-md-3">
 <button type="submit" class="btn btn-primary">
 <i class="fas fa-search"></i> Filtrar
 </button>
 <a href="{{ route('admin.financeiro.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-times"></i> Limpar
 </a>
 </div>
 </form>
 </div>
 </div>
 <!-- Tabela de Faturas -->
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
 <thead>
 <tr>
 <th>Número</th>
 <th>Empresa</th>
 <th>Referência</th>
 <th>Vencimento</th>
 <th>Valor</th>
 <th>Status</th>
 <th class="text-center">Ações</th>
 </tr>
 </thead>
 <tbody>
 @forelse($faturas as $fatura)
 <tr>
 <td><strong>{{ $fatura->numero_fatura }}</strong></td>
 <td>{{ $fatura->contrato->empresa->nome_fantasia }}</td>
 <td>{{ \Carbon\Carbon::parse($fatura->data_referencia)->format('m/Y') }}</td>
 <td>
 {{ \Carbon\Carbon::parse($fatura->data_vencimento)->format('d/m/Y') }}
 @if($fatura->status == 'pendente' && \Carbon\Carbon::parse($fatura->data_vencimento)->isPast())
 <br><small class="text-danger">Venceu há {{ \Carbon\Carbon::parse($fatura->data_vencimento)->diffInDays() }} dias</small>
 @endif
 </td>
 <td><strong class="text-success">R$ {{ number_format($fatura->valor_total, 2, ',', '.') }}</strong></td>
 <td>
 @if($fatura->status == 'pago')
 <span class="badge bg-success">PAGO</span>
 @elseif($fatura->status == 'pendente')
 <span class="badge bg-warning">PENDENTE</span>
 @elseif($fatura->status == 'vencido')
 <span class="badge bg-danger">VENCIDO</span>
 @else
 <span class="badge bg-secondary">CANCELADO</span>
 @endif
 </td>
 <td class="text-center">
 <a href="{{ route('admin.financeiro.show', $fatura) }}" class="btn btn-sm btn-outline-primary">
 <i class="fas fa-eye"></i> Ver
 </a>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="7" class="text-center py-4">
 <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
 <p class="text-muted">Nenhuma fatura encontrada</p>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 @if($faturas->hasPages())
 <div class="card-footer">
 {{ $faturas->links() }}
 </div>
 @endif
 </div>
</div>
@endsection