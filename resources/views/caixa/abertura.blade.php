@extends('layouts.app')
@section('title', 'Abertura de Caixa')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-unlock me-2"></i>
 Abertura de Caixa
 </h1>
 <p class="page-subtitle">Registre a abertura do caixa para iniciar as operações</p>
 </div>
 <a href="{{ route('caixa.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 <div class="row justify-content-center">
 <div class="col-lg-6">
 <div class="card shadow-sm">
 <div class="card-body">
 <form action="{{ route('caixa.abrir') }}" method="POST">
 @csrf
 <div class="text-center mb-4">
 <div class="stat-icon-large bg-success mx-auto mb-3">
 <i class="fas fa-cash-register"></i>
 </div>
 <h4>Abrir Caixa</h4>
 <p class="text-muted">Informe o valor inicial para troco</p>
 </div>
 <div class="mb-4">
 <label for="saldo_inicial" class="form-label fw-bold">
 <i class="fas fa-dollar-sign me-1"></i>
 Valor Inicial (Troco)
 </label>
 <div class="input-group input-group-lg">
 <span class="input-group-text">R$</span>
 <input type="number" 
 class="form-control" 
 id="saldo_inicial" 
 name="saldo_inicial" 
 step="0.01" 
 min="0"
 value="0.00"
 required>
 </div>
 <small class="text-muted">Valor disponível em dinheiro para troco</small>
 </div>
 <div class="mb-4">
 <label for="observacoes" class="form-label fw-bold">
 <i class="fas fa-comment me-1"></i>
 Observações (Opcional)
 </label>
 <textarea class="form-control" 
 id="observacoes" 
 name="observacoes" 
 rows="3"
 placeholder="Anotações sobre a abertura do caixa..."></textarea>
 </div>
 <div class="d-grid gap-2">
 <button type="submit" class="btn btn-success btn-lg">
 <i class="fas fa-unlock me-2"></i>
 Abrir Caixa
 </button>
 <a href="{{ route('caixa.index') }}" class="btn btn-outline-secondary">
 Cancelar
 </a>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
</div>
@push('styles')
<style>
 .stat-icon-large {
 width: 80px;
 height: 80px;
 border-radius: 50%;
 display: flex;
 align-items: center;
 justify-content: center;
 font-size: 2.5rem;
 color: white;
 }
</style>
@endpush
@endsection