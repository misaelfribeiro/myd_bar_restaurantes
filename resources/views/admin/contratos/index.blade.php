@extends('layouts.app')
@section('title', 'Gerenciamento de Contratos')
@section('content')
<div class="container-fluid px-4">
 <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">📄 Gerenciamento de Contratos</h1>
 <p class="text-muted mb-0">Controle completo de contratos e assinaturas</p>
 </div>
 <a href="{{ route('admin.contratos.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>Novo Contrato
 </a>
 </div>
 <!-- Estatísticas -->
 <div class="row g-3 mb-4">
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <h6 class="text-muted mb-0">TOTAL CONTRATOS</h6>
 <h2 class="mb-0">{{ $stats['total'] }}</h2>
 </div>
 <div class="bg-primary bg-opacity-10 p-2 rounded">
 <i class="fas fa-file-contract text-primary fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <h6 class="text-muted mb-0">ATIVOS</h6>
 <h2 class="mb-0">{{ $stats['ativos'] }}</h2>
 </div>
 <div class="bg-success bg-opacity-10 p-2 rounded">
 <i class="fas fa-check-circle text-success fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <h6 class="text-muted mb-0">VENCENDO</h6>
 <h2 class="mb-0">{{ $stats['vencendo'] }}</h2>
 </div>
 <div class="bg-warning bg-opacity-10 p-2 rounded">
 <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <h6 class="text-muted mb-0">RECEITA MENSAL</h6>
 <h2 class="mb-0 text-success">R$ {{ number_format($stats['receita_mensal'], 2, ',', '.') }}</h2>
 </div>
 <div class="bg-success bg-opacity-10 p-2 rounded">
 <i class="fas fa-dollar-sign text-success fa-2x"></i>
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
 <div class="col-md-4">
 <input type="text" name="search" class="form-control" 
 placeholder="Buscar por empresa..." value="{{ request('search') }}">
 </div>
 <div class="col-md-3">
 <select name="status" class="form-select">
 <option value="">Todos os status</option>
 <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
 <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Trial</option>
 <option value="suspenso" {{ request('status') == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
 <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
 <option value="vencido" {{ request('status') == 'vencido' ? 'selected' : '' }}>Vencido</option>
 </select>
 </div>
 <div class="col-md-3">
 <select name="plano" class="form-select">
 <option value="">Todos os planos</option>
 @foreach($planos as $plano)
 <option value="{{ $plano->codigo }}" {{ request('plano') == $plano->codigo ? 'selected' : '' }}>
 {{ $plano->nome }}
 </option>
 @endforeach
 </select>
 </div>
 <div class="col-md-2">
 <button type="submit" class="btn btn-primary w-100">
 <i class="fas fa-search me-1"></i>Filtrar
 </button>
 </div>
 </form>
 </div>
 </div>
 <!-- Tabela de Contratos -->
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
 <thead>
 <tr>
 <th>Nº Contrato</th>
 <th>Empresa</th>
 <th>Plano</th>
 <th>Tipo</th>
 <th>Início</th>
 <th>Vencimento</th>
 <th>Status</th>
 <th class="text-end">Valor</th>
 <th class="text-center">Ações</th>
 </tr>
 </thead>
 <tbody>
 @forelse($contratos as $contrato)
 <tr>
 <td><code>{{ $contrato->numero_contrato }}</code></td>
 <td><strong>{{ $contrato->empresa->nome_fantasia }}</strong></td>
 <td>
 <span class="badge 
 @if($contrato->plano->codigo == 'enterprise') bg-primary
 @elseif($contrato->plano->codigo == 'premium') bg-success
 @elseif($contrato->plano->codigo == 'profissional') bg-info
 @else bg-secondary
 @endif">
 {{ $contrato->plano->nome }}
 </span>
 </td>
 <td>
 <span class="badge bg-light text-dark">
 {{ strtoupper($contrato->tipo_pagamento) }}
 </span>
 </td>
 <td>{{ $contrato->data_inicio->format('d/m/Y') }}</td>
 <td>
 {{ $contrato->data_fim->format('d/m/Y') }}
 @php
 $dias = $contrato->diasAteVencimento();
 @endphp
 @if($dias < 0 && abs($dias) <= 30)
 <span class="badge bg-warning ms-1">{{ abs($dias) }}d</span>
 @elseif($dias >= 0)
 <span class="badge bg-danger ms-1">Vencido</span>
 @endif
 </td>
 <td>
 <span class="badge 
 @if($contrato->status == 'ativo') bg-success
 @elseif($contrato->status == 'trial') bg-warning
 @elseif($contrato->status == 'suspenso') bg-danger
 @else bg-secondary
 @endif">
 {{ strtoupper($contrato->status) }}
 </span>
 </td>
 <td class="text-end">
 <strong class="text-success">R$ {{ number_format($contrato->valor_final, 2, ',', '.') }}</strong>
 </td>
 <td class="text-center">
 <div class="btn-group btn-group-sm">
 <a href="{{ route('admin.contratos.show', $contrato->id) }}" 
 class="btn btn-outline-primary" 
 title="Ver detalhes">
 <i class="fas fa-eye"></i>
 </a>
 @if($contrato->status == 'ativo')
 <button class="btn btn-outline-success" 
 onclick="renovarContrato({{ $contrato->id }})" 
 title="Renovar">
 <i class="fas fa-redo"></i>
 </button>
 <button class="btn btn-outline-warning" 
 onclick="suspenderContrato({{ $contrato->id }})" 
 title="Suspender">
 <i class="fas fa-pause"></i>
 </button>
 @elseif($contrato->status == 'suspenso')
 <button class="btn btn-outline-success" 
 onclick="reativarContrato({{ $contrato->id }})" 
 title="Reativar">
 <i class="fas fa-play"></i>
 </button>
 @endif
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="9" class="text-center py-5">
 <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
 <p class="text-muted mb-0">Nenhum contrato encontrado</p>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 @if($contratos->hasPages())
 <div class="card-footer bg-white">
 {{ $contratos->links() }}
 </div>
 @endif
 </div>
</div>
<script>
async function renovarContrato(id) {
 if (!confirm('Deseja renovar este contrato?')) return;
 try {
 const response = await fetch(`/admin/contratos/${id}/renovar`, {
 method: 'POST',
 headers: {
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
 'Accept': 'application/json',
 'Content-Type': 'application/json'
 },
 body: JSON.stringify({
 tipo_pagamento: 'mensal',
 desconto_aplicado: 0
 })
 });
 const data = await response.json();
 alert(data.message);
 if (data.success) location.reload();
 } catch (error) {
 alert('Erro ao renovar contrato');
 }
}
async function suspenderContrato(id) {
 const motivo = prompt('Motivo da suspensão:');
 if (!motivo) return;
 try {
 const response = await fetch(`/admin/contratos/${id}/suspender`, {
 method: 'POST',
 headers: {
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
 'Accept': 'application/json',
 'Content-Type': 'application/json'
 },
 body: JSON.stringify({ motivo })
 });
 const data = await response.json();
 alert(data.message);
 if (data.success) location.reload();
 } catch (error) {
 alert('Erro ao suspender contrato');
 }
}
async function reativarContrato(id) {
 if (!confirm('Deseja reativar este contrato?')) return;
 try {
 const response = await fetch(`/admin/contratos/${id}/reativar`, {
 method: 'POST',
 headers: {
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
 'Accept': 'application/json'
 }
 });
 const data = await response.json();
 alert(data.message);
 if (data.success) location.reload();
 } catch (error) {
 alert('Erro ao reativar contrato');
 }
}
</script>
@endsection