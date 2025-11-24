<!DOCTYPE html>
<html lang="pt-BR">
<head>    <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <title>Dashboard do Caixa - MyD Bar & Restaurantes</title>
 <!-- Bootstrap CSS -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <!-- Bootstrap Icons -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
 <style>
 body {
 background-color: #f8f9fa;
 }
 .navbar {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 }
 .stat-card {
 border-radius: 15px;
 box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
 border: none;
 overflow: hidden;
 }
 .stat-icon {
 width: 60px;
 height: 60px;
 border-radius: 50%;
 display: flex;
 align-items: center;
 justify-content: center;
 font-size: 1.5rem;
 color: white;
 }
 .pedidos-table {
 border-radius: 15px;
 overflow: hidden;
 box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
 }
 .btn-pagamento {
 border-radius: 8px;
 padding: 8px 16px;
 font-weight: 500;
 }
 .status-aberto {
 background: linear-gradient(45deg, #28a745, #20c997);
 }
 .refresh-indicator {
 animation: spin 1s linear infinite;
 }
 @keyframes spin {
 0% { transform: rotate(0deg); }
 100% { transform: rotate(360deg); }
 }
 </style>
</head>
<body>
 <!-- Navbar -->
 <nav class="navbar navbar-dark">
 <div class="container-fluid">
 <a class="navbar-brand" href="#">
 <i class="bi bi-cash-coin me-2"></i>
 Dashboard do Caixa
 </a>
 <div class="d-flex align-items-center">
 <span class="badge bg-success me-3">
 <i class="bi bi-unlock me-1"></i>
 Caixa Aberto
 </span>
 <button class="btn btn-outline-light me-2" onclick="atualizarDados()">
 <i class="bi bi-arrow-clockwise" id="refresh-icon"></i>
 </button>
 <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
 <i class="bi bi-house me-1"></i>
 Dashboard Principal
 </a>
 </div>
 </div>
 </nav>
 <div class="container-fluid mt-4">
 @if(session('success'))
 <div class="alert alert-success alert-dismissible fade show">
 <i class="bi bi-check-circle me-2"></i>
 {{ session('success') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 @if(session('error'))
 <div class="alert alert-danger alert-dismissible fade show">
 <i class="bi bi-exclamation-triangle me-2"></i>
 {{ session('error') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 <!-- Informações do Caixa -->
 <div class="row mb-4">
 <div class="col-md-12">
 <div class="card stat-card">
 <div class="card-body">
 <div class="row align-items-center">
 <div class="col-auto">
 <div class="stat-icon status-aberto">
 <i class="bi bi-cash-stack"></i>
 </div>
 </div>
 <div class="col">
 <h5 class="card-title mb-1">Caixa Aberto</h5>                                <p class="text-muted mb-0">
 Operador: <strong>{{ $caixaAberto->usuario->nome ?? 'Sistema' }}</strong> |
 Abertura: <strong>{{ $caixaAberto ? $caixaAberto->data_abertura->format('d/m/Y H:i') : 'N/A' }}</strong>
 </p>
 </div>
 <div class="col-auto">
 <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalFecharCaixa">
 <i class="bi bi-lock me-2"></i>
 Fechar Caixa
 </button>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Resumo de Vendas -->
 <div class="row mb-4">
 <div class="col-md-3">
 <div class="card stat-card text-center">
 <div class="card-body">
 <div class="stat-icon mx-auto mb-3" style="background: #007bff;">
 <i class="bi bi-currency-dollar"></i>
 </div>
 <h3 class="mb-1" id="total-vendas">R$ {{ number_format($totaisCaixa['total_vendas'], 2, ',', '.') }}</h3>
 <p class="text-muted mb-0">Total de Vendas</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card stat-card text-center">
 <div class="card-body">
 <div class="stat-icon mx-auto mb-3" style="background: #28a745;">
 <i class="bi bi-receipt"></i>
 </div>
 <h3 class="mb-1" id="quantidade-vendas">{{ $totaisCaixa['quantidade_vendas'] }}</h3>
 <p class="text-muted mb-0">Vendas Realizadas</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card stat-card text-center">
 <div class="card-body">
 <div class="stat-icon mx-auto mb-3" style="background: #ffc107;">
 <i class="bi bi-cash"></i>
 </div>
 <h3 class="mb-1" id="total-troco">R$ {{ number_format($totaisCaixa['total_troco'], 2, ',', '.') }}</h3>
 <p class="text-muted mb-0">Troco Dado</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card stat-card text-center">
 <div class="card-body">
 <div class="stat-icon mx-auto mb-3" style="background: #dc3545;">
 <i class="bi bi-clock-history"></i>
 </div>
 <h3 class="mb-1" id="pedidos-pendentes">{{ $pedidosPendentes->count() }}</h3>
 <p class="text-muted mb-0">Pedidos Pendentes</p>
 </div>
 </div>
 </div>
 </div>
 <!-- Formas de Pagamento -->
 <div class="row mb-4">
 <div class="col-md-12">
 <div class="card stat-card">
 <div class="card-header">
 <h5 class="mb-0">
 <i class="bi bi-credit-card me-2"></i>
 Resumo por Forma de Pagamento
 </h5>
 </div>
 <div class="card-body">
 <div class="row" id="formas-pagamento">                            @foreach(['dinheiro' => 'Dinheiro', 'cartao_credito' => 'Cartão Crédito', 'cartao_debito' => 'Cartão Débito', 'pix' => 'PIX', 'vale_refeicao' => 'Vale Refeição'] as $forma => $nome)
 @php
 $dados = $totaisCaixa['por_forma_pagamento'][$forma] ?? ['quantidade' => 0, 'total' => 0];
 @endphp
 <div class="col-md-2 text-center">
 <div class="border rounded p-3">
 <h6 class="mb-2">{{ $nome }}</h6>
 <p class="mb-1"><strong>{{ $dados['quantidade'] }}</strong> vendas</p>
 <p class="mb-0 text-success">
 <strong>R$ {{ number_format($dados['total'], 2, ',', '.') }}</strong>
 </p>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 </div>        </div>
 </div>
 <!-- Seção de Pedidos Pendentes de Pagamento -->
 <div class="row mb-4">
 <div class="col-md-12">
 <div class="card stat-card">                    <div class="card-header">
 <h5 class="mb-0">
 <i class="bi bi-clock-history me-2"></i>
 Pedidos Pendentes
 </h5>
 <small class="text-muted">Pedidos abertos para finalizar e pedidos finalizados para receber pagamento</small>
 </div>
 <div class="card-body">
 @if($pedidosPendentes->count() > 0)
 <div class="table-responsive">
 <table class="table table-hover">                                        <thead>
 <tr>
 <th>Pedido</th>
 <th>Mesa</th>
 <th>Status</th>
 <th>Valor</th>
 <th>Tempo</th>
 <th>Ações</th>
 </tr>
 </thead>
 <tbody id="pedidos-pendentes-lista">                                        @foreach($pedidosPendentes as $pedido)
 <tr>
 <td>
 <span class="badge bg-primary">#{{ $pedido->id }}</span>
 </td>
 <td>
 <i class="bi bi-table me-1"></i>
 {{ $pedido->mesa->identificador ?? 'Mesa ' . $pedido->mesa->numero }}
 </td>
 <td>
 @if($pedido->status === 'aberto')
 <span class="badge bg-warning text-dark">
 <i class="bi bi-clock me-1"></i>Aberto
 </span>
 @elseif($pedido->status === 'finalizado')
 <span class="badge bg-info">
 <i class="bi bi-check me-1"></i>Finalizado
 </span>
 @endif
 </td>
 <td>
 <div>
 <strong class="text-success">R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong>
 @if($pedido->status === 'finalizado' && isset($pedido->total_pago) && $pedido->total_pago > 0)
 <br><small class="text-muted">
 Pago: R$ {{ number_format($pedido->total_pago, 2, ',', '.') }} | 
 Restante: R$ {{ number_format($pedido->saldo_restante, 2, ',', '.') }}
 </small>
 @endif
 </div>
 </td>
 <td>
 <small class="text-muted">
 {{ $pedido->created_at->diffForHumans() }}
 </small>
 </td>
 <td>
 @if($pedido->status === 'aberto')
 <button class="btn btn-warning btn-sm" onclick="finalizarPedido({{ $pedido->id }})">
 <i class="bi bi-check-circle me-1"></i>
 Finalizar Pedido
 </button>
 @elseif($pedido->status === 'finalizado')
 @if(isset($pedido->saldo_restante) && $pedido->saldo_restante > 0)
 <a href="{{ route('caixa.recebimento', $pedido->id) }}" 
 class="btn btn-success btn-sm btn-pagamento">
 <i class="bi bi-cash-coin me-1"></i>
 Receber {{ isset($pedido->total_pago) && $pedido->total_pago > 0 ? 'Restante' : 'Pagamento' }}
 </a>
 @else
 <span class="badge bg-success">
 <i class="bi bi-check-circle me-1"></i>Pago
 </span>
 @endif
 @endif
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 @else
 <div class="text-center py-4">
 <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
 <h5 class="mt-3">Todos os pedidos estão pagos!</h5>
 <p class="text-muted">Não há pedidos pendentes de pagamento no momento.</p>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
 <!-- Informações Adicionais do Caixa -->
 <div class="row">
 <div class="col-md-12">
 <div class="card">
 <div class="card-header">
 <h5 class="mb-0">
 <i class="bi bi-info-circle me-2"></i>
 Status do Sistema
 </h5>
 </div>
 <div class="card-body">
 <div class="row">                            <div class="col-md-6">
 <p class="mb-2"><strong>Caixa Aberto:</strong> {{ $caixaAberto ? $caixaAberto->data_abertura->format('d/m/Y H:i') : 'N/A' }}</p>
 <p class="mb-2"><strong>Operador:</strong> {{ $caixaAberto->usuario->nome ?? 'Sistema' }}</p>
 <p class="mb-2"><strong>Saldo Inicial:</strong> R$ {{ $caixaAberto ? number_format($caixaAberto->saldo_inicial, 2, ',', '.') : '0,00' }}</p>
 </div>
 <div class="col-md-6">
 <div class="alert alert-info mb-0">
 <i class="bi bi-lightbulb me-2"></i>
 <strong>Novo Fluxo:</strong> Os pagamentos agora são processados automaticamente no <strong>Modo Garçom</strong> através do botão "Finalizar" das mesas.
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Modal Fechar Caixa -->
 <div class="modal fade" id="modalFecharCaixa" tabindex="-1">
 <div class="modal-dialog">
 <div class="modal-content">
 <div class="modal-header">
 <h5 class="modal-title">
 <i class="bi bi-lock me-2"></i>
 Fechar Caixa
 </h5>
 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
 </div>
 <form action="{{ route('caixa.fechar') }}" method="POST">
 @csrf
 <div class="modal-body">
 <div class="alert alert-warning">
 <i class="bi bi-exclamation-triangle me-2"></i>
 <strong>Atenção!</strong> Esta ação fechará o caixa e encerrará todas as operações do dia.
 </div>
 <div class="mb-3">
 <label for="observacoes_fechamento" class="form-label">Observações de Fechamento</label>
 <textarea class="form-control" 
 id="observacoes_fechamento" 
 name="observacoes" 
 rows="3" 
 placeholder="Observações sobre o fechamento do caixa (opcional)"></textarea>
 </div>
 <div class="row">
 <div class="col-6">
 <h6>Total de Vendas</h6>
 <p class="h5 text-success">R$ {{ number_format($totaisCaixa['total_vendas'], 2, ',', '.') }}</p>
 </div>
 <div class="col-6">
 <h6>Total de Troco</h6>
 <p class="h5 text-warning">R$ {{ number_format($totaisCaixa['total_troco'], 2, ',', '.') }}</p>
 </div>
 </div>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
 <button type="submit" class="btn btn-danger">
 <i class="bi bi-lock me-2"></i>
 Confirmar Fechamento
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 <!-- Bootstrap JS -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 <script>
 let intervalAtualizacao;
 function finalizarPedido(pedidoId) {
 console.log('🔄 Iniciando finalização do pedido:', pedidoId);
 if (!confirm('Deseja finalizar este pedido?')) {
 console.log('❌ Finalização cancelada pelo usuário');
 return;
 }
 const botao = event.target.closest('button');
 const textoOriginal = botao.innerHTML;
 botao.disabled = true;
 botao.innerHTML = '<i class="bi bi-arrow-clockwise refresh-indicator me-1"></i>Finalizando...';
 console.log('📡 Enviando requisição PATCH para:', `/api/pedidos-public/${pedidoId}`);
 fetch(`/api/pedidos-public/${pedidoId}`, {
 method: 'PATCH',
 headers: {
 'Content-Type': 'application/json',
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
 'Accept': 'application/json'
 },
 body: JSON.stringify({
 status: 'finalizado'
 })
 })
 .then(response => {
 console.log('📥 Resposta recebida - Status:', response.status);
 console.log('📥 Headers da resposta:', [...response.headers.entries()]);
 if (!response.ok) {
 return response.text().then(text => {
 console.error('❌ Resposta de erro:', text);
 throw new Error(`Erro HTTP ${response.status}: ${text}`);
 });
 }
 return response.json();
 })
 .then(data => {
 console.log('✅ Dados recebidos:', data);
 if (data) {
 alert('Pedido finalizado com sucesso!');
 console.log('🔄 Recarregando página...');
 window.location.reload();
 } else {
 console.error('❌ Resposta inválida:', data);
 alert('Erro ao finalizar pedido: Resposta inválida');
 botao.disabled = false;
 botao.innerHTML = textoOriginal;
 }
 })
 .catch(error => {
 console.error('❌ Erro na requisição:', error);
 alert('Erro ao finalizar pedido: ' + error.message);
 botao.disabled = false;
 botao.innerHTML = textoOriginal;
 });
 }
 function atualizarDados() {
 const refreshIcon = document.getElementById('refresh-icon');
 refreshIcon.classList.add('refresh-indicator');
 fetch('{{ route('caixa.api.totais') }}')
 .then(response => response.json())
 .then(data => {
 document.getElementById('total-vendas').textContent = 
 'R$ ' + data.totais.total_vendas.toLocaleString('pt-BR', {minimumFractionDigits: 2});
 document.getElementById('quantidade-vendas').textContent = data.totais.quantidade_vendas;
 document.getElementById('total-troco').textContent = 
 'R$ ' + data.totais.total_troco.toLocaleString('pt-BR', {minimumFractionDigits: 2});
 refreshIcon.classList.remove('refresh-indicator');
 })
 .catch(error => {
 console.error('Erro ao atualizar dados:', error);
 refreshIcon.classList.remove('refresh-indicator');
 });
 }
 intervalAtualizacao = setInterval(atualizarDados, 30000);
 document.addEventListener('visibilitychange', function() {
 if (document.hidden) {
 clearInterval(intervalAtualizacao);
 } else {
 intervalAtualizacao = setInterval(atualizarDados, 30000);
 }
 });
 </script>
</body>
</html>