<!DOCTYPE html>
<html lang="pt-BR">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Gestão de Pagamentos - MyD Bar & Restaurantes</title>
 <!-- Bootstrap CSS -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <!-- Bootstrap Icons -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
 <style>
 .pagamentos-header {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 color: white;
 padding: 2rem 0;
 margin-bottom: 2rem;
 }
 .status-badge {
 padding: 0.375rem 0.75rem;
 border-radius: 20px;
 font-size: 0.875rem;
 font-weight: 600;
 }
 .status-confirmado { background-color: #d4edda; color: #155724; }
 .status-pendente { background-color: #fff3cd; color: #856404; }
 .status-cancelado { background-color: #f8d7da; color: #721c24; }
 .pagamento-card {
 border: none;
 border-radius: 15px;
 box-shadow: 0 3px 10px rgba(0,0,0,0.1);
 margin-bottom: 1rem;
 transition: transform 0.2s ease;
 }
 .pagamento-card:hover {
 transform: translateY(-2px);
 }
 .forma-pagamento {
 display: inline-flex;
 align-items: center;
 padding: 0.25rem 0.75rem;
 border-radius: 15px;
 font-size: 0.875rem;
 font-weight: 500;
 }
 .forma-dinheiro { background-color: #e7f3ff; color: #004085; }
 .forma-cartao { background-color: #fff3e0; color: #e65100; }
 .forma-pix { background-color: #f3e5f5; color: #4a148c; }
 .forma-vale { background-color: #e8f5e8; color: #1b5e20; }
 </style>
</head>
<body>
 <div class="pagamentos-header">
 <div class="container">
 <div class="row align-items-center">
 <div class="col-md-8">
 <h1 class="mb-0"><i class="bi bi-credit-card me-2"></i>Gestão de Pagamentos</h1>
 <p class="mb-0 opacity-75">Visualize e gerencie todos os pagamentos do sistema</p>
 </div>
 <div class="col-md-4 text-md-end">
 <a href="{{ url('/caixa') }}" class="btn btn-light">
 <i class="bi bi-arrow-left me-2"></i>Voltar ao Caixa
 </a>
 </div>
 </div>
 </div>
 </div>
 <div class="container">
 <!-- Filtros e Busca -->
 <div class="card mb-4">
 <div class="card-body">
 <div class="row align-items-end">
 <div class="col-md-3">
 <label class="form-label">Período</label>
 <select class="form-select">
 <option value="hoje">Hoje</option>
 <option value="ontem">Ontem</option>
 <option value="semana">Esta Semana</option>
 <option value="mes">Este Mês</option>
 <option value="custom">Período Customizado</option>
 </select>
 </div>
 <div class="col-md-3">
 <label class="form-label">Status</label>
 <select class="form-select">
 <option value="">Todos os status</option>
 <option value="confirmado">Confirmado</option>
 <option value="pendente">Pendente</option>
 <option value="cancelado">Cancelado</option>
 </select>
 </div>
 <div class="col-md-3">
 <label class="form-label">Forma de Pagamento</label>
 <select class="form-select">
 <option value="">Todas as formas</option>
 <option value="dinheiro">Dinheiro</option>
 <option value="cartao">Cartão</option>
 <option value="pix">PIX</option>
 <option value="vale">Vale Refeição</option>
 </select>
 </div>
 <div class="col-md-3">
 <button class="btn btn-primary w-100">
 <i class="bi bi-search me-2"></i>Filtrar
 </button>
 </div>
 </div>
 </div>
 </div>
 <!-- Resumo dos Pagamentos -->
 <div class="row mb-4">
 <div class="col-md-3">
 <div class="card text-center border-0" style="background: linear-gradient(135deg, #4CAF50, #45a049); color: white;">
 <div class="card-body">
 <h4 class="fw-bold">{{ $total_pagamentos ?? 25 }}</h4>
 <p class="mb-0">Total de Pagamentos</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card text-center border-0" style="background: linear-gradient(135deg, #2196F3, #1976D2); color: white;">
 <div class="card-body">
 <h4 class="fw-bold">R$ {{ number_format($total_valor ?? 2847.50, 2, ',', '.') }}</h4>
 <p class="mb-0">Valor Total</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card text-center border-0" style="background: linear-gradient(135deg, #FF9800, #F57C00); color: white;">
 <div class="card-body">
 <h4 class="fw-bold">R$ {{ number_format($total_troco ?? 87.30, 2, ',', '.') }}</h4>
 <p class="mb-0">Total em Troco</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card text-center border-0" style="background: linear-gradient(135deg, #9C27B0, #7B1FA2); color: white;">
 <div class="card-body">
 <h4 class="fw-bold">R$ {{ number_format(($total_valor ?? 2847.50) / ($total_pagamentos ?? 25), 2, ',', '.') }}</h4>
 <p class="mb-0">Ticket Médio</p>
 </div>
 </div>
 </div>
 </div>
 <!-- Lista de Pagamentos -->
 <div class="row">
 @php
 $pagamentos_exemplo = [
 [
 'id' => 15,
 'pedido_id' => 23,
 'mesa' => 5,
 'forma_pagamento' => 'dinheiro',
 'valor' => 125.50,
 'valor_recebido' => 150.00,
 'troco' => 24.50,
 'status' => 'confirmado',
 'operador' => 'João Silva',
 'data_pagamento' => '11/11/2025 14:30',
 'observacoes' => ''
 ],
 [
 'id' => 14,
 'pedido_id' => 22,
 'mesa' => 3,
 'forma_pagamento' => 'cartao',
 'valor' => 89.90,
 'valor_recebido' => 89.90,
 'troco' => 0.00,
 'status' => 'confirmado',
 'operador' => 'Maria Santos',
 'data_pagamento' => '11/11/2025 13:45',
 'observacoes' => 'Cartão de crédito Visa'
 ],
 [
 'id' => 13,
 'pedido_id' => 21,
 'mesa' => 7,
 'forma_pagamento' => 'pix',
 'valor' => 67.80,
 'valor_recebido' => 67.80,
 'troco' => 0.00,
 'status' => 'confirmado',
 'operador' => 'João Silva',
 'data_pagamento' => '11/11/2025 12:15',
 'observacoes' => 'PIX processado via QR Code'
 ]
 ];
 @endphp
 @foreach($pagamentos ?? $pagamentos_exemplo as $pagamento)
 <div class="col-md-6">
 <div class="pagamento-card">
 <div class="card-body">
 <!-- Header do Pagamento -->
 <div class="d-flex justify-content-between align-items-start mb-3">
 <div>
 <h6 class="card-title mb-1">
 Pagamento #{{ is_array($pagamento) ? $pagamento['id'] : $pagamento->id }}
 <small class="text-muted">- Pedido #{{ is_array($pagamento) ? $pagamento['pedido_id'] : $pagamento->pedido_id }}</small>
 </h6>
 <small class="text-muted">
 <i class="bi bi-table me-1"></i>Mesa {{ is_array($pagamento) ? $pagamento['mesa'] : $pagamento->pedido->mesa->numero }}
 • {{ is_array($pagamento) ? $pagamento['data_pagamento'] : $pagamento->created_at->format('d/m/Y H:i') }}
 </small>
 </div>
 <span class="status-badge status-{{ is_array($pagamento) ? $pagamento['status'] : $pagamento->status }}">
 {{ ucfirst(is_array($pagamento) ? $pagamento['status'] : $pagamento->status) }}
 </span>
 </div>
 <!-- Informações do Pagamento -->
 <div class="row mb-3">
 <div class="col-6">
 <small class="text-muted">Forma de Pagamento</small>
 @php
 $forma = is_array($pagamento) ? $pagamento['forma_pagamento'] : $pagamento->forma_pagamento;
 $classe = 'forma-' . strtolower($forma);
 $icone = match(strtolower($forma)) {
 'dinheiro' => 'bi-cash',
 'cartao' => 'bi-credit-card',
 'pix' => 'bi-qr-code',
 'vale' => 'bi-wallet',
 default => 'bi-wallet'
 };
 @endphp
 <div class="forma-pagamento {{ $classe }}">
 <i class="{{ $icone }} me-1"></i>{{ ucfirst($forma) }}
 </div>
 </div>
 <div class="col-6">
 <small class="text-muted">Operador</small>
 <div class="fw-bold">{{ is_array($pagamento) ? $pagamento['operador'] : $pagamento->usuario->nome }}</div>
 </div>
 </div>
 <!-- Valores -->
 <div class="row mb-3">
 <div class="col-4">
 <small class="text-muted">Valor</small>
 <div class="fw-bold text-success">
 R$ {{ number_format(is_array($pagamento) ? $pagamento['valor'] : $pagamento->valor, 2, ',', '.') }}
 </div>
 </div>
 <div class="col-4">
 <small class="text-muted">Recebido</small>
 <div class="fw-bold">
 R$ {{ number_format(is_array($pagamento) ? $pagamento['valor_recebido'] : $pagamento->valor_recebido, 2, ',', '.') }}
 </div>
 </div>
 <div class="col-4">
 <small class="text-muted">Troco</small>
 <div class="fw-bold {{ (is_array($pagamento) ? $pagamento['troco'] : $pagamento->troco) > 0 ? 'text-warning' : '' }}">
 R$ {{ number_format(is_array($pagamento) ? $pagamento['troco'] : $pagamento->troco, 2, ',', '.') }}
 </div>
 </div>
 </div>
 <!-- Observações -->
 @if((is_array($pagamento) ? $pagamento['observacoes'] : $pagamento->observacoes))
 <div class="alert alert-light mb-3">
 <small>
 <i class="bi bi-chat-text me-1"></i>
 {{ is_array($pagamento) ? $pagamento['observacoes'] : $pagamento->observacoes }}
 </small>
 </div>
 @endif
 <!-- Ações -->
 <div class="d-flex justify-content-end">
 <button class="btn btn-outline-primary btn-sm me-2" 
 data-bs-toggle="modal" 
 data-bs-target="#modalDetalhes{{ is_array($pagamento) ? $pagamento['id'] : $pagamento->id }}">
 <i class="bi bi-eye me-1"></i>Detalhes
 </button>
 @if((is_array($pagamento) ? $pagamento['status'] : $pagamento->status) == 'confirmado')
 <button class="btn btn-outline-warning btn-sm me-2">
 <i class="bi bi-arrow-clockwise me-1"></i>Estornar
 </button>
 @endif
 <button class="btn btn-outline-secondary btn-sm">
 <i class="bi bi-printer me-1"></i>Imprimir
 </button>
 </div>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 <!-- Paginação -->
 <div class="d-flex justify-content-center mt-4">
 <nav aria-label="Page navigation">
 <ul class="pagination">
 <li class="page-item disabled">
 <a class="page-link" href="#" tabindex="-1">Anterior</a>
 </li>
 <li class="page-item active"><a class="page-link" href="#">1</a></li>
 <li class="page-item"><a class="page-link" href="#">2</a></li>
 <li class="page-item"><a class="page-link" href="#">3</a></li>
 <li class="page-item">
 <a class="page-link" href="#">Próximo</a>
 </li>
 </ul>
 </nav>
 </div>
 </div>
 <!-- Bootstrap JS -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>