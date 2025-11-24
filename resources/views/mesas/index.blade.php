@extends('layouts.app')
@section('title', 'Mesas')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-chair me-2"></i>
 Mesas
 </h1>
 <p class="page-subtitle">Gerencie as mesas do estabelecimento</p>
 </div>
 <a href="{{ route('mesas.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Nova Mesa
 </a>
 </div>
 </div>
 <!-- Estatísticas -->
 <div class="row mb-4">
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-primary">
 <i class="fas fa-chair"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $mesas->count() }}</h3>
 <p>Total de Mesas</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-success">
 <i class="fas fa-check-circle"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $mesas->where('status', 'livre')->count() }}</h3>
 <p>Livres</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-warning">
 <i class="fas fa-utensils"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $mesas->where('status', 'ocupada')->count() }}</h3>
 <p>Ocupadas</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-danger">
 <i class="fas fa-tools"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $mesas->where('status', 'manutencao')->count() }}</h3>
 <p>Manutenção</p>
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
 <input type="text" id="searchInput" placeholder="Buscar mesas..." class="form-control">
 </div>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="statusFilter">
 <option value="">Todos Status</option>
 <option value="livre">Livre</option>
 <option value="ocupada">Ocupada</option>
 <option value="reservada">Reservada</option>
 <option value="manutencao">Manutenção</option>
 </select>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="capacidadeFilter">
 <option value="">Todas Capacidades</option>
 <option value="2">2 pessoas</option>
 <option value="4">4 pessoas</option>
 <option value="6">6 pessoas</option>
 <option value="8">8+ pessoas</option>
 </select>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="sortSelect">
 <option value="numero">Ordenar por Número</option>
 <option value="capacidade">Ordenar por Capacidade</option>
 <option value="status">Ordenar por Status</option>
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
 <!-- Grade de Mesas -->
 <div class="row" id="mesasGrid">
 @foreach($mesas as $mesa)
 <div class="col-lg-3 col-md-4 col-sm-6 mb-4 mesa-item" 
 data-numero="{{ $mesa->numero }}" 
 data-status="{{ $mesa->status }}" 
 data-capacidade="{{ $mesa->capacidade }}">
 <div class="card mesa-card mesa-{{ $mesa->status }}">
 <div class="card-header">
 <div class="d-flex justify-content-between align-items-center">
 <h5 class="card-title mb-0">
 <i class="fas fa-chair me-2"></i>
 Mesa {{ $mesa->numero }}
 </h5>
 <div class="mesa-status-badge">
 @switch($mesa->status)
 @case('livre')
 <span class="badge bg-success">Livre</span>
 @break
 @case('ocupada')
 <span class="badge bg-warning">Ocupada</span>
 @break
 @case('reservada')
 <span class="badge bg-info">Reservada</span>
 @break
 @case('manutencao')
 <span class="badge bg-danger">Manutenção</span>
 @break
 @endswitch
 </div>
 </div>
 </div>
 <div class="card-body">
 <div class="mesa-info">
 <div class="info-row">
 <i class="fas fa-users me-2"></i>
 <span>Capacidade: {{ $mesa->capacidade }} pessoas</span>
 </div>
 @if($mesa->localizacao)
 <div class="info-row">
 <i class="fas fa-map-marker-alt me-2"></i>
 <span>{{ $mesa->localizacao }}</span>
 </div>
 @endif
 @if($mesa->observacoes)
 <div class="info-row">
 <i class="fas fa-comment me-2"></i>
 <span>{{ Str::limit($mesa->observacoes, 50) }}</span>
 </div>
 @endif
 </div>
 <!-- Pedidos ativos -->
 @if($mesa->pedidos && $mesa->pedidos->whereIn('status', ['pendente', 'em_preparo', 'pronto'])->count() > 0)
 <div class="pedidos-ativos mt-3">
 <h6 class="text-warning">
 <i class="fas fa-utensils me-1"></i>
 {{ $mesa->pedidos->whereIn('status', ['pendente', 'em_preparo', 'pronto'])->count() }} pedido(s) ativo(s)
 </h6>
 </div>
 @endif
 </div>
 <div class="card-footer">
 <div class="d-flex justify-content-between align-items-center">
 <div class="dropdown">
 <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
 Ações
 </button>
 <ul class="dropdown-menu">
 <li>
 <a class="dropdown-item" href="{{ route('mesas.show', $mesa->id) }}">
 <i class="fas fa-eye me-2"></i>Visualizar
 </a>
 </li>
 <li>
 <a class="dropdown-item" href="{{ route('mesas.edit', $mesa->id) }}">
 <i class="fas fa-edit me-2"></i>Editar
 </a>
 </li>
 <li><hr class="dropdown-divider"></li>
 @if($mesa->status !== 'ocupada')
 <li>
 <a class="dropdown-item" href="#" onclick="alterarStatus({{ $mesa->id }}, 'ocupada')">
 <i class="fas fa-utensils me-2"></i>Ocupar
 </a>
 </li>
 @endif
 @if($mesa->status !== 'livre')
 <li>
 <a class="dropdown-item" href="#" onclick="alterarStatus({{ $mesa->id }}, 'livre')">
 <i class="fas fa-check me-2"></i>Liberar
 </a>
 </li>
 @endif
 <li>
 <a class="dropdown-item text-warning" href="#" onclick="alterarStatus({{ $mesa->id }}, 'manutencao')">
 <i class="fas fa-tools me-2"></i>Manutenção
 </a>
 </li>
 <li><hr class="dropdown-divider"></li>
 <li>
 <a class="dropdown-item text-danger" href="#" onclick="confirmarExclusao({{ $mesa->id }})">
 <i class="fas fa-trash me-2"></i>Excluir
 </a>
 </li>
 </ul>
 </div>
 <div class="btn-group btn-group-sm">
 <a href="{{ route('mesas.show', $mesa->id) }}" class="btn btn-outline-primary">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('mesas.edit', $mesa->id) }}" class="btn btn-outline-success">
 <i class="fas fa-edit"></i>
 </a>
 </div>
 </div>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 @if($mesas->isEmpty())
 <div class="empty-state">
 <i class="fas fa-chair"></i>
 <h3>Nenhuma mesa encontrada</h3>
 <p>Comece criando sua primeira mesa</p>
 <a href="{{ route('mesas.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Criar Primeira Mesa
 </a>
 </div>
 @endif
