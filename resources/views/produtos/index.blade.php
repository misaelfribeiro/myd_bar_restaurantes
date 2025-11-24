@extends('layouts.app')
@section('title', 'Produtos')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-box me-2"></i>
 Produtos
 </h1>
 <p class="page-subtitle">Gerencie o catálogo de produtos</p>
 </div>
 <div class="d-flex gap-2">
 <button class="btn btn-outline-primary" onclick="toggleView()">
 <i class="fas fa-th-large me-2"></i>
 <span id="viewToggleText">Lista</span>
 </button>
 <a href="{{ route('produtos.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Novo Produto
 </a>
 </div>
 </div>
 </div>
 <!-- Estatísticas -->
 <div class="row mb-4">
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-primary">
 <i class="fas fa-box"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $produtos->count() }}</h3>
 <p>Total de Produtos</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-success">
 <i class="fas fa-eye"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $produtos->where('disponivel', true)->count() }}</h3>
 <p>Disponíveis</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-warning">
 <i class="fas fa-eye-slash"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $produtos->where('disponivel', false)->count() }}</h3>
 <p>Indisponíveis</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-info">
 <i class="fas fa-dollar-sign"></i>
 </div>
 <div class="stats-content">
 <h3>R$ {{ number_format($produtos->avg('preco'), 2, ',', '.') }}</h3>
 <p>Preço Médio</p>
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
 <input type="text" id="searchInput" placeholder="Buscar produtos..." class="form-control">
 </div>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="categoriaFilter">
 <option value="">Todas Categorias</option>
 @foreach($categorias ?? [] as $categoria)
 <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
 @endforeach
 </select>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="statusFilter">
 <option value="">Todos Status</option>
 <option value="1">Disponível</option>
 <option value="0">Indisponível</option>
 </select>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="sortSelect">
 <option value="nome">Ordenar por Nome</option>
 <option value="preco_asc">Menor Preço</option>
 <option value="preco_desc">Maior Preço</option>
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
 <!-- Grade de Produtos -->
 <div class="produtos-container" id="produtosContainer">
 <div class="row" id="produtosGrid">
 @foreach($produtos as $produto)
 <div class="col-lg-4 col-md-6 mb-4 produto-item" 
 data-nome="{{ strtolower($produto->nome) }}" 
 data-categoria="{{ $produto->categoria_id ?? '' }}" 
 data-status="{{ $produto->disponivel ? '1' : '0' }}"
 data-preco="{{ $produto->preco }}">
 <div class="card produto-card">
 @if($produto->imagem)
 <div class="card-img-top-wrapper">
 <img src="{{ asset('storage/' . $produto->imagem) }}" class="card-img-top" alt="{{ $produto->nome }}">
 <div class="status-badge">
 @if($produto->disponivel)
 <span class="badge bg-success">Disponível</span>
 @else
 <span class="badge bg-danger">Indisponível</span>
 @endif
 </div>
 </div>
 @endif
 <div class="card-header">
 <div class="d-flex justify-content-between align-items-start">
 <h5 class="card-title mb-0">{{ $produto->nome }}</h5>
 <div class="dropdown">
 <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
 <i class="fas fa-ellipsis-v"></i>
 </button>
 <ul class="dropdown-menu">
 <li>
 <a class="dropdown-item" href="{{ route('produtos.show', $produto->id) }}">
 <i class="fas fa-eye me-2"></i>Visualizar
 </a>
 </li>
 <li>
 <a class="dropdown-item" href="{{ route('produtos.edit', $produto->id) }}">
 <i class="fas fa-edit me-2"></i>Editar
 </a>
 </li>
 <li><hr class="dropdown-divider"></li>
 <li>
 <a class="dropdown-item text-danger" href="#" onclick="confirmarExclusao({{ $produto->id }})">
 <i class="fas fa-trash me-2"></i>Excluir
 </a>
 </li>
 </ul>
 </div>
 </div>
 </div>
 <div class="card-body">
 @if($produto->descricao)
 <p class="card-text">{{ Str::limit($produto->descricao, 80) }}</p>
 @endif
 <div class="produto-info">
 <div class="preco-tag">
 <strong>R$ {{ number_format($produto->preco, 2, ',', '.') }}</strong>
 </div>
 @if($produto->categoria)
 <span class="badge bg-info">
 <i class="fas fa-tag me-1"></i>
 {{ $produto->categoria->nome }}
 </span>
 @endif
 </div>
 </div>
 <div class="card-footer">
 <div class="d-flex justify-content-between align-items-center">
 <small class="text-muted">
 <i class="fas fa-calendar me-1"></i>
 {{ $produto->created_at->format('d/m/Y') }}
 </small>
 <div class="btn-group btn-group-sm">
 <a href="{{ route('produtos.show', $produto->id) }}" class="btn btn-outline-primary">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-outline-success">
 <i class="fas fa-edit"></i>
 </a>
 <button class="btn btn-outline-{{ $produto->disponivel ? 'warning' : 'success' }}" 
 onclick="toggleDisponibilidade({{ $produto->id }}, {{ $produto->disponivel ? 'false' : 'true' }})">
 <i class="fas fa-{{ $produto->disponivel ? 'eye-slash' : 'eye' }}"></i>
 </button>
 </div>
 </div>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @if($produtos->isEmpty())
 <div class="empty-state">
 <i class="fas fa-box"></i>
 <h3>Nenhum produto encontrado</h3>
 <p>Comece criando seu primeiro produto</p>
 <a href="{{ route('produtos.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Criar Primeiro Produto
 </a>
 </div>
 @endif
