@extends('layouts.app')
@section('title', 'Gerenciar Empresas')
@section('content')
<div class="container-fluid px-4">
 <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">🏢 Gerenciar Empresas</h1>
 <p class="text-muted mb-0">Todas as empresas cadastradas na plataforma</p>
 </div>
 <a href="{{ route('empresas.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>Nova Empresa
 </a>
 </div>
 @if(session('success'))
 <div class="alert alert-success alert-dismissible fade show" role="alert">
 <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 @if(session('error'))
 <div class="alert alert-danger alert-dismissible fade show" role="alert">
 <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 <!-- Estatísticas -->
 <div class="row g-3 mb-4">
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-primary bg-opacity-10 text-primary p-3 rounded">
 <i class="fas fa-building fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0">{{ $empresas->total() }}</h3>
 <small class="text-muted">Total de Empresas</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-success bg-opacity-10 text-success p-3 rounded">
 <i class="fas fa-check-circle fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0">{{ $empresas->where('ativo', true)->count() }}</h3>
 <small class="text-muted">Empresas Ativas</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-info bg-opacity-10 text-info p-3 rounded">
 <i class="fas fa-store fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0">{{ $empresas->where('tipo', 'matriz')->count() }}</h3>
 <small class="text-muted">Matrizes</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-warning bg-opacity-10 text-warning p-3 rounded">
 <i class="fas fa-store-alt fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0">{{ $empresas->where('tipo', 'filial')->count() }}</h3>
 <small class="text-muted">Filiais</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Tabela de Empresas -->
 <div class="card border-0 shadow-sm">
 <div class="card-header bg-white border-0 py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-list me-2"></i>Lista de Empresas
 </h5>
 </div>
 <div class="card-body">
 @if($empresas->count() > 0)
 <div class="table-responsive">
 <table class="table table-hover align-middle">
 <thead>
 <tr>
 <th width="80">Logo</th>
 <th>Nome Fantasia</th>
 <th>Tenant Code</th>
 <th>CNPJ</th>
 <th>Plano</th>
 <th>Status Contrato</th>
 <th>Tipo</th>
 <th width="100">Status</th>
 <th width="150" class="text-center">Ações</th>
 </tr>
 </thead>
 <tbody>
 @foreach($empresas as $empresa)
 <tr>
 <td>
 @if($empresa->logo)
 <img src="{{ asset('storage/' . $empresa->logo) }}" 
 alt="{{ $empresa->nome_fantasia }}" 
 class="rounded" 
 width="60">
 @else
 <div class="bg-secondary bg-opacity-10 text-secondary rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
 <i class="fas fa-building fa-2x"></i>
 </div>
 @endif
 </td>
 <td>
 <strong>{{ $empresa->nome_fantasia }}</strong><br>
 <small class="text-muted">{{ $empresa->razao_social }}</small>
 </td>
 <td><code>{{ $empresa->tenant_code ?? '-' }}</code></td>
 <td>{{ $empresa->cnpj }}</td>
 <td>
 @if($empresa->plano)
 <span class="badge 
 @if($empresa->plano == 'enterprise') bg-primary
 @elseif($empresa->plano == 'premium') bg-success
 @elseif($empresa->plano == 'profissional') bg-info
 @else bg-secondary
 @endif">
 {{ strtoupper($empresa->plano) }}
 </span>
 @else
 <span class="text-muted">-</span>
 @endif
 </td>
 <td>
 @if($empresa->status_contrato)
 <span class="badge 
 @if($empresa->status_contrato == 'ativo') bg-success
 @elseif($empresa->status_contrato == 'trial') bg-warning
 @elseif($empresa->status_contrato == 'suspenso') bg-danger
 @else bg-secondary
 @endif">
 {{ strtoupper($empresa->status_contrato) }}
 </span>
 @else
 <span class="text-muted">-</span>
 @endif
 </td>
 <td>
 @if($empresa->is_master)
 <span class="badge bg-warning">
 <i class="fas fa-crown me-1"></i>MASTER
 </span>
 @elseif($empresa->tipo === 'matriz')
 <span class="badge bg-primary">
 <i class="fas fa-building me-1"></i>Matriz
 </span>
 @else
 <span class="badge bg-info">
 <i class="fas fa-store me-1"></i>Filial
 </span>
 @endif
 </td>
 <td>
 @if($empresa->ativo)
 <span class="badge bg-success">Ativo</span>
 @else
 <span class="badge bg-danger">Inativo</span>
 @endif
 </td>
 <td class="text-center">
 <div class="btn-group btn-group-sm" role="group">
 <a href="{{ route('empresas.show', $empresa->id) }}" 
 class="btn btn-outline-primary" 
 title="Visualizar">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('empresas.edit', $empresa->id) }}" 
 class="btn btn-outline-info" 
 title="Editar">
 <i class="fas fa-edit"></i>
 </a>
 @if(!$empresa->is_master)
 <button type="button" 
 class="btn btn-outline-danger" 
 title="Deletar"
 onclick="confirmDelete({{ $empresa->id }}, '{{ $empresa->nome_fantasia }}')">
 <i class="fas fa-trash"></i>
 </button>
 @endif
 </div>
 @if(!$empresa->is_master)
 <form id="delete-form-{{ $empresa->id }}" 
 action="{{ route('empresas.destroy', $empresa->id) }}" 
 method="POST" 
 class="d-none">
 @csrf
 @method('DELETE')
 </form>
 @endif
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 @if($empresas->hasPages())
 <div class="d-flex justify-content-center mt-4">
 {{ $empresas->links() }}
 </div>
 @endif
 @else
 <div class="text-center py-5">
 <i class="fas fa-building fa-3x text-muted mb-3 d-block"></i>
 <p class="text-muted">Nenhuma empresa cadastrada</p>
 <a href="{{ route('empresas.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>Cadastrar Primeira Empresa
 </a>
 </div>
 @endif
 </div>
 </div>
</div>
<script>
function confirmDelete(empresaId, empresaNome) {
 if (confirm(`Tem certeza que deseja deletar a empresa "${empresaNome}"?\n\nEsta ação não pode ser desfeita!`)) {
 document.getElementById('delete-form-' + empresaId).submit();
 }
}
</script>
@endsection