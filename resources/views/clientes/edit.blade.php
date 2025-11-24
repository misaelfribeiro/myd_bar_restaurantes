@extends('layouts.app')
@section('content')
<div class="container">
 <div class="row">
 <div class="col-md-8 mx-auto">
 <div class="card">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-user-edit"></i> Editar Cliente</h5>
 </div>
 <div class="card-body">
 <form method="POST" action="{{ route('clientes.update', $cliente) }}">
 @csrf
 @method('PUT')
 <!-- Status -->
 <div class="row">
 <div class="col-md-12">
 <div class="form-group">
 <div class="form-check">
 <input type="checkbox" name="ativo" id="ativo" class="form-check-input" 
 value="1" {{ old('ativo', $cliente->ativo) ? 'checked' : '' }}>
 <label class="form-check-label" for="ativo">
 Cliente Ativo
 </label>
 </div>
 </div>
 </div>
 </div>
 <hr>
 <!-- Dados Pessoais -->
 <div class="row">
 <div class="col-md-12">
 <h6 class="text-muted mb-3"><i class="fas fa-user"></i> Dados Pessoais</h6>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="nome">Nome *</label>
 <input type="text" name="nome" id="nome" 
 class="form-control @error('nome') is-invalid @enderror" 
 value="{{ old('nome', $cliente->nome) }}" required>
 @error('nome')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="telefone">Telefone *</label>
 <input type="text" name="telefone" id="telefone" 
 class="form-control @error('telefone') is-invalid @enderror" 
 value="{{ old('telefone', $cliente->telefone) }}" required>
 @error('telefone')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-12">
 <div class="form-group">
 <label for="email">Email</label>
 <input type="email" name="email" id="email" 
 class="form-control @error('email') is-invalid @enderror" 
 value="{{ old('email', $cliente->email) }}">
 @error('email')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <hr>
 <!-- Endereço para Entrega -->
 <div class="row">
 <div class="col-md-12">
 <h6 class="text-muted mb-3"><i class="fas fa-map-marker-alt"></i> Endereço para Entrega</h6>
 </div>
 <div class="col-md-8">
 <div class="form-group">
 <label for="endereco_rua">Rua</label>
 <input type="text" name="endereco_rua" id="endereco_rua" 
 class="form-control @error('endereco_rua') is-invalid @enderror" 
 value="{{ old('endereco_rua', $cliente->endereco_rua) }}">
 @error('endereco_rua')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-4">
 <div class="form-group">
 <label for="endereco_numero">Número</label>
 <input type="text" name="endereco_numero" id="endereco_numero" 
 class="form-control @error('endereco_numero') is-invalid @enderror" 
 value="{{ old('endereco_numero', $cliente->endereco_numero) }}">
 @error('endereco_numero')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="endereco_complemento">Complemento</label>
 <input type="text" name="endereco_complemento" id="endereco_complemento" 
 class="form-control @error('endereco_complemento') is-invalid @enderror" 
 value="{{ old('endereco_complemento', $cliente->endereco_complemento) }}">
 @error('endereco_complemento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="endereco_bairro">Bairro</label>
 <input type="text" name="endereco_bairro" id="endereco_bairro" 
 class="form-control @error('endereco_bairro') is-invalid @enderror" 
 value="{{ old('endereco_bairro', $cliente->endereco_bairro) }}">
 @error('endereco_bairro')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="endereco_cidade">Cidade</label>
 <input type="text" name="endereco_cidade" id="endereco_cidade" 
 class="form-control @error('endereco_cidade') is-invalid @enderror" 
 value="{{ old('endereco_cidade', $cliente->endereco_cidade) }}">
 @error('endereco_cidade')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="endereco_cep">CEP</label>
 <input type="text" name="endereco_cep" id="endereco_cep" 
 class="form-control @error('endereco_cep') is-invalid @enderror" 
 value="{{ old('endereco_cep', $cliente->endereco_cep) }}">
 @error('endereco_cep')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <hr>
 <!-- Dados Adicionais -->
 <div class="row">
 <div class="col-md-12">
 <h6 class="text-muted mb-3"><i class="fas fa-info-circle"></i> Informações Adicionais</h6>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="cpf">CPF</label>
 <input type="text" name="cpf" id="cpf" 
 class="form-control @error('cpf') is-invalid @enderror" 
 value="{{ old('cpf', $cliente->cpf) }}">
 @error('cpf')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="data_nascimento">Data de Nascimento</label>
 <input type="date" name="data_nascimento" id="data_nascimento" 
 class="form-control @error('data_nascimento') is-invalid @enderror" 
 value="{{ old('data_nascimento', $cliente->data_nascimento ? $cliente->data_nascimento->format('Y-m-d') : '') }}">
 @error('data_nascimento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-12">
 <div class="form-group">
 <label for="observacoes">Observações</label>
 <textarea name="observacoes" id="observacoes" rows="3"
 class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes', $cliente->observacoes) }}</textarea>
 @error('observacoes')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <div class="form-group text-right">
 <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
 <i class="fas fa-times"></i> Cancelar
 </a>
 <button type="submit" class="btn btn-primary">
 <i class="fas fa-save"></i> Atualizar
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
</div>
@endsection
@section('scripts')
<script>
 document.getElementById('telefone').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 if (value.length <= 11) {
 value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
 if (value.length < 14) {
 value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
 }
 }
 e.target.value = value;
 });
 document.getElementById('cpf').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
 e.target.value = value;
 });
 document.getElementById('endereco_cep').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
 e.target.value = value;
 });
</script>
@endsection