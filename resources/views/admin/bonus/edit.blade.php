@extends('layouts.app')
@section('content')
<div class="container-fluid">
 <div class="row mb-4">
 <div class="col-md-12">
 <h2><i class="fas fa-gift"></i> Editar Bônus</h2>
 </div>
 </div>
 <form action="{{ route('admin.bonus.update', $bonus->id) }}" method="POST">
 @csrf
 @method('PUT')
 <div class="row">
 <div class="col-md-8">
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0">Dados do Bônus</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-12 mb-3">
 <label class="form-label">Funcionário *</label>
 <select name="funcionario_id" class="form-select @error('funcionario_id') is-invalid @enderror" required>
 <option value="">Selecione...</option>
 @foreach($funcionarios as $funcionario)
 <option value="{{ $funcionario->id }}" 
 {{ old('funcionario_id', $bonus->funcionario_id) == $funcionario->id ? 'selected' : '' }}>
 {{ $funcionario->nome_completo }} - {{ $funcionario->cargo->nome }}
 </option>
 @endforeach
 </select>
 @error('funcionario_id')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-6 mb-3">
 <label class="form-label">Tipo de Bônus *</label>
 <input type="text" name="tipo" 
 class="form-control @error('tipo') is-invalid @enderror" 
 value="{{ old('tipo', $bonus->tipo) }}" required>
 @error('tipo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6 mb-3">
 <label class="form-label">Título *</label>
 <input type="text" name="titulo" 
 class="form-control @error('titulo') is-invalid @enderror" 
 value="{{ old('titulo', $bonus->titulo) }}" placeholder="Ex: Bônus de Vendas Novembro" required>
 @error('titulo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <label class="form-label">Valor *</label>
 <input type="number" name="valor" 
 class="form-control @error('valor') is-invalid @enderror" 
 value="{{ old('valor', $bonus->valor) }}" step="0.01" min="0" required>
 @error('valor')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-12 mb-3">
 <label class="form-label">Data de Referência *</label>
 <input type="date" name="data_referencia" 
 class="form-control @error('data_referencia') is-invalid @enderror" 
 value="{{ old('data_referencia', $bonus->data_referencia->format('Y-m-d')) }}" required>
 @error('data_referencia')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-12 mb-3">
 <label class="form-label">Descrição *</label>
 <textarea name="descricao" class="form-control @error('descricao') is-invalid @enderror" 
 rows="3" required>{{ old('descricao', $bonus->descricao) }}</textarea>
 @error('descricao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-12 mb-3">
 <label class="form-label">Observações</label>
 <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" 
 rows="2">{{ old('observacoes', $bonus->observacoes) }}</textarea>
 @error('observacoes')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-4">
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0">Status do Pagamento</h5>
 </div>
 <div class="card-body">
 <div class="mb-3">
 <label class="form-label">Status *</label>
 <select name="status" class="form-select @error('status') is-invalid @enderror" required>
 <option value="pendente" {{ old('status', $bonus->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
 <option value="pago" {{ old('status', $bonus->status) == 'pago' ? 'selected' : '' }}>Pago</option>
 <option value="cancelado" {{ old('status', $bonus->status) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
 </select>
 @error('status')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label class="form-label">Data de Pagamento</label>
 <input type="date" name="data_pagamento" 
 class="form-control @error('data_pagamento') is-invalid @enderror" 
 value="{{ old('data_pagamento', $bonus->data_pagamento?->format('Y-m-d')) }}">
 @error('data_pagamento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <div class="card">
 <div class="card-body">
 <button type="submit" class="btn btn-success w-100 mb-2">
 <i class="fas fa-save"></i> Atualizar Bônus
 </button>
 <a href="{{ route('admin.bonus.index') }}" class="btn btn-secondary w-100">
 <i class="fas fa-times"></i> Cancelar
 </a>
 </div>
 </div>
 </div>
 </div>
 </form>
</div>
@endsection