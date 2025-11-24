@extends('layouts.app')
@section('title', 'Editar Administrador')
@section('content')
<div class="container-fluid px-4">
 <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">✏️ Editar Administrador</h1>
 <p class="text-muted mb-0">{{ $user->name }} ({{ $user->email }})</p>
 </div>
 <div>
 <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-2"></i>Voltar
 </a>
 </div>
 </div>
 <div class="row">
 <div class="col-lg-8 mx-auto">
 <div class="card border-0 shadow-sm">
 <div class="card-header bg-info text-white py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-user-edit me-2"></i>Dados do Administrador
 </h5>
 </div>
 <div class="card-body p-4">
 <form action="{{ route('admin.users.update', $user) }}" method="POST">
 @csrf
 @method('PUT')
 <!-- Nome -->
 <div class="mb-3">
 <label for="name" class="form-label">
 <i class="fas fa-user me-1"></i>Nome Completo *
 </label>
 <input type="text" 
 class="form-control @error('name') is-invalid @enderror" 
 id="name" 
 name="name" 
 value="{{ old('name', $user->name) }}" 
 required>
 @error('name')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <!-- Email -->
 <div class="mb-3">
 <label for="email" class="form-label">
 <i class="fas fa-envelope me-1"></i>Email *
 </label>
 <input type="email" 
 class="form-control @error('email') is-invalid @enderror" 
 id="email" 
 name="email" 
 value="{{ old('email', $user->email) }}" 
 required>
 @error('email')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <!-- Empresa -->
 <div class="mb-3">
 <label for="tenant_code" class="form-label">
 <i class="fas fa-building me-1"></i>Empresa *
 </label>
 <select class="form-select @error('tenant_code') is-invalid @enderror" 
 id="tenant_code" 
 name="tenant_code" 
 required>
 @foreach($empresas as $empresa)
 <option value="{{ $empresa->tenant_code }}" 
 {{ old('tenant_code', $user->tenant_code) == $empresa->tenant_code ? 'selected' : '' }}>
 {{ $empresa->nome_fantasia }} ({{ $empresa->tenant_code }})
 </option>
 @endforeach
 </select>
 @error('tenant_code')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <hr class="my-4">
 <div class="alert alert-warning">
 <i class="fas fa-exclamation-triangle me-2"></i>
 <strong>Atenção:</strong> Deixe os campos de senha em branco se não quiser alterá-la.
 </div>
 <!-- Nova Senha -->
 <div class="mb-3">
 <label for="password" class="form-label">
 <i class="fas fa-lock me-1"></i>Nova Senha (opcional)
 </label>
 <input type="password" 
 class="form-control @error('password') is-invalid @enderror" 
 id="password" 
 name="password">
 @error('password')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 <small class="form-text text-muted">
 Mínimo de 8 caracteres
 </small>
 </div>
 <!-- Confirmar Nova Senha -->
 <div class="mb-4">
 <label for="password_confirmation" class="form-label">
 <i class="fas fa-lock me-1"></i>Confirmar Nova Senha
 </label>
 <input type="password" 
 class="form-control" 
 id="password_confirmation" 
 name="password_confirmation">
 </div>
 <!-- Botões -->
 <div class="d-flex justify-content-between">
 <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
 <i class="fas fa-times me-2"></i>Cancelar
 </a>
 <button type="submit" class="btn btn-primary">
 <i class="fas fa-save me-2"></i>Salvar Alterações
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
</div>
@endsection