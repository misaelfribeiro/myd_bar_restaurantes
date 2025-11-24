@extends('layouts.app')
@section('title', 'Usuários')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-users me-2"></i>
 Usuários
 </h1>
 <p class="page-subtitle">Gerencie usuários do sistema</p>
 </div>
 <div class="d-flex gap-2">
 <button class="btn btn-outline-info" onclick="exportUsers()">
 <i class="fas fa-download me-2"></i>
 Exportar
 </button>
 <a href="{{ route('users.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Novo Usuário
 </a>
 </div>
 </div>
 </div>
 <!-- Estatísticas -->
 <div class="row mb-4">
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-primary">
 <i class="fas fa-users"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $usuarios->count() }}</h3>
 <p>Total de Usuários</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-success">
 <i class="fas fa-user-check"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $usuarios->where('status', 'ativo')->count() }}</h3>
 <p>Ativos</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-warning">
 <i class="fas fa-user-clock"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $usuarios->where('status', 'inativo')->count() }}</h3>
 <p>Inativos</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-info">
 <i class="fas fa-user-tie"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $usuarios->where('tipo', 'admin')->count() }}</h3>
 <p>Administradores</p>
 </div>
 </div>
 </div>
 </div>
 <!-- Filtros -->
 <div class="filters-card mb-4">
 <div class="row">
 <div class="col-md-4">
 <div class="search-box">
 <i class="fas fa-search"></i>
 <input type="text" id="searchInput" placeholder="Buscar usuários..." class="form-control">
 </div>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="tipoFilter">
 <option value="">Todos os Tipos</option>
 <option value="admin">Administrador</option>
 <option value="garcom">Garçom</option>
 <option value="caixa">Caixa</option>
 <option value="cozinha">Cozinha</option>
 </select>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="statusFilter">
 <option value="">Todos Status</option>
 <option value="ativo">Ativo</option>
 <option value="inativo">Inativo</option>
 </select>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="sortSelect">
 <option value="nome">Ordenar por Nome</option>
 <option value="email">Ordenar por Email</option>
 <option value="tipo">Ordenar por Tipo</option>
 <option value="recente">Mais Recente</option>
 </select>
 </div>
 <div class="col-md-2">
 <button class="btn btn-outline-primary w-100" onclick="refreshData()">
 <i class="fas fa-sync-alt me-2"></i>
 Atualizar
 </button>
 </div>
 </div>
 </div>
 <!-- Lista de Usuários -->
 <div class="usuarios-table-card">
 <div class="table-responsive">
 <table class="table table-hover">
 <thead>
 <tr>
 <th>
 <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
 </th>
 <th>Usuário</th>
 <th>Email</th>
 <th>Tipo</th>
 <th>Status</th>
 <th>Último Acesso</th>
 <th>Criado em</th>
 <th>Ações</th>
 </tr>
 </thead>
 <tbody id="usuariosTableBody">
 @foreach($usuarios as $usuario)
 <tr class="usuario-item" 
 data-nome="{{ strtolower($usuario->name) }}" 
 data-email="{{ strtolower($usuario->email) }}" 
 data-tipo="{{ $usuario->tipo ?? 'admin' }}" 
 data-status="{{ $usuario->status ?? 'ativo' }}">
 <td>
 <input type="checkbox" class="user-checkbox" value="{{ $usuario->id }}">
 </td>
 <td>
 <div class="user-info">
 <div class="user-avatar">
 @if($usuario->avatar)
 <img src="{{ asset('storage/' . $usuario->avatar) }}" alt="{{ $usuario->name }}" class="avatar-img">
 @else
 <div class="avatar-placeholder">
 {{ strtoupper(substr($usuario->name, 0, 2)) }}
 </div>
 @endif
 </div>
 <div class="user-details">
 <strong>{{ $usuario->name }}</strong>
 @if($usuario->telefone)
 <br><small class="text-muted">{{ $usuario->telefone }}</small>
 @endif
 </div>
 </div>
 </td>
 <td>{{ $usuario->email }}</td>
 <td>
 @switch($usuario->tipo ?? 'admin')
 @case('admin')
 <span class="badge bg-danger">
 <i class="fas fa-user-shield me-1"></i>Administrador
 </span>
 @break
 @case('garcom')
 <span class="badge bg-primary">
 <i class="fas fa-user-tie me-1"></i>Garçom
 </span>
 @break
 @case('caixa')
 <span class="badge bg-success">
 <i class="fas fa-cash-register me-1"></i>Caixa
 </span>
 @break
 @case('cozinha')
 <span class="badge bg-warning">
 <i class="fas fa-utensils me-1"></i>Cozinha
 </span>
 @break
 @endswitch
 </td>
 <td>
 @if(($usuario->status ?? 'ativo') === 'ativo')
 <span class="badge bg-success">
 <i class="fas fa-check-circle me-1"></i>Ativo
 </span>
 @else
 <span class="badge bg-warning">
 <i class="fas fa-pause-circle me-1"></i>Inativo
 </span>
 @endif
 </td>
 <td>
 @if($usuario->ultimo_acesso)
 <small class="text-muted">
 {{ \Carbon\Carbon::parse($usuario->ultimo_acesso)->diffForHumans() }}
 </small>
 @else
 <small class="text-muted">Nunca acessou</small>
 @endif
 </td>
 <td>
 <small class="text-muted">
 {{ $usuario->created_at->format('d/m/Y') }}
 <br>{{ $usuario->created_at->format('H:i') }}
 </small>
 </td>
 <td>
 <div class="btn-group btn-group-sm">
 <a href="{{ route('users.show', $usuario->id) }}" class="btn btn-outline-primary" title="Visualizar">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('users.edit', $usuario->id) }}" class="btn btn-outline-success" title="Editar">
 <i class="fas fa-edit"></i>
 </a>
 <div class="dropdown">
 <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="Mais opções">
 <i class="fas fa-ellipsis-v"></i>
 </button>
 <ul class="dropdown-menu">
 @if(($usuario->status ?? 'ativo') === 'ativo')
 <li>
 <a class="dropdown-item" href="#" onclick="alterarStatus({{ $usuario->id }}, 'inativo')">
 <i class="fas fa-pause me-2"></i>Desativar
 </a>
 </li>
 @else
 <li>
 <a class="dropdown-item" href="#" onclick="alterarStatus({{ $usuario->id }}, 'ativo')">
 <i class="fas fa-play me-2"></i>Ativar
 </a>
 </li>
 @endif
 <li>
 <a class="dropdown-item" href="#" onclick="resetarSenha({{ $usuario->id }})">
 <i class="fas fa-key me-2"></i>Resetar Senha
 </a>
 </li>
 <li><hr class="dropdown-divider"></li>
 <li>
 <a class="dropdown-item text-danger" href="#" onclick="confirmarExclusao({{ $usuario->id }})">
 <i class="fas fa-trash me-2"></i>Excluir
 </a>
 </li>
 </ul>
 </div>
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 @if($usuarios->isEmpty())
 <div class="empty-state">
 <i class="fas fa-users"></i>
 <h3>Nenhum usuário encontrado</h3>
 <p>Comece criando o primeiro usuário do sistema</p>
 <a href="{{ route('users.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Criar Primeiro Usuário
 </a>
 </div>
 @endif
 <!-- Ações em Lote -->
 <div class="bulk-actions mt-3" id="bulkActions" style="display: none;">
 <div class="card">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <strong><span id="selectedCount">0</span> usuário(s) selecionado(s)</strong>
 </div>
 <div class="btn-group">
 <button class="btn btn-warning" onclick="bulkAction('desativar')">
 <i class="fas fa-pause me-2"></i>Desativar Selecionados
 </button>
 <button class="btn btn-success" onclick="bulkAction('ativar')">
 <i class="fas fa-play me-2"></i>Ativar Selecionados
 </button>
 <button class="btn btn-danger" onclick="bulkAction('excluir')">
 <i class="fas fa-trash me-2"></i>Excluir Selecionados
 </button>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', filterUsuarios);
