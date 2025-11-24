@extends('layouts.app')
@section('title', 'Detalhes do Administrador')
@section('content')
<div class="container-fluid px-4">
 <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">👤 Detalhes do Administrador</h1>
 <p class="text-muted mb-0">Informações completas do usuário</p>
 </div>
 <div>
 <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">
 <i class="fas fa-arrow-left me-2"></i>Voltar
 </a>
 <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
 <i class="fas fa-edit me-2"></i>Editar
 </a>
 </div>
 </div>
 <div class="row">
 <!-- Informações Principais -->
 <div class="col-lg-8">
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-header bg-primary text-white py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-info-circle me-2"></i>Informações Pessoais
 </h5>
 </div>
 <div class="card-body p-4">
 <div class="row mb-3">
 <div class="col-md-6">
 <label class="text-muted mb-1">ID do Usuário</label>
 <div class="h5">#{{ $user->id }}</div>
 </div>
 <div class="col-md-6">
 <label class="text-muted mb-1">Status</label>
 <div>
 @if($user->updated_at->diffInDays(now()) < 7)
 <span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
 <i class="fas fa-check-circle me-1"></i>Ativo Recentemente
 </span>
 @else
 <span class="badge bg-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">
 <i class="fas fa-clock me-1"></i>Inativo
 </span>
 @endif
 </div>
 </div>
 </div>
 <hr>
 <div class="row mb-3">
 <div class="col-md-12">
 <label class="text-muted mb-1">
 <i class="fas fa-user me-1"></i>Nome Completo
 </label>
 <div class="h5">{{ $user->name }}</div>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-12">
 <label class="text-muted mb-1">
 <i class="fas fa-envelope me-1"></i>Email
 </label>
 <div class="h5">{{ $user->email }}</div>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <label class="text-muted mb-1">
 <i class="fas fa-building me-1"></i>Empresa
 </label>
 <div class="h5">
 @if($user->empresa)
 {{ $user->empresa->nome_fantasia }}
 @else
 <span class="text-muted">-</span>
 @endif
 </div>
 </div>
 <div class="col-md-6">
 <label class="text-muted mb-1">
 <i class="fas fa-code me-1"></i>Tenant Code
 </label>
 <div class="h5">
 <code style="font-size: 1.1rem;">{{ $user->tenant_code }}</code>
 </div>
 </div>
 </div>
 <hr>
 <div class="row">
 <div class="col-md-6">
 <label class="text-muted mb-1">
 <i class="fas fa-calendar-plus me-1"></i>Cadastrado em
 </label>
 <div>{{ $user->created_at->format('d/m/Y H:i:s') }}</div>
 <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
 </div>
 <div class="col-md-6">
 <label class="text-muted mb-1">
 <i class="fas fa-clock me-1"></i>Última Atualização
 </label>
 <div>{{ $user->updated_at->format('d/m/Y H:i:s') }}</div>
 <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
 </div>
 </div>
 </div>
 </div>
 <!-- Empresa Associada -->
 @if($user->empresa)
 <div class="card border-0 shadow-sm">
 <div class="card-header bg-info text-white py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-building me-2"></i>Empresa Associada
 </h5>
 </div>
 <div class="card-body p-4">
 <div class="row mb-3">
 <div class="col-md-6">
 <label class="text-muted mb-1">Razão Social</label>
 <div class="h6">{{ $user->empresa->razao_social }}</div>
 </div>
 <div class="col-md-6">
 <label class="text-muted mb-1">Nome Fantasia</label>
 <div class="h6">{{ $user->empresa->nome_fantasia }}</div>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <label class="text-muted mb-1">CNPJ</label>
 <div>{{ $user->empresa->cnpj }}</div>
 </div>
 <div class="col-md-6">
 <label class="text-muted mb-1">Plano</label>
 <div>
 <span class="badge 
 @if($user->empresa->plano == 'enterprise') bg-primary
 @elseif($user->empresa->plano == 'premium') bg-success
 @elseif($user->empresa->plano == 'profissional') bg-info
 @else bg-secondary
 @endif" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
 {{ strtoupper($user->empresa->plano) }}
 </span>
 </div>
 </div>
 </div>
 <div class="row">
 <div class="col-md-6">
 <label class="text-muted mb-1">Status do Contrato</label>
 <div>
 <span class="badge 
 @if($user->empresa->status_contrato == 'ativo') bg-success
 @elseif($user->empresa->status_contrato == 'trial') bg-warning
 @else bg-danger
 @endif">
 {{ strtoupper($user->empresa->status_contrato) }}
 </span>
 </div>
 </div>
 <div class="col-md-6">
 <label class="text-muted mb-1">Vencimento</label>
 <div>{{ \Carbon\Carbon::parse($user->empresa->data_fim_contrato)->format('d/m/Y') }}</div>
 </div>
 </div>
 <hr>
 <a href="{{ route('empresas.show', $user->empresa) }}" class="btn btn-outline-primary">
 <i class="fas fa-external-link-alt me-2"></i>Ver Detalhes da Empresa
 </a>
 </div>
 </div>
 @endif
 </div>
 <!-- Ações Rápidas -->
 <div class="col-lg-4">
 <div class="card border-0 shadow-sm mb-3">
 <div class="card-header bg-white border-0 py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-bolt text-warning me-2"></i>Ações Rápidas
 </h5>
 </div>
 <div class="card-body">
 <div class="d-grid gap-2">
 <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
 <i class="fas fa-edit me-2"></i>Editar Dados
 </a>
 <button class="btn btn-danger" onclick="confirmDelete()">
 <i class="fas fa-trash me-2"></i>Deletar Usuário
 </button>
 </div>
 </div>
 </div>
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <h6 class="card-title">
 <i class="fas fa-info-circle text-info me-2"></i>Informações
 </h6>
 <ul class="mb-0 small">
 <li>Este usuário é administrador da empresa</li>
 <li>Ele tem acesso total aos dados do tenant</li>
 <li>Pode gerenciar produtos, pedidos, usuários operacionais</li>
 <li>Não pode acessar dados de outras empresas</li>
 </ul>
 </div>
 </div>
 </div>
 </div>
</div>
<form id="delete-form" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
 @csrf
 @method('DELETE')
</form>
<script>
function confirmDelete() {
 if (confirm('Tem certeza que deseja deletar o administrador "{{ $user->name }}"?\n\nEsta ação não pode ser desfeita!')) {
 document.getElementById('delete-form').submit();
 }
}
</script>
@endsection