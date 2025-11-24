@extends('layouts.app')
@section('title', 'Nova Entrega')
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1><i class="fas fa-plus me-2"></i>Nova Entrega</h1>
 <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-2"></i>Voltar
 </a>
 </div>
 <div class="row justify-content-center">
 <div class="col-lg-8">
 <div class="card">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Informações da Entrega</h5>
 </div>
 <div class="card-body">
 <form action="{{ route('deliveries.store') }}" method="POST">
 @csrf
 <!-- Seleção Simples de Cliente -->
 <div class="row mb-4">
 <div class="col-12">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-user me-2"></i>Selecionar Cliente
 </h6>
 <div class="row">
 <div class="col-md-8">
 <label for="cliente_id" class="form-label">Cliente *</label>
 <select class="form-select @error('cliente_id') is-invalid @enderror" 
 id="cliente_id" name="cliente_id" required>
 <option value="">Selecione um cliente</option>
 @foreach($clientes as $cliente)
 <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
 {{ $cliente->nome }} - {{ $cliente->telefone }}
 @if($cliente->endereco_cidade)
 - {{ $cliente->endereco_cidade }}
 @endif
 </option>
 @endforeach
 </select>
 @error('cliente_id')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4 d-flex align-items-end">
 <a href="{{ route('clientes.create') }}" class="btn btn-outline-success" target="_blank">
 <i class="fas fa-user-plus me-2"></i>
 Novo Cliente
 </a>
 </div>
 </div>
 </div>
 </div>
 <!-- Dados do Cliente Selecionado -->
 <div class="row mb-4" id="dados-cliente-selecionado" style="display: none;">
 <div class="col-12">
 <div class="card border-success">
 <div class="card-header bg-success text-white">
 <h6 class="mb-0">
 <i class="fas fa-user-check me-2"></i>
 Dados do Cliente
 </h6>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-6">
 <strong>Nome:</strong> <span id="cliente-nome-display"></span><br>
 <strong>Telefone:</strong> <span id="cliente-telefone-display"></span><br>
 <strong>Email:</strong> <span id="cliente-email-display"></span>
 </div>
 <div class="col-md-6">
 <strong>Endereço:</strong><br>
 <span id="cliente-endereco-display"></span>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Informações da Entrega -->
 <div class="row mb-4">
 <div class="col-12">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-shipping-fast me-2"></i>Detalhes da Entrega
 </h6>
 </div>
 <div class="col-md-4">
 <label for="pedido_id" class="form-label">Pedido Vinculado</label>
 <select class="form-select @error('pedido_id') is-invalid @enderror" 
 id="pedido_id" name="pedido_id">
 <option value="">Selecione um pedido (opcional)</option>
 @foreach($pedidos as $pedido)
 <option value="{{ $pedido->id }}" {{ old('pedido_id') == $pedido->id ? 'selected' : '' }}>
 Pedido #{{ $pedido->id }} - R$ {{ number_format($pedido->total, 2, ',', '.') }}
 </option>
 @endforeach
 </select>
 @error('pedido_id')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label for="taxa_entrega" class="form-label">Taxa de Entrega (R$) *</label>
 <input type="number" step="0.01" min="0" 
 class="form-control @error('taxa_entrega') is-invalid @enderror" 
 id="taxa_entrega" name="taxa_entrega" 
 value="{{ old('taxa_entrega', '5.00') }}" required>
 @error('taxa_entrega')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label for="tempo_estimado" class="form-label">Tempo Estimado (min) *</label>
 <input type="number" min="10" max="180" 
 class="form-control @error('tempo_estimado') is-invalid @enderror" 
 id="tempo_estimado" name="tempo_estimado" 
 value="{{ old('tempo_estimado', '30') }}" required>
 @error('tempo_estimado')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <!-- Observações -->
 <div class="row mb-4">
 <div class="col-12">
 <label for="observacoes" class="form-label">Observações</label>
 <textarea class="form-control @error('observacoes') is-invalid @enderror" 
 id="observacoes" name="observacoes" rows="3" 
 placeholder="Observações sobre a entrega...">{{ old('observacoes') }}</textarea>
 @error('observacoes')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <!-- Botões -->
 <div class="row">
 <div class="col-12">
 <div class="d-flex justify-content-between">
 <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
 <i class="fas fa-times me-2"></i>Cancelar
 </a>
 <button type="submit" class="btn btn-primary">
 <i class="fas fa-save me-2"></i>Criar Entrega
 </button>
 </div>
 </div>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
