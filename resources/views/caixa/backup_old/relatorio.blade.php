<!DOCTYPE html>
<html lang="pt-BR">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Relatório de Caixa - MyD Bar & Restaurantes</title>
 <!-- Bootstrap CSS -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <!-- Bootstrap Icons -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
 <style>
 .relatorio-header {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 color: white;
 padding: 2rem 0;
 margin-bottom: 2rem;
 }
 .stat-card {
 border: none;
 border-radius: 15px;
 box-shadow: 0 5px 15px rgba(0,0,0,0.1);
 transition: transform 0.3s ease;
 }
 .stat-card:hover {
 transform: translateY(-5px);
 }
 .stat-icon {
 width: 60px;
 height: 60px;
 border-radius: 15px;
 display: flex;
 align-items: center;
 justify-content: center;
 font-size: 24px;
 color: white;
 }
 .bg-vendas { background: linear-gradient(135deg, #4CAF50, #45a049); }
 .bg-dinheiro { background: linear-gradient(135deg, #FF9800, #F57C00); }
 .bg-cartao { background: linear-gradient(135deg, #2196F3, #1976D2); }
 .bg-pix { background: linear-gradient(135deg, #9C27B0, #7B1FA2); }
 .table-hover tbody tr:hover {
 background-color: rgba(102, 126, 234, 0.1);
 }
 .print-button {
 position: fixed;
 bottom: 20px;
 right: 20px;
 z-index: 1000;
 }
 </style>
</head>
<body>
 <div class="relatorio-header">
 <div class="container">
 <div class="row align-items-center">
 <div class="col-md-8">
 <h1 class="mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>Relatório de Caixa</h1>                    <p class="mb-0 opacity-75">
 Caixa #{{ $caixa->id ?? 1 }} - 
 {{ $caixa->data_abertura ? $caixa->data_abertura->format('d/m/Y H:i') : '11/11/2025 08:00' }}
 @if($caixa->data_fechamento ?? false)
 até {{ $caixa->data_fechamento ? $caixa->data_fechamento->format('d/m/Y H:i') : '11/11/2025 18:00' }}
 @endif
 </p>
 </div>
 <div class="col-md-4 text-md-end">
 <span class="badge bg-light text-dark fs-6 px-3 py-2">
 <i class="bi bi-person me-1"></i>
 {{ $caixa->usuario->nome ?? 'Operador Teste' }}
 </span>
 </div>
 </div>
 </div>
 </div>
 <div class="container">        <!-- Resumo Financeiro -->
 <div class="row mb-4">
 <div class="col-md-3 mb-3">
 <div class="card stat-card h-100">
 <div class="card-body d-flex align-items-center">
 <div class="stat-icon bg-vendas me-3">
 <i class="bi bi-graph-up"></i>
 </div>                        <div>
 <h6 class="card-title mb-1 text-muted">Total de Vendas</h6>
 <h4 class="mb-0 text-success">R$ {{ number_format($totalizacoes['total_vendas'] ?? 0, 2, ',', '.') }}</h4>
 <small class="text-muted">{{ $totalizacoes['quantidade_vendas'] ?? 0 }} vendas</small>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3 mb-3">
 <div class="card stat-card h-100">
 <div class="card-body d-flex align-items-center">
 <div class="stat-icon bg-dinheiro me-3">
 <i class="bi bi-cash"></i>
 </div>                        <div>
 <h6 class="card-title mb-1 text-muted">Dinheiro</h6>
 <h4 class="mb-0">R$ {{ number_format($totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0, 2, ',', '.') }}</h4>
 <small class="text-muted">{{ $totalizacoes['por_forma_pagamento']['dinheiro']['quantidade'] ?? 0 }} transações</small>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3 mb-3">
 <div class="card stat-card h-100">
 <div class="card-body d-flex align-items-center">
 <div class="stat-icon bg-cartao me-3">
 <i class="bi bi-credit-card"></i>
 </div>                        <div>
 <h6 class="card-title mb-1 text-muted">Cartão</h6>
 <h4 class="mb-0">R$ {{ number_format($totalizacoes['por_forma_pagamento']['cartao']['total'] ?? 0, 2, ',', '.') }}</h4>
 <small class="text-muted">{{ $totalizacoes['por_forma_pagamento']['cartao']['quantidade'] ?? 0 }} transações</small>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-3 mb-3">
 <div class="card stat-card h-100">
 <div class="card-body d-flex align-items-center">
 <div class="stat-icon bg-pix me-3">
 <i class="bi bi-qr-code"></i>
 </div>                        <div>
 <h6 class="card-title mb-1 text-muted">PIX</h6>
 <h4 class="mb-0">R$ {{ number_format($totalizacoes['por_forma_pagamento']['pix']['total'] ?? 0, 2, ',', '.') }}</h4>
 <small class="text-muted">{{ $totalizacoes['por_forma_pagamento']['pix']['quantidade'] ?? 0 }} transações</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Detalhamento de Pagamentos -->
 <div class="card">
 <div class="card-header">
 <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Detalhamento de Pagamentos</h5>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover">
 <thead class="table-light">
 <tr>
 <th>Horário</th>
 <th>Pedido</th>
 <th>Mesa</th>
 <th>Forma</th>
 <th>Valor</th>
 <th>Recebido</th>
 <th>Troco</th>
 <th>Status</th>
 </tr>                        </thead>                        <tbody>
 @forelse($pagamentos as $pagamento)
 <tr>
 <td>{{ $pagamento->created_at->format('H:i') }}</td>
 <td>
 <span class="badge bg-primary">
 #{{ $pagamento->pedido_id }}
 </span>
 </td>
 <td>{{ $pagamento->pedido->mesa->numero ?? '-' }}</td>
 <td>
 @php
 $forma = $pagamento->forma_pagamento;
 $icons = [
 'dinheiro' => 'bi-cash',
 'cartao_credito' => 'bi-credit-card',
 'cartao_debito' => 'bi-credit-card',
 'cartao' => 'bi-credit-card',
 'pix' => 'bi-qr-code',
 'vale' => 'bi-ticket'
 ];
 @endphp
 <i class="bi {{ $icons[$forma] ?? 'bi-cash' }} me-1"></i>
 {{ ucfirst($forma) }}
 </td>
 <td class="fw-bold text-success">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</td>
 <td>R$ {{ number_format($pagamento->valor_recebido, 2, ',', '.') }}</td>
 <td>R$ {{ number_format($pagamento->troco, 2, ',', '.') }}</td>
 <td>
 <span class="badge bg-success">{{ ucfirst($pagamento->status) }}</span>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="8" class="text-center text-muted py-4">
 <i class="bi bi-inbox display-6"></i>
 <p class="mt-2">Nenhum pagamento registrado neste caixa</p>
 </td>                            </tr>
 @endforelse
 </tbody>
 <tfoot class="table-light">
 <tr>
 <th colspan="4" class="text-end">Total:</th>
 <th>R$ {{ number_format($totalizacoes['total_vendas'] ?? 0, 2, ',', '.') }}</th>
 <th>-</th>
 <th>R$ {{ number_format($totalizacoes['total_troco'] ?? 0, 2, ',', '.') }}</th>
 <th>-</th>
 </tr>
 </tfoot>
 </table>
 </div>
 </div>
 </div>
 <!-- Resumo do Caixa -->
 @if($caixa->data_fechamento ?? false)
 <div class="row mt-4">
 <div class="col-md-6">
 <div class="card">
 <div class="card-header">
 <h6 class="mb-0">💰 Resumo Financeiro</h6>
 </div>                    <div class="card-body">
 <div class="d-flex justify-content-between mb-2">
 <span>Saldo Inicial:</span>
 <span>R$ {{ number_format($caixa->saldo_inicial ?? 100.00, 2, ',', '.') }}</span>
 </div>
 <div class="d-flex justify-content-between mb-2">
 <span>Total Vendas:</span>
 <span class="text-success">+R$ {{ number_format($caixa->total_vendas ?? 195.30, 2, ',', '.') }}</span>
 </div>
 <div class="d-flex justify-content-between mb-2">
 <span>Troco Dado:</span>
 <span class="text-danger">-R$ 14,50</span>
 </div>
 <hr>
 <div class="d-flex justify-content-between fw-bold">
 <span>Saldo Final:</span>
 <span>R$ {{ number_format(($caixa->saldo_final ?? 280.80), 2, ',', '.') }}</span>
 </div>
 </div>
 </div>
 </div>
 <div class="col-md-6">
 <div class="card">
 <div class="card-header">
 <h6 class="mb-0">📊 Estatísticas</h6>
 </div>                    <div class="card-body">
 <div class="d-flex justify-content-between mb-2">
 <span>Pedidos Processados:</span>
 <span>{{ $total_pedidos ?? 3 }}</span>
 </div>
 <div class="d-flex justify-content-between mb-2">
 <span>Ticket Médio:</span>
 <span>R$ {{ number_format(($caixa->total_vendas ?? 195.30) / ($total_pedidos ?? 3), 2, ',', '.') }}</span>
 </div>
 <div class="d-flex justify-content-between mb-2">
 <span>Maior Venda:</span>
 <span>R$ 85,50</span>
 </div>
 <div class="d-flex justify-content-between">
 <span>Tempo Operação:</span>
 <span>2h 15min</span>
 </div>
 </div>
 </div>
 </div>
 </div>
 @endif
 </div>
 <!-- Botão de Impressão -->
 <button class="btn btn-primary print-button" onclick="window.print()">
 <i class="bi bi-printer me-2"></i>Imprimir
 </button>
 <!-- Bootstrap JS -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 <style media="print">
 .print-button { display: none; }        .relatorio-header { 
 background: #6c757d !important; 
 -webkit-print-color-adjust: exact;
 print-color-adjust: exact;
 }
 .stat-card { 
 box-shadow: none; 
 border: 1px solid #dee2e6;
 }
 </style>
</body>
</html>