@extends('layouts.app')
@section('title', 'Nova Categoria')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-plus-circle me-2"></i>
 Nova Categoria
 </h1>
 <p class="page-subtitle">Adicione uma nova categoria para organizar seus produtos</p>
 </div>
 <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 <!-- Dicas -->
 <div class="alert alert-info mb-4">
 <h6 class="mb-3">
 <i class="fas fa-lightbulb me-2"></i>
 Dicas para criar uma boa categoria
 </h6>
 <ul class="mb-0">
 <li>Use nomes claros e descritivos que facilitem a organização</li>
 <li>Evite nomes muito longos ou complicados</li>
 <li>Pense na descrição como uma forma de ajudar sua equipe</li>
 <li>Considere como os produtos serão agrupados logicamente</li>
 </ul>
 </div>
 <div class="row">
 <div class="col-lg-8">
 <div class="card shadow-sm">
 <div class="card-body">
 <h5 class="card-title mb-4">
 <i class="fas fa-edit me-2"></i>
 Informações da Categoria
 </h5>
 <form action="{{ route('categorias.store') }}" method="POST" id="categoriaForm">
 @csrf
 <!-- Nome da Categoria -->
 <div class="mb-4">
 <label for="nome" class="form-label fw-bold">
 <i class="fas fa-tag me-1"></i>
 Nome da Categoria *
 </label>
 <input type="text" 
 class="form-control" 
 id="nome" 
 name="nome" 
 placeholder="Ex: Bebidas, Pratos Principais, Sobremesas..."
 maxlength="255"
 required
 value="{{ old('nome') }}">
 <small class="text-muted">
 O nome será usado para organizar seus produtos. Deve ser único e descritivo.
 </small>
 <div class="text-end text-muted mt-1">
 <small><span id="nomeCounter">0</span>/255 caracteres</small>
 </div>
 @error('nome')
 <div class="text-danger mt-1">{{ $message }}</div>
 @enderror
 </div>
 <!-- Descrição da Categoria -->
 <div class="mb-4">
 <label for="descricao" class="form-label fw-bold">
 <i class="fas fa-align-left me-1"></i>
 Descrição (Opcional)
 </label>
 <textarea class="form-control" 
 id="descricao" 
 name="descricao" 
 rows="3"
 placeholder="Descreva o tipo de produtos que pertencem a esta categoria..."
 maxlength="500">{{ old('descricao') }}</textarea>
 <small class="text-muted">
 Adicione uma descrição para ajudar sua equipe a entender melhor esta categoria.
 </small>
 <div class="text-end text-muted mt-1">
 <small><span id="descricaoCounter">0</span>/500 caracteres</small>
 </div>
 @error('descricao')
 <div class="text-danger mt-1">{{ $message }}</div>
 @enderror
 </div>
 <!-- Botões de Ação -->
 <div class="d-flex gap-2 justify-content-end">
 <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-times me-2"></i>
 Cancelar
 </a>
 <button type="submit" class="btn btn-primary">
 <i class="fas fa-save me-2"></i>
 Salvar Categoria
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 <!-- Coluna Lateral com Exemplos -->
 <div class="col-lg-4">
 <div class="card shadow-sm">
 <div class="card-body">
 <h6 class="card-title mb-3">
 <i class="fas fa-info-circle me-2"></i>
 Exemplos de Categorias
 </h6>
 <div class="mb-3">
 <strong class="d-block">Bebidas</strong>
 <small class="text-muted">Refrigerantes, sucos, cervejas, vinhos e outras bebidas</small>
 </div>
 <div class="mb-3">
 <strong class="d-block">Pratos Principais</strong>
 <small class="text-muted">Carnes, massas, risotos e outros pratos principais</small>
 </div>
 <div class="mb-3">
 <strong class="d-block">Sobremesas</strong>
 <small class="text-muted">Doces, sorvetes, tortas e outras sobremesas</small>
 </div>
 <div class="mb-3">
 <strong class="d-block">Entradas</strong>
 <small class="text-muted">Porções, aperitivos e petiscos</small>
 </div>
 <div>
 <strong class="d-block">Lanches</strong>
 <small class="text-muted">Sanduíches, hambúrgueres e lanches rápidos</small>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
@push('scripts')
<script>
 document.getElementById('nome').addEventListener('input', function() {
 document.getElementById('nomeCounter').textContent = this.value.length;
 });
 document.getElementById('descricao').addEventListener('input', function() {
 document.getElementById('descricaoCounter').textContent = this.value.length;
 });
 document.getElementById('nomeCounter').textContent = document.getElementById('nome').value.length;
 document.getElementById('descricaoCounter').textContent = document.getElementById('descricao').value.length;
</script>
@endpush
@endsection