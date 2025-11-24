@extends('layouts.admin')
@section('content')
<div class="container-fluid">
 <div class="row">
 <div class="col-12">
 <div class="page-title-box d-flex align-items-center justify-content-between">
 <h4 class="mb-0">🏢 EatsFood Master - Painel de Controle</h4>
 <div class="page-title-right">
 <span class="badge badge-success" style="font-size: 14px;">Master Account</span>
 </div>
 </div>
 </div>
 </div>
 <!-- Cards de Estatísticas -->
 <div class="row">
 <div class="col-md-3">
 <div class="card">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h6 class="text-muted mb-2">Total de Tenants</h6>
 <h2 class="mb-0">{{ $stats['total_tenants'] }}</h2>
 </div>
 <div class="avatar-sm">
 <span class="avatar-title bg-primary rounded-circle">
 <i class="fas fa-building fa-2x"></i>
 </span>
 </div>
 </div>
 <p class="text-success mb-0 mt-2">
 <i class="fas fa-check-circle"></i> {{ $stats['tenants_ativos'] }} ativos
 </p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h6 class="text-muted mb-2">Receita Mensal</h6>
 <h2 class="mb-0">R$ {{ number_format($stats['receita_mensal'], 2, ',', '.') }}</h2>
 </div>
 <div class="avatar-sm">
 <span class="avatar-title bg-success rounded-circle">
 <i class="fas fa-dollar-sign fa-2x"></i>
 </span>
 </div>
 </div>
 <p class="text-muted mb-0 mt-2">Mensalidades ativas</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h6 class="text-muted mb-2">Pedidos Hoje</h6>
 <h2 class="mb-0">{{ $stats['pedidos_hoje'] }}</h2>
 </div>
 <div class="avatar-sm">
 <span class="avatar-title bg-info rounded-circle">
 <i class="fas fa-shopping-cart fa-2x"></i>
 </span>
 </div>
 </div>
 <p class="text-muted mb-0 mt-2">Todos os tenants</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h6 class="text-muted mb-2">Total Usuários</h6>
 <h2 class="mb-0">{{ $stats['usuarios_total'] }}</h2>
 </div>
 <div class="avatar-sm">
 <span class="avatar-title bg-warning rounded-circle">
 <i class="fas fa-users fa-2x"></i>
 </span>
 </div>
 </div>
 <p class="text-muted mb-0 mt-2">Todos os tenants</p>
 </div>
 </div>
 </div>
 </div>
 <!-- Alertas -->
 <div class="row">
 <div class="col-md-4">
 <div class="card bg-warning text-white">
 <div class="card-body">
 <h5><i class="fas fa-exclamation-triangle"></i> Tenants em Trial</h5>
 <h2>{{ $stats['tenants_trial'] }}</h2>
 <p class="mb-0">Aguardando conversão</p>
 </div>
 </div>
 </div>
 <div class="col-md-4">
 <div class="card bg-danger text-white">
 <div class="card-body">
 <h5><i class="fas fa-ban"></i> Tenants Suspensos</h5>
 <h2>{{ $stats['tenants_suspensos'] }}</h2>
 <p class="mb-0">Requer atenção</p>
 </div>
 </div>
 </div>
 <div class="col-md-4">
 <div class="card bg-info text-white">
 <div class="card-body">
 <h5><i class="fas fa-calendar-alt"></i> Vencendo em 30 dias</h5>
 <h2>{{ $vencendo->count() }}</h2>
 <p class="mb-0">Renovação necessária</p>
 </div>
 </div>
 </div>
 </div>
 <!-- Distribuição por Plano -->
 <div class="row">
 <div class="col-md-6">
 <div class="card">
 <div class="card-header">
 <h5 class="card-title mb-0">📊 Distribuição por Plano</h5>
 </div>
 <div class="card-body">
 <table class="table table-sm">
 <thead>
 <tr>
 <th>Plano</th>
 <th class="text-center">Quantidade</th>
 <th class="text-right">Receita</th>
 </tr>
 </thead>
 <tbody>
 @foreach($por_plano as $plano)
 <tr>
 <td>
 <span class="badge badge-{{ $plano->plano == 'enterprise' ? 'primary' : ($plano->plano == 'premium' ? 'success' : 'info') }}">
 {{ strtoupper($plano->plano) }}
 </span>
 </td>
 <td class="text-center">{{ $plano->total }}</td>
 <td class="text-right">
 R$ {{ number_format(\App\Models\Empresa::where('plano', $plano->plano)->sum('valor_mensalidade'), 2, ',', '.') }}
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 </div>
 <div class="col-md-6">
 <div class="card">
 <div class="card-header">
 <h5 class="card-title mb-0">🆕 Últimos Tenants Cadastrados</h5>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-sm">
 <thead>
 <tr>
 <th>Empresa</th>
 <th>Plano</th>
 <th>Data</th>
 <th>Status</th>
 </tr>
 </thead>
 <tbody>
 @foreach($novos_tenants as $tenant)
 <tr>
 <td>
 <a href="{{ route('admin.tenants.show', $tenant->id) }}">
 {{ $tenant->nome_fantasia }}
 </a>
 </td>
 <td>
 <span class="badge badge-info">{{ strtoupper($tenant->plano) }}</span>
 </td>
 <td>{{ $tenant->created_at->format('d/m/Y') }}</td>
 <td>{!! $tenant->status_contrato_badge !!}</td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Contratos Vencendo -->
 @if($vencendo->count() > 0)
 <div class="row">
 <div class="col-12">
 <div class="card border-warning">
 <div class="card-header bg-warning text-white">
 <h5 class="card-title mb-0">⚠️ Contratos Vencendo nos Próximos 30 Dias</h5>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table">
 <thead>
 <tr>
 <th>Empresa</th>
 <th>Plano</th>
 <th>Vencimento</th>
 <th>Dias Restantes</th>
 <th>Valor</th>
 <th>Ações</th>
 </tr>
 </thead>
 <tbody>
 @foreach($vencendo as $tenant)
 <tr>
 <td>
 <strong>{{ $tenant->nome_fantasia }}</strong><br>
 <small class="text-muted">{{ $tenant->tenant_code }}</small>
 </td>
 <td><span class="badge badge-primary">{{ strtoupper($tenant->plano) }}</span></td>
 <td>{{ \Carbon\Carbon::parse($tenant->data_fim_contrato)->format('d/m/Y') }}</td>
 <td>
 <span class="badge badge-warning">
 {{ $tenant->dias_restantes_contrato }} dias
 </span>
 </td>
 <td>R$ {{ number_format($tenant->valor_mensalidade, 2, ',', '.') }}</td>
 <td>
 <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="btn btn-sm btn-primary">
 Ver Detalhes
 </a>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 </div>
 </div>
 </div>
 @endif
 <!-- Ações Rápidas -->
 <div class="row">
 <div class="col-12">
 <div class="card">
 <div class="card-header">
 <h5 class="card-title mb-0">🚀 Ações Rápidas</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-3">
 <a href="{{ route('admin.tenants.list') }}" class="btn btn-primary btn-block">
 <i class="fas fa-list"></i> Ver Todos os Tenants
 </a>
 </div>
 <div class="col-md-3">
 <a href="{{ route('admin.tenants.financial') }}" class="btn btn-success btn-block">
 <i class="fas fa-chart-line"></i> Relatório Financeiro
 </a>
 </div>
 <div class="col-md-3">
 <a href="{{ route('empresas.create') }}" class="btn btn-info btn-block">
 <i class="fas fa-plus"></i> Novo Tenant
 </a>
 </div>
 <div class="col-md-3">
 <button class="btn btn-warning btn-block">
 <i class="fas fa-cog"></i> Configurações
 </button>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
<style>
.avatar-sm {
 height: 3rem;
 width: 3rem;
}
.avatar-title {
 align-items: center;
 display: flex;
 height: 100%;
 justify-content: center;
 width: 100%;
}
</style>
@endsection