document.getElementById('tipoFilter').addEventListener('change', filterUsuarios);
document.getElementById('statusFilter').addEventListener('change', filterUsuarios);
function filterUsuarios() {
 const searchTerm = document.getElementById('searchInput').value.toLowerCase();
 const tipoFilter = document.getElementById('tipoFilter').value;
 const statusFilter = document.getElementById('statusFilter').value;
 const items = document.querySelectorAll('.usuario-item');
 items.forEach(item => {
 const nome = item.dataset.nome;
 const email = item.dataset.email;
 const tipo = item.dataset.tipo;
 const status = item.dataset.status;
 let show = true;
 if (searchTerm && !nome.includes(searchTerm) && !email.includes(searchTerm)) {
 show = false;
 }
 if (tipoFilter && tipo !== tipoFilter) {
 show = false;
 }
 if (statusFilter && status !== statusFilter) {
 show = false;
 }
 item.style.display = show ? '' : 'none';
 });
}
document.getElementById('sortSelect').addEventListener('change', function() {
 const sortBy = this.value;
 const tbody = document.getElementById('usuariosTableBody');
 const items = Array.from(tbody.children);
 items.sort((a, b) => {
 switch(sortBy) {
 case 'nome':
 return a.dataset.nome.localeCompare(b.dataset.nome);
 case 'email':
 return a.dataset.email.localeCompare(b.dataset.email);
 case 'tipo':
 return a.dataset.tipo.localeCompare(b.dataset.tipo);
 default:
 return 0;
 }
 });
 items.forEach(item => tbody.appendChild(item));
});
function toggleSelectAll() {
 const selectAll = document.getElementById('selectAll');
 const checkboxes = document.querySelectorAll('.user-checkbox');
 checkboxes.forEach(checkbox => {
 checkbox.checked = selectAll.checked;
 });
 updateBulkActions();
}
function updateBulkActions() {
 const checkboxes = document.querySelectorAll('.user-checkbox:checked');
 const bulkActions = document.getElementById('bulkActions');
 const selectedCount = document.getElementById('selectedCount');
 if (checkboxes.length > 0) {
 bulkActions.style.display = 'block';
 selectedCount.textContent = checkboxes.length;
 } else {
 bulkActions.style.display = 'none';
 }
}
document.addEventListener('change', function(e) {
 if (e.target.classList.contains('user-checkbox')) {
 updateBulkActions();
 }
});
function alterarStatus(id, novoStatus) {
 const mensagem = novoStatus === 'ativo' ? 'ativar' : 'desativar';
 if (confirm(`Deseja ${mensagem} este usuário?`)) {
 console.log('Alterar status usuário:', id, 'para', novoStatus);
 }
}
function resetarSenha(id) {
 if (confirm('Deseja resetar a senha deste usuário? Uma nova senha será enviada por email.')) {
 console.log('Resetar senha usuário:', id);
 }
}
function confirmarExclusao(id) {
 if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
 console.log('Excluir usuário:', id);
 }
}
function bulkAction(acao) {
 const checkboxes = document.querySelectorAll('.user-checkbox:checked');
 const ids = Array.from(checkboxes).map(cb => cb.value);
 if (ids.length === 0) {
 alert('Nenhum usuário selecionado');
 return;
 }
 let mensagem = '';
 switch(acao) {
 case 'ativar':
 mensagem = `ativar ${ids.length} usuário(s)`;
 break;
 case 'desativar':
 mensagem = `desativar ${ids.length} usuário(s)`;
 break;
 case 'excluir':
 mensagem = `excluir ${ids.length} usuário(s)`;
 break;
 }
 if (confirm(`Deseja ${mensagem}?`)) {
 console.log('Ação em lote:', acao, 'IDs:', ids);
 }
}
function exportUsers() {
 console.log('Exportar usuários');
}
function refreshData() {
 location.reload();
}
</script>
@endpush