@extends('layouts.app')
@section('content')
<div class="container-fluid">
 <div class="row mb-4">
 <div class="col-md-12">
 <h2><i class="fas fa-user-plus"></i> Novo Funcionário</h2>
 </div>
 </div>
 @if(session('error'))
 <div class="alert alert-danger alert-dismissible fade show" role="alert">
 <strong>Erro:</strong> {{ session('error') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 @if($errors->any())
 <div class="alert alert-danger alert-dismissible fade show" role="alert">
 <strong>Erros de validação:</strong>
 <ul class="mb-0">
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 <form action="{{ route('admin.funcionarios.store') }}" method="POST">
 @csrf
 <div class="row">
 <div class="col-md-8">
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0">Informações Pessoais</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-8 mb-3">
 <label class="form-label">Nome Completo *</label>
 <input type="text" name="nome_completo" class="form-control @error('nome_completo') is-invalid @enderror" 
 value="{{ old('nome_completo') }}" required>
 @error('nome_completo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4 mb-3">
 <label class="form-label">Data de Nascimento *</label>
 <input type="date" name="data_nascimento" 
 class="form-control @error('data_nascimento') is-invalid @enderror" 
 value="{{ old('data_nascimento') }}" required>
 @error('data_nascimento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-6 mb-3">
 <label class="form-label">CPF *</label>
 <input type="text" name="cpf" class="form-control @error('cpf') is-invalid @enderror" 
 value="{{ old('cpf') }}" maxlength="14" placeholder="000.000.000-00" required>
 @error('cpf')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6 mb-3">
 <label class="form-label">RG</label>
 <input type="text" name="rg" class="form-control @error('rg') is-invalid @enderror" 
 value="{{ old('rg') }}">
 @error('rg')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-6 mb-3">
 <label class="form-label">Email *</label>
 <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
 value="{{ old('email') }}" required>
 @error('email')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-3 mb-3">
 <label class="form-label">Telefone</label>
 <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror" 
 value="{{ old('telefone') }}" placeholder="(00) 0000-0000">
 @error('telefone')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-3 mb-3">
 <label class="form-label">Celular</label>
 <input type="text" name="celular" class="form-control @error('celular') is-invalid @enderror" 
 value="{{ old('celular') }}" placeholder="(00) 00000-0000">
 @error('celular')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 </div>
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0">Endereço</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-3 mb-3">
 <label class="form-label">CEP</label>
 <input type="text" name="cep" class="form-control @error('cep') is-invalid @enderror" 
 value="{{ old('cep') }}" maxlength="10" placeholder="00000-000">
 @error('cep')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6 mb-3">
 <label class="form-label">Endereço</label>
 <input type="text" name="endereco" class="form-control @error('endereco') is-invalid @enderror" 
 value="{{ old('endereco') }}">
 @error('endereco')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-3 mb-3">
 <label class="form-label">Número</label>
 <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror" 
 value="{{ old('numero') }}" maxlength="10">
 @error('numero')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-4 mb-3">
 <label class="form-label">Complemento</label>
 <input type="text" name="complemento" class="form-control @error('complemento') is-invalid @enderror" 
 value="{{ old('complemento') }}">
 @error('complemento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4 mb-3">
 <label class="form-label">Bairro</label>
 <input type="text" name="bairro" class="form-control @error('bairro') is-invalid @enderror" 
 value="{{ old('bairro') }}">
 @error('bairro')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4 mb-3">
 <label class="form-label">Cidade</label>
 <input type="text" name="cidade" class="form-control @error('cidade') is-invalid @enderror" 
 value="{{ old('cidade') }}">
 @error('cidade')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-12 mb-3">
 <label class="form-label">Estado</label>
 <input type="text" name="estado" class="form-control @error('estado') is-invalid @enderror" 
 value="{{ old('estado') }}" maxlength="2" placeholder="SP">
 @error('estado')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 </div>
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0">Documentos</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-4 mb-3">
 <label class="form-label">PIS/PASEP</label>
 <input type="text" name="pis_pasep" class="form-control @error('pis_pasep') is-invalid @enderror" 
 value="{{ old('pis_pasep') }}" maxlength="20">
 @error('pis_pasep')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4 mb-3">
 <label class="form-label">Título de Eleitor</label>
 <input type="text" name="titulo_eleitor" class="form-control @error('titulo_eleitor') is-invalid @enderror" 
 value="{{ old('titulo_eleitor') }}" maxlength="20">
 @error('titulo_eleitor')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4 mb-3">
 <label class="form-label">Carteira de Trabalho</label>
 <input type="text" name="carteira_trabalho" class="form-control @error('carteira_trabalho') is-invalid @enderror" 
 value="{{ old('carteira_trabalho') }}" maxlength="20">
 @error('carteira_trabalho')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 </div>
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0">Dados Bancários</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-6 mb-3">
 <label class="form-label">Banco</label>
 <input type="text" name="banco" class="form-control @error('banco') is-invalid @enderror" 
 value="{{ old('banco') }}">
 @error('banco')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6 mb-3">
 <label class="form-label">Tipo de Conta</label>
 <select name="tipo_conta" class="form-select @error('tipo_conta') is-invalid @enderror">
 <option value="">Selecione...</option>
 <option value="corrente" {{ old('tipo_conta') == 'corrente' ? 'selected' : '' }}>Corrente</option>
 <option value="poupanca" {{ old('tipo_conta') == 'poupanca' ? 'selected' : '' }}>Poupança</option>
 </select>
 @error('tipo_conta')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-md-6 mb-3">
 <label class="form-label">Agência</label>
 <input type="text" name="agencia" class="form-control @error('agencia') is-invalid @enderror" 
 value="{{ old('agencia') }}">
 @error('agencia')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6 mb-3">
 <label class="form-label">Conta Bancária</label>
 <input type="text" name="conta_bancaria" class="form-control @error('conta_bancaria') is-invalid @enderror" 
 value="{{ old('conta_bancaria') }}">
 @error('conta_bancaria')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 </div>
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0">Observações</h5>
 </div>
 <div class="card-body">
 <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" 
 rows="4">{{ old('observacoes') }}</textarea>
 @error('observacoes')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <div class="col-md-4">
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0">Informações Profissionais</h5>
 </div>
 <div class="card-body">
 <div class="mb-3">
 <label class="form-label">Cargo *</label>
 <select name="cargo_id" class="form-select @error('cargo_id') is-invalid @enderror" required>
 <option value="">Selecione...</option>
 @foreach($cargos as $cargo)
 <option value="{{ $cargo->id }}" {{ old('cargo_id') == $cargo->id ? 'selected' : '' }}>
 {{ $cargo->nome }}
 </option>
 @endforeach
 </select>
 @error('cargo_id')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label class="form-label">Data de Admissão *</label>
 <input type="date" name="data_admissao" 
 class="form-control @error('data_admissao') is-invalid @enderror" 
 value="{{ old('data_admissao') }}" required>
 @error('data_admissao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label class="form-label">Data de Demissão</label>
 <input type="date" name="data_demissao" 
 class="form-control @error('data_demissao') is-invalid @enderror" 
 value="{{ old('data_demissao') }}">
 @error('data_demissao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label class="form-label">Tipo de Contrato *</label>
 <select name="tipo_contrato" class="form-select @error('tipo_contrato') is-invalid @enderror" required>
 <option value="clt" {{ old('tipo_contrato', 'clt') == 'clt' ? 'selected' : '' }}>CLT</option>
 <option value="pj" {{ old('tipo_contrato') == 'pj' ? 'selected' : '' }}>PJ</option>
 <option value="estagio" {{ old('tipo_contrato') == 'estagio' ? 'selected' : '' }}>Estágio</option>
 <option value="temporario" {{ old('tipo_contrato') == 'temporario' ? 'selected' : '' }}>Temporário</option>
 </select>
 @error('tipo_contrato')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label class="form-label">Salário</label>
 <input type="number" name="salario" 
 class="form-control @error('salario') is-invalid @enderror" 
 value="{{ old('salario') }}" step="0.01" min="0" placeholder="0.00">
 @error('salario')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label class="form-label">Tipo de Comissão</label>
 <select name="tipo_comissao" class="form-select @error('tipo_comissao') is-invalid @enderror">
 <option value="">Nenhuma</option>
 <option value="percentual" {{ old('tipo_comissao') == 'percentual' ? 'selected' : '' }}>Percentual</option>
 <option value="fixa" {{ old('tipo_comissao') == 'fixa' ? 'selected' : '' }}>Fixa</option>
 </select>
 @error('tipo_comissao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label class="form-label">Percentual de Comissão (%)</label>
 <input type="number" name="percentual_comissao" 
 class="form-control @error('percentual_comissao') is-invalid @enderror" 
 value="{{ old('percentual_comissao') }}" step="0.01" min="0" max="100" placeholder="0.00">
 @error('percentual_comissao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="form-check">
 <input type="checkbox" name="ativo" class="form-check-input" id="ativo" 
 {{ old('ativo', true) ? 'checked' : '' }}>
 <label class="form-check-label" for="ativo">
 Funcionário Ativo
 </label>
 </div>
 </div>
 </div>
 <div class="card">
 <div class="card-body">
 <button type="submit" class="btn btn-success w-100 mb-2">
 <i class="fas fa-save"></i> Salvar Funcionário
 </button>
 <a href="{{ route('admin.funcionarios.index') }}" class="btn btn-secondary w-100">
 <i class="fas fa-times"></i> Cancelar
 </a>
 </div>
 </div>
 </div>
 </div>
 </form>
</div>
@endsection