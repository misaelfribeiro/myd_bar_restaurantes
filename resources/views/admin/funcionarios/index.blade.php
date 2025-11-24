@extends('layouts.app')
@section('title', 'Funcionários')
@section('content')
<div class="container-fluid px-4">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">👥 Funcionários</h1>
 <p class="text-muted mb-0">Gerencie os funcionários da empresa</p>
 </div>
 <a href="{{ route('admin.funcionarios.create') }}" class="btn btn-primary">
 <i class="fas fa-plus"></i> Novo Funcionário
 </a>
 </div>
 @if(session('success'))
 <div class="alert alert-success alert-dismissible fade show">
 {{ session('success') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 @if(session('error'))
 <div class="alert alert-danger alert-dismissible fade show">
 {{ session('error') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 <div class="row g-3 mb-4">
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-grow-1">
 <p class="text-muted mb-1 small">Total de Funcionários</p>
 <h4 class="mb-0">{{ $funcionarios->count() }}</h4>
 </div>
 <div class="bg-primary bg-opacity-10 p-3 rounded">
 <i class="fas fa-users text-primary fa-2x"></i>
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
 <p class="text-muted mb-1 small">Ativos</p>
 <h4 class="mb-0">{{ $funcionarios->where('ativo', true)->count() }}</h4>
 </div>
 <div class="bg-success bg-opacity-10 p-3 rounded">
 <i class="fas fa-user-check text-success fa-2x"></i>
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
 <p class="text-muted mb-1 small">Inativos</p>
 <h4 class="mb-0">{{ $funcionarios->where('ativo', false)->count() }}</h4>
 </div>
 <div class="bg-danger bg-opacity-10 p-3 rounded">
 <i class="fas fa-user-times text-danger fa-2x"></i>
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
 <p class="text-muted mb-1 small">Folha Salarial</p>
 <h4 class="mb-0">R$ {{ number_format($funcionarios->sum('salario'), 2, ',', '.') }}</h4>
 </div>
 <div class="bg-info bg-opacity-10 p-3 rounded">
 <i class="fas fa-money-bill-wave text-info fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover align-middle">
 <thead>
 <tr>
 <th>Nome</th>
 <th>Cargo</th>
 <th>CPF</th>
 <th>Email</th>
 <th>Telefone</th>
 <th>Admissão</th>
 <th>Salário</th>
 <th>Status</th>
 <th class="text-center">Ações</th>
 </tr>
 </thead>
 <tbody>
 @forelse($funcionarios as $funcionario)
 <tr>
 <td>
 <strong>{{ $funcionario->nome_completo }}</strong>
 </td>
 <td>{{ $funcionario->cargo->nome ?? 'N/A' }}</td>
 <td>{{ $funcionario->cpf }}</td>
 <td>{{ $funcionario->email }}</td>
 <td>{{ $funcionario->celular ?? $funcionario->telefone }}</td>
 <td>{{ $funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : '-' }}</td>
 <td>R$ {{ number_format($funcionario->salario ?? 0, 2, ',', '.') }}</td>
 <td>
 @if($funcionario->ativo)
 <span class="badge bg-success">Ativo</span>
 @else
 <span class="badge bg-secondary">Inativo</span>
 @endif
 </td>
 <td class="text-center">
 <div class="btn-group btn-group-sm">
 <a href="{{ route('admin.funcionarios.show', $funcionario->id) }}" 
 class="btn btn-outline-info" title="Visualizar">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('admin.funcionarios.edit', $funcionario->id) }}" 
 class="btn btn-outline-primary" title="Editar">
 <i class="fas fa-edit"></i>
 </a>
 <button type="button" class="btn btn-outline-danger" onclick="confirmDelete({{ $funcionario->id }})" title="Excluir">
 <i class="fas fa-trash"></i>
 </button>
 </div>
 <form id="delete-form-{{ $funcionario->id }}" action="{{ route('admin.funcionarios.destroy', $funcionario->id) }}" 
 method="POST" class="d-none">
 @csrf
 @method('DELETE')
 </form>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="9" class="text-center py-4">
 <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
 <p class="text-muted">Nenhum funcionário cadastrado</p>
 <a href="{{ route('admin.funcionarios.create') }}" class="btn btn-sm btn-primary">
 <i class="fas fa-plus"></i> Cadastrar Primeiro Funcionário
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
<script>
function confirmDelete(id) {
 if (confirm('Tem certeza que deseja excluir este funcionário?')) {
 document.getElementById('delete-form-' + id).submit();
 }
}
</script>
@endsection