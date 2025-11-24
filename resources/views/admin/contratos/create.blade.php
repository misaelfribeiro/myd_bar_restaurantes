@extends('layouts.app')
@section('title', 'Novo Contrato')
@section('content')
<div class="container-fluid px-4">
 <div class="row justify-content-center">
 <div class="col-lg-10">
 <!-- Header -->
 <div class="mb-4">
 <h1 class="h3 mb-1">📄 Novo Contrato</h1>
 <p class="text-muted">Preencha os dados para criar um novo contrato de assinatura</p>
 </div>
 <form action="{{ route('admin.contratos.store') }}" method="POST" enctype="multipart/form-data">
 @csrf
 <!-- Dados Básicos -->
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-header bg-primary text-white">
 <h5 class="mb-0"><i class="fas fa-building me-2"></i>Dados do Contrato</h5>
 </div>
 <div class="card-body">
 <div class="row g-3">
 <div class="col-md-6">
 <label class="form-label">Empresa <span class="text-danger">*</span></label>
 <select name="empresa_id" class="form-select @error('empresa_id') is-invalid @enderror" required>
 <option value="">Selecione a empresa...</option>
 @foreach($empresas as $empresa)
 <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
 {{ $empresa->nome_fantasia }} ({{ $empresa->tenant_code }})
 </option>
 @endforeach
 </select>
 @error('empresa_id')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6">
 <label class="form-label">Plano <span class="text-danger">*</span></label>
 <select name="plano_id" id="plano_id" class="form-select @error('plano_id') is-invalid @enderror" required>
 <option value="">Selecione o plano...</option>
 @foreach($planos as $plano)
 <option value="{{ $plano->id }}" 
 data-valor-mensal="{{ $plano->valor_mensal }}"
 data-valor-anual="{{ $plano->valor_anual }}"
 {{ old('plano_id') == $plano->id ? 'selected' : '' }}>
 {{ $plano->nome }} - R$ {{ number_format($plano->valor_mensal, 2, ',', '.') }}/mês
 </option>
 @endforeach
 </select>
 @error('plano_id')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label class="form-label">Data de Início <span class="text-danger">*</span></label>
 <input type="date" name="data_inicio" class="form-control @error('data_inicio') is-invalid @enderror" 
 value="{{ old('data_inicio', date('Y-m-d')) }}" required>
 @error('data_inicio')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label class="form-label">Tipo de Pagamento <span class="text-danger">*</span></label>
 <select name="tipo_pagamento" id="tipo_pagamento" class="form-select @error('tipo_pagamento') is-invalid @enderror" required>
 <option value="mensal" {{ old('tipo_pagamento') == 'mensal' ? 'selected' : '' }}>Mensal</option>
 <option value="anual" {{ old('tipo_pagamento') == 'anual' ? 'selected' : '' }}>Anual</option>
 </select>
 @error('tipo_pagamento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label class="form-label">Desconto Aplicado (R$)</label>
 <input type="number" name="desconto_aplicado" class="form-control @error('desconto_aplicado') is-invalid @enderror" 
 value="{{ old('desconto_aplicado', 0) }}" step="0.01" min="0">
 @error('desconto_aplicado')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-12">
 <label class="form-label">Observações</label>
 <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3">{{ old('observacoes') }}</textarea>
 @error('observacoes')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 </div>
 <!-- Documentos -->
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-header bg-info text-white">
 <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>Documentos</h5>
 </div>
 <div class="card-body">
 <div class="row g-3">
 <div class="col-md-4">
 <label class="form-label">Contrato Assinado (PDF)</label>
 <input type="file" name="documento_assinado" class="form-control @error('documento_assinado') is-invalid @enderror" accept=".pdf">
 <small class="text-muted">Máximo 10MB</small>
 @error('documento_assinado')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label class="form-label">Documento de Identidade</label>
 <input type="file" name="documento_identidade" class="form-control @error('documento_identidade') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
 <small class="text-muted">Máximo 5MB</small>
 @error('documento_identidade')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label class="form-label">Comprovante de Endereço</label>
 <input type="file" name="comprovante_endereco" class="form-control @error('comprovante_endereco') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
 <small class="text-muted">Máximo 5MB</small>
 @error('comprovante_endereco')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 </div>
 <!-- Resumo -->
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-header bg-success text-white">
 <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Resumo do Contrato</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-6">
 <p><strong>Valor do Plano:</strong> <span id="valor_plano">R$ 0,00</span></p>
 <p><strong>Desconto:</strong> <span id="valor_desconto">R$ 0,00</span></p>
 </div>
 <div class="col-md-6 text-end">
 <h4><strong>Valor Final:</strong> <span id="valor_final" class="text-success">R$ 0,00</span></h4>
 </div>
 </div>
 </div>
 </div>
 <!-- Botões -->
 <div class="d-flex justify-content-between mb-5">
 <a href="{{ route('admin.contratos.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-2"></i>Cancelar
 </a>
 <button type="submit" class="btn btn-primary btn-lg">
 <i class="fas fa-save me-2"></i>Criar Contrato
 </button>
 </div>
 </form>
 </div>
 </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
 const planoSelect = document.getElementById('plano_id');
 const tipoPagamentoSelect = document.getElementById('tipo_pagamento');
 const descontoInput = document.querySelector('[name="desconto_aplicado"]');
 function atualizarResumo() {
 const planoOption = planoSelect.options[planoSelect.selectedIndex];
 if (!planoOption.value) return;
 const tipoPagamento = tipoPagamentoSelect.value;
 const valorPlano = tipoPagamento === 'mensal' 
 ? parseFloat(planoOption.dataset.valorMensal) 
 : parseFloat(planoOption.dataset.valorAnual);
 const desconto = parseFloat(descontoInput.value) || 0;
 const valorFinal = valorPlano - desconto;
 document.getElementById('valor_plano').textContent = 'R$ ' + valorPlano.toFixed(2).replace('.', ',');
 document.getElementById('valor_desconto').textContent = 'R$ ' + desconto.toFixed(2).replace('.', ',');
 document.getElementById('valor_final').textContent = 'R$ ' + valorFinal.toFixed(2).replace('.', ',');
 }
 planoSelect.addEventListener('change', atualizarResumo);
 tipoPagamentoSelect.addEventListener('change', atualizarResumo);
 descontoInput.addEventListener('input', atualizarResumo);
 atualizarResumo();
});
</script>
@endsection