@extends('layouts.app')
@section('title', 'Cargos')
@section('content')
<div class="container-fluid px-4">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">👔 Cargos</h1>
 <p class="text-muted mb-0">Gerencie os cargos e níveis hierárquicos</p>
 </div>
 <a href="{{ route('admin.cargos.create') }}" class="btn btn-primary">
 <i class="fas fa-plus"></i> Novo Cargo
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
 <p class="text-muted mb-1 small">Total de Cargos</p>
 <h4 class="mb-0">{{ $cargos->count() }}</h4>
 </div>
 <div class="bg-primary bg-opacity-10 p-3 rounded">
 <i class="fas fa-user-tie text-primary fa-2x"></i>
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
 <th>Nível Hierárquico</th>
 <th>Salário Base</th>
 <th>Funcionários</th>
 <th>Status</th>
 <th class="text-center">Ações</th>
 </tr>
 </thead>
 <tbody>
 @forelse($cargos as $cargo)
 <tr>
 <td>
 <strong>{{ $cargo->nome }}</strong>
 @if($cargo->descricao)
 <br><small class="text-muted">{{ Str::limit($cargo->descricao, 50) }}</small>
 @endif
 </td>
 <td>
 @if($cargo->nivel_hierarquico == 5)
 <span class="badge bg-danger">Master</span>
 @elseif($cargo->nivel_hierarquico == 4)
 <span class="badge bg-warning">Diretor</span>
 @elseif($cargo->nivel_hierarquico == 3)
 <span class="badge bg-info">Gerente</span>
 @elseif($cargo->nivel_hierarquico == 2)
 <span class="badge bg-primary">Supervisor</span>
 @else
 <span class="badge bg-secondary">Operacional</span>
 @endif
 </td>
 <td>
 @if($cargo->salario_base)
 R$ {{ number_format($cargo->salario_base, 2, ',', '.') }}
 @else
 <span class="text-muted">Não definido</span>
 @endif
 </td>
 <td>
 <span class="badge bg-light text-dark">
 {{ $cargo->funcionarios_count }} funcionário(s)
 </span>
 </td>
 <td>
 @if($cargo->ativo)
 <span class="badge bg-success">Ativo</span>
 @else
 <span class="badge bg-secondary">Inativo</span>
 @endif
 </td>
 <td class="text-center">
 <div class="btn-group btn-group-sm">
 <a href="{{ route('admin.cargos.edit', $cargo->id) }}" class="btn btn-outline-primary" title="Editar">
 <i class="fas fa-edit"></i>
 </a>
 <button type="button" class="btn btn-outline-danger" onclick="confirmDelete({{ $cargo->id }})" title="Excluir">
 <i class="fas fa-trash"></i>
 </button>
 </div>
 <form id="delete-form-{{ $cargo->id }}" action="{{ route('admin.cargos.destroy', $cargo->id) }}" method="POST" class="d-none">
 @csrf
 @method('DELETE')
 </form>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="text-center py-4">
 <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
 <p class="text-muted">Nenhum cargo cadastrado</p>
 <a href="{{ route('admin.cargos.create') }}" class="btn btn-sm btn-primary">
 <i class="fas fa-plus"></i> Cadastrar Primeiro Cargo
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
 if (confirm('Tem certeza que deseja excluir este cargo?')) {
 document.getElementById('delete-form-' + id).submit();
 }
}
</script>
@endsection