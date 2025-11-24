@extends('layouts.app')
@section('title', 'Editar Produto')
@section('content')
<div class="container-fluid">
 <div class="page-header mb-4">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-edit me-2"></i>
 Editar Produto
 </h1>
 <p class="page-subtitle">Atualize as informações do produto</p>
 </div>
 <div class="btn-group">
 <a href="{{ route('produtos.show', $produto->id) }}" class="btn btn-outline-info">
 <i class="fas fa-eye me-2"></i>
 Visualizar
 </a>
 <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 </div>
 <div class="row justify-content-center">
 <div class="col-lg-8">
 <div class="card shadow-sm mb-4">
 <div class="card-body">
 <div class="row align-items-center">
 <div class="col-md-8">
 <h5 class="mb-1">
 <i class="fas fa-utensils me-2 text-primary"></i>
 {{ $produto->nome }}
 </h5>
 <small class="text-muted">
 <i class="fas fa-info-circle me-1"></i>
 ID: #{{ $produto->id }} | 
 Criado em {{ $produto->created_at->format('d/m/Y H:i') }}
 </small>
 </div>
 <div class="col-md-4 text-md-end mt-3 mt-md-0">
 <span class="badge {{ $produto->ativo ? 'bg-success' : 'bg-secondary' }} fs-6 px-3 py-2">
 <i class="fas {{ $produto->ativo ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
 {{ $produto->status }}
 </span>
 </div>
 </div>
 </div>
 </div>
 <div class="card shadow-sm">
 <div class="card-body p-4">
 @if ($errors->any())
 <div class="alert alert-danger alert-dismissible fade show">
 <h6 class="alert-heading">
 <i class="fas fa-exclamation-triangle me-1"></i>
 Erro na validação
 </h6>
 <ul class="mb-0 mt-2">
 @foreach ($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 @if (session('success'))
 <div class="alert alert-success alert-dismissible fade show">
 <i class="fas fa-check-circle me-1"></i>
 {{ session('success') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 <form action="{{ route('produtos.update', $produto->id) }}" method="POST" id="formProduto">
 @csrf
 @method('PUT')
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
 value="{{ old('nome', $produto->nome) }}" 
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
 value="{{ old('preco', $produto->preco) }}" 
 step="0.01" 
 min="0" 
 max="9999.99"
 placeholder="0,00"
 required>
 @error('preco')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="form-text">
 Atual: <strong>{{ $produto->preco_formatado }}</strong>
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
 {{ old('categoria_id', $produto->categoria_id) == $categoria->id ? 'selected' : '' }}>
 {{ $categoria->nome }}
 </option>
 @endforeach
 </select>
 @error('categoria_id')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 <div class="form-text">
 Atual: <strong>{{ $produto->categoria ? $produto->categoria->nome : 'Sem categoria' }}</strong>
 </div>
 </div>
 <div class="col-md-6 mb-3">
 <label for="ativo" class="form-label">
 <i class="fas fa-toggle-on me-1"></i>
 Status
 </label>
 <select class="form-select @error('ativo') is-invalid @enderror" 
 id="ativo" 
 name="ativo">
 <option value="1" {{ old('ativo', $produto->ativo) == 1 ? 'selected' : '' }}>
 <i class="fas fa-check-circle"></i> Ativo
 </option>
 <option value="0" {{ old('ativo', $produto->ativo) == 0 ? 'selected' : '' }}>
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
 maxlength="1000">{{ old('descricao', $produto->descricao) }}</textarea>
 @error('descricao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 <div class="form-text">
 <span id="charCount">{{ strlen(old('descricao', $produto->descricao)) }}</span>/1000 caracteres
 </div>
 </div>
 <div class="d-grid gap-2 d-md-flex justify-content-md-between">
 <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
 <i class="fas fa-trash me-1"></i>
 Excluir
 </button>
 <div class="d-grid gap-2 d-md-flex">
 <a href="{{ route('produtos.index') }}" class="btn btn-secondary me-md-2">
 <i class="fas fa-times me-1"></i>
 Cancelar
 </a>
 <button type="submit" class="btn btn-primary" id="btnSalvar">
 <i class="fas fa-save me-1"></i>
 <span class="btn-text">Atualizar Produto</span>
 </button>
 </div>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
</div>
<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
 <div class="modal-dialog modal-dialog-centered">
 <div class="modal-content">
 <div class="modal-header bg-danger text-white">
 <h5 class="modal-title">
 <i class="fas fa-exclamation-triangle me-2"></i>
 Confirmar Exclusão
 </h5>
 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
 </div>
 <div class="modal-body">
 <p class="mb-3">
 <strong>Tem certeza que deseja excluir este produto?</strong>
 </p>
 <div class="bg-light p-3 rounded">
 <h6 class="mb-1">{{ $produto->nome }}</h6>
 <small class="text-muted">
 Preço: {{ $produto->preco_formatado }} | 
 Categoria: {{ $produto->categoria ? $produto->categoria->nome : 'Sem categoria' }}
 </small>
 </div>
 <p class="mt-3 mb-0 text-danger">
 <i class="fas fa-exclamation-circle me-1"></i>
 <strong>Atenção:</strong> Esta ação não pode ser desfeita.
 </p>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
 <i class="fas fa-times me-1"></i>
 Cancelar
 </button>
 <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="d-inline">
 @csrf
 @method('DELETE')
 <button type="submit" class="btn btn-danger">
 <i class="fas fa-trash me-1"></i>
 Excluir Definitivamente
 </button>
 </form>
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
 btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Atualizando...';
 });
 let formChanged = false;
 const inputs = form.querySelectorAll('input, select, textarea');
 inputs.forEach(input => {
 input.addEventListener('change', function() {
 formChanged = true;
 });
 });
 window.addEventListener('beforeunload', function(e) {
 if (formChanged) {
 const message = 'Você tem alterações não salvas. Deseja realmente sair?';
 e.returnValue = message;
 return message;
 }
 });
 form.addEventListener('submit', function() {
 formChanged = false;
 });
 });
</script>
@endpush
@endsection