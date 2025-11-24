@extends('layouts.app')
@section('title', 'Histórico de Caixas')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-history me-2"></i>
 Histórico de Caixas
 </h1>
 <p class="page-subtitle">Consulte o histórico de aberturas e fechamentos</p>
 </div>
 <a href="{{ route('caixa.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 <!-- Filtros -->
 <div class="card shadow-sm mb-4">
 <div class="card-body">
 <form method="GET" class="row g-3">
 <div class="col-md-3">
 <label for="data_inicio" class="form-label">Data Início</label>
 <input type="date" 
 class="form-control" 
 id="data_inicio" 
 name="data_inicio"
 value="{{ request('data_inicio') }}">
 </div>
 <div class="col-md-3">
 <label for="data_fim" class="form-label">Data Fim</label>
 <input type="date" 
 class="form-control" 
 id="data_fim" 
 name="data_fim"
 value="{{ request('data_fim') }}">
 </div>
 <div class="col-md-3">
 <label for="status" class="form-label">Status</label>
 <select class="form-select" id="status" name="status">
 <option value="">Todos</option>
 <option value="aberto" {{ request('status') == 'aberto' ? 'selected' : '' }}>Aberto</option>
 <option value="fechado" {{ request('status') == 'fechado' ? 'selected' : '' }}>Fechado</option>
 </select>
 </div>
 <div class="col-md-3 d-flex align-items-end">
 <button type="submit" class="btn btn-primary w-100">
 <i class="fas fa-search me-2"></i>
 Filtrar
 </button>
 </div>
 </form>        </div>
 </div>
 <!-- Estatísticas Gerais -->
 @if(isset($totaisGerais) && $totaisGerais['quantidade_caixas'] > 0)
 <div class="row g-3 mb-4">
 <!-- Card: Caixas -->
 <div class="col-md-2">
 <div class="card stat-card h-100 border-0 shadow-sm">
 <div class="card-body text-center">
 <div class="stat-icon bg-primary bg-opacity-10 rounded-circle mx-auto mb-2">
 <i class="fas fa-cash-register text-primary"></i>
 </div>
 <h3 class="stat-value mb-1">{{ $totaisGerais['quantidade_caixas'] }}</h3>
 <small class="stat-label text-muted">Caixas</small>
 </div>
 </div>
 </div>
 <!-- Card: Total Vendas -->
 <div class="col-md-2">
 <div class="card stat-card h-100 border-0 shadow-sm">
 <div class="card-body text-center">
 <div class="stat-icon bg-success bg-opacity-10 rounded-circle mx-auto mb-2">
 <i class="fas fa-dollar-sign text-success"></i>
 </div>                    <h4 class="stat-value mb-1">R$ {{ number_format($totaisGerais['total_vendas'], 2, ',', '.') }}</h4>
 <small class="stat-label text-muted d-block">Total Vendas</small>
 <small class="badge bg-success bg-opacity-10 text-success mt-1">
 {{ $totaisGerais['quantidade_vendas'] }} vendas
 </small>
 </div>
 </div>
 </div>
 <!-- Card: Dinheiro -->
 <div class="col-md-2">
 <div class="card stat-card h-100 border-0 shadow-sm">
 <div class="card-body text-center">
 <div class="stat-icon bg-success bg-opacity-10 rounded-circle mx-auto mb-2">
 <i class="fas fa-money-bill-wave text-success"></i>
 </div>                    <h5 class="stat-value mb-1">R$ {{ number_format($totaisGerais['total_dinheiro'], 2, ',', '.') }}</h5>
 <small class="stat-label text-muted d-block">Dinheiro</small>
 @php
 $percDinheiro = $totaisGerais['total_vendas'] > 0 
 ? ($totaisGerais['total_dinheiro'] / $totaisGerais['total_vendas']) * 100 
 : 0;
 @endphp
 <small class="badge bg-success bg-opacity-10 text-success mt-1">
 {{ number_format($percDinheiro, 1) }}%
 </small>
 </div>
 </div>
 </div>
 <!-- Card: Cartões (com detalhamento) -->
 <div class="col-md-2">
 <div class="card stat-card h-100 border-0 shadow-sm">
 <div class="card-body text-center">
 <div class="stat-icon bg-primary bg-opacity-10 rounded-circle mx-auto mb-2">
 <i class="fas fa-credit-card text-primary"></i>
 </div>                    <h5 class="stat-value mb-1">R$ {{ number_format($totaisGerais['total_cartao'], 2, ',', '.') }}</h5>
 <small class="stat-label text-muted d-block">Cartões Total</small>
 <div class="mt-2 pt-2 border-top">
 <div class="d-flex justify-content-between align-items-center mb-1">
 <small class="text-muted">
 <i class="fas fa-credit-card text-primary me-1"></i>Crédito
 </small>
 <small class="fw-bold">R$ {{ number_format($totaisGerais['total_cartao_credito'], 2, ',', '.') }}</small>
 </div>
 <div class="d-flex justify-content-between align-items-center">
 <small class="text-muted">
 <i class="fas fa-money-check text-info me-1"></i>Débito
 </small>
 <small class="fw-bold">R$ {{ number_format($totaisGerais['total_cartao_debito'], 2, ',', '.') }}</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Card: PIX -->
 <div class="col-md-2">
 <div class="card stat-card h-100 border-0 shadow-sm">
 <div class="card-body text-center">
 <div class="stat-icon bg-info bg-opacity-10 rounded-circle mx-auto mb-2">
 <i class="fas fa-qrcode text-info"></i>
 </div>                    <h5 class="stat-value mb-1">R$ {{ number_format($totaisGerais['total_pix'], 2, ',', '.') }}</h5>
 <small class="stat-label text-muted d-block">PIX</small>
 @php
 $percPix = $totaisGerais['total_vendas'] > 0 
 ? ($totaisGerais['total_pix'] / $totaisGerais['total_vendas']) * 100 
 : 0;
 @endphp
 <small class="badge bg-info bg-opacity-10 text-info mt-1">
 {{ number_format($percPix, 1) }}%
 </small>
 </div>
 </div>
 </div>
 <!-- Card: Vale Refeição -->
 <div class="col-md-2">
 <div class="card stat-card h-100 border-0 shadow-sm">
 <div class="card-body text-center">
 <div class="stat-icon bg-warning bg-opacity-10 rounded-circle mx-auto mb-2">
 <i class="fas fa-ticket-alt text-warning"></i>
 </div>                    <h5 class="stat-value mb-1">R$ {{ number_format($totaisGerais['total_vale'], 2, ',', '.') }}</h5>
 <small class="stat-label text-muted d-block">Vale Refeição</small>
 @php
 $percVale = $totaisGerais['total_vendas'] > 0 
 ? ($totaisGerais['total_vale'] / $totaisGerais['total_vendas']) * 100 
 : 0;
 @endphp
 <small class="badge bg-warning bg-opacity-10 text-warning mt-1">
 {{ number_format($percVale, 1) }}%
 </small>
 </div>
 </div>        </div>
 </div>
 @endif
 <!-- Lista de Caixas -->
 <div class="card shadow-sm">
 <div class="card-body">
 @if($caixas->count() > 0)
 <div class="caixas-list">
 @foreach($caixas as $caixa)
 <div class="caixa-card">
 <div class="caixa-header">
 <div class="caixa-id">
 <strong>#{{ $caixa->id }}</strong>
 </div>
 <div class="caixa-operador">
 <i class="fas fa-user-circle me-2"></i>
 <span>{{ $caixa->usuario->nome ?? 'Sistema' }}</span>
 </div>
 <div class="caixa-status">
 @if($caixa->status == 'aberto')
 <span class="badge bg-success">
 <i class="fas fa-unlock me-1"></i>Aberto
 </span>
 @else
 <span class="badge bg-secondary">
 <i class="fas fa-lock me-1"></i>Fechado
 </span>
 @endif
 </div>
 </div>
 <div class="caixa-body">
 <div class="row g-3">
 <!-- Coluna Esquerda: Info Geral -->
 <div class="col-md-4">
 <div class="info-group">
 <label><i class="fas fa-calendar me-1"></i> Período</label>
 <div class="info-value">
 <strong>{{ $caixa->data_abertura->format('d/m/Y') }}</strong>
 <small class="text-muted d-block">
 {{ $caixa->data_abertura->format('H:i') }} 
 <i class="fas fa-arrow-right mx-1"></i>
 {{ $caixa->data_fechamento ? $caixa->data_fechamento->format('H:i') : '...' }}
 </small>
 </div>
 </div>
 <div class="info-group mt-3">
 <label><i class="fas fa-wallet me-1"></i> Valor Inicial</label>
 <div class="info-value">
 <span class="badge bg-secondary">
 R$ {{ number_format($caixa->valor_abertura ?? 0, 2, ',', '.') }}
 </span>
 </div>
 </div>
 <div class="info-group mt-3">
 <label><i class="fas fa-shopping-cart me-1"></i> Total Vendas</label>
 <div class="info-value">
 <strong class="text-success">
 R$ {{ number_format($caixa->total_vendas_real ?? 0, 2, ',', '.') }}
 </strong>
 <small class="text-muted d-block">
 <i class="fas fa-receipt me-1"></i>{{ $caixa->quantidade_vendas ?? 0 }} vendas
 </small>
 </div>
 </div>
 </div>
 <!-- Coluna Direita: Formas de Pagamento -->
 <div class="col-md-8">
 <div class="pagamentos-grid">
 <!-- Dinheiro -->
 <div class="pagamento-item">
 <div class="pagamento-icon bg-success">
 <i class="fas fa-money-bill-wave"></i>
 </div>
 <div class="pagamento-info">
 <small class="pagamento-label">Dinheiro</small>
 <strong class="pagamento-valor">
 R$ {{ number_format($caixa->total_dinheiro_real ?? 0, 2, ',', '.') }}
 </strong>
 </div>
 </div>
 <!-- Cartões -->
 <div class="pagamento-item pagamento-cartao">
 <div class="pagamento-icon bg-primary">
 <i class="fas fa-credit-card"></i>
 </div>
 <div class="pagamento-info">
 <small class="pagamento-label">Cartões</small>
 <strong class="pagamento-valor">
 R$ {{ number_format($caixa->total_cartao_real ?? 0, 2, ',', '.') }}
 </strong>
 <div class="pagamento-breakdown">
 <span class="breakdown-item">
 <i class="fas fa-credit-card text-primary"></i>
 Créd: {{ number_format($caixa->total_cartao_credito_real ?? 0, 2, ',', '.') }}
 </span>
 <span class="breakdown-item">
 <i class="fas fa-money-check text-info"></i>
 Déb: {{ number_format($caixa->total_cartao_debito_real ?? 0, 2, ',', '.') }}
 </span>
 </div>
 </div>
 </div>
 <!-- PIX -->
 <div class="pagamento-item">
 <div class="pagamento-icon bg-info">
 <i class="fas fa-qrcode"></i>
 </div>
 <div class="pagamento-info">
 <small class="pagamento-label">PIX</small>
 <strong class="pagamento-valor">
 R$ {{ number_format($caixa->total_pix_real ?? 0, 2, ',', '.') }}
 </strong>
 </div>
 </div>
 <!-- Vale Refeição -->
 <div class="pagamento-item">
 <div class="pagamento-icon bg-warning">
 <i class="fas fa-ticket-alt"></i>
 </div>
 <div class="pagamento-info">
 <small class="pagamento-label">Vale Refeição</small>
 <strong class="pagamento-valor">
 R$ {{ number_format($caixa->total_vale_real ?? 0, 2, ',', '.') }}
 </strong>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="caixa-footer">
 @if($caixa->status == 'aberto')
 <a href="{{ route('caixa.relatorio', $caixa) }}" class="btn btn-sm btn-outline-primary">
 <i class="fas fa-file-alt me-1"></i> Ver Relatório
 </a>
 <form action="{{ route('caixa.fechar', $caixa) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja finalizar este caixa?')">
 @csrf
 @method('PUT')
 <button type="submit" class="btn btn-sm btn-success">
 <i class="fas fa-lock me-1"></i> Finalizar Caixa
 </button>
 </form>
 @else
 <a href="{{ route('caixa.relatorio', $caixa) }}" class="btn btn-sm btn-primary">
 <i class="fas fa-file-alt me-1"></i> Ver Relatório
 </a>
 @endif
 </div>
 </div>
 @endforeach
 </div>
 <!-- Paginação -->
 @if($caixas->hasPages())
 <div class="mt-4">
 {{ $caixas->links() }}
 </div>
 @endif
 @else
 <div class="text-center py-5">
 <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
 <p class="text-muted mt-3 mb-0">Nenhum registro encontrado</p>
 </div>
 @endif
 </div>
 </div>
