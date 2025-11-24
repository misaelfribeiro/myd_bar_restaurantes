@extends('layouts.app')
@section('title', 'Editar Entregador')
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1 class="h3 text-gray-800">
 <i class="fas fa-edit mr-2"></i>Editar Entregador
 </h1>
 <div>
 <a href="{{ route('entregadores.show', $entregador) }}" class="btn btn-info mr-2">
 <i class="fas fa-eye mr-2"></i>Ver Detalhes
 </a>
 <a href="{{ route('entregadores.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left mr-2"></i>Voltar
 </a>
 </div>
 </div>
 <div class="row">
 <div class="col-12">
 <div class="card shadow">
 <div class="card-header py-3">
 <h6 class="m-0 font-weight-bold text-primary">
 <i class="fas fa-user-edit mr-2"></i>Editar Dados do Entregador
 </h6>
 </div>
 <div class="card-body">
 <form method="POST" action="{{ route('entregadores.update', $entregador) }}" enctype="multipart/form-data">
 @csrf
 @method('PUT')
 <!-- Dados Pessoais -->
 <div class="row">
 <div class="col-12">
 <h6 class="text-primary font-weight-bold mb-3">
 <i class="fas fa-user mr-2"></i>Dados Pessoais
 </h6>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="nome">Nome Completo *</label>
 <input type="text" class="form-control @error('nome') is-invalid @enderror" 
 id="nome" name="nome" value="{{ old('nome', $entregador->nome) }}" required>
 @error('nome')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="email">Email *</label>
 <input type="email" class="form-control @error('email') is-invalid @enderror" 
 id="email" name="email" value="{{ old('email', $entregador->email) }}" required>
 @error('email')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-4">
 <div class="form-group">
 <label for="telefone">Telefone</label>
 <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
 id="telefone" name="telefone" value="{{ old('telefone', $entregador->telefone) }}" 
 placeholder="(11) 1234-5678">
 @error('telefone')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-4">
 <div class="form-group">
 <label for="whatsapp">WhatsApp</label>
 <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" 
 id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $entregador->whatsapp) }}" 
 placeholder="(11) 99999-9999">
 @error('whatsapp')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-4">
 <div class="form-group">
 <label for="data_nascimento">Data de Nascimento *</label>
 <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" 
 id="data_nascimento" name="data_nascimento" 
 value="{{ old('data_nascimento', $entregador->data_nascimento ? $entregador->data_nascimento->format('Y-m-d') : '') }}" required>
 @error('data_nascimento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="cpf">CPF *</label>
 <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
 id="cpf" name="cpf" value="{{ old('cpf', $entregador->cpf) }}" 
 placeholder="000.000.000-00" required>
 @error('cpf')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="rg">RG</label>
 <input type="text" class="form-control @error('rg') is-invalid @enderror" 
 id="rg" name="rg" value="{{ old('rg', $entregador->rg) }}">
 @error('rg')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <hr>
 <!-- Endereço -->
 <div class="row">
 <div class="col-12">
 <h6 class="text-primary font-weight-bold mb-3">
 <i class="fas fa-map-marker-alt mr-2"></i>Endereço
 </h6>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="cep">CEP *</label>
 <input type="text" class="form-control @error('cep') is-invalid @enderror" 
 id="cep" name="cep" value="{{ old('cep', $entregador->cep) }}" 
 placeholder="00000-000" required>
 @error('cep')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="endereco">Endereço *</label>
 <input type="text" class="form-control @error('endereco') is-invalid @enderror" 
 id="endereco" name="endereco" value="{{ old('endereco', $entregador->endereco) }}" required>
 @error('endereco')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="numero">Número *</label>
 <input type="text" class="form-control @error('numero') is-invalid @enderror" 
 id="numero" name="numero" value="{{ old('numero', $entregador->numero) }}" required>
 @error('numero')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="complemento">Complemento</label>
 <input type="text" class="form-control @error('complemento') is-invalid @enderror" 
 id="complemento" name="complemento" value="{{ old('complemento', $entregador->complemento) }}">
 @error('complemento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="bairro">Bairro *</label>
 <input type="text" class="form-control @error('bairro') is-invalid @enderror" 
 id="bairro" name="bairro" value="{{ old('bairro', $entregador->bairro) }}" required>
 @error('bairro')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-4">
 <div class="form-group">
 <label for="cidade">Cidade *</label>
 <input type="text" class="form-control @error('cidade') is-invalid @enderror" 
 id="cidade" name="cidade" value="{{ old('cidade', $entregador->cidade) }}" required>
 @error('cidade')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-2">
 <div class="form-group">
 <label for="estado">Estado *</label>
 <select class="form-control @error('estado') is-invalid @enderror" 
 id="estado" name="estado" required>
 <option value="">Selecione...</option>
 @foreach($estados ?? [] as $uf => $nome)
 <option value="{{ $uf }}" {{ old('estado', $entregador->estado) == $uf ? 'selected' : '' }}>
 {{ $uf }}
 </option>
 @endforeach
 </select>
 @error('estado')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <hr>
 <!-- Tipo e Veículo -->
 <div class="row">
 <div class="col-12">
 <h6 class="text-primary font-weight-bold mb-3">
 <i class="fas fa-motorcycle mr-2"></i>Tipo e Veículo
 </h6>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="tipo">Tipo de Entregador *</label>
 <select class="form-control @error('tipo') is-invalid @enderror" 
 id="tipo" name="tipo" required>
 <option value="">Selecione...</option>
 <option value="interno" {{ old('tipo', $entregador->tipo) == 'interno' ? 'selected' : '' }}>Interno (Funcionário)</option>
 <option value="externo" {{ old('tipo', $entregador->tipo) == 'externo' ? 'selected' : '' }}>Externo (App)</option>
 </select>
 @error('tipo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-6">
 <div class="form-group">
 <label for="tipo_veiculo">Tipo de Veículo *</label>
 <select class="form-control @error('tipo_veiculo') is-invalid @enderror" 
 id="tipo_veiculo" name="tipo_veiculo" required onchange="toggleCNHFields()">
 <option value="">Selecione...</option>
 <option value="moto" {{ old('tipo_veiculo', $entregador->tipo_veiculo) == 'moto' ? 'selected' : '' }}>Motocicleta</option>
 <option value="carro" {{ old('tipo_veiculo', $entregador->tipo_veiculo) == 'carro' ? 'selected' : '' }}>Carro</option>
 <option value="bicicleta" {{ old('tipo_veiculo', $entregador->tipo_veiculo) == 'bicicleta' ? 'selected' : '' }}>Bicicleta</option>
 <option value="pe" {{ old('tipo_veiculo', $entregador->tipo_veiculo) == 'pe' ? 'selected' : '' }}>A pé</option>
 </select>
 @error('tipo_veiculo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <!-- Dados do Veículo -->
 <div id="veiculo-fields">
 <div class="row">
 <div class="col-md-3">
 <div class="form-group">
 <label for="marca_veiculo">Marca</label>
 <input type="text" class="form-control @error('marca_veiculo') is-invalid @enderror" 
 id="marca_veiculo" name="marca_veiculo" value="{{ old('marca_veiculo', $entregador->marca_veiculo) }}">
 @error('marca_veiculo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="modelo_veiculo">Modelo</label>
 <input type="text" class="form-control @error('modelo_veiculo') is-invalid @enderror" 
 id="modelo_veiculo" name="modelo_veiculo" value="{{ old('modelo_veiculo', $entregador->modelo_veiculo) }}">
 @error('modelo_veiculo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="placa_veiculo">Placa</label>
 <input type="text" class="form-control @error('placa_veiculo') is-invalid @enderror" 
 id="placa_veiculo" name="placa_veiculo" value="{{ old('placa_veiculo', $entregador->placa_veiculo) }}" 
 placeholder="ABC-1234">
 @error('placa_veiculo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="ano_veiculo">Ano</label>
 <input type="number" class="form-control @error('ano_veiculo') is-invalid @enderror" 
 id="ano_veiculo" name="ano_veiculo" value="{{ old('ano_veiculo', $entregador->ano_veiculo) }}" 
 min="1990" max="{{ date('Y') + 1 }}">
 @error('ano_veiculo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-12">
 <div class="form-group">
 <label for="cor_veiculo">Cor do Veículo</label>
 <input type="text" class="form-control @error('cor_veiculo') is-invalid @enderror" 
 id="cor_veiculo" name="cor_veiculo" value="{{ old('cor_veiculo', $entregador->cor_veiculo) }}">
 @error('cor_veiculo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <!-- CNH -->
 <div class="row">
 <div class="col-12">
 <h6 class="text-primary font-weight-bold mb-3">
 <i class="fas fa-id-card mr-2"></i>CNH
 </h6>
 </div>
 <div class="col-md-4">
 <div class="form-group">
 <label for="cnh_numero">Número da CNH</label>
 <input type="text" class="form-control @error('cnh_numero') is-invalid @enderror" 
 id="cnh_numero" name="cnh_numero" value="{{ old('cnh_numero', $entregador->cnh_numero) }}">
 @error('cnh_numero')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-4">
 <div class="form-group">
 <label for="cnh_categoria">Categoria da CNH</label>
 <select class="form-control @error('cnh_categoria') is-invalid @enderror" 
 id="cnh_categoria" name="cnh_categoria">
 <option value="">Selecione...</option>
 <option value="A" {{ old('cnh_categoria', $entregador->cnh_categoria) == 'A' ? 'selected' : '' }}>A</option>
 <option value="B" {{ old('cnh_categoria', $entregador->cnh_categoria) == 'B' ? 'selected' : '' }}>B</option>
 <option value="AB" {{ old('cnh_categoria', $entregador->cnh_categoria) == 'AB' ? 'selected' : '' }}>AB</option>
 <option value="C" {{ old('cnh_categoria', $entregador->cnh_categoria) == 'C' ? 'selected' : '' }}>C</option>
 <option value="D" {{ old('cnh_categoria', $entregador->cnh_categoria) == 'D' ? 'selected' : '' }}>D</option>
 <option value="E" {{ old('cnh_categoria', $entregador->cnh_categoria) == 'E' ? 'selected' : '' }}>E</option>
 </select>
 @error('cnh_categoria')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-4">
 <div class="form-group">
 <label for="cnh_validade">Validade da CNH</label>
 <input type="date" class="form-control @error('cnh_validade') is-invalid @enderror" 
 id="cnh_validade" name="cnh_validade" 
 value="{{ old('cnh_validade', $entregador->cnh_validade ? $entregador->cnh_validade->format('Y-m-d') : '') }}">
 @error('cnh_validade')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 </div>
 <hr>
 <!-- Dados Bancários -->
 <div class="row">
 <div class="col-12">
 <h6 class="text-primary font-weight-bold mb-3">
 <i class="fas fa-credit-card mr-2"></i>Dados Bancários (Opcional)
 </h6>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="banco">Banco</label>
 <input type="text" class="form-control @error('banco') is-invalid @enderror" 
 id="banco" name="banco" value="{{ old('banco', $entregador->banco) }}">
 @error('banco')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="agencia">Agência</label>
 <input type="text" class="form-control @error('agencia') is-invalid @enderror" 
 id="agencia" name="agencia" value="{{ old('agencia', $entregador->agencia) }}">
 @error('agencia')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="conta">Conta</label>
 <input type="text" class="form-control @error('conta') is-invalid @enderror" 
 id="conta" name="conta" value="{{ old('conta', $entregador->conta) }}">
 @error('conta')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="pix">PIX</label>
 <input type="text" class="form-control @error('pix') is-invalid @enderror" 
 id="pix" name="pix" value="{{ old('pix', $entregador->pix) }}" 
 placeholder="CPF, email ou chave PIX">
 @error('pix')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <hr>
 <!-- Upload de Documentos -->
 <div class="row">
 <div class="col-12">
 <h6 class="text-primary font-weight-bold mb-3">
 <i class="fas fa-file-upload mr-2"></i>Atualizar Documentos (Opcional)
 </h6>
 <p class="text-muted">Deixe em branco para manter os documentos atuais</p>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="foto_rg">RG</label>
 <input type="file" class="form-control @error('foto_rg') is-invalid @enderror" 
 id="foto_rg" name="foto_rg" accept="image/*">
 @error('foto_rg')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 @if($entregador->foto_rg)
 <small class="form-text text-success">✓ Documento atual cadastrado</small>
 @else
 <small class="form-text text-danger">✗ Documento não cadastrado</small>
 @endif
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="foto_cpf">CPF</label>
 <input type="file" class="form-control @error('foto_cpf') is-invalid @enderror" 
 id="foto_cpf" name="foto_cpf" accept="image/*">
 @error('foto_cpf')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 @if($entregador->foto_cpf)
 <small class="form-text text-success">✓ Documento atual cadastrado</small>
 @else
 <small class="form-text text-danger">✗ Documento não cadastrado</small>
 @endif
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="foto_entregador">Foto do Entregador</label>
 <input type="file" class="form-control @error('foto_entregador') is-invalid @enderror" 
 id="foto_entregador" name="foto_entregador" accept="image/*">
 @error('foto_entregador')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 @if($entregador->foto_entregador)
 <small class="form-text text-success">✓ Foto atual cadastrada</small>
 @else
 <small class="form-text text-danger">✗ Foto não cadastrada</small>
 @endif
 </div>
 </div>
 <div class="col-md-3">
 <div class="form-group">
 <label for="foto_comprovante_endereco">Comprovante de Endereço</label>
 <input type="file" class="form-control @error('foto_comprovante_endereco') is-invalid @enderror" 
 id="foto_comprovante_endereco" name="foto_comprovante_endereco" accept="image/*">
 @error('foto_comprovante_endereco')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 @if($entregador->foto_comprovante_endereco)
 <small class="form-text text-success">✓ Documento atual cadastrado</small>
 @else
 <small class="form-text text-danger">✗ Documento não cadastrado</small>
 @endif
 </div>
 </div>
 <div class="col-md-3" id="foto-cnh-field">
 <div class="form-group">
 <label for="foto_cnh">CNH</label>
 <input type="file" class="form-control @error('foto_cnh') is-invalid @enderror" 
 id="foto_cnh" name="foto_cnh" accept="image/*">
 @error('foto_cnh')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 @if($entregador->foto_cnh)
 <small class="form-text text-success">✓ Documento atual cadastrado</small>
 @else
 <small class="form-text text-danger">✗ Documento não cadastrado</small>
 @endif
 </div>
 </div>
 </div>
 <hr>
 <!-- Botões -->
 <div class="row">
 <div class="col-12 text-right">
 <a href="{{ route('entregadores.show', $entregador) }}" class="btn btn-secondary mr-2">
 <i class="fas fa-times mr-2"></i>Cancelar
 </a>
 <button type="submit" class="btn btn-primary">
 <i class="fas fa-save mr-2"></i>Salvar Alterações
 </button>
 </div>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
</div>
<script>
function toggleCNHFields() {
 const tipoVeiculo = document.getElementById('tipo_veiculo').value;
 const veiculoFields = document.getElementById('veiculo-fields');
 const fotoCnhField = document.getElementById('foto-cnh-field');
 if (tipoVeiculo === 'moto' || tipoVeiculo === 'carro') {
 veiculoFields.style.display = 'block';
 fotoCnhField.style.display = 'block';
 } else {
 veiculoFields.style.display = 'none';
 fotoCnhField.style.display = 'none';
 }
}
document.addEventListener('DOMContentLoaded', function() {
 toggleCNHFields();
});
document.getElementById('cpf').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 value = value.replace(/(\d{3})(\d)/, '$1.$2');
 value = value.replace(/(\d{3})(\d)/, '$1.$2');
 value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
 e.target.value = value;
});
document.getElementById('cep').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 value = value.replace(/(\d{5})(\d)/, '$1-$2');
 e.target.value = value;
});
document.getElementById('telefone').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 value = value.replace(/(\d{2})(\d)/, '($1) $2');
 value = value.replace(/(\d)(\d{4})$/, '$1-$2');
 e.target.value = value;
});
document.getElementById('whatsapp').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 value = value.replace(/(\d{2})(\d)/, '($1) $2');
 value = value.replace(/(\d)(\d{4})$/, '$1-$2');
 e.target.value = value;
});
</script>
@endsection