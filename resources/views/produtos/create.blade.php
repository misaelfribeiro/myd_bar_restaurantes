@extends('layouts.app')
@section('title', 'Novo Produto')
@section('content')
<div class="container-fluid">
 <div class="page-header mb-4">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-plus-circle me-2"></i>
 Novo Produto
 </h1>
 <p class="page-subtitle">Adicione um novo produto ao catálogo</p>
 </div>
 <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 <div class="row justify-content-center">
 <div class="col-lg-8">
 <div class="card shadow-sm">
 <div class="card-body p-4">
 @if ($errors->any())
 <div class="alert alert-danger">
 <h6 class="alert-heading">
 <i class="fas fa-exclamation-triangle me-1"></i>
 Erro na validação
 </h6>
 <ul class="mb-0 mt-2">
 @foreach ($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif
 <form action="{{ route('produtos.store') }}" method="POST" id="formProduto">
 @csrf
 <div class="row">
 <div class="col-md-8 mb-3">
 <label for="nome" class="form-label">
 <i class="fas fa-utensils me-1"></i>
 Nome do Produto *
 </label>
 <input type="text" 
 class="form-control @error('nome') is-invalid @enderror" 
 id="nome" 
 name="nome" 
 value="{{ old('nome') }}" 
 placeholder="Ex: Hambúrguer Especial"
 maxlength="255"
 required>
 @error('nome')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 <div class="form-text">
 <i class="fas fa-info-circle me-1"></i>
 Máximo 255 caracteres
 </div>
 </div>
 <div class="col-md-4 mb-3">
 <label for="preco" class="form-label">
 <i class="fas fa-dollar-sign me-1"></i>
 Preço *
 </label>
 <div class="input-group">
 <span class="input-group-text">R$</span>
 <input type="number" 
 class="form-control @error('preco') is-invalid @enderror" 
 id="preco" 
 name="preco" 
 value="{{ old('preco') }}" 
 step="0.01" 
 min="0" 
 max="9999.99"
 placeholder="0,00"
 required>
 @error('preco')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <div class="row">
 <div class="col-md-6 mb-3">
 <label for="categoria_id" class="form-label">
 <i class="fas fa-tags me-1"></i>
 Categoria *
 </label>
 <select class="form-select @error('categoria_id') is-invalid @enderror" 
 id="categoria_id" 
 name="categoria_id" 
 required>
 <option value="">Selecione uma categoria</option>
 @foreach($categorias as $categoria)
 <option value="{{ $categoria->id }}" 
 {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
 {{ $categoria->nome }}
 </option>
 @endforeach
 </select>
 @error('categoria_id')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6 mb-3">
 <label for="ativo" class="form-label">
 <i class="fas fa-toggle-on me-1"></i>
 Status
 </label>
 <select class="form-select @error('ativo') is-invalid @enderror" 
 id="ativo" 
 name="ativo">
 <option value="1" {{ old('ativo', 1) == 1 ? 'selected' : '' }}>
 <i class="fas fa-check-circle"></i> Ativo
 </option>
 <option value="0" {{ old('ativo') == 0 ? 'selected' : '' }}>
 <i class="fas fa-times-circle"></i> Inativo
 </option>
 </select>
 @error('ativo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="mb-4">
 <label for="descricao" class="form-label">
 <i class="fas fa-align-left me-1"></i>
 Descrição
 </label>
 <textarea class="form-control @error('descricao') is-invalid @enderror" 
 id="descricao" 
 name="descricao" 
 rows="4" 
 placeholder="Descreva o produto (ingredientes, características, etc.)"
 maxlength="1000">{{ old('descricao') }}</textarea>
 @error('descricao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 <div class="form-text">
 <span id="charCount">0</span>/1000 caracteres
 </div>
 </div>
 <div class="d-grid gap-2 d-md-flex justify-content-md-end">
 <a href="{{ route('produtos.index') }}" class="btn btn-secondary me-md-2">
 <i class="fas fa-times me-1"></i>
 Cancelar
 </a>
 <button type="submit" class="btn btn-primary" id="btnSalvar">
 <i class="fas fa-save me-1"></i>
 <span class="btn-text">Salvar Produto</span>
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
@push('scripts')
<script>
 document.addEventListener('DOMContentLoaded', function() {
 const descricaoTextarea = document.getElementById('descricao');
 const charCount = document.getElementById('charCount');
 const btnSalvar = document.getElementById('btnSalvar');
 const form = document.getElementById('formProduto');
 function updateCharCount() {
 const count = descricaoTextarea.value.length;
 charCount.textContent = count;
 if (count > 900) {
 charCount.style.color = '#dc3545';
 } else if (count > 800) {
 charCount.style.color = '#ffc107';
 } else {
 charCount.style.color = '#6c757d';
 }
 }
 descricaoTextarea.addEventListener('input', updateCharCount);
 updateCharCount();
 const precoInput = document.getElementById('preco');
 precoInput.addEventListener('input', function() {
 let value = this.value;
 if (value && !isNaN(value)) {
 value = parseFloat(value);
 if (value > 9999.99) {
 this.value = 9999.99;
 }
 }
 });
 form.addEventListener('submit', function() {
 btnSalvar.disabled = true;
 btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';
 });
 });
</script>
@endpush
@endsection