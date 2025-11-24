@extends('layouts.app')
@section('title', 'Lista de Empresas')
@section('content')
<div class="container-fluid px-4">
 <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">🏢 Lista de Empresas</h1>
 <p class="text-muted mb-0">Gerenciamento completo de todas as empresas da plataforma</p>
 </div>
 <div>
 <a href="{{ route('empresas.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>Nova Empresa
 </a>
 </div>
 </div>
 <!-- Filtros -->
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-body">
 <form method="GET" action="{{ route('admin.tenants.list') }}" class="row g-3">
 <div class="col-md-4">
 <label class="form-label">Buscar</label>
 <input type="text" name="search" class="form-control" 
 placeholder="Nome, razão social ou tenant code..." 
 value="{{ request('search') }}">
 </div>
 <div class="col-md-3">
 <label class="form-label">Plano</label>
 <select name="plano" class="form-select">
 <option value="">Todos os planos</option>
 <option value="basico" {{ request('plano') == 'basico' ? 'selected' : '' }}>Básico</option>
 <option value="profissional" {{ request('plano') == 'profissional' ? 'selected' : '' }}>Profissional</option>
 <option value="premium" {{ request('plano') == 'premium' ? 'selected' : '' }}>Premium</option>
 <option value="enterprise" {{ request('plano') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
 </select>
 </div>
 <div class="col-md-3">
 <label class="form-label">Status Contrato</label>
 <select name="status" class="form-select">
 <option value="">Todos os status</option>
 <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
 <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Trial</option>
 <option value="suspenso" {{ request('status') == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
 <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
 </select>
 </div>
 <div class="col-md-2">
 <label class="form-label d-block">&nbsp;</label>
 <button type="submit" class="btn btn-primary w-100">
 <i class="fas fa-search me-1"></i>Filtrar
 </button>
 </div>
 </form>
 </div>
 </div>
 <!-- Tabela de Empresas -->
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
 <thead>
 <tr>
 <th style="width: 80px;">Logo</th>
 <th>Empresa</th>
 <th>Tenant Code</th>
 <th>CNPJ</th>
 <th>Plano</th>
 <th>Status</th>
 <th>Contrato</th>
 <th class="text-end">Mensalidade</th>
 <th class="text-center">Ações</th>
 </tr>
 </thead>
 <tbody>
 @forelse($tenants as $tenant)
 <tr>
 <td>
 @if($tenant->logo)
 <img src="{{ asset('storage/' . $tenant->logo) }}" 
 alt="{{ $tenant->nome_fantasia }}" 
 class="rounded" 
 style="width: 60px; height: 60px; object-fit: cover;">
 @else
 <div class="bg-light rounded d-flex align-items-center justify-content-center" 
 style="width: 60px; height: 60px;">
 <i class="fas fa-store text-muted fa-2x"></i>
 </div>
 @endif
 </td>
 <td>
 <div>
 <strong class="d-block">{{ $tenant->nome_fantasia }}</strong>
 <small class="text-muted">{{ $tenant->razao_social }}</small>
 </div>
 </td>
 <td><code>{{ $tenant->tenant_code }}</code></td>
 <td>{{ $tenant->cnpj }}</td>
 <td>
 <span class="badge 
 @if($tenant->plano == 'enterprise') bg-primary
 @elseif($tenant->plano == 'premium') bg-success
 @elseif($tenant->plano == 'profissional') bg-info
 @else bg-secondary
 @endif">
 {{ strtoupper($tenant->plano) }}
 </span>
 </td>
 <td>
 <span class="badge 
 @if($tenant->status_contrato == 'ativo') bg-success
 @elseif($tenant->status_contrato == 'trial') bg-warning
 @elseif($tenant->status_contrato == 'suspenso') bg-danger
 @else bg-secondary
 @endif">
 {{ strtoupper($tenant->status_contrato) }}
 </span>
 </td>
 <td>
 @if($tenant->data_fim_contrato)
 {{ \Carbon\Carbon::parse($tenant->data_fim_contrato)->format('d/m/Y') }}
 @php
 $dias = \Carbon\Carbon::parse($tenant->data_fim_contrato)->diffInDays(now(), false);
 @endphp
 @if($dias < 0 && abs($dias) <= 30)
 <span class="badge bg-warning ms-1">{{ abs($dias) }}d</span>
 @elseif($dias >= 0)
 <span class="badge bg-danger ms-1">Vencido</span>
 @endif
 @else
 <span class="text-muted">-</span>
 @endif
 </td>
 <td class="text-end">
 <strong class="text-success">R$ {{ number_format($tenant->valor_mensalidade, 2, ',', '.') }}</strong>
 </td>
 <td class="text-center">
 <div class="btn-group btn-group-sm" role="group">
 <a href="{{ route('admin.tenants.show', $tenant->id) }}" 
 class="btn btn-outline-primary" 
 title="Ver detalhes">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('empresas.edit', $tenant->id) }}" 
 class="btn btn-outline-warning" 
 title="Editar">
 <i class="fas fa-edit"></i>
 </a>
 @if($tenant->status_contrato == 'ativo')
 <button type="button" 
 class="btn btn-outline-danger" 
 onclick="suspendTenant({{ $tenant->id }})" 
 title="Suspender">
 <i class="fas fa-ban"></i>
 </button>
 @else
 <button type="button" 
 class="btn btn-outline-success" 
 onclick="activateTenant({{ $tenant->id }})" 
 title="Ativar">
 <i class="fas fa-check"></i>
 </button>
 @endif
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="9" class="text-center py-5">
 <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
 <p class="text-muted mb-0">Nenhuma empresa encontrada</p>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 @if($tenants->hasPages())
 <div class="card-footer bg-white border-top">
 {{ $tenants->links() }}
 </div>
 @endif
 </div>
</div>
<script>
async function suspendTenant(tenantId) {
 if (!confirm('Tem certeza que deseja suspender esta empresa?\n\nIsso bloqueará o acesso de todos os usuários.')) {
 return;
 }
 try {
 const response = await fetch(`/admin/tenants/${tenantId}/suspend`, {
 method: 'POST',
 headers: {
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
 'Accept': 'application/json',
 'Content-Type': 'application/json'
 }
 });
 const data = await response.json();
 if (data.success) {
 alert('Empresa suspensa com sucesso!');
 location.reload();
 } else {
 alert('Erro ao suspender empresa: ' + data.message);
 }
 } catch (error) {
 alert('Erro ao suspender empresa');
 console.error(error);
 }
}
async function activateTenant(tenantId) {
 if (!confirm('Tem certeza que deseja ativar esta empresa?')) {
 return;
 }
 try {
 const response = await fetch(`/admin/tenants/${tenantId}/activate`, {
 method: 'POST',
 headers: {
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
 'Accept': 'application/json',
 'Content-Type': 'application/json'
 }
 });
 const data = await response.json();
 if (data.success) {
 alert('Empresa ativada com sucesso!');
 location.reload();
 } else {
 alert('Erro ao ativar empresa: ' + data.message);
 }
 } catch (error) {
 alert('Erro ao ativar empresa');
 console.error(error);
 }
}
</script>
@endsection