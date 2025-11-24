@extends('layouts.app')
@section('content')
<div class="container-fluid">
 <div class="row mb-4">
 <div class="col-md-12">
 <h2><i class="fas fa-percent"></i> Editar Comissão</h2>
 </div>
 </div>
 <form action="{{ route('admin.comissoes.update', $comissao->id) }}" method="POST">
 @csrf
 @method('PUT')
 <div class="row">
 <div class="col-md-8">
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0">Dados da Comissão</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-12 mb-3">
 <label class="form-label">Funcionário *</label>
 <select name="funcionario_id" class="form-select @error('funcionario_id') is-invalid @enderror" required>
 <option value="">Selecione...</option>
 @foreach($funcionarios as $funcionario)
 <option value="{{ $funcionario->id }}" 
 {{ old('funcionario_id', $comissao->funcionario_id) == $funcionario->id ? 'selected' : '' }}>
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
 <div class="col-md-12 mb-3">
 <label class="form-label">Tipo de Comissão *</label>
 <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
 <option value="">Selecione o tipo...</option>
 <option value="venda" {{ old('tipo', $comissao->tipo) == 'venda' ? 'selected' : '' }}>Venda</option>
 <option value="meta" {{ old('tipo', $comissao->tipo) == 'meta' ? 'selected' : '' }}>Meta</option>
 <option value="projeto" {{ old('tipo', $comissao->tipo) == 'projeto' ? 'selected' : '' }}>Projeto</option>
 <option value="extra" {{ old('tipo', $comissao->tipo) == 'extra' ? 'selected' : '' }}>Extra</option>
 </select>
 @error('tipo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-12 mb-3">
 <label class="form-label">Descrição *</label>
 <input type="text" name="descricao" 
 class="form-control @error('descricao') is-invalid @enderror" 
 value="{{ old('descricao', $comissao->descricao) }}" 
 placeholder="Ex: Comissão sobre vendas de novembro" 
 required>
 @error('descricao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-6 mb-3">
 <label class="form-label">Data de Referência *</label>
 <input type="date" name="data_referencia" 
 class="form-control @error('data_referencia') is-invalid @enderror" 
 value="{{ old('data_referencia', $comissao->data_referencia->format('Y-m-d')) }}" required>
 @error('data_referencia')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6 mb-3">
 <label class="form-label">Valor Base *</label>
 <input type="number" name="valor_base" 
 class="form-control @error('valor_base') is-invalid @enderror" 
 value="{{ old('valor_base', $comissao->valor_base) }}" step="0.01" min="0" required>
 @error('valor_base')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-12 mb-3">
 <label class="form-label">Percentual de Comissão (%) *</label>
 <input type="number" name="percentual" 
 class="form-control @error('percentual') is-invalid @enderror" 
 value="{{ old('percentual', $comissao->percentual) }}" step="0.01" min="0" max="100" required>
 @error('percentual')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-12 mb-3">
 <label class="form-label">Observações</label>
 <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" 
 rows="3">{{ old('observacoes', $comissao->observacoes) }}</textarea>
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
 <option value="pendente" {{ old('status', $comissao->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
 <option value="pago" {{ old('status', $comissao->status) == 'pago' ? 'selected' : '' }}>Pago</option>
 <option value="cancelado" {{ old('status', $comissao->status) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
 </select>
 @error('status')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label class="form-label">Data de Pagamento</label>
 <input type="date" name="data_pagamento" 
 class="form-control @error('data_pagamento') is-invalid @enderror" 
 value="{{ old('data_pagamento', $comissao->data_pagamento?->format('Y-m-d')) }}">
 @error('data_pagamento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <div class="card">
 <div class="card-body">
 <button type="submit" class="btn btn-success w-100 mb-2">
 <i class="fas fa-save"></i> Atualizar Comissão
 </button>
 <a href="{{ route('admin.comissoes.index') }}" class="btn btn-secondary w-100">
 <i class="fas fa-times"></i> Cancelar
 </a>
 </div>
 </div>
 </div>
 </div>
 </form>
</div>
@endsection