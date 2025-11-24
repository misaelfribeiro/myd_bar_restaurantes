@extends('layouts.app')
@section('title', isset($cargo) ? 'Editar Cargo' : 'Novo Cargo')
@section('content')
<div class="container-fluid px-4">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">
 <i class="fas fa-user-tie me-2"></i>
 {{ isset($cargo) ? 'Editar Cargo' : 'Novo Cargo' }}
 </h1>
 </div>
 <a href="{{ route('admin.cargos.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-1"></i> Voltar
 </a>
 </div>
 <div class="row">
 <div class="col-lg-8">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 @if ($errors->any())
 <div class="alert alert-danger">
 <h6 class="alert-heading">
 <i class="fas fa-exclamation-triangle me-1"></i>
 Erro ao salvar cargo
 </h6>
 <ul class="mb-0">
 @foreach ($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif
 <form action="{{ isset($cargo) ? route('admin.cargos.update', $cargo->id) : route('admin.cargos.store') }}" method="POST">
 @csrf
 @if(isset($cargo))
 @method('PUT')
 @endif
 <div class="row mb-3">
 <div class="col-md-8">
 <label for="nome" class="form-label">Nome do Cargo *</label>
 <input type="text" class="form-control @error('nome') is-invalid @enderror" 
 id="nome" name="nome" 
 value="{{ old('nome', $cargo->nome ?? '') }}" required>
 @error('nome')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label for="nivel_hierarquico" class="form-label">Nível Hierárquico *</label>
 <select class="form-select @error('nivel_hierarquico') is-invalid @enderror" 
 id="nivel_hierarquico" name="nivel_hierarquico" required>
 <option value="">Selecione...</option>
 <option value="1" {{ old('nivel_hierarquico', $cargo->nivel_hierarquico ?? '') == 1 ? 'selected' : '' }}>
 Operacional
 </option>
 <option value="2" {{ old('nivel_hierarquico', $cargo->nivel_hierarquico ?? '') == 2 ? 'selected' : '' }}>
 Supervisor
 </option>
 <option value="3" {{ old('nivel_hierarquico', $cargo->nivel_hierarquico ?? '') == 3 ? 'selected' : '' }}>
 Gerente
 </option>
 <option value="4" {{ old('nivel_hierarquico', $cargo->nivel_hierarquico ?? '') == 4 ? 'selected' : '' }}>
 Diretor
 </option>
 <option value="5" {{ old('nivel_hierarquico', $cargo->nivel_hierarquico ?? '') == 5 ? 'selected' : '' }}>
 Master
 </option>
 </select>
 @error('nivel_hierarquico')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="mb-3">
 <label for="descricao" class="form-label">Descrição</label>
 <textarea class="form-control @error('descricao') is-invalid @enderror" 
 id="descricao" name="descricao" rows="3"
 placeholder="Descrição das responsabilidades e atribuições do cargo">{{ old('descricao', $cargo->descricao ?? '') }}</textarea>
 @error('descricao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <label for="salario_base" class="form-label">Salário Base</label>
 <div class="input-group">
 <span class="input-group-text">R$</span>
 <input type="number" class="form-control @error('salario_base') is-invalid @enderror" 
 id="salario_base" name="salario_base" step="0.01" min="0"
 value="{{ old('salario_base', $cargo->salario_base ?? '') }}">
 @error('salario_base')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <small class="text-muted">Salário base de referência para este cargo</small>
 </div>
 <div class="col-md-6">
 <label for="percentual_comissao" class="form-label">Percentual de Comissão</label>
 <div class="input-group">
 <input type="number" class="form-control @error('percentual_comissao') is-invalid @enderror" 
 id="percentual_comissao" name="percentual_comissao" step="0.01" min="0" max="100"
 value="{{ old('percentual_comissao', $cargo->percentual_comissao ?? '') }}">
 <span class="input-group-text">%</span>
 @error('percentual_comissao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <small class="text-muted">Percentual padrão de comissão sobre vendas</small>
 </div>
 </div>
 <div class="mb-3">
 <div class="form-check form-switch">
 <input class="form-check-input" type="checkbox" id="tem_comissao" name="tem_comissao" 
 value="1" {{ old('tem_comissao', $cargo->tem_comissao ?? false) ? 'checked' : '' }}>
 <label class="form-check-label" for="tem_comissao">
 Este cargo recebe comissão sobre vendas
 </label>
 </div>
 </div>
 <div class="mb-3">
 <div class="form-check form-switch">
 <input class="form-check-input" type="checkbox" id="ativo" name="ativo" 
 value="1" {{ old('ativo', $cargo->ativo ?? true) ? 'checked' : '' }}>
 <label class="form-check-label" for="ativo">
 Cargo ativo
 </label>
 </div>
 </div>
 <hr class="my-4">
 <div class="d-flex justify-content-end gap-2">
 <a href="{{ route('admin.cargos.index') }}" class="btn btn-secondary">
 <i class="fas fa-times me-1"></i> Cancelar
 </a>
 <button type="submit" class="btn btn-primary">
 <i class="fas fa-save me-1"></i>
 {{ isset($cargo) ? 'Atualizar' : 'Cadastrar' }} Cargo
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 <div class="col-lg-4">
 <div class="card border-0 shadow-sm">
 <div class="card-header bg-info text-white">
 <h5 class="mb-0">
 <i class="fas fa-info-circle me-2"></i>
 Informações
 </h5>
 </div>
 <div class="card-body">
 <h6 class="text-muted mb-2">Níveis Hierárquicos</h6>
 <ul class="small mb-3">
 <li><strong>Operacional (1):</strong> Funções operacionais básicas</li>
 <li><strong>Supervisor (2):</strong> Supervisiona equipes operacionais</li>
 <li><strong>Gerente (3):</strong> Gerencia departamentos</li>
 <li><strong>Diretor (4):</strong> Direção executiva</li>
 <li><strong>Master (5):</strong> Acesso total ao sistema</li>
 </ul>
 <h6 class="text-muted mb-2">Comissões</h6>
 <p class="small mb-0">
 Ative a opção "Recebe comissão" para cargos que devem receber comissão sobre vendas. 
 O percentual definido será o padrão para funcionários deste cargo.
 </p>
 </div>
 </div>
 </div>
 </div>
</div>
@endsection