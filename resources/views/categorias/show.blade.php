@extends('layouts.app')
@section('title', 'Categoria: ' . $categoria->nome)
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-tag me-2"></i>
 {{ $categoria->nome }}
 </h1>
 <p class="page-subtitle">Categoria de produtos</p>
 </div>
 <div class="btn-group">
 <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-warning">
 <i class="fas fa-edit me-2"></i>
 Editar
 </a>
 <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 </div>
 <div class="row">
 <!-- Informações da Categoria -->
 <div class="col-lg-4 mb-4">
 <div class="card">
 <div class="card-header">
 <h5 class="card-title mb-0">
 <i class="fas fa-info-circle me-2"></i>
 Informações da Categoria
 </h5>
 </div>
 <div class="card-body">
 <div class="mb-3">
 <label class="form-label text-muted small">Nome</label>
 <div class="fw-bold">{{ $categoria->nome }}</div>
 </div>
 @if($categoria->descricao)
 <div class="mb-3">
 <label class="form-label text-muted small">Descrição</label>
 <div>{{ $categoria->descricao }}</div>
 </div>
 @endif
 <div class="mb-3">
 <label class="form-label text-muted small">Criada em</label>
 <div>{{ $categoria->created_at->format('d/m/Y H:i') }}</div>
 </div>
 @if($categoria->updated_at != $categoria->created_at)
 <div class="mb-3">
 <label class="form-label text-muted small">Última atualização</label>
 <div>{{ $categoria->updated_at->format('d/m/Y H:i') }}</div>
 </div>
 @endif
 <div class="mb-3">
 <label class="form-label text-muted small">Total de produtos</label>
 <div class="fw-bold text-primary">{{ $categoria->produtos->count() }} produtos</div>
 </div>
 <div class="mb-3">
 <label class="form-label text-muted small">Produtos ativos</label>
 <div class="fw-bold text-success">{{ $categoria->produtos->where('ativo', true)->count() }} ativos</div>
 </div>
 @if($categoria->produtos->where('ativo', false)->count() > 0)
 <div>
 <label class="form-label text-muted small">Produtos inativos</label>
 <div class="fw-bold text-warning">{{ $categoria->produtos->where('ativo', false)->count() }} inativos</div>
 </div>
 @endif
 </div>
 </div>
 </div>
 <!-- Lista de Produtos -->
 <div class="col-lg-8">
 <div class="card">
 <div class="card-header d-flex justify-content-between align-items-center">
 <h5 class="card-title mb-0">
 <i class="fas fa-box me-2"></i>
 Produtos desta Categoria
 </h5>
 <a href="{{ route('produtos.create') }}?categoria_id={{ $categoria->id }}" class="btn btn-primary btn-sm">
 <i class="fas fa-plus me-2"></i>
 Novo Produto
 </a>
 </div>
 <div class="card-body">
 @if($categoria->produtos->count() > 0)
 <!-- Filtros -->
 <div class="row mb-4">
 <div class="col-md-6">
 <input type="text" 
 class="form-control" 
 id="searchProduto" 
 placeholder="Buscar produtos..."
 onkeyup="filterProducts()">
 </div>
 <div class="col-md-6">
 <select class="form-select" id="statusFilter" onchange="filterProducts()">
 <option value="">Todos os status</option>
 <option value="ativo">Apenas ativos</option>
 <option value="inativo">Apenas inativos</option>
 </select>
 </div>
 </div>
 <!-- Grid de Produtos -->
 <div class="row" id="produtosGrid">
 @foreach($categoria->produtos as $produto)
 <div class="col-lg-6 col-xl-4 mb-4 produto-item" 
 data-nome="{{ strtolower($produto->nome) }}" 
 data-status="{{ $produto->ativo ? 'ativo' : 'inativo' }}">
 <div class="card h-100">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-start mb-2">
 <h6 class="card-title mb-0">{{ $produto->nome }}</h6>
 <span class="badge bg-{{ $produto->ativo ? 'success' : 'secondary' }}">
 {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
 </span>
 </div>
 @if($produto->descricao)
 <p class="card-text text-muted small">
 {{ Str::limit($produto->descricao, 80) }}
 </p>
 @endif
 <div class="row">
 <div class="col-6">
 <small class="text-muted">Preço</small>
 <div class="fw-bold text-success">
 R$ {{ number_format($produto->preco, 2, ',', '.') }}
 </div>
 </div>
 @if($produto->codigo)
 <div class="col-6">
 <small class="text-muted">Código</small>
 <div class="fw-bold">{{ $produto->codigo }}</div>
 </div>
 @endif
 </div>
 <div class="mt-3 d-flex gap-2">
 <a href="{{ route('produtos.show', $produto->id) }}" 
 class="btn btn-outline-primary btn-sm">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('produtos.edit', $produto->id) }}" 
 class="btn btn-outline-warning btn-sm">
 <i class="fas fa-edit"></i>
 </a>
 </div>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 <!-- Resultado vazio da busca -->
 <div id="emptySearch" class="text-center text-muted py-4" style="display: none;">
 <i class="fas fa-search fa-2x mb-3"></i>
 <h5>Nenhum produto encontrado</h5>
 <p>Tente alterar os filtros de busca</p>
 </div>
 @else
 <!-- Estado vazio -->
 <div class="text-center text-muted py-5">
 <i class="fas fa-box fa-3x mb-3"></i>
 <h5>Nenhum produto cadastrado</h5>
 <p>Esta categoria ainda não possui produtos.</p>
 <a href="{{ route('produtos.create') }}?categoria_id={{ $categoria->id }}" 
 class="btn btn-primary mt-3">
 <i class="fas fa-plus me-2"></i>
 Cadastrar Primeiro Produto
 </a>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
 <!-- Estatísticas Adicionais -->
 @if($categoria->produtos->count() > 0)
 <div class="row mt-4">
 <div class="col-12">
 <div class="card">
 <div class="card-header">
 <h6 class="card-title mb-0">
 <i class="fas fa-chart-bar me-2"></i>
 Estatísticas da Categoria
 </h6>
 </div>
 <div class="card-body">
 <div class="row text-center">
 <div class="col-md-3">
 <div class="border-end">
 <h4 class="text-primary mb-0">{{ $categoria->produtos->count() }}</h4>
 <small class="text-muted">Total de Produtos</small>
 </div>
 </div>
 <div class="col-md-3">
 <div class="border-end">
 <h4 class="text-success mb-0">{{ $categoria->produtos->where('ativo', true)->count() }}</h4>
 <small class="text-muted">Produtos Ativos</small>
 </div>
 </div>
 <div class="col-md-3">
 <div class="border-end">
 <h4 class="text-success mb-0">
 R$ {{ number_format($categoria->produtos->where('ativo', true)->avg('preco') ?? 0, 2, ',', '.') }}
 </h4>
 <small class="text-muted">Preço Médio</small>
 </div>
 </div>
 <div class="col-md-3">
 <h4 class="text-info mb-0">
 R$ {{ number_format($categoria->produtos->where('ativo', true)->sum('preco'), 2, ',', '.') }}
 </h4>
 <small class="text-muted">Valor Total Catálogo</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 @endif
</div>
@endsection
@push('scripts')
<script>
function filterProducts() {
 const searchTerm = document.getElementById('searchProduto').value.toLowerCase();
 const statusFilter = document.getElementById('statusFilter').value;
 const items = document.querySelectorAll('.produto-item');
 let visibleCount = 0;
 items.forEach(item => {
 const nome = item.dataset.nome;
 const status = item.dataset.status;
 let show = true;
 if (searchTerm && !nome.includes(searchTerm)) {
 show = false;
 }
 if (statusFilter && status !== statusFilter) {
 show = false;
 }
 if (show) {
 item.style.display = 'block';
 visibleCount++;
 } else {
 item.style.display = 'none';
 }
 });
 const emptySearch = document.getElementById('emptySearch');
 const produtosGrid = document.getElementById('produtosGrid');
 if (visibleCount === 0 && items.length > 0) {
 emptySearch.style.display = 'block';
 produtosGrid.style.display = 'none';
 } else {
 emptySearch.style.display = 'none';
 produtosGrid.style.display = 'block';
 }
}
</script>
@endpush