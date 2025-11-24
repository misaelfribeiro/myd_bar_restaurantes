@extends('layouts.app')

@section('title', 'Gestão de Usuários')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-users-cog me-2"></i>
                    Gestão de Usuários
                </h1>
                <p class="page-subtitle">Gerencie usuários e suas permissões no sistema</p>
            </div>
            <div>
                <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Novo Usuário
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $usuarios->total() }}</h4>
                            <p class="text-muted mb-0">Total de Usuários</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="fas fa-user-shield fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $usuarios->where('role', 'admin')->count() }}</h4>
                            <p class="text-muted mb-0">Administradores</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                <i class="fas fa-user-tie fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $usuarios->where('role', 'garcom')->count() }}</h4>
                            <p class="text-muted mb-0">Garçons</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                <i class="fas fa-user-check fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $usuarios->where('ativo', true)->count() }}</h4>
                            <p class="text-muted mb-0">Ativos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Usuários -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Usuários</h5>
                <div class="input-group" style="width: 300px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Buscar usuário..." id="searchInput">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3">Usuário</th>
                            <th class="border-0 px-4 py-3">Email</th>
                            <th class="border-0 px-4 py-3">Perfil</th>
                            @if($isMaster)
                            <th class="border-0 px-4 py-3">Empresa</th>
                            @endif
                            <th class="border-0 px-4 py-3">Status</th>
                            <th class="border-0 px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        {{ strtoupper(substr($usuario->nome, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $usuario->nome }}</div>
                                        <small class="text-muted">ID: {{ $usuario->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                {{ $usuario->email }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $badges = [
                                        'admin' => ['class' => 'danger', 'icon' => 'user-shield', 'label' => 'Administrador'],
                                        'garcom' => ['class' => 'primary', 'icon' => 'user-tie', 'label' => 'Garçom'],
                                        'caixa' => ['class' => 'success', 'icon' => 'cash-register', 'label' => 'Caixa'],
                                        'cozinha' => ['class' => 'warning', 'icon' => 'utensils', 'label' => 'Cozinha'],
                                        'entregador' => ['class' => 'info', 'icon' => 'motorcycle', 'label' => 'Entregador']
                                    ];
                                    $badge = $badges[$usuario->role] ?? ['class' => 'secondary', 'icon' => 'user', 'label' => ucfirst($usuario->role)];
                                @endphp
                                <span class="badge bg-{{ $badge['class'] }}">
                                    <i class="fas fa-{{ $badge['icon'] }} me-1"></i>
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            @if($isMaster)
                            <td class="px-4 py-3">
                                <small class="text-muted">
                                    <i class="fas fa-building me-1"></i>
                                    {{ $usuario->tenant_code }}
                                </small>
                            </td>
                            @endif
                            <td class="px-4 py-3">
                                @if($usuario->ativo)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Ativo
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle me-1"></i>
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('usuarios.permissoes.edit', $usuario->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Editar Permissões">
                                        <i class="fas fa-lock"></i>
                                    </a>
                                    <a href="{{ route('usuarios.edit', $usuario->id) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('usuarios.toggle-status', $usuario->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-{{ $usuario->ativo ? 'warning' : 'success' }}" 
                                                title="{{ $usuario->ativo ? 'Desativar' : 'Ativar' }}"
                                                onclick="return confirm('Tem certeza que deseja {{ $usuario->ativo ? 'desativar' : 'ativar' }} este usuário?')">
                                            <i class="fas fa-{{ $usuario->ativo ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('usuarios.destroy', $usuario->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Excluir"
                                                onclick="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isMaster ? 6 : 5 }}" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Nenhum usuário encontrado</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($usuarios->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $usuarios->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.page-subtitle {
    color: #718096;
    margin-bottom: 0;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-radius: 0.25rem 0 0 0.25rem;
}

.btn-group .btn:last-child {
    border-radius: 0 0.25rem 0.25rem 0;
}
</style>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endsection
