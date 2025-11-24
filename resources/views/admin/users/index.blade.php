@extends('layouts.app')
@section('title', 'Gerenciar Administradores das Empresas')
@section('content')
<div class="container-fluid px-4">
 <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">👥 Administradores das Empresas</h1>
 <p class="text-muted mb-0">Gerencie os usuários administradores de cada empresa</p>
 </div>
 <div>
 <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>Novo Administrador
 </a>
 </div>
 </div>
 <!-- Mensagens de Sucesso/Erro -->
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
 <div class="col-md-4">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-primary bg-opacity-10 text-primary p-3 rounded">
 <i class="fas fa-users fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0">{{ $users->total() }}</h3>
 <small class="text-muted">Total de Admins</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-4">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-success bg-opacity-10 text-success p-3 rounded">
 <i class="fas fa-user-check fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0">{{ $users->where('updated_at', '>=', now()->subDays(30))->count() }}</h3>
 <small class="text-muted">Ativos (30 dias)</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-4">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-info bg-opacity-10 text-info p-3 rounded">
 <i class="fas fa-building fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0">{{ $users->pluck('tenant_code')->unique()->count() }}</h3>
 <small class="text-muted">Empresas com Admin</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Tabela de Usuários -->
 <div class="card border-0 shadow-sm">
 <div class="card-header bg-white border-0 py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-list me-2"></i>Lista de Administradores
 </h5>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover align-middle">
 <thead>
 <tr>
 <th>ID</th>
 <th>Nome</th>
 <th>Email</th>
 <th>Empresa</th>
 <th>Tenant Code</th>
 <th>Cadastrado em</th>
 <th>Último Acesso</th>
 <th class="text-center">Ações</th>
 </tr>
 </thead>
 <tbody>
 @forelse($users as $user)
 <tr>
 <td><strong>#{{ $user->id }}</strong></td>
 <td>
 <div class="d-flex align-items-center">
 <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
 <strong>{{ strtoupper(substr($user->name, 0, 2)) }}</strong>
 </div>
 <strong>{{ $user->name }}</strong>
 </div>
 </td>
 <td>{{ $user->email }}</td>
 <td>
 @if($user->empresa)
 <span class="badge bg-info">{{ $user->empresa->nome_fantasia }}</span>
 @else
 <span class="text-muted">-</span>
 @endif
 </td>
 <td><code>{{ $user->tenant_code }}</code></td>
 <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
 <td>
 @if($user->updated_at->diffInDays(now()) < 7)
 <span class="badge bg-success">{{ $user->updated_at->diffForHumans() }}</span>
 @elseif($user->updated_at->diffInDays(now()) < 30)
 <span class="badge bg-warning">{{ $user->updated_at->diffForHumans() }}</span>
 @else
 <span class="badge bg-secondary">{{ $user->updated_at->diffForHumans() }}</span>
 @endif
 </td>
 <td class="text-center">
 <div class="btn-group btn-group-sm">
 <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary" title="Ver">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-info" title="Editar">
 <i class="fas fa-edit"></i>
 </a>
 <button type="button" class="btn btn-outline-danger" title="Deletar" 
 onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
 <i class="fas fa-trash"></i>
 </button>
 </div>
 <form id="delete-form-{{ $user->id }}" 
 action="{{ route('admin.users.destroy', $user) }}" 
 method="POST" class="d-none">
 @csrf
 @method('DELETE')
 </form>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="8" class="text-center text-muted py-4">
 <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
 Nenhum administrador cadastrado ainda.
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 <!-- Paginação -->
 @if($users->hasPages())
 <div class="d-flex justify-content-center mt-4">
 {{ $users->links() }}
 </div>
 @endif
 </div>
 </div>
</div>
<script>
function confirmDelete(userId, userName) {
 if (confirm(`Tem certeza que deseja deletar o administrador "${userName}"?\n\nEsta ação não pode ser desfeita!`)) {
 document.getElementById('delete-form-' + userId).submit();
 }
}
</script>
@endsection