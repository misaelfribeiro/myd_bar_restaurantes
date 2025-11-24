@extends('layouts.app')
@section('title', 'Categorias')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-tags me-2"></i>
 Categorias
 </h1>
 <p class="page-subtitle">Gerencie as categorias dos produtos</p>
 </div>
 <a href="{{ route('categorias.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Nova Categoria
 </a>
 </div>
 </div>
 <!-- Estatísticas -->
 <div class="row mb-4">
 <div class="col-md-4">
 <div class="stats-card">
 <div class="stats-icon bg-primary">
 <i class="fas fa-tags"></i>
 </div>
 <div class="stats-content">
 <h3 id="totalCategorias">{{ $categorias->count() }}</h3>
 <p>Total de Categorias</p>
 </div>
 </div>
 </div>
 <div class="col-md-4">
 <div class="stats-card">
 <div class="stats-icon bg-success">
 <i class="fas fa-box"></i>
 </div>
 <div class="stats-content">
 <h3 id="totalProdutos">{{ $categorias->sum(function($cat) { return $cat->produtos->count(); }) }}</h3>
 <p>Produtos Vinculados</p>
 </div>
 </div>
 </div>
 <div class="col-md-4">
 <div class="stats-card">
 <div class="stats-icon bg-warning">
 <i class="fas fa-chart-bar"></i>
 </div>
 <div class="stats-content">
 <h3 id="mediaprodutos">{{ $categorias->count() > 0 ? number_format($categorias->sum(function($cat) { return $cat->produtos->count(); }) / $categorias->count(), 1) : 0 }}</h3>
 <p>Média por Categoria</p>
 </div>
 </div>
 </div>
 </div>
 <!-- Filtros -->
 <div class="filters-card mb-4">
 <div class="row">
 <div class="col-md-6">
 <div class="search-box">
 <i class="fas fa-search"></i>
 <input type="text" id="searchInput" placeholder="Buscar categorias..." class="form-control">
 </div>
 </div>
 <div class="col-md-3">
 <select class="form-select" id="sortSelect">
 <option value="nome">Ordenar por Nome</option>
 <option value="produtos">Ordenar por Produtos</option>
 <option value="recente">Mais Recente</option>
 </select>
 </div>
 <div class="col-md-3">
 <button class="btn btn-outline-primary w-100" onclick="refreshData()">
 <i class="fas fa-sync-alt me-2"></i>
 Atualizar
 </button>
 </div>
 </div>
 </div>
 <!-- Lista de Categorias -->
 <div class="row" id="categoriasGrid">
 @foreach($categorias as $categoria)
 <div class="col-lg-4 col-md-6 mb-4 categoria-item" data-nome="{{ strtolower($categoria->nome) }}">
 <div class="card categoria-card">
 <div class="card-header">
 <div class="d-flex justify-content-between align-items-start">
 <h5 class="card-title mb-0">{{ $categoria->nome }}</h5>
 <div class="dropdown">
 <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
 <i class="fas fa-ellipsis-v"></i>
 </button>
 <ul class="dropdown-menu">
 <li>
 <a class="dropdown-item" href="{{ route('categorias.show', $categoria->id) }}">
 <i class="fas fa-eye me-2"></i>Visualizar
 </a>
 </li>
 <li>
 <a class="dropdown-item" href="{{ route('categorias.edit', $categoria->id) }}">
 <i class="fas fa-edit me-2"></i>Editar
 </a>
 </li>
 <li><hr class="dropdown-divider"></li>
 <li>
 <a class="dropdown-item text-danger" href="#" onclick="confirmarExclusao({{ $categoria->id }})">
 <i class="fas fa-trash me-2"></i>Excluir
 </a>
 </li>
 </ul>
 </div>
 </div>
 </div>
 <div class="card-body">
 @if($categoria->descricao)
 <p class="card-text">{{ Str::limit($categoria->descricao, 100) }}</p>
 @endif
 <div class="categoria-stats">
 <span class="badge bg-primary">
 <i class="fas fa-box me-1"></i>
 {{ $categoria->produtos->count() }} produtos
 </span>
 </div>
 </div>
 <div class="card-footer">
 <div class="d-flex justify-content-between align-items-center">
 <small class="text-muted">
 <i class="fas fa-calendar me-1"></i>
 {{ $categoria->created_at->format('d/m/Y') }}
 </small>
 <div class="btn-group btn-group-sm">
 <a href="{{ route('categorias.show', $categoria->id) }}" class="btn btn-outline-primary">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-outline-success">
 <i class="fas fa-edit"></i>
 </a>
 </div>
 </div>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 @if($categorias->isEmpty())
 <div class="empty-state">
 <i class="fas fa-tags"></i>
 <h3>Nenhuma categoria encontrada</h3>
 <p>Comece criando sua primeira categoria de produtos</p>
 <a href="{{ route('categorias.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Criar Primeira Categoria
 </a>
 </div>
 @endif
</div>
@endsection
@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', function() {
 const searchTerm = this.value.toLowerCase();
 const items = document.querySelectorAll('.categoria-item');
 items.forEach(item => {
 const nome = item.dataset.nome;
 if (nome.includes(searchTerm)) {
 item.style.display = 'block';
 } else {
 item.style.display = 'none';
 }
 });
});
document.getElementById('sortSelect').addEventListener('change', function() {
 const sortBy = this.value;
 const grid = document.getElementById('categoriasGrid');
 const items = Array.from(grid.children);
 items.sort((a, b) => {
 if (sortBy === 'nome') {
 return a.dataset.nome.localeCompare(b.dataset.nome);
 }
 return 0;
 });
 items.forEach(item => grid.appendChild(item));
});
function confirmarExclusao(id) {
 if (confirm('Tem certeza que deseja excluir esta categoria?')) {
 console.log('Excluir categoria:', id);
 }
}
function refreshData() {
 location.reload();
}
</script>
@endpush