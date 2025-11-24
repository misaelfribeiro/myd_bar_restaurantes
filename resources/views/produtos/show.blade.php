@extends('layouts.app')
@section('title', 'Detalhes do Produto')
@section('content')
<div class="container-fluid">
 <div class="page-header mb-4">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-box me-2"></i>
 Detalhes do Produto
 </h1>
 <p class="page-subtitle">Visualize as informações completas do produto</p>
 </div>
 <div class="btn-group">
 <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning">
 <i class="fas fa-edit me-2"></i>
 Editar
 </a>
 <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 </div>
 <div class="row">
 <!-- Informações Principais -->
 <div class="col-lg-8">
 <div class="card shadow-sm mb-4">
 <div class="card-header bg-primary text-white">
 <h5 class="mb-0">
 <i class="fas fa-info-circle me-2"></i>
 Informações do Produto
 </h5>
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-6">
 <label class="text-muted mb-1">
 <i class="fas fa-hashtag me-1"></i>
 ID
 </label>
 <p class="h5">#{{ $produto->id }}</p>
 </div>
 <div class="col-md-6">
 <label class="text-muted mb-1">
 <i class="fas fa-toggle-on me-1"></i>
 Status
 </label>
 <p>
 <span class="badge {{ $produto->ativo ? 'bg-success' : 'bg-secondary' }} fs-6 px-3 py-2">
 <i class="fas {{ $produto->ativo ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
 {{ $produto->status }}
 </span>
 </p>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-12">
 <label class="text-muted mb-1">
 <i class="fas fa-utensils me-1"></i>
 Nome do Produto
 </label>
 <p class="h4">{{ $produto->nome }}</p>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <label class="text-muted mb-1">
 <i class="fas fa-dollar-sign me-1"></i>
 Preço
 </label>
 <p class="h3 text-success">{{ $produto->preco_formatado }}</p>
 </div>
 <div class="col-md-6">
 <label class="text-muted mb-1">
 <i class="fas fa-tags me-1"></i>
 Categoria
 </label>
 <p class="h5">
 @if($produto->categoria)
 <span class="badge bg-info text-dark fs-6 px-3 py-2">
 {{ $produto->categoria->nome }}
 </span>
 @else
 <span class="text-muted">Sem categoria</span>
 @endif
 </p>
 </div>
 </div>
 @if($produto->descricao)
 <div class="row mb-3">
 <div class="col-md-12">
 <label class="text-muted mb-1">
 <i class="fas fa-align-left me-1"></i>
 Descrição
 </label>
 <div class="p-3 bg-light rounded">
 <p class="mb-0">{{ $produto->descricao }}</p>
 </div>
 </div>
 </div>
 @endif
 <div class="row">
 <div class="col-md-6">
 <label class="text-muted mb-1">
 <i class="fas fa-calendar-plus me-1"></i>
 Criado em
 </label>
 <p>{{ $produto->created_at->format('d/m/Y H:i:s') }}</p>
 </div>
 <div class="col-md-6">
 <label class="text-muted mb-1">
 <i class="fas fa-calendar-check me-1"></i>
 Última atualização
 </label>
 <p>{{ $produto->updated_at->format('d/m/Y H:i:s') }}</p>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Ações e Informações Adicionais -->
 <div class="col-lg-4">
 <!-- Card de Ações -->
 <div class="card shadow-sm mb-4">
 <div class="card-header bg-secondary text-white">
 <h5 class="mb-0">
 <i class="fas fa-cogs me-2"></i>
 Ações
 </h5>
 </div>
 <div class="card-body">
 <div class="d-grid gap-2">
 <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning">
 <i class="fas fa-edit me-2"></i>
 Editar Produto
 </a>
 <form action="{{ route('produtos.toggle-status', $produto->id) }}" method="POST">
 @csrf
 @method('PATCH')
 <button type="submit" class="btn btn-{{ $produto->ativo ? 'secondary' : 'success' }} w-100">
 <i class="fas fa-{{ $produto->ativo ? 'times' : 'check' }}-circle me-2"></i>
 {{ $produto->ativo ? 'Desativar' : 'Ativar' }} Produto
 </button>
 </form>
 <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
 <i class="fas fa-trash me-2"></i>
 Excluir Produto
 </button>
 <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-list me-2"></i>
 Ver Todos os Produtos
 </a>
 </div>
 </div>
 </div>
 <!-- Card de Informações Estatísticas (futuro) -->
 <div class="card shadow-sm">
 <div class="card-header bg-info text-white">
 <h5 class="mb-0">
 <i class="fas fa-chart-bar me-2"></i>
 Estatísticas
 </h5>
 </div>
 <div class="card-body">
 <div class="mb-3">
 <small class="text-muted d-block mb-1">
 <i class="fas fa-shopping-cart me-1"></i>
 Total de Vendas
 </small>
 <h4 class="mb-0">-</h4>
 <small class="text-muted">Em breve</small>
 </div>
 <div class="mb-3">
 <small class="text-muted d-block mb-1">
 <i class="fas fa-dollar-sign me-1"></i>
 Receita Total
 </small>
 <h4 class="mb-0">-</h4>
 <small class="text-muted">Em breve</small>
 </div>
 <div>
 <small class="text-muted d-block mb-1">
 <i class="fas fa-star me-1"></i>
 Produto Mais Vendido
 </small>
 <p class="mb-0">
 <span class="badge bg-secondary">Em breve</span>
 </p>
 </div>
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
@endsection