</div>
@endsection
@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', function() {
 filterMesas();
});
document.getElementById('statusFilter').addEventListener('change', filterMesas);
document.getElementById('capacidadeFilter').addEventListener('change', filterMesas);
function filterMesas() {
 const searchTerm = document.getElementById('searchInput').value.toLowerCase();
 const statusFilter = document.getElementById('statusFilter').value;
 const capacidadeFilter = document.getElementById('capacidadeFilter').value;
 const items = document.querySelectorAll('.mesa-item');
 items.forEach(item => {
 const numero = item.dataset.numero;
 const status = item.dataset.status;
 const capacidade = item.dataset.capacidade;
 let show = true;
 if (searchTerm && !numero.includes(searchTerm)) {
 show = false;
 }
 if (statusFilter && status !== statusFilter) {
 show = false;
 }
 if (capacidadeFilter) {
 if (capacidadeFilter === '8' && parseInt(capacidade) < 8) {
 show = false;
 } else if (capacidadeFilter !== '8' && capacidade !== capacidadeFilter) {
 show = false;
 }
 }
 item.style.display = show ? 'block' : 'none';
 });
}
document.getElementById('sortSelect').addEventListener('change', function() {
 const sortBy = this.value;
 const grid = document.getElementById('mesasGrid');
 const items = Array.from(grid.children);
 items.sort((a, b) => {
 switch(sortBy) {
 case 'numero':
 return parseInt(a.dataset.numero) - parseInt(b.dataset.numero);
 case 'capacidade':
 return parseInt(a.dataset.capacidade) - parseInt(b.dataset.capacidade);
 case 'status':
 return a.dataset.status.localeCompare(b.dataset.status);
 default:
 return 0;
 }
 });
 items.forEach(item => grid.appendChild(item));
});
function alterarStatus(id, novoStatus) {
 let mensagem = '';
 switch(novoStatus) {
 case 'ocupada':
 mensagem = 'ocupar esta mesa';
 break;
 case 'livre':
 mensagem = 'liberar esta mesa';
 break;
 case 'manutencao':
 mensagem = 'colocar esta mesa em manutenção';
 break;
 case 'reservada':
 mensagem = 'reservar esta mesa';
 break;
 }
 if (confirm(`Deseja ${mensagem}?`)) {
 console.log('Alterar status mesa:', id, 'para', novoStatus);
 }
}
function confirmarExclusao(id) {
 if (confirm('Tem certeza que deseja excluir esta mesa?')) {
 console.log('Excluir mesa:', id);
 }
}
function refreshData() {
 location.reload();
}
setInterval(function() {
 console.log('Auto-refresh mesas');
}, 30000);
</script>
@endpush