</div>
@endsection
@push('scripts')
<script>
let currentView = 'grid';
document.getElementById('searchInput').addEventListener('input', function() {
 filterProducts();
});
document.getElementById('categoriaFilter').addEventListener('change', filterProducts);
document.getElementById('statusFilter').addEventListener('change', filterProducts);
function filterProducts() {
 const searchTerm = document.getElementById('searchInput').value.toLowerCase();
 const categoriaFilter = document.getElementById('categoriaFilter').value;
 const statusFilter = document.getElementById('statusFilter').value;
 const items = document.querySelectorAll('.produto-item');
 items.forEach(item => {
 const nome = item.dataset.nome;
 const categoria = item.dataset.categoria;
 const status = item.dataset.status;
 let show = true;
 if (searchTerm && !nome.includes(searchTerm)) {
 show = false;
 }
 if (categoriaFilter && categoria !== categoriaFilter) {
 show = false;
 }
 if (statusFilter && status !== statusFilter) {
 show = false;
 }
 item.style.display = show ? 'block' : 'none';
 });
}
document.getElementById('sortSelect').addEventListener('change', function() {
 const sortBy = this.value;
 const grid = document.getElementById('produtosGrid');
 const items = Array.from(grid.children);
 items.sort((a, b) => {
 switch(sortBy) {
 case 'nome':
 return a.dataset.nome.localeCompare(b.dataset.nome);
 case 'preco_asc':
 return parseFloat(a.dataset.preco) - parseFloat(b.dataset.preco);
 case 'preco_desc':
 return parseFloat(b.dataset.preco) - parseFloat(a.dataset.preco);
 default:
 return 0;
 }
 });
 items.forEach(item => grid.appendChild(item));
});
function toggleDisponibilidade(id, novoStatus) {
 if (confirm(`Deseja ${novoStatus ? 'ativar' : 'desativar'} este produto?`)) {
 console.log('Toggle disponibilidade:', id, novoStatus);
 }
}
function confirmarExclusao(id) {
 if (confirm('Tem certeza que deseja excluir este produto?')) {
 console.log('Excluir produto:', id);
 }
}
function refreshData() {
 location.reload();
}
function toggleView() {
 console.log('Toggle view');
}
</script>
@endpush