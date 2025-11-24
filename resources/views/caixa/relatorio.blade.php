@extends('layouts.app')
@section('title', 'Relatório de Caixa')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-file-alt me-2"></i>
 Relatório de Caixa #{{ $caixa->id }}
 </h1>
 <p class="page-subtitle">
 Período: {{ $caixa->data_abertura->format('d/m/Y H:i') }} - 
 {{ $caixa->data_fechamento ? $caixa->data_fechamento->format('d/m/Y H:i') : 'Em andamento' }}
 </p>
 </div>
 <div class="d-flex gap-2">
 <button onclick="window.print()" class="btn btn-outline-primary">
 <i class="fas fa-print me-2"></i>
 Imprimir
 </button>
 <a href="{{ route('caixa.historico') }}" class="btn btn-outline-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 </div>
 <!-- Resumo Geral -->
 <div class="row g-3 mb-4">
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-success">
 <i class="fas fa-dollar-sign"></i>
 </div>
 <div class="stats-content">
 <div class="stats-value">
 R$ {{ number_format($caixa->valor_abertura, 2, ',', '.') }}
 </div>
 <div class="stats-label">Valor Abertura</div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-primary">
 <i class="fas fa-cash-register"></i>
 </div>
 <div class="stats-content">
 <div class="stats-value">
 R$ {{ number_format($totalVendas, 2, ',', '.') }}
 </div>
 <div class="stats-label">Total Vendas</div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-info">
 <i class="fas fa-receipt"></i>
 </div>
 <div class="stats-content">
 <div class="stats-value">
 {{ $quantidadeVendas }}
 </div>
 <div class="stats-label">Nº de Vendas</div>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="stats-card">
 <div class="stats-icon bg-danger">
 <i class="fas fa-coins"></i>
 </div>
 <div class="stats-content">
 <div class="stats-value">
 R$ {{ number_format($caixa->valor_fechamento ?? 0, 2, ',', '.') }}
 </div>
 <div class="stats-label">Valor Fechamento</div>
 </div>
 </div>
 </div>
 </div>
 <!-- Formas de Pagamento -->
 <div class="card shadow-sm mb-4">
 <div class="card-header">
 <h5 class="mb-0">
 <i class="fas fa-credit-card me-2"></i>
 Resumo por Forma de Pagamento
 </h5>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table">
 <thead>
 <tr>
 <th>Forma de Pagamento</th>
 <th class="text-center">Quantidade</th>
 <th class="text-end">Total</th>
 </tr>
 </thead>
 <tbody>
 @foreach($formasPagamento as $forma => $dados)
 <tr>
 <td>
 <i class="fas fa-{{ $forma == 'pix' ? 'qrcode' : 'credit-card' }} me-2"></i>
 {{ ucfirst(str_replace('_', ' ', $forma)) }}
 </td>
 <td class="text-center">
 <span class="badge bg-primary">{{ $dados['quantidade'] }}</span>
 </td>
 <td class="text-end">
 <strong class="text-success">
 R$ {{ number_format($dados['total'], 2, ',', '.') }}
 </strong>
 </td>
 </tr>
 @endforeach
 </tbody>
 <tfoot>
 <tr class="table-active">
 <th>TOTAL</th>
 <th class="text-center">
 {{ array_sum(array_column($formasPagamento, 'quantidade')) }}
 </th>
 <th class="text-end">
 R$ {{ number_format(array_sum(array_column($formasPagamento, 'total')), 2, ',', '.') }}
 </th>
 </tr>
 </tfoot>
 </table>
 </div>
 </div>
 </div>
 <!-- Informações Adicionais -->
 <div class="card shadow-sm">
 <div class="card-header">
 <h5 class="mb-0">
 <i class="fas fa-info-circle me-2"></i>
 Informações Adicionais
 </h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-6">
 <p><strong>Operador:</strong> {{ $caixa->usuario->nome ?? 'Sistema' }}</p>
 <p><strong>Status:</strong> 
 <span class="badge bg-{{ $caixa->status == 'aberto' ? 'success' : 'secondary' }}">
 {{ ucfirst($caixa->status) }}
 </span>
 </p>
 <p><strong>Data Abertura:</strong> {{ $caixa->data_abertura->format('d/m/Y H:i:s') }}</p>
 </div>
 <div class="col-md-6">
 @if($caixa->data_fechamento)
 <p><strong>Data Fechamento:</strong> {{ $caixa->data_fechamento->format('d/m/Y H:i:s') }}</p>
 <p><strong>Duração:</strong> {{ $caixa->data_abertura->diffForHumans($caixa->data_fechamento, true) }}</p>
 @else
 <p class="text-muted">Caixa ainda aberto</p>
 @endif
 </div>
 </div>
 @if($caixa->observacoes)
 <hr>
 <p><strong>Observações:</strong></p>
 <p class="text-muted">{{ $caixa->observacoes }}</p>
 @endif
 </div>
 </div>
</div>
@push('styles')
<style>
 @media print {
 .page-header .btn,
 .sidebar,
 .topbar {
 display: none !important;
 }
 .container-fluid {
 padding: 20px !important;
 }
 .card {
 break-inside: avoid;
 margin-bottom: 20px;
 }
 }
</style>
@endpush
@endsection