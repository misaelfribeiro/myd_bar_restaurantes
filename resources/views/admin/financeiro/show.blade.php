@extends('layouts.app')
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1 class="h3 mb-0">
 <i class="fas fa-file-invoice me-2"></i>
 Detalhes da Fatura
 </h1>
 <a href="{{ route('admin.financeiro.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-1"></i> Voltar
 </a>
 </div>
 <div class="row">
 <!-- Informações da Fatura -->
 <div class="col-lg-8 mb-4">
 <div class="card shadow-sm">
 <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
 <h5 class="mb-0">
 <i class="fas fa-info-circle me-2"></i>
 Fatura {{ $fatura->numero_fatura }}
 </h5>
 @if($fatura->status === 'pago')
 <span class="badge bg-success fs-6">
 <i class="fas fa-check-circle me-1"></i> Pago
 </span>
 @elseif($fatura->status === 'pendente')
 <span class="badge bg-warning fs-6">
 <i class="fas fa-clock me-1"></i> Pendente
 </span>
 @elseif($fatura->status === 'vencido')
 <span class="badge bg-danger fs-6">
 <i class="fas fa-exclamation-triangle me-1"></i> Vencido
 </span>
 @else
 <span class="badge bg-secondary fs-6">
 <i class="fas fa-times-circle me-1"></i> Cancelado
 </span>
 @endif
 </div>
 <div class="card-body">
 <div class="row mb-4">
 <div class="col-md-6">
 <h6 class="text-muted mb-3">Informações da Empresa</h6>
 <p class="mb-2">
 <strong>Empresa:</strong><br>
 {{ $fatura->contrato->empresa->nome }}
 </p>
 <p class="mb-2">
 <strong>CNPJ:</strong><br>
 {{ $fatura->contrato->empresa->cnpj ?? 'Não informado' }}
 </p>
 <p class="mb-2">
 <strong>E-mail:</strong><br>
 {{ $fatura->contrato->empresa->email }}
 </p>
 </div>
 <div class="col-md-6">
 <h6 class="text-muted mb-3">Informações do Contrato</h6>
 <p class="mb-2">
 <strong>Plano:</strong><br>
 {{ $fatura->contrato->plano->nome }}
 </p>
 <p class="mb-2">
 <strong>Tipo de Pagamento:</strong><br>
 {{ $fatura->contrato->tipo_pagamento === 'mensal' ? 'Mensal' : 'Anual' }}
 </p>
 <p class="mb-2">
 <strong>Vigência:</strong><br>
 {{ \Carbon\Carbon::parse($fatura->contrato->data_inicio)->format('d/m/Y') }}
 até
 {{ \Carbon\Carbon::parse($fatura->contrato->data_fim)->format('d/m/Y') }}
 </p>
 </div>
 </div>
 <hr>
 <div class="row mb-4">
 <div class="col-md-6">
 <h6 class="text-muted mb-3">Datas</h6>
 <p class="mb-2">
 <strong>Data de Referência:</strong><br>
 {{ \Carbon\Carbon::parse($fatura->data_referencia)->format('m/Y') }}
 </p>
 <p class="mb-2">
 <strong>Data de Vencimento:</strong><br>
 {{ \Carbon\Carbon::parse($fatura->data_vencimento)->format('d/m/Y') }}
 @if($fatura->status === 'pendente' && \Carbon\Carbon::parse($fatura->data_vencimento)->isPast())
 <span class="badge bg-danger ms-2">
 Vencido há {{ \Carbon\Carbon::parse($fatura->data_vencimento)->diffInDays(now()) }} dias
 </span>
 @endif
 </p>
 @if($fatura->data_pagamento)
 <p class="mb-2">
 <strong>Data de Pagamento:</strong><br>
 {{ \Carbon\Carbon::parse($fatura->data_pagamento)->format('d/m/Y H:i') }}
 </p>
 @endif
 </div>
 <div class="col-md-6">
 <h6 class="text-muted mb-3">Valores</h6>
 @if($fatura->valor_plano > 0)
 <p class="mb-2">
 <strong>Valor do Plano:</strong><br>
 R$ {{ number_format($fatura->valor_plano, 2, ',', '.') }}
 </p>
 @endif
 @if($fatura->valor_adicional > 0)
 <p class="mb-2">
 <strong>Valor Adicional:</strong><br>
 R$ {{ number_format($fatura->valor_adicional, 2, ',', '.') }}
 </p>
 @endif
 @if($fatura->valor_desconto > 0)
 <p class="mb-2">
 <strong>Desconto:</strong><br>
 - R$ {{ number_format($fatura->valor_desconto, 2, ',', '.') }}
 </p>
 @endif
 <p class="mb-0">
 <strong class="fs-5">Valor Total:</strong><br>
 <span class="fs-4 text-primary fw-bold">
 R$ {{ number_format($fatura->valor_total, 2, ',', '.') }}
 </span>
 </p>
 </div>
 </div>
 @if($fatura->descricao)
 <hr>
 <div class="mb-3">
 <h6 class="text-muted mb-2">Descrição</h6>
 <p class="mb-0">{{ $fatura->descricao }}</p>
 </div>
 @endif
 @if($fatura->forma_pagamento)
 <hr>
 <div class="mb-3">
 <h6 class="text-muted mb-2">Forma de Pagamento</h6>
 <p class="mb-0">
 @if($fatura->forma_pagamento === 'boleto')
 <i class="fas fa-barcode me-1"></i> Boleto Bancário
 @elseif($fatura->forma_pagamento === 'cartao')
 <i class="fas fa-credit-card me-1"></i> Cartão de Crédito
 @elseif($fatura->forma_pagamento === 'pix')
 <i class="fas fa-qrcode me-1"></i> PIX
 @elseif($fatura->forma_pagamento === 'transferencia')
 <i class="fas fa-exchange-alt me-1"></i> Transferência Bancária
 @else
 {{ ucfirst($fatura->forma_pagamento) }}
 @endif
 </p>
 </div>
 @endif
 @if($fatura->observacoes)
 <hr>
 <div class="mb-3">
 <h6 class="text-muted mb-2">Observações</h6>
 <p class="mb-0">{{ $fatura->observacoes }}</p>
 </div>
 @endif
 @if($fatura->status === 'cancelado' && $fatura->observacoes)
 <hr>
 <div class="alert alert-danger mb-0">
 <h6 class="alert-heading">
 <i class="fas fa-ban me-1"></i> Motivo do Cancelamento
 </h6>
 <p class="mb-0">{{ $fatura->observacoes }}</p>
 </div>
 @endif
 </div>
 </div>
 </div>
 <!-- Ações e Informações Adicionais -->
 <div class="col-lg-4">
 <!-- Ações -->
 @if($fatura->status !== 'cancelado' && $fatura->status !== 'pago')
 <div class="card shadow-sm mb-4">
 <div class="card-header bg-success text-white">
 <h5 class="mb-0">
 <i class="fas fa-cog me-2"></i>
 Ações
 </h5>
 </div>
 <div class="card-body">
 <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modalMarcarPago">
 <i class="fas fa-check-circle me-1"></i>
 Marcar como Pago
 </button>
 <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#modalCancelar">
 <i class="fas fa-times-circle me-1"></i>
 Cancelar Fatura
 </button>
 </div>
 </div>
 @endif
 <!-- Histórico -->
 <div class="card shadow-sm">
 <div class="card-header bg-info text-white">
 <h5 class="mb-0">
 <i class="fas fa-history me-2"></i>
 Histórico
 </h5>
 </div>
 <div class="card-body">
 <div class="timeline">
 <div class="timeline-item mb-3">
 <div class="timeline-marker bg-primary"></div>
 <div class="timeline-content">
 <small class="text-muted">
 {{ \Carbon\Carbon::parse($fatura->created_at)->format('d/m/Y H:i') }}
 </small>
 <p class="mb-0">Fatura criada</p>
 </div>
 </div>
 @if($fatura->data_pagamento)
 <div class="timeline-item mb-3">
 <div class="timeline-marker bg-success"></div>
 <div class="timeline-content">
 <small class="text-muted">
 {{ \Carbon\Carbon::parse($fatura->data_pagamento)->format('d/m/Y H:i') }}
 </small>
 <p class="mb-0">
 Pagamento realizado
 @if($fatura->forma_pagamento)
 via {{ ucfirst($fatura->forma_pagamento) }}
 @endif
 </p>
 </div>
 </div>
 @endif
 @if($fatura->status === 'cancelado')
 <div class="timeline-item mb-3">
 <div class="timeline-marker bg-danger"></div>
 <div class="timeline-content">
 <small class="text-muted">
 {{ \Carbon\Carbon::parse($fatura->updated_at)->format('d/m/Y H:i') }}
 </small>
 <p class="mb-0">Fatura cancelada</p>
 </div>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