</div>
@endsection
@push('scripts')
<script>
 document.addEventListener('DOMContentLoaded', function() {
 var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
 var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
 return new bootstrap.Tooltip(tooltipTriggerEl);
 });
 });
</script>
@endpush
@push('styles')
<style>
 .stat-card {
 transition: all 0.3s ease;
 border: 1px solid rgba(0, 0, 0, 0.05);
 overflow: hidden;
 position: relative;
 }
 .stat-card::before {
 content: '';
 position: absolute;
 top: 0;
 left: 0;
 right: 0;
 height: 3px;
 background: linear-gradient(90deg, 
 rgba(13, 110, 253, 0.8) 0%, 
 rgba(13, 202, 240, 0.8) 100%);
 transform: scaleX(0);
 transition: transform 0.3s ease;
 }
 .stat-card:hover {
 transform: translateY(-5px);
 box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
 }
 .stat-card:hover::before {
 transform: scaleX(1);
 }
 .stat-icon {
 width: 50px;
 height: 50px;
 display: flex;
 align-items: center;
 justify-content: center;
 transition: transform 0.3s ease;
 }
 .stat-card:hover .stat-icon {
 transform: scale(1.1) rotate(5deg);
 }
 .stat-icon i {
 font-size: 1.5rem;
 }
 .stat-value {
 font-weight: 700;
 margin: 0;
 line-height: 1.3;
 color: #212529 !important;
 font-size: 1.1rem;
 }
 .stat-label {
 font-size: 0.75rem;
 text-transform: uppercase;
 letter-spacing: 0.5px;
 font-weight: 600;
 opacity: 0.9;
 color: #6c757d !important;
 display: block;
 margin-top: 0.25rem;
 }
 .stat-card .border-top {
 border-color: rgba(0, 0, 0, 0.1) !important;
 }
 .stat-card .fw-bold,
 .stat-card .text-muted,
 .stat-card small {
 color: inherit !important;
 opacity: 1 !important;
 }
 .stat-card .d-flex small {
 font-size: 0.7rem !important;
 line-height: 1.4;
 }
 .caixas-list {
 display: flex;
 flex-direction: column;
 gap: 1.5rem;
 }
 .caixa-card {
 background: white;
 border-radius: 12px;
 box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
 overflow: hidden;
 transition: all 0.3s ease;
 }
 .caixa-card:hover {
 box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
 transform: translateY(-2px);
 }
 .caixa-header {
 display: flex;
 align-items: center;
 justify-content: space-between;
 padding: 1.25rem 1.5rem;
 background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
 border-bottom: 1px solid #dee2e6;
 }
 .caixa-id {
 font-size: 1.25rem;
 color: #0d6efd;
 }
 .caixa-operador {
 display: flex;
 align-items: center;
 flex: 1;
 padding: 0 1.5rem;
 font-size: 1rem;
 color: #212529;
 }
 .caixa-operador i {
 font-size: 1.5rem;
 color: #6c757d;
 }
 .caixa-body {
 padding: 1.5rem;
 }
 .info-group {
 margin-bottom: 0.75rem;
 }
 .info-group label {
 display: block;
 font-size: 0.75rem;
 text-transform: uppercase;
 letter-spacing: 0.5px;
 color: #6c757d;
 margin-bottom: 0.5rem;
 font-weight: 600;
 }
 .info-value {
 font-size: 1rem;
 }
 .info-value strong {
 font-size: 1.1rem;
 }
 .pagamentos-grid {
 display: grid;
 grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
 gap: 1rem;
 }
 .pagamento-item {
 display: flex;
 align-items: flex-start;
 gap: 0.75rem;
 padding: 1rem;
 background: #f8f9fa;
 border-radius: 8px;
 transition: all 0.2s ease;
 }
 .pagamento-item:hover {
 background: #e9ecef;
 }
 .pagamento-icon {
 width: 40px;
 height: 40px;
 border-radius: 8px;
 display: flex;
 align-items: center;
 justify-content: center;
 color: white;
 font-size: 1.25rem;
 flex-shrink: 0;
 }
 .pagamento-info {
 flex: 1;
 min-width: 0;
 }
 .pagamento-label {
 display: block;
 font-size: 0.7rem;
 text-transform: uppercase;
 letter-spacing: 0.5px;
 color: #6c757d;
 margin-bottom: 0.25rem;
 font-weight: 600;
 }
 .pagamento-valor {
 display: block;
 font-size: 1rem;
 color: #212529;
 font-weight: 700;
 }
 .pagamento-breakdown {
 display: flex;
 flex-direction: column;
 gap: 0.25rem;
 margin-top: 0.5rem;
 font-size: 0.75rem;
 color: #6c757d;
 }
 .breakdown-item {
 display: flex;
 align-items: center;
 gap: 0.25rem;
 }
 .breakdown-item i {
 font-size: 0.7rem;
 }
 .caixa-footer {
 padding: 1rem 1.5rem;
 background: #f8f9fa;
 border-top: 1px solid #dee2e6;
 display: flex;
 justify-content: flex-end;
 }
 .badge {
 font-weight: 600;
 padding: 0.5rem 0.75rem;
 font-size: 0.875rem;
 }
 @keyframes fadeInUp {
 from {
 opacity: 0;
 transform: translateY(20px);
 }
 to {
 opacity: 1;
 transform: translateY(0);
 }
 }
 .stat-card {
 animation: fadeInUp 0.5s ease backwards;
 }
 .stat-card:nth-child(1) { animation-delay: 0.05s; }
 .stat-card:nth-child(2) { animation-delay: 0.1s; }
 .stat-card:nth-child(3) { animation-delay: 0.15s; }
 .stat-card:nth-child(4) { animation-delay: 0.2s; }
 .stat-card:nth-child(5) { animation-delay: 0.25s; }
 .stat-card:nth-child(6) { animation-delay: 0.3s; }
 @media (max-width: 1400px) {
 .stat-value {
 font-size: 1rem;
 }
 }
 @media (max-width: 992px) {
 .stat-icon {
 width: 40px;
 height: 40px;
 }
 .stat-icon i {
 font-size: 1.25rem;
 }
 }
 @media (max-width: 768px) {
 .caixa-header {
 flex-direction: column;
 gap: 1rem;
 align-items: stretch;
 }
 .caixa-operador {
 padding: 0;
 }
 .pagamentos-grid {
 grid-template-columns: 1fr;
 }
 .caixa-body .row {
 flex-direction: column;
 }
 .stat-value {
 font-size: 0.9rem;
 }
 .stat-label {
 font-size: 0.7rem;
 }
 .col-md-2 {
 flex: 0 0 50%;
 max-width: 50%;
 }
 }
 @media (max-width: 576px) {
 .caixa-card {
 border-radius: 8px;
 }
 .caixa-header,
 .caixa-body,
 .caixa-footer {
 padding: 1rem;
 }
 .pagamento-item {
 padding: 0.75rem;
 }
 .pagamento-icon {
 width: 35px;
 height: 35px;
 font-size: 1rem;
 }
 .page-header h1 {
 font-size: 1.5rem;
 }
 .page-subtitle {
 font-size: 0.85rem;
 }
 .stat-card {
 margin-bottom: 0.5rem;
 }
 .col-md-2 {
 flex: 0 0 100%;
 max-width: 100%;
 }
 }
 @media print {
 .btn, .page-header .btn-outline-secondary {
 display: none !important;
 }
 .card {
 box-shadow: none !important;
 border: 1px solid #dee2e6 !important;
 }
 .stat-card:hover, .caixa-card:hover {
 transform: none !important;
 }
 }
</style>
@endpush