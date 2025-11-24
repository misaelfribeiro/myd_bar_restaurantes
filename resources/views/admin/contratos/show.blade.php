@extends('layouts.app')
@section('title', 'Detalhes do Contrato')
@section('content')
<div class="container-fluid px-4">
 <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-1">📄 Contrato {{ $contrato->numero_contrato }}</h1>
 <p class="text-muted mb-0">Informações completas do contrato</p>
 </div>
 <a href="{{ route('admin.contratos.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-2"></i>Voltar
 </a>
 </div>
 <div class="row">
 <!-- Coluna Principal -->
 <div class="col-lg-8">
 <!-- Informações do Contrato -->
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-header bg-white py-3">
 <h5 class="mb-0">Informações do Contrato</h5>
 </div>
 <div class="card-body">
 <div class="row g-3">
 <div class="col-md-6">
 <strong class="text-muted d-block mb-1">Empresa</strong>
 <p class="mb-0">
 {{ $contrato->empresa->nome_fantasia }}
 <br><small class="text-muted">{{ $contrato->empresa->razao_social }}</small>
 </p>
 </div>
 <div class="col-md-6">
 <strong class="text-muted d-block mb-1">Plano Contratado</strong>
 <p class="mb-0">
 <span class="badge 
 @if($contrato->plano->codigo == 'enterprise') bg-primary
 @elseif($contrato->plano->codigo == 'premium') bg-success
 @elseif($contrato->plano->codigo == 'profissional') bg-info
 @else bg-secondary
 @endif fs-6">
 {{ $contrato->plano->nome }}
 </span>
 </p>
 </div>
 <div class="col-md-3">
 <strong class="text-muted d-block mb-1">Data Início</strong>
 <p class="mb-0">{{ $contrato->data_inicio->format('d/m/Y') }}</p>
 </div>
 <div class="col-md-3">
 <strong class="text-muted d-block mb-1">Data Fim</strong>
 <p class="mb-0">{{ $contrato->data_fim->format('d/m/Y') }}</p>
 </div>
 <div class="col-md-3">
 <strong class="text-muted d-block mb-1">Tipo Pagamento</strong>
 <p class="mb-0">{{ strtoupper($contrato->tipo_pagamento) }}</p>
 </div>
 <div class="col-md-3">
 <strong class="text-muted d-block mb-1">Status</strong>
 <p class="mb-0">
 <span class="badge 
 @if($contrato->status == 'ativo') bg-success
 @elseif($contrato->status == 'trial') bg-warning
 @elseif($contrato->status == 'suspenso') bg-danger
 @else bg-secondary
 @endif">
 {{ strtoupper($contrato->status) }}
 </span>
 </p>
 </div>
 <div class="col-12">
 <hr>
 </div>
 <div class="col-md-4">
 <strong class="text-muted d-block mb-1">Valor Contratado</strong>
 <p class="mb-0">R$ {{ number_format($contrato->valor_contratado, 2, ',', '.') }}</p>
 </div>
 <div class="col-md-4">
 <strong class="text-muted d-block mb-1">Desconto Aplicado</strong>
 <p class="mb-0 text-danger">- R$ {{ number_format($contrato->desconto_aplicado, 2, ',', '.') }}</p>
 </div>
 <div class="col-md-4">
 <strong class="text-muted d-block mb-1">Valor Final</strong>
 <p class="mb-0"><strong class="text-success fs-5">R$ {{ number_format($contrato->valor_final, 2, ',', '.') }}</strong></p>
 </div>
 @if($contrato->observacoes)
 <div class="col-12">
 <strong class="text-muted d-block mb-1">Observações</strong>
 <p class="mb-0">{{ $contrato->observacoes }}</p>
 </div>
 @endif
 </div>
 </div>
 </div>
 <!-- Limites do Plano -->
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-header bg-white py-3">
 <h5 class="mb-0">Limites Contratados</h5>
 </div>
 <div class="card-body">
 <div class="row g-3">
 <div class="col-md-3">
 <div class="text-center">
 <i class="fas fa-users fa-2x text-primary mb-2"></i>
 <p class="mb-0"><strong>{{ $contrato->max_usuarios }}</strong></p>
 <small class="text-muted">Usuários</small>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center">
 <i class="fas fa-box fa-2x text-success mb-2"></i>
 <p class="mb-0"><strong>{{ $contrato->max_produtos }}</strong></p>
 <small class="text-muted">Produtos</small>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center">
 <i class="fas fa-shopping-cart fa-2x text-info mb-2"></i>
 <p class="mb-0"><strong>{{ $contrato->max_pedidos_mes }}</strong></p>
 <small class="text-muted">Pedidos/Mês</small>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center">
 <i class="fas fa-store fa-2x text-warning mb-2"></i>
 <p class="mb-0"><strong>{{ $contrato->max_filiais }}</strong></p>
 <small class="text-muted">Filiais</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Histórico -->
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-header bg-white py-3">
 <h5 class="mb-0">Histórico de Alterações</h5>
 </div>
 <div class="card-body">
 <div class="timeline">
 @forelse($contrato->historico as $item)
 <div class="timeline-item mb-3">
 <div class="d-flex">
 <div class="flex-shrink-0">
 <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
 <i class="fas fa-{{ $item->acao == 'criado' ? 'plus' : ($item->acao == 'renovado' ? 'redo' : 'edit') }}"></i>
 </div>
 </div>
 <div class="flex-grow-1 ms-3">
 <strong>{{ ucfirst($item->acao) }}</strong>
 <p class="mb-1">{{ $item->descricao }}</p>
 <small class="text-muted">
 {{ $item->created_at->format('d/m/Y H:i') }}
 @if($item->usuario)
 por {{ $item->usuario->name }}
 @endif
 </small>
 </div>
 </div>
 </div>
 @empty
 <p class="text-muted text-center py-3">Nenhum histórico disponível</p>
 @endforelse
 </div>
 </div>
 </div>
 </div>
 <!-- Coluna Lateral -->
 <div class="col-lg-4">
 <!-- Ações -->
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-header bg-white py-3">
 <h5 class="mb-0">Ações</h5>
 </div>
 <div class="card-body">
 @if($contrato->status == 'ativo')
 <button class="btn btn-success w-100 mb-2" onclick="renovarContrato()">
 <i class="fas fa-redo me-2"></i>Renovar Contrato
 </button>
 <button class="btn btn-warning w-100 mb-2" onclick="suspenderContrato()">
 <i class="fas fa-pause me-2"></i>Suspender
 </button>
 <button class="btn btn-danger w-100" onclick="cancelarContrato()">
 <i class="fas fa-times me-2"></i>Cancelar
 </button>
 @elseif($contrato->status == 'suspenso')
 <button class="btn btn-success w-100" onclick="reativarContrato()">
 <i class="fas fa-play me-2"></i>Reativar Contrato
 </button>
 @endif
 </div>
 </div>
 <!-- Documentos -->
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-header bg-white py-3">
 <h5 class="mb-0">Documentos</h5>
 </div>
 <div class="card-body">
 @if($contrato->documento_assinado)
 <a href="{{ asset('storage/' . $contrato->documento_assinado) }}" 
 class="btn btn-outline-primary w-100 mb-2" target="_blank">
 <i class="fas fa-file-pdf me-2"></i>Contrato Assinado
 </a>
 @endif
 @if($contrato->documento_identidade)
 <a href="{{ asset('storage/' . $contrato->documento_identidade) }}" 
 class="btn btn-outline-primary w-100 mb-2" target="_blank">
 <i class="fas fa-id-card me-2"></i>Documento Identidade
 </a>
 @endif
 @if($contrato->comprovante_endereco)
 <a href="{{ asset('storage/' . $contrato->comprovante_endereco) }}" 
 class="btn btn-outline-primary w-100" target="_blank">
 <i class="fas fa-file-alt me-2"></i>Comprovante Endereço
 </a>
 @endif
 @if(!$contrato->documento_assinado && !$contrato->documento_identidade && !$contrato->comprovante_endereco)
 <p class="text-muted text-center mb-0">Nenhum documento anexado</p>
 @endif
 </div>
 </div>
 <!-- Faturas -->
 <div class="card border-0 shadow-sm">
 <div class="card-header bg-white py-3">
 <h5 class="mb-0">Faturas</h5>
 </div>
 <div class="card-body">
 @forelse($contrato->faturas as $fatura)
 <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
 <div>
 <strong>{{ $fatura->numero_fatura }}</strong>
 <br><small class="text-muted">Venc: {{ $fatura->data_vencimento->format('d/m/Y') }}</small>
 </div>
 <span class="badge 
 @if($fatura->status == 'pago') bg-success
 @elseif($fatura->status == 'vencido') bg-danger
 @else bg-warning
 @endif">
 {{ strtoupper($fatura->status) }}
 </span>
 </div>
 @empty
 <p class="text-muted text-center mb-0">Nenhuma fatura gerada</p>
 @endforelse
 </div>
 </div>
 </div>
 </div>
</div>
<script>
</script>
@endsection