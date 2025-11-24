@extends('layouts.app')
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1 class="h3 mb-0">
 <i class="fas fa-plus-circle me-2"></i>
 Nova Fatura
 </h1>
 <a href="{{ route('admin.financeiro.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-1"></i> Voltar
 </a>
 </div>
 <div class="row">
 <div class="col-lg-8">
 <div class="card shadow-sm">
 <div class="card-header bg-primary text-white">
 <h5 class="mb-0">
 <i class="fas fa-file-invoice me-2"></i>
 Informações da Fatura
 </h5>
 </div>
 <div class="card-body">
 @if ($errors->any())
 <div class="alert alert-danger">
 <h6 class="alert-heading">
 <i class="fas fa-exclamation-triangle me-1"></i>
 Erro ao criar fatura
 </h6>
 <ul class="mb-0">
 @foreach ($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif
 <form action="{{ route('admin.financeiro.store') }}" method="POST" id="formNovaFatura">
 @csrf
 <div class="row mb-3">
 <div class="col-md-6">
 <label for="empresa_id" class="form-label">Empresa *</label>
 <select class="form-select" id="empresa_id" name="empresa_id" required>
 <option value="">Selecione uma empresa...</option>
 @foreach($empresas as $empresa)
 <option value="{{ $empresa->id }}" 
 data-plano="{{ $empresa->plano }}"
 data-valor="{{ $empresa->valor_mensalidade }}"
 {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
 {{ $empresa->nome_fantasia }} - {{ ucfirst($empresa->plano) }}
 </option>
 @endforeach
 </select>
 </div>
 <div class="col-md-6">
 <label for="contrato_id" class="form-label">Contrato *</label>
 <select class="form-select" id="contrato_id" name="contrato_id" required disabled>
 <option value="">Selecione uma empresa primeiro...</option>
 </select>
 <small class="text-muted">Contratos ativos da empresa selecionada</small>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <label for="data_referencia" class="form-label">Data de Referência *</label>
 <input type="month" class="form-control" id="data_referencia" name="data_referencia" 
 value="{{ old('data_referencia', now()->format('Y-m')) }}" required>
 <small class="text-muted">Mês/ano de referência da fatura</small>
 </div>
 <div class="col-md-6">
 <label for="data_vencimento" class="form-label">Data de Vencimento *</label>
 <input type="date" class="form-control" id="data_vencimento" name="data_vencimento" 
 value="{{ old('data_vencimento', now()->addDays(5)->format('Y-m-d')) }}" required>
 </div>
 </div>
 <hr class="my-4">
 <h6 class="text-muted mb-3">Valores</h6>
 <div class="row mb-3">
 <div class="col-md-4">
 <label for="valor_plano" class="form-label">Valor do Plano *</label>
 <div class="input-group">
 <span class="input-group-text">R$</span>
 <input type="number" class="form-control" id="valor_plano" name="valor_plano" 
 step="0.01" min="0" value="{{ old('valor_plano', '0.00') }}" required>
 </div>
 </div>
 <div class="col-md-4">
 <label for="valor_adicional" class="form-label">Valor Adicional</label>
 <div class="input-group">
 <span class="input-group-text">R$</span>
 <input type="number" class="form-control" id="valor_adicional" name="valor_adicional" 
 step="0.01" min="0" value="{{ old('valor_adicional', '0.00') }}">
 </div>
 <small class="text-muted">Serviços extras, ajustes, etc.</small>
 </div>
 <div class="col-md-4">
 <label for="valor_desconto" class="form-label">Desconto</label>
 <div class="input-group">
 <span class="input-group-text">R$</span>
 <input type="number" class="form-control" id="valor_desconto" name="valor_desconto" 
 step="0.01" min="0" value="{{ old('valor_desconto', '0.00') }}">
 </div>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-12">
 <div class="alert alert-info">
 <div class="d-flex justify-content-between align-items-center">
 <span><strong>Valor Total:</strong></span>
 <span class="fs-4 fw-bold" id="valor_total_display">R$ 0,00</span>
 </div>
 </div>
 </div>
 </div>
 <div class="mb-3">
 <label for="descricao" class="form-label">Descrição</label>
 <textarea class="form-control" id="descricao" name="descricao" rows="3" 
 placeholder="Descrição ou observações sobre esta fatura">{{ old('descricao') }}</textarea>
 </div>
 <div class="d-flex justify-content-end gap-2">
 <a href="{{ route('admin.financeiro.index') }}" class="btn btn-secondary">
 <i class="fas fa-times me-1"></i>
 Cancelar
 </a>
 <button type="submit" class="btn btn-primary">
 <i class="fas fa-save me-1"></i>
 Criar Fatura
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 <div class="col-lg-4">
 <div class="card shadow-sm">
 <div class="card-header bg-info text-white">
 <h5 class="mb-0">
 <i class="fas fa-info-circle me-2"></i>
 Informações
 </h5>
 </div>
 <div class="card-body">
 <h6 class="text-muted mb-2">Número da Fatura</h6>
 <p class="mb-3">Será gerado automaticamente no formato: <strong>FAT-YYYY-00001</strong></p>
 <h6 class="text-muted mb-2">Status Inicial</h6>
 <p class="mb-3">A fatura será criada com status <span class="badge bg-warning">Pendente</span></p>
 <h6 class="text-muted mb-2">Cálculo do Total</h6>
 <p class="mb-0">
 <small>
 <strong>Valor Total = </strong><br>
 Valor do Plano + Valor Adicional - Desconto
 </small>
 </p>
 </div>
 </div>
 <div class="card shadow-sm mt-3" id="empresa_info" style="display: none;">
 <div class="card-header bg-success text-white">
 <h5 class="mb-0">
 <i class="fas fa-building me-2"></i>
 Dados da Empresa
 </h5>
 </div>
 <div class="card-body">
 <p class="mb-2">
 <strong>Nome:</strong><br>
 <span id="info_nome">-</span>
 </p>
 <p class="mb-2">
 <strong>Plano Atual:</strong><br>
 <span id="info_plano" class="badge bg-primary">-</span>
 </p>
 <p class="mb-0">
 <strong>Mensalidade:</strong><br>
 <span id="info_valor" class="text-success fw-bold">-</span>
 </p>
 </div>
 </div>
 </div>
 </div>
</div>
<script>
(function() {
 var empresaSelect = document.getElementById('empresa_id');
 var contratoSelect = document.getElementById('contrato_id');
 var valorPlanoInput = document.getElementById('valor_plano');
 var valorAdicionalInput = document.getElementById('valor_adicional');
 var valorDescontoInput = document.getElementById('valor_desconto');
 var valorTotalDisplay = document.getElementById('valor_total_display');
 var empresaInfoCard = document.getElementById('empresa_info');
 var infoNome = document.getElementById('info_nome');
 var infoPlano = document.getElementById('info_plano');
 var infoValor = document.getElementById('info_valor');
 function calcularTotal() {
 var valorPlano = parseFloat(valorPlanoInput.value) || 0;
 var valorAdicional = parseFloat(valorAdicionalInput.value) || 0;
 var valorDesconto = parseFloat(valorDescontoInput.value) || 0;
 var total = valorPlano + valorAdicional - valorDesconto;
 valorTotalDisplay.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
 }
 function carregarContratos(empresaId) {
 if (!empresaId) {
 contratoSelect.disabled = true;
 contratoSelect.innerHTML = '<option value="">Selecione uma empresa primeiro...</option>';
 empresaInfoCard.style.display = 'none';
 return;
 }
 var selectedOption = empresaSelect.options[empresaSelect.selectedIndex];
 var plano = selectedOption.getAttribute('data-plano');
 var valor = selectedOption.getAttribute('data-valor');
 var nomeEmpresa = selectedOption.textContent;
 infoNome.textContent = nomeEmpresa.split(' - ')[0];
 infoPlano.textContent = plano ? plano.charAt(0).toUpperCase() + plano.slice(1) : '-';
 infoValor.textContent = valor ? 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',') : '-';
 empresaInfoCard.style.display = 'block';
 if (valor) {
 valorPlanoInput.value = parseFloat(valor).toFixed(2);
 calcularTotal();
 }
 contratoSelect.disabled = true;
 contratoSelect.innerHTML = '<option value="">Carregando contratos...</option>';
 fetch('/admin/financeiro/contratos/' + empresaId)
 .then(function(response) {
 return response.json();
 })
 .then(function(data) {
 contratoSelect.innerHTML = '<option value="">Selecione um contrato...</option>';
 if (data.contratos && data.contratos.length > 0) {
 data.contratos.forEach(function(contrato) {
 var option = document.createElement('option');
 option.value = contrato.id;
 option.textContent = 'Contrato #' + contrato.id + ' - ' + 
 contrato.plano.nome + ' (' + 
 contrato.data_inicio + ' até ' + contrato.data_fim + ')';
 contratoSelect.appendChild(option);
 });
 contratoSelect.disabled = false;
 } else {
 contratoSelect.innerHTML = '<option value="">Nenhum contrato ativo encontrado</option>';
 }
 })
 .catch(function(error) {
 console.error('Erro ao carregar contratos:', error);
 contratoSelect.innerHTML = '<option value="">Erro ao carregar contratos</option>';
 });
 }
 empresaSelect.addEventListener('change', function() {
 carregarContratos(this.value);
 });
 valorPlanoInput.addEventListener('input', calcularTotal);
 valorAdicionalInput.addEventListener('input', calcularTotal);
 valorDescontoInput.addEventListener('input', calcularTotal);
 calcularTotal();
 if (empresaSelect.value) {
 carregarContratos(empresaSelect.value);
 }
})();
</script>
@endsection