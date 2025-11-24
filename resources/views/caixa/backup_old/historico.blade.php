<!DOCTYPE html>
<html lang="pt-BR">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Histórico de Caixas - MyD Bar & Restaurantes</title>
 <!-- Bootstrap CSS -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <!-- Bootstrap Icons -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
 <style>
 .historico-header {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 color: white;
 padding: 2rem 0;
 margin-bottom: 2rem;
 }
 .caixa-card {
 border: none;
 border-radius: 15px;
 box-shadow: 0 5px 15px rgba(0,0,0,0.1);
 transition: transform 0.3s ease;
 margin-bottom: 1.5rem;
 height: 100%;
 }
 .caixa-card:hover {
 transform: translateY(-5px);
 box-shadow: 0 8px 25px rgba(0,0,0,0.15);
 }
 .status-badge {
 position: absolute;
 top: 15px;
 right: 15px;
 padding: 0.4rem 0.8rem;
 border-radius: 20px;
 font-size: 0.8rem;
 font-weight: 600;
 z-index: 2;
 }
 .status-aberto {
 background: linear-gradient(135deg, #4CAF50, #45a049);
 color: white;
 box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
 }
 .status-fechado {
 background: linear-gradient(135deg, #6c757d, #5a6268);
 color: white;
 box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
 }
 .valor-destaque {
 font-size: 1.4rem;
 font-weight: 700;
 color: #4CAF50;
 }
 .filtros-card {
 background: #f8f9fa;
 border: 1px solid #e9ecef;
 border-radius: 15px;
 padding: 1.5rem;
 margin-bottom: 2rem;
 }
 .resumo-card {
 background: white;
 border: none;
 border-radius: 15px;
 box-shadow: 0 3px 10px rgba(0,0,0,0.1);
 }
 .resumo-card:hover {
 transform: translateY(-2px);
 box-shadow: 0 5px 15px rgba(0,0,0,0.15);
 }
 .resumo-icon {
 width: 45px;
 height: 45px;
 border-radius: 12px;
 display: flex;
 align-items: center;
 justify-content: center;
 font-size: 1.2rem;
 color: white;
 margin: 0 auto 0.5rem;
 }
 .bg-primary-gradient { background: linear-gradient(135deg, #6f42c1, #6610f2); }
 .bg-success-gradient { background: linear-gradient(135deg, #28a745, #20c997); }
 .bg-info-gradient { background: linear-gradient(135deg, #17a2b8, #6f42c1); }
 .bg-warning-gradient { background: linear-gradient(135deg, #ffc107, #fd7e14); }
 .badge-payment {
 font-size: 0.75rem;
 padding: 0.3rem 0.6rem;
 border-radius: 8px;
 }
 @media (max-width: 768px) {
 .caixa-card {
 margin-bottom: 1rem;
 }
 .valor-destaque {
 font-size: 1.2rem;
 }
 .resumo-icon {
 width: 40px;
 height: 40px;
 }
 }
 </style>
</head>
<body>
 <div class="historico-header">
 <div class="container">
 <div class="row align-items-center">
 <div class="col-md-8">
 <h1 class="mb-0"><i class="bi bi-clock-history me-2"></i>Histórico de Caixas</h1>
 <p class="mb-0 opacity-75">Visualize todos os caixas já operados no sistema</p>
 </div>
 <div class="col-md-4 text-md-end">
 <a href="{{ url('/caixa') }}" class="btn btn-light btn-lg">
 <i class="bi bi-arrow-left me-2"></i>Voltar ao Caixa
 </a>
 </div>
 </div>
 </div>
 </div>
 <div class="container">
 <!-- Filtros -->
 <div class="filtros-card">
 <h5 class="mb-3"><i class="bi bi-funnel me-2"></i>Filtros de Pesquisa</h5>
 <form method="GET">
 <div class="row align-items-end">
 <div class="col-md-3">
 <label class="form-label fw-bold">Data Inicial</label>
 <input type="date" name="data_inicio" class="form-control" 
 value="{{ request('data_inicio') }}">
 </div>
 <div class="col-md-3">
 <label class="form-label fw-bold">Data Final</label>
 <input type="date" name="data_fim" class="form-control" 
 value="{{ request('data_fim') }}">
 </div>
 <div class="col-md-3">
 <label class="form-label fw-bold">Operador</label>
 <select name="usuario_id" class="form-select">
 <option value="">Todos os operadores</option>
 <!-- Usuários serão carregados dinamicamente -->
 </select>
 </div>
 <div class="col-md-3">
 <button type="submit" class="btn btn-primary w-100">
 <i class="bi bi-search me-2"></i>Filtrar Resultados
 </button>
 </div>
 </div>
 </form>
 </div>        <!-- Resumo Geral -->
 <div class="row mb-4">
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card resumo-card text-center h-100">
 <div class="card-body">
 <div class="resumo-icon bg-primary-gradient">
 <i class="bi bi-cash-stack"></i>
 </div>
 <h5 class="text-primary mb-1">{{ $totaisGerais['quantidade_caixas'] ?? 0 }}</h5>
 <p class="text-muted mb-0 small">Total de Caixas</p>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card resumo-card text-center h-100">
 <div class="card-body">
 <div class="resumo-icon bg-success-gradient">
 <i class="bi bi-graph-up"></i>
 </div>
 <h5 class="text-success mb-1">R$ {{ number_format($totaisGerais['total_vendas'] ?? 0, 2, ',', '.') }}</h5>
 <p class="text-muted mb-0 small">Total Vendido</p>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card resumo-card text-center h-100">
 <div class="card-body">
 <div class="resumo-icon bg-info-gradient">
 <i class="bi bi-credit-card"></i>
 </div>
 <h5 class="text-info mb-1">R$ {{ number_format($totaisGerais['total_cartao'] ?? 0, 2, ',', '.') }}</h5>
 <p class="text-muted mb-0 small">Total Cartões</p>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card resumo-card text-center h-100">
 <div class="card-body">
 <div class="resumo-icon bg-warning-gradient">
 <i class="bi bi-cash"></i>
 </div>
 <h5 class="text-warning mb-1">R$ {{ number_format($totaisGerais['total_dinheiro'] ?? 0, 2, ',', '.') }}</h5>
 <p class="text-muted mb-0 small">Total Dinheiro</p>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card resumo-card text-center h-100">
 <div class="card-body">
 <div class="resumo-icon bg-info-gradient">
 <i class="bi bi-phone"></i>
 </div>
 <h5 class="text-info mb-1">R$ {{ number_format($totaisGerais['total_pix'] ?? 0, 2, ',', '.') }}</h5>
 <p class="text-muted mb-0 small">Total PIX</p>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card resumo-card text-center h-100">
 <div class="card-body">
 <div class="resumo-icon bg-secondary">
 <i class="bi bi-receipt"></i>
 </div>
 <h5 class="text-dark mb-1">{{ $totaisGerais['quantidade_vendas'] ?? 0 }}</h5>
 <p class="text-muted mb-0 small">Transações</p>
 </div>
 </div>
 </div>
 </div>
 <!-- Resumo de Cartões Detalhado -->
 <div class="row mb-4">
 <div class="col-md-4 mb-3">
 <div class="card border-success">
 <div class="card-header bg-success text-white">
 <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Cartão Crédito</h6>
 </div>
 <div class="card-body text-center">
 <h4 class="text-success">R$ {{ number_format($totaisGerais['total_cartao_credito'] ?? 0, 2, ',', '.') }}</h4>
 <p class="text-muted mb-0">Total em Crédito</p>
 </div>
 </div>
 </div>
 <div class="col-md-4 mb-3">
 <div class="card border-info">
 <div class="card-header bg-info text-white">
 <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Cartão Débito</h6>
 </div>
 <div class="card-body text-center">
 <h4 class="text-info">R$ {{ number_format($totaisGerais['total_cartao_debito'] ?? 0, 2, ',', '.') }}</h4>
 <p class="text-muted mb-0">Total em Débito</p>
 </div>
 </div>
 </div>
 <div class="col-md-4 mb-3">
 <div class="card border-warning">
 <div class="card-header bg-warning text-dark">
 <h6 class="mb-0"><i class="bi bi-ticket me-2"></i>Vale Refeição</h6>
 </div>
 <div class="card-body text-center">
 <h4 class="text-warning">R$ {{ number_format($totaisGerais['total_vale'] ?? 0, 2, ',', '.') }}</h4>
 <p class="text-muted mb-0">Total em Vales</p>
 </div>
 </div>
 </div>
 </div>
 <!-- Lista de Caixas -->
 <div class="row">
 @forelse($caixas as $caixa)
 <div class="col-lg-6 col-md-12 mb-4">
 <div class="card caixa-card position-relative">
 <span class="status-badge {{ $caixa->status == 'aberto' ? 'status-aberto' : 'status-fechado' }}">
 <i class="bi bi-{{ $caixa->status == 'aberto' ? 'unlock' : 'lock' }} me-1"></i>
 {{ ucfirst($caixa->status) }}
 </span>
 <div class="card-body p-4">
 <!-- Cabeçalho do Caixa -->
 <div class="d-flex align-items-center mb-3">
 <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" 
 style="width: 55px; height: 55px;">
 <i class="bi bi-cash-coin fs-4"></i>
 </div>
 <div class="flex-grow-1">
 <h5 class="mb-1 fw-bold">Caixa #{{ $caixa->id }}</h5>
 <p class="text-muted mb-0">
 <i class="bi bi-person-circle me-1"></i>
 {{ $caixa->usuario->nome ?? 'Operador não identificado' }}
 </p>
 </div>
 </div>
 <!-- Valores Principal -->
 <div class="row text-center mb-3">
 <div class="col-6">
 <div class="border-end">
 <div class="valor-destaque">
 R$ {{ number_format($caixa->total_vendas_real ?? 0, 2, ',', '.') }}
 </div>
 <small class="text-muted">Total de Vendas</small>
 @if(isset($caixa->diferenca_total) && $caixa->diferenca_total != 0)
 <div class="badge bg-{{ $caixa->diferenca_total > 0 ? 'success' : 'danger' }} mt-1">
 {{ $caixa->diferenca_total > 0 ? '+' : '' }}R$ {{ number_format(abs($caixa->diferenca_total), 2, ',', '.') }}
 </div>
 @endif
 </div>
 </div>
 <div class="col-6">
 <div class="h6 mb-1 fw-bold">
 {{ $caixa->data_abertura ? $caixa->data_abertura->format('d/m/Y') : '-' }}
 </div>
 <small class="text-muted">{{ $caixa->data_abertura ? $caixa->data_abertura->format('H:i') : '-' }}</small>
 @if($caixa->data_fechamento)
 <small class="text-muted"> - {{ $caixa->data_fechamento->format('H:i') }}</small>
 @endif
 <p class="text-muted mb-0 small">Período de Operação</p>
 </div>
 </div>
 <!-- Data de Fechamento -->
 @if($caixa->data_fechamento)
 <div class="alert alert-light mb-3 py-2">
 <small class="d-flex align-items-center">
 <i class="bi bi-calendar-check me-2 text-success"></i>
 <span>Fechado em: <strong>{{ $caixa->data_fechamento->format('d/m/Y H:i') }}</strong></span>
 </small>
 </div>
 @endif
 <!-- Formas de Pagamento -->
 <div class="row g-2 mb-3">
 <div class="col-6 col-md-2">
 <div class="text-center p-2 bg-light rounded">
 <small class="text-muted d-block">💵 Dinheiro</small>
 <div class="fw-bold small">R$ {{ number_format($caixa->total_dinheiro_real ?? 0, 2, ',', '.') }}</div>
 </div>
 </div>
 <div class="col-6 col-md-2">
 <div class="text-center p-2 bg-light rounded">
 <small class="text-muted d-block">💳 Crédito</small>
 <div class="fw-bold small">R$ {{ number_format($caixa->total_cartao_credito_real ?? 0, 2, ',', '.') }}</div>
 </div>
 </div>
 <div class="col-6 col-md-2">
 <div class="text-center p-2 bg-light rounded">
 <small class="text-muted d-block">💳 Débito</small>
 <div class="fw-bold small">R$ {{ number_format($caixa->total_cartao_debito_real ?? 0, 2, ',', '.') }}</div>
 </div>
 </div>
 <div class="col-6 col-md-2">
 <div class="text-center p-2 bg-light rounded">
 <small class="text-muted d-block">📱 PIX</small>
 <div class="fw-bold small">R$ {{ number_format($caixa->total_pix_real ?? 0, 2, ',', '.') }}</div>
 </div>
 </div>
 <div class="col-6 col-md-2">
 <div class="text-center p-2 bg-light rounded">
 <small class="text-muted d-block">🎫 Vale</small>
 <div class="fw-bold small">R$ {{ number_format($caixa->total_vale_real ?? 0, 2, ',', '.') }}</div>
 </div>
 </div>                            <div class="col-6 col-md-2">
 <div class="text-center p-2 bg-success-subtle rounded">
 <small class="text-muted d-block">💰 Total</small>
 <div class="fw-bold small">R$ {{ number_format($caixa->total_vendas_real ?? 0, 2, ',', '.') }}</div>
 </div>
 </div>
 </div>
 <!-- Saldo do Caixa -->
 <div class="row g-2 mb-3">
 <div class="col-md-4">
 <div class="text-center p-2 bg-info-subtle rounded">
 <small class="text-muted d-block">🏦 Saldo Inicial</small>
 <div class="fw-bold small text-info">R$ {{ number_format($caixa->saldo_inicial ?? 0, 2, ',', '.') }}</div>
 </div>
 </div>
 <div class="col-md-4">
 @php
 $saldoFinal = $caixa->saldo_final ?? (($caixa->saldo_inicial ?? 0) + ($caixa->total_dinheiro_real ?? 0));
 @endphp
 <div class="text-center p-2 bg-success-subtle rounded">
 <small class="text-muted d-block">🏦 Saldo Final</small>
 <div class="fw-bold small text-success">R$ {{ number_format($saldoFinal, 2, ',', '.') }}</div>
 </div>
 </div>
 <div class="col-md-4">
 @php
 $diferenca = $saldoFinal - ($caixa->saldo_inicial ?? 0);
 @endphp
 <div class="text-center p-2 bg-{{ $diferenca >= 0 ? 'success' : 'danger' }}-subtle rounded">
 <small class="text-muted d-block">📊 Diferença</small>
 <div class="fw-bold small text-{{ $diferenca >= 0 ? 'success' : 'danger' }}">
 {{ $diferenca >= 0 ? '+' : '' }}R$ {{ number_format($diferenca, 2, ',', '.') }}
 </div>
 </div>
 </div>
 </div>
 <!-- Rodapé do Card -->
 <div class="d-flex justify-content-between align-items-center">
 <small class="text-muted">
 <i class="bi bi-receipt me-1"></i>
 {{ $caixa->quantidade_vendas ?? 0 }} vendas realizadas
 </small>
 <a href="{{ url('/caixa/relatorio/' . $caixa->id) }}" 
 class="btn btn-outline-primary btn-sm">
 <i class="bi bi-eye me-1"></i>Ver Detalhes
 </a>
 </div>
 </div>
 </div>
 </div>
 @empty
 <div class="col-12">
 <div class="text-center py-5">
 <div class="display-1 text-muted mb-3">
 <i class="bi bi-inbox"></i>
 </div>
 <h4 class="text-muted">Nenhum caixa encontrado</h4>
 <p class="text-muted mb-4">Não há caixas registrados no período selecionado.</p>
 <a href="{{ url('/caixa') }}" class="btn btn-primary">
 <i class="bi bi-plus-circle me-2"></i>Abrir Novo Caixa
 </a>
 </div>
 </div>
 @endforelse
 </div>
 <!-- Paginação -->
 @if($caixas->hasPages())
 <div class="d-flex justify-content-center mt-5 mb-4">
 {{ $caixas->appends(request()->query())->links() }}
 </div>
 @endif
 </div>
 <!-- Bootstrap JS -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>