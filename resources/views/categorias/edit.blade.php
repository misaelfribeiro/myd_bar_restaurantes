@extends('layouts.app')
@section('title', 'Editar Categoria')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-edit me-2"></i>
 Editar Categoria
 </h1>
 <p class="page-subtitle">Atualize as informações da categoria "{{ $categoria->nome }}"</p>
 </div>
 <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 <div class="row">
 <div class="col-lg-8">
 <div class="card shadow-sm">
 <div class="card-body">
 <h5 class="card-title mb-4">
 <i class="fas fa-info-circle me-2"></i>
 Informações da Categoria
 </h5>
 <form action="{{ route('categorias.update', $categoria) }}" method="POST" id="categoriaForm">
 @csrf
 @method('PUT')
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
 value="{{ old('nome', $categoria->nome) }}">
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
 maxlength="500">{{ old('descricao', $categoria->descricao) }}</textarea>
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
 <div class="d-flex gap-2 justify-content-between">
 <button type="button" class="btn btn-danger" onclick="confirmarExclusao()">
 <i class="fas fa-trash me-2"></i>
 Excluir
 </button>
 <div class="d-flex gap-2">
 <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-times me-2"></i>
 Cancelar
 </a>
 <button type="submit" class="btn btn-primary">
 <i class="fas fa-save me-2"></i>
 Salvar Alterações
 </button>
 </div>
 </div>
 </form>
 <!-- Form de Exclusão (oculto) -->
 <form id="deleteForm" action="{{ route('categorias.destroy', $categoria) }}" method="POST" style="display: none;">
 @csrf
 @method('DELETE')
 </form>
 </div>
 </div>
 </div>
 <!-- Coluna Lateral com Informações -->
 <div class="col-lg-4">
 <div class="card shadow-sm mb-4">
 <div class="card-body">
 <h6 class="card-title mb-3">
 <i class="fas fa-box me-2"></i>
 Produtos Associados
 </h6>
 @if($categoria->produtos->count() > 0)
 <p class="mb-2">Esta categoria possui <strong>{{ $categoria->produtos->count() }}</strong> produto(s):</p>
 <ul class="list-unstyled">
 @foreach($categoria->produtos->take(5) as $produto)
 <li class="mb-2">
 <i class="fas fa-chevron-right text-primary me-2"></i>
 {{ $produto->nome }}
 </li>
 @endforeach
 @if($categoria->produtos->count() > 5)
 <li class="text-muted">
 <small>E mais {{ $categoria->produtos->count() - 5 }} produto(s)...</small>
 </li>
 @endif
 </ul>
 @else
 <p class="text-muted mb-0">
 <i class="fas fa-info-circle me-2"></i>
 Nenhum produto associado ainda.
 </p>
 @endif
 </div>
 </div>
 <div class="card shadow-sm">
 <div class="card-body">
 <h6 class="card-title mb-3">
 <i class="fas fa-clock me-2"></i>
 Informações
 </h6>
 <div class="mb-3">
 <small class="text-muted d-block">Criado em:</small>
 <strong>{{ $categoria->created_at->format('d/m/Y H:i') }}</strong>
 </div>
 <div>
 <small class="text-muted d-block">Última atualização:</small>
 <strong>{{ $categoria->updated_at->format('d/m/Y H:i') }}</strong>
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
 function confirmarExclusao() {
 const produtosCount = {{ $categoria->produtos->count() }};
 let mensagem = 'Tem certeza que deseja excluir esta categoria?';
 if (produtosCount > 0) {
 mensagem = `Esta categoria possui ${produtosCount} produto(s) associado(s).\n\n` +
 'Ao excluir a categoria, os produtos ficarão sem categoria.\n\n' +
 'Tem certeza que deseja continuar?';
 }
 if (confirm(mensagem)) {
 document.getElementById('deleteForm').submit();
 }
 }
</script>
@endpush
@endsection