</div>
<script>
function clienteSelecionadoParaDelivery(cliente) {
 console.log('🎯 Cliente selecionado:', cliente.nome, 'ID:', cliente.id);
 const campo = document.getElementById('cliente_id');
 if (campo) {
 campo.value = cliente.id;
 console.log('✅ Campo preenchido com:', cliente.id);
 }
 document.getElementById('cliente-selecionado-nome').textContent = cliente.nome;
 document.getElementById('cliente-selecionado-telefone').textContent = cliente.telefone;
 document.getElementById('cliente-selecionado-email').textContent = cliente.email || 'Não informado';
 document.getElementById('cliente-selecionado-endereco').textContent = cliente.endereco_completo || 'Endereço não cadastrado';
 document.getElementById('dados-cliente-selecionado').style.display = 'block';
}
window.clienteSelecionadoParaDelivery = clienteSelecionadoParaDelivery;
window.testarPreenchimentoCliente = function(clienteId) {
 const campo = document.getElementById('cliente_id');
 if (campo) {
 campo.value = clienteId;
 console.log('✅ Teste: Campo cliente_id preenchido com:', clienteId);
 console.log('🔍 Valor atual:', campo.value);
 return true;
 } else {
 console.error('❌ Teste: Campo cliente_id não encontrado');
 return false;
 }
};
function limparClienteSelecionado() {
 const campo = document.getElementById('cliente_id');
 if (campo) campo.value = '';
 document.getElementById('dados-cliente-selecionado').style.display = 'none';
 document.getElementById('buscaClienteDelivery').value = '';
 console.log('🧹 Cliente limpo');
}
document.addEventListener('DOMContentLoaded', function() {
 const form = document.querySelector('form[action*="deliveries"]');
 console.log('🚀 Formulário encontrado:', !!form);
 if (form) {
 console.log('✅ Formulário configurado - sem validação JavaScript');
 form.addEventListener('submit', function(e) {
 const clienteId = document.getElementById('cliente_id');
 const formData = new FormData(this);
 console.log('📤 ENVIANDO FORMULÁRIO...');
 console.log('🎯 Cliente ID no campo:', clienteId?.value);
 console.log('📋 Todos os dados do formulário:');
 for (let [key, value] of formData.entries()) {
 console.log(`  ${key}: ${value}`);
 }
 return true;
 });
 }
});
function showSuccessMessage(message) {
 const toastHtml = `
 <div class="toast align-items-center text-white bg-success border-0" role="alert">
 <div class="d-flex">
 <div class="toast-body">
 <i class="fas fa-check-circle me-2"></i>${message}
 </div>
 <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
 </div>
 </div>
 `;
 let toastContainer = document.querySelector('.toast-container');
 if (!toastContainer) {
 toastContainer = document.createElement('div');
 toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
 toastContainer.style.zIndex = '1055';
 document.body.appendChild(toastContainer);
 }
 toastContainer.insertAdjacentHTML('beforeend', toastHtml);
 const toast = toastContainer.lastElementChild;
 const bsToast = new bootstrap.Toast(toast);
 bsToast.show();
 toast.addEventListener('hidden.bs.toast', () => {
 toast.remove();
 });
}
<script>
document.addEventListener('DOMContentLoaded', function() {
 const clienteSelect = document.getElementById('cliente_id');
 const dadosClienteDiv = document.getElementById('dados-cliente-selecionado');
 clienteSelect.addEventListener('change', function() {
 const clienteId = this.value;
 if (clienteId) {
 console.log('📋 Cliente selecionado ID:', clienteId);
 fetch(`/api/clientes/${clienteId}`)
 .then(response => response.json())
 .then(data => {
 if (data.success && data.data) {
 const cliente = data.data;
 document.getElementById('cliente-nome-display').textContent = cliente.nome;
 document.getElementById('cliente-telefone-display').textContent = cliente.telefone;
 document.getElementById('cliente-email-display').textContent = cliente.email || 'Não informado';
 document.getElementById('cliente-endereco-display').textContent = cliente.endereco_completo || 'Endereço não cadastrado';
 dadosClienteDiv.style.display = 'block';
 console.log('✅ Dados do cliente carregados:', cliente.nome);
 }
 })
 .catch(error => {
 console.error('❌ Erro ao carregar cliente:', error);
 });
 } else {
 dadosClienteDiv.style.display = 'none';
 }
 });
 const form = document.querySelector('form');
 if (form) {
 form.addEventListener('submit', function(e) {
 const clienteId = document.getElementById('cliente_id').value;
 console.log('📤 Enviando formulário com cliente ID:', clienteId);
 return true;
 });
 }
 console.log('✅ Sistema de seleção de cliente carregado');
});
</script>
@endsection