<!-- Modal Marcar como Pago -->
<div class="modal fade" id="modalMarcarPago" tabindex="-1" aria-labelledby="modalMarcarPagoLabel" aria-hidden="true">
 <div class="modal-dialog">
 <div class="modal-content">
 <div class="modal-header bg-success text-white">
 <h5 class="modal-title" id="modalMarcarPagoLabel">
 <i class="fas fa-check-circle me-2"></i>
 Marcar como Pago
 </h5>
 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <form action="{{ route('admin.financeiro.pagar', $fatura->id) }}" method="POST">
 @csrf
 <div class="modal-body">
 <div class="mb-3">
 <label for="data_pagamento" class="form-label">Data do Pagamento *</label>
 <input type="datetime-local" class="form-control" id="data_pagamento" name="data_pagamento" value="{{ now()->format('Y-m-d\TH:i') }}" required>
 </div>
 <div class="mb-3">
 <label for="forma_pagamento" class="form-label">Forma de Pagamento *</label>
 <select class="form-select" id="forma_pagamento" name="forma_pagamento" required>
 <option value="">Selecione...</option>
 <option value="boleto">Boleto Bancário</option>
 <option value="cartao">Cartão de Crédito</option>
 <option value="pix">PIX</option>
 <option value="transferencia">Transferência Bancária</option>
 </select>
 </div>
 <div class="mb-3">
 <label for="observacoes" class="form-label">Observações</label>
 <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Adicione observações sobre o pagamento (opcional)"></textarea>
 </div>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
 <button type="submit" class="btn btn-success">
 <i class="fas fa-check me-1"></i>
 Confirmar Pagamento
 </button>
 </div>
 </form>
 </div>
 </div>
