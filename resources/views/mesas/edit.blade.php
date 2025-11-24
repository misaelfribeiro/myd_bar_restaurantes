@extends('layouts.app')
@section('title', 'Editar Mesa {{ $mesa->identificador }}')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-edit me-2"></i>
 Editar Mesa {{ $mesa->identificador }}
 </h1>
 <p class="page-subtitle">Atualize as informações da mesa</p>
 </div>
 <div>
 <a href="{{ route('mesas.show', $mesa->id) }}" class="btn btn-info me-2">
 <i class="fas fa-eye me-2"></i>
 Visualizar
 </a>
 <a href="{{ route('mesas.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 </div>
 <!-- Alerts -->
 @if($errors->any())
 <div class="alert alert-danger alert-dismissible fade show">
 <i class="fas fa-exclamation-triangle me-2"></i>
 <strong>Ops! Há alguns problemas:</strong>
 <ul class="mt-2 mb-0">
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 <div class="row">
 <!-- Formulário -->
 <div class="col-lg-8">
 <!-- Informações Atuais -->
 <div class="card mb-4">
 <div class="card-header">
 <h3 class="card-title mb-0">
 <i class="fas fa-info-circle me-2"></i>
 Informações Atuais
 </h3>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-6">
 <strong>Identificador:</strong> {{ $mesa->identificador }}
 </div>
 <div class="col-md-6">
 <strong>Lugares:</strong> {{ $mesa->lugares }}
 </div>
 </div>
 <div class="row mt-2">
 <div class="col-md-6">
 <strong>Criado em:</strong> {{ $mesa->created_at->format('d/m/Y H:i') }}
 </div>
 <div class="col-md-6">
 <strong>Status:</strong> 
 @if($mesa->pedidos->count() > 0)
 <span class="badge bg-warning">Ocupada</span>
 @else
 <span class="badge bg-success">Livre</span>
 @endif
 </div>
 </div>
 </div>
 </div>
 <div class="card">
 <div class="card-header">
 <h3 class="card-title mb-0">
 <i class="fas fa-chair me-2"></i>
 Atualizar Informações
 </h3>
 </div>
 <div class="card-body">
 <form method="POST" action="{{ route('mesas.update', $mesa->id) }}" id="mesaForm">
 @csrf
 @method('PUT')
 <div class="mb-3">
 <label for="identificador" class="form-label">
 <i class="fas fa-tag me-1"></i>
 Identificador da Mesa
 </label>
 <input type="text" 
 class="form-control @error('identificador') is-invalid @enderror" 
 id="identificador" 
 name="identificador" 
 value="{{ old('identificador', $mesa->identificador) }}" 
 placeholder="Ex: A1, Mesa 01, Varanda 3..."
 required
 autocomplete="off">
 <div class="form-text">
 Digite um identificador único para a mesa (letras, números ou texto)
 </div>
 @error('identificador')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label for="lugares" class="form-label">
 <i class="fas fa-users me-1"></i>
 Número de Lugares
 </label>
 <input type="number" 
 class="form-control @error('lugares') is-invalid @enderror" 
 id="lugares" 
 name="lugares" 
 value="{{ old('lugares', $mesa->lugares) }}" 
 min="1" 
 max="20"
 required>
 <div class="form-text">
 Quantidade de pessoas que a mesa comporta (1 a 20 lugares)
 </div>
 @error('lugares')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 @if($mesa->pedidos->count() > 0)
 <div class="alert alert-warning">
 <i class="fas fa-exclamation-triangle me-2"></i>
 <strong>Atenção:</strong> Esta mesa possui {{ $mesa->pedidos->count() }} pedido(s) ativo(s). 
 As alterações não afetarão os pedidos existentes.
 </div>
 @endif
 <div class="d-grid gap-2 d-md-flex justify-content-md-end">
 <a href="{{ route('mesas.show', $mesa->id) }}" class="btn btn-secondary me-md-2">
 <i class="fas fa-times me-2"></i>
 Cancelar
 </a>
 <button type="submit" class="btn btn-warning">
 <i class="fas fa-save me-2"></i>
 Atualizar Mesa
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 <!-- Painel Lateral -->
 <div class="col-lg-4">
 <!-- Histórico -->
 <div class="card mb-4">
 <div class="card-header">
 <h3 class="card-title mb-0">
 <i class="fas fa-history me-2"></i>
 Histórico
 </h3>
 </div>
 <div class="card-body">
 <div class="timeline">
 <div class="timeline-item">
 <div class="timeline-marker bg-success"></div>
 <div class="timeline-content">
 <div class="d-flex justify-content-between align-items-start">
 <span><i class="fas fa-plus text-success me-2"></i>Criada</span>
 <span class="text-muted">{{ $mesa->created_at->format('d/m/Y H:i') }}</span>
 </div>
 </div>
 </div>
 @if($mesa->updated_at != $mesa->created_at)
 <div class="timeline-item">
 <div class="timeline-marker bg-warning"></div>
 <div class="timeline-content">
 <div class="d-flex justify-content-between align-items-start">
 <span><i class="fas fa-edit text-warning me-2"></i>Última Edição</span>
 <span class="text-muted">{{ $mesa->updated_at->format('d/m/Y H:i') }}</span>
 </div>
 </div>
 </div>
 @endif
 @if($mesa->pedidos->count() > 0)
 <div class="timeline-item">
 <div class="timeline-marker bg-info"></div>
 <div class="timeline-content">
 <div class="d-flex justify-content-between align-items-start">
 <span><i class="fas fa-clipboard text-info me-2"></i>Pedidos Ativos</span>
 <span class="text-muted">{{ $mesa->pedidos->count() }}</span>
 </div>
 </div>
 </div>
 @endif
 </div>
 </div>
 </div>
 <!-- Ações Rápidas -->
 <div class="card">
 <div class="card-header">
 <h3 class="card-title mb-0">
 <i class="fas fa-cogs me-2"></i>
 Ações Rápidas
 </h3>
 </div>
 <div class="card-body">
 <div class="d-grid gap-2">
 <a href="{{ route('mesas.show', $mesa->id) }}" class="btn btn-info">
 <i class="fas fa-eye me-2"></i>
 Visualizar Mesa
 </a>
 @if($mesa->pedidos->count() == 0)
 <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluir">
 <i class="fas fa-trash me-2"></i>
 Excluir Mesa
 </button>
 @else
 <button type="button" class="btn btn-danger" disabled title="Não é possível excluir mesa com pedidos ativos">
 <i class="fas fa-trash me-2"></i>
 Excluir Mesa
 </button>
 @endif
 <a href="{{ route('mesas.index') }}" class="btn btn-secondary">
 <i class="fas fa-list me-2"></i>
 Todas as Mesas
 </a>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
 <div class="modal-dialog">
 <div class="modal-content">
 <div class="modal-header bg-danger text-white">
 <h5 class="modal-title">
 <i class="fas fa-exclamation-triangle me-2"></i>
 Confirmar Exclusão
 </h5>
 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
 </div>
 <div class="modal-body text-center">
 <i class="fas fa-trash-alt text-danger display-1 mb-3"></i>
 <h4>Excluir Mesa {{ $mesa->identificador }}?</h4>
 <p class="text-muted">Esta ação não pode ser desfeita.</p>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
 <i class="fas fa-times me-2"></i>
 Cancelar
 </button>
 <form id="formExcluir" method="POST" action="{{ route('mesas.destroy', $mesa->id) }}" style="display: inline;">
 @csrf
 @method('DELETE')
 <button type="submit" class="btn btn-danger">
 <i class="fas fa-trash me-2"></i>
 Confirmar Exclusão
 </button>
 </form>
 </div>
 </div>
 </div>
</div>
@endsection
@push('scripts')
<script>
 document.getElementById('mesaForm').addEventListener('submit', function(e) {
 const identificador = document.getElementById('identificador').value.trim();
 const lugares = document.getElementById('lugares').value;
 if (!identificador) {
 e.preventDefault();
 alert('O identificador da mesa é obrigatório!');
 return false;
 }
 if (lugares < 1 || lugares > 20) {
 e.preventDefault();
 alert('O número de lugares deve estar entre 1 e 20!');
 return false;
 }
 });
 setTimeout(function() {
 const alert = document.querySelector('.alert');
 if (alert) {
 alert.style.transition = 'opacity 0.5s';
 alert.style.opacity = '0';
 setTimeout(() => alert.remove(), 500);
 }
 }, 5000);
 function confirmarExclusao() {
 return confirm('Tem certeza que deseja excluir esta mesa? Esta ação não pode ser desfeita.');
 }
</script>
@endpush