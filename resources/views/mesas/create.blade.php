@extends('layouts.app')
@section('title', 'Nova Mesa')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-plus-circle me-2"></i>
 Nova Mesa
 </h1>
 <p class="page-subtitle">Cadastre uma nova mesa para o seu restaurante</p>
 </div>
 <a href="{{ route('mesas.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
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
 <div class="card">
 <div class="card-header">
 <h3 class="card-title mb-0">
 <i class="fas fa-chair me-2"></i>
 Informações da Mesa
 </h3>
 </div>
 <div class="card-body">
 <form method="POST" action="{{ route('mesas.store') }}" id="mesaForm">
 @csrf
 <div class="mb-3">
 <label for="identificador" class="form-label">
 <i class="fas fa-tag me-1"></i>
 Identificador da Mesa
 </label>
 <input type="text" 
 class="form-control @error('identificador') is-invalid @enderror" 
 id="identificador" 
 name="identificador" 
 value="{{ old('identificador') }}" 
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
 value="{{ old('lugares', 4) }}" 
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
 <div class="d-grid gap-2 d-md-flex justify-content-md-end">
 <a href="{{ route('mesas.index') }}" class="btn btn-secondary me-md-2">
 <i class="fas fa-times me-2"></i>
 Cancelar
 </a>
 <button type="submit" class="btn btn-primary">
 <i class="fas fa-save me-2"></i>
 Criar Mesa
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 <!-- Preview e Dicas -->
 <div class="col-lg-4">
 <!-- Preview -->
 <div class="card mb-4">
 <div class="card-header">
 <h3 class="card-title mb-0">
 <i class="fas fa-eye me-2"></i>
 Preview
 </h3>
 </div>
 <div class="card-body text-center">
 <div class="mb-3">
 <i class="fas fa-chair display-1 text-primary"></i>
 </div>
 <h4 id="previewIdentificador">Nova Mesa</h4>
 <p id="previewLugares" class="text-muted">4 lugares</p>
 <small class="text-muted">Preview da mesa que será criada</small>
 </div>
 </div>
 <!-- Dicas -->
 <div class="card">
 <div class="card-header">
 <h3 class="card-title mb-0">
 <i class="fas fa-lightbulb me-2 text-warning"></i>
 Dicas
 </h3>
 </div>
 <div class="card-body">
 <ul class="list-unstyled">
 <li class="mb-2">
 <i class="fas fa-check text-success me-2"></i>
 Use identificadores únicos e fáceis de lembrar
 </li>
 <li class="mb-2">
 <i class="fas fa-check text-success me-2"></i>
 Considere a localização (Ex: "Varanda 1", "Salão A2")
 </li>
 <li class="mb-2">
 <i class="fas fa-check text-success me-2"></i>
 Defina a capacidade baseada no conforto dos clientes
 </li>
 <li class="mb-2">
 <i class="fas fa-check text-success me-2"></i>
 Você pode editar essas informações depois
 </li>
 </ul>
 </div>
 </div>
 </div>
 </div>
</div>
@endsection
@push('scripts')
<script>
 document.getElementById('identificador').addEventListener('input', function() {
 const valor = this.value || 'Nova Mesa';
 document.getElementById('previewIdentificador').textContent = valor;
 });
 document.getElementById('lugares').addEventListener('input', function() {
 const valor = this.value || 4;
 document.getElementById('previewLugares').textContent = 
 valor + (valor == 1 ? ' lugar' : ' lugares');
 });
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
</script>
@endpush