</div>
<!-- Modal Cancelar -->
<div class="modal fade" id="modalCancelar" tabindex="-1" aria-labelledby="modalCancelarLabel" aria-hidden="true">
 <div class="modal-dialog">
 <div class="modal-content">
 <div class="modal-header bg-danger text-white">
 <h5 class="modal-title" id="modalCancelarLabel">
 <i class="fas fa-times-circle me-2"></i>
 Cancelar Fatura
 </h5>
 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <form action="{{ route('admin.financeiro.cancelar', $fatura->id) }}" method="POST">
 @csrf
 <div class="modal-body">
 <div class="alert alert-warning">
 <i class="fas fa-exclamation-triangle me-1"></i>
 Esta ação não pode ser desfeita. A fatura será cancelada permanentemente.
 </div>
 <div class="mb-3">
 <label for="motivo_cancelamento" class="form-label">Motivo do Cancelamento *</label>
 <textarea class="form-control" id="motivo_cancelamento" name="motivo_cancelamento" rows="4" placeholder="Informe o motivo do cancelamento desta fatura" required></textarea>
 </div>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
 <button type="submit" class="btn btn-danger">
 <i class="fas fa-times me-1"></i>
 Confirmar Cancelamento
 </button>
 </div>
 </form>
 </div>
 </div>
</div>
<style>
.timeline {
 position: relative;
 padding-left: 30px;
}
.timeline-item {
 position: relative;
}
.timeline-marker {
 position: absolute;
 left: -30px;
 top: 5px;
 width: 12px;
 height: 12px;
 border-radius: 50%;
 border: 2px solid #fff;
}
.timeline-item:not(:last-child)::before {
 content: '';
 position: absolute;
 left: -25px;
 top: 17px;
 bottom: -20px;
 width: 2px;
 background: #dee2e6;
}
</style>
@endsection