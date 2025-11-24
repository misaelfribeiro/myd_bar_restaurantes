@extends('layouts.app')
@section('title', 'Pedidos')
@section('content')
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-clipboard-list me-2"></i>
 Pedidos
 </h1>
 <p class="page-subtitle">Gerencie todos os pedidos do estabelecimento</p>
 </div>
 <div class="d-flex gap-2">
 <button class="btn btn-outline-info" onclick="toggleAutoRefresh()" id="autoRefreshBtn">
 <i class="fas fa-sync-alt me-2"></i>
 Auto-Refresh: OFF
 </button>
 <a href="{{ route('pedidos.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Novo Pedido
 </a>
 </div>
 </div>
 </div>
 <!-- Estat�sticas -->
 <div class="row mb-4">
 <div class="col-md-2">
 <div class="stats-card">
 <div class="stats-icon bg-primary">
 <i class="fas fa-clipboard-list"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $pedidos->count() }}</h3>
 <p>Total</p>
 </div>
 </div>
 </div>
 <div class="col-md-2">
 <div class="stats-card">
 <div class="stats-icon bg-warning">
 <i class="fas fa-clock"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $pedidos->where('status', 'pendente')->count() }}</h3>
 <p>Pendentes</p>
 </div>
 </div>
 </div>
 <div class="col-md-2">
 <div class="stats-card">
 <div class="stats-icon bg-info">
 <i class="fas fa-fire"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $pedidos->where('status', 'em_preparo')->count() }}</h3>
 <p>Em Preparo</p>
 </div>
 </div>
 </div>
 <div class="col-md-2">
 <div class="stats-card">
 <div class="stats-icon bg-success">
 <i class="fas fa-check"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $pedidos->where('status', 'pronto')->count() }}</h3>
 <p>Prontos</p>
 </div>
 </div>
 </div>
 <div class="col-md-2">
 <div class="stats-card">
 <div class="stats-icon bg-primary">
 <i class="fas fa-truck"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $pedidos->where('status', 'entregue')->count() }}</h3>
 <p>Entregues</p>
 </div>
 </div>
 </div>
 <div class="col-md-2">
 <div class="stats-card">
 <div class="stats-icon bg-danger">
 <i class="fas fa-times"></i>
 </div>
 <div class="stats-content">
 <h3>{{ $pedidos->where('status', 'cancelado')->count() }}</h3>
 <p>Cancelados</p>
 </div>
 </div>
 </div>
 </div>
 <!-- Filtros -->
 <div class="filters-card mb-4">
 <div class="row">
 <div class="col-md-3">
 <div class="search-box">
 <i class="fas fa-search"></i>
 <input type="text" id="searchInput" placeholder="Buscar pedidos..." class="form-control">
 </div>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="statusFilter">
 <option value="">Todos Status</option>
 <option value="pendente">Pendente</option>
 <option value="em_preparo">Em Preparo</option>
 <option value="pronto">Pronto</option>
 <option value="entregue">Entregue</option>
 <option value="finalizado">Finalizado</option>
 <option value="cancelado">Cancelado</option>
 </select>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="mesaFilter">
 <option value="">Todas Mesas</option>
 @foreach($mesas ?? [] as $mesa)
 <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero }}</option>
 @endforeach
 </select>
 </div>
 <div class="col-md-2">
 <select class="form-select" id="sortSelect">
 <option value="recente">Mais Recente</option>
 <option value="antigo">Mais Antigo</option>
 <option value="valor_desc">Maior Valor</option>
 <option value="valor_asc">Menor Valor</option>
 <option value="status">Por Status</option>
 </select>
 </div>
 <div class="col-md-1">
 <input type="date" class="form-control" id="dataFilter" title="Filtrar por data">
 </div>
 <div class="col-md-2">
 <button class="btn btn-outline-primary w-100" onclick="refreshData()">
 <i class="fas fa-sync-alt me-2"></i>
 Atualizar
 </button>
 </div>
 </div>
 </div>
 <!-- Lista de Pedidos em Cards -->
 <div class="row" id="pedidosContainer">
 @foreach($pedidos as $pedido)
 <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
 <!-- Card do Pedido -->
 <div class="card pedido-card-compact h-100 border-start border-4 
 @if($pedido->status == 'pendente') border-warning
 @elseif($pedido->status == 'em_preparo') border-info
 @elseif($pedido->status == 'pronto') border-success
 @elseif($pedido->status == 'entregue') border-primary
 @elseif($pedido->status == 'cancelado') border-danger
 @else border-secondary
 @endif"
 data-id="{{ $pedido->id }}" 
 data-status="{{ $pedido->status }}">
 
 <!-- Header do Card -->
 <div class="card-header 
 @if($pedido->delivery && !$pedido->entregador_id && $pedido->status == 'em_preparo')
 bg-warning bg-opacity-25 border-warning border-3
 @elseif($pedido->status == 'pendente') bg-light border-secondary
 @elseif($pedido->status == 'em_preparo' && $pedido->entregador_id) bg-success bg-opacity-25 border-success
 @elseif($pedido->status == 'em_preparo') bg-info bg-opacity-25 border-info
 @elseif($pedido->status == 'pronto') bg-success bg-opacity-50 border-success border-2
 @elseif($pedido->status == 'entregue') bg-primary bg-opacity-25 border-primary
 @elseif($pedido->status == 'finalizado') bg-success bg-opacity-50 border-success border-2
 @elseif($pedido->status == 'cancelado') bg-danger bg-opacity-25 border-danger
 @else bg-light
 @endif py-2">
 <div class="d-flex justify-content-between align-items-start">
 <div class="flex-grow-1">
 <h6 class="mb-0 fw-bold">#{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</h6>
 <small class="text-muted">
 <i class="fas fa-clock me-1"></i>{{ $pedido->created_at->format('H:i') }}
 </small>
 
 @php
 $empresa = auth('admin')->check() ? auth('admin')->user()->empresa : null;
 $tempoEntrega = $empresa->tempo_entrega_minutos ?? 45;
 $horaInicio = $pedido->created_at->copy()->addMinutes(10);
 $horaFim = $pedido->created_at->copy()->addMinutes($tempoEntrega);
 @endphp
 
 @if($pedido->delivery || !$pedido->mesa)
 <div class="mt-1">
 <small class="text-primary fw-bold">
 <i class="fas fa-shipping-fast me-1"></i>
 Entrega: {{ $horaInicio->format('H:i') }} - {{ $horaFim->format('H:i') }}
 </small>
 </div>
 @endif
 </div>
 <div class="dropdown">
 <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
 <i class="fas fa-ellipsis-v"></i>
 </button>
 <ul class="dropdown-menu dropdown-menu-end">
 <li>
 <a class="dropdown-item" href="{{ route('pedidos.detalhes', $pedido->id) }}">
 <i class="fas fa-eye me-2"></i>Ver Detalhes
 </a>
 </li>
 <li>
 <a class="dropdown-item" href="{{ route('pedidos.comanda', $pedido->id) }}" target="_blank">
 <i class="fas fa-print me-2"></i>Imprimir Comanda
 </a>
 </li>
 @if($pedido->status == 'pendente' || $pedido->status == 'em_preparo')
 <li><hr class="dropdown-divider"></li>
 <li>
 <a class="dropdown-item text-danger" href="#" 
 onclick="if(confirm('Cancelar pedido?')) { alterarStatus({{ $pedido->id }}, 'cancelado'); }">
 <i class="fas fa-times me-2"></i>Cancelar
 </a>
 </li>
 @endif
 </ul>
 </div>
 </div>
 </div>
 
 <!-- Body do Card -->
 <div class="card-body p-3">
 <!-- Tipo de Pedido -->
 <div class="mb-2">
 @if($pedido->mesa)
 <span class="badge bg-secondary">
 <i class="fas fa-chair me-1"></i>Mesa {{ $pedido->mesa->numero }}
 </span>
 @elseif($pedido->delivery)
 <span class="badge bg-info">
 <i class="fas fa-truck me-1"></i>Delivery
 </span>
 @else
 <span class="badge bg-warning text-dark">
 <i class="fas fa-shopping-bag me-1"></i>Balcão
 </span>
 @endif
 
 <!-- Status do Pedido -->
 <span class="badge 
 @if($pedido->status == 'aberto') bg-warning text-dark
 @elseif($pedido->status == 'fechado' || $pedido->status == 'finalizado') bg-secondary
 @elseif($pedido->status == 'cancelado') bg-danger
 @else bg-info
 @endif">
 <i class="fas 
 @if($pedido->status == 'aberto') fa-hourglass-half
 @elseif($pedido->status == 'fechado' || $pedido->status == 'finalizado') fa-check-circle
 @elseif($pedido->status == 'cancelado') fa-times-circle
 @else fa-clock
 @endif me-1"></i>{{ strtoupper($pedido->status) }}
 </span>
 
 <!-- Status de Pagamento -->
 @php
 $totalPago = $pedido->pagamentos->where('status', 'confirmado')->sum('valor');
 $isPago = $totalPago >= $pedido->total;
 @endphp
 
 @if($isPago)
 <span class="badge bg-success">
 <i class="fas fa-check-circle me-1"></i>PAGO
 </span>
 @else
 <span class="badge bg-danger">
 <i class="fas fa-exclamation-circle me-1"></i>PENDENTE PAGAMENTO
 </span>
 @endif
 </div>
 
 <!-- Cliente/Gar�om -->
 @if($pedido->delivery && $pedido->delivery->cliente_nome)
 <div class="mb-2">
 <small class="text-muted">
 <i class="fas fa-user me-1"></i>{{ Str::limit($pedido->delivery->cliente_nome, 25) }}
 </small>
 </div>
 @elseif($pedido->usuario)
 <div class="mb-2">
 <small class="text-muted">
 <i class="fas fa-user-tie me-1"></i>{{ Str::limit($pedido->usuario->nome, 25) }}
 </small>
 </div>
 @endif
 
 <!-- Entregador (se delivery) -->
 @if($pedido->delivery && $pedido->entregador)
 <div class="mb-2">
 <small class="badge bg-success">
 <i class="fas fa-motorcycle me-1"></i>{{ Str::limit($pedido->entregador->nome, 20) }}
 </small>
 </div>
 @elseif($pedido->delivery && $pedido->status == 'em_preparo' && !$pedido->entregador)
 <div class="mb-2">
 <small class="badge bg-danger">
 <i class="fas fa-exclamation-triangle me-1"></i>Sem entregador
 </small>
 </div>
 @endif
 
 <!-- Itens -->
 <div class="mb-2">
 <small class="text-muted">
 <i class="fas fa-shopping-cart me-1"></i>{{ $pedido->itens->count() }} 
 {{ $pedido->itens->count() == 1 ? 'item' : 'itens' }}
 </small>
 </div>
 
 <!-- Forma de Pagamento -->
 @if($pedido->forma_pagamento)
 <div class="mb-2">
 <small class="text-muted">
 @switch($pedido->forma_pagamento)
 @case('dinheiro')
 <i class="fas fa-money-bill-wave me-1"></i>Dinheiro
 @break
 @case('cartao_credito')
 <i class="fas fa-credit-card me-1"></i>Cr�dito
 @break
 @case('cartao_debito')
 <i class="fas fa-credit-card me-1"></i>D�bito
 @break
 @case('pix')
 <i class="fab fa-pix me-1"></i>PIX
 @break
 @case('app')
 <i class="fas fa-mobile-alt me-1"></i>App
 @break
 @default
 <i class="fas fa-question-circle me-1"></i>{{ ucfirst($pedido->forma_pagamento) }}
 @endswitch
 </small>
 </div>
 @endif
 
 <!-- Valor Total -->
 <div class="d-flex justify-content-between align-items-center pt-2 border-top">
 <strong class="text-muted">Total:</strong>
 <h5 class="mb-0 text-success fw-bold">
 R$ {{ number_format($pedido->total, 2, ',', '.') }}
 </h5>
 </div>
 </div>
 
 <!-- Footer com Ações Rápidas -->
 @if($pedido->status == 'aberto')
 <div class="card-footer bg-light p-2">
 <div class="d-flex gap-1">
 @php
 $totalPagoBtn = $pedido->pagamentos->where('status', 'confirmado')->sum('valor');
 $isPagoBtn = $totalPagoBtn >= $pedido->total;
 @endphp
 
 <!-- Sempre mostrar botão para cozinha primeiro -->
 <button class="btn btn-warning btn-sm flex-fill" 
 onclick="alterarStatus({{ $pedido->id }}, 'em_preparo')" 
 title="Enviar para Cozinha">
 <i class="fas fa-fire me-1"></i>P/ Cozinha
 </button>
 
 @if(!$isPagoBtn)
 <a href="{{ route('pedidos.detalhes', $pedido->id) }}" 
 class="btn btn-success btn-sm" title="Registrar Pagamento">
 <i class="fas fa-dollar-sign"></i>
 </a>
 @endif
 
 <a href="{{ route('pedidos.detalhes', $pedido->id) }}" 
 class="btn btn-outline-primary btn-sm" title="Ver Detalhes">
 <i class="fas fa-eye"></i>
 </a>
 </div>
 </div>
 @elseif($pedido->status == 'pendente')
 <div class="card-footer bg-light p-2">
 <div class="d-flex gap-1">
 <button class="btn btn-warning btn-sm flex-fill" 
 onclick="alterarStatus({{ $pedido->id }}, 'em_preparo')" 
 title="Enviar para Cozinha">
 <i class="fas fa-fire me-1"></i>Enviar p/ Cozinha
 </button>
 
 <a href="{{ route('pedidos.detalhes', $pedido->id) }}" 
 class="btn btn-outline-primary btn-sm" title="Ver Detalhes">
 <i class="fas fa-eye"></i>
 </a>
 </div>
 </div>
 @elseif($pedido->status == 'em_preparo')
 <div class="card-footer bg-light p-2">
 <div class="d-flex gap-1">
 <button class="btn btn-success btn-sm flex-fill" 
 onclick="alterarStatus({{ $pedido->id }}, 'pronto')" 
 title="Marcar como Pronto">
 <i class="fas fa-check me-1"></i>Marcar Pronto
 </button>
 
 <a href="{{ route('pedidos.detalhes', $pedido->id) }}" 
 class="btn btn-outline-primary btn-sm" title="Ver Detalhes">
 <i class="fas fa-eye"></i>
 </a>
 </div>
 </div>
 @elseif($pedido->status == 'pronto')
 <div class="card-footer bg-light p-2">
 <div class="d-flex gap-1">
 <button class="btn btn-primary btn-sm flex-fill" 
 onclick="alterarStatus({{ $pedido->id }}, 'entregue')" 
 title="Marcar como Entregue">
 <i class="fas fa-truck me-1"></i>Entregar
 </button>
 
 <a href="{{ route('pedidos.detalhes', $pedido->id) }}" 
 class="btn btn-outline-primary btn-sm" title="Ver Detalhes">
 <i class="fas fa-eye"></i>
 </a>
 </div>
 </div>
 @elseif($pedido->status == 'entregue')
 <div class="card-footer bg-light p-2">
 <div class="d-flex gap-1">
 @php
 $totalPagoEntregue = $pedido->pagamentos->where('status', 'confirmado')->sum('valor');
 $isPagoEntregue = $totalPagoEntregue >= $pedido->total;
 @endphp
 
 @if(!$isPagoEntregue)
 <a href="{{ route('pedidos.detalhes', $pedido->id) }}" 
 class="btn btn-success btn-sm flex-fill" title="Registrar Pagamento">
 <i class="fas fa-dollar-sign me-1"></i>Receber Pagamento
 </a>
 @else
 <button class="btn btn-primary btn-sm flex-fill" 
 onclick="alterarStatus({{ $pedido->id }}, 'finalizado')" 
 title="Finalizar Pedido">
 <i class="fas fa-check-circle me-1"></i>Finalizar
 </button>
 @endif
 
 <a href="{{ route('pedidos.detalhes', $pedido->id) }}" 
 class="btn btn-outline-primary btn-sm" title="Ver Detalhes">
 <i class="fas fa-eye"></i>
 </a>
 </div>
 </div>
 @endif
 </div>
 </div>
 @endforeach
 </div>
 
 @if($pedidos->isEmpty())
 <div class="empty-state text-center py-5">
 <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
 <h3>Nenhum pedido encontrado</h3>
 <p class="text-muted">Os pedidos aparecerão aqui conforme forem criados</p>
 <a href="{{ route('pedidos.create') }}" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Criar Primeiro Pedido
 </a>
 </div>
 @endif
 
</div>

@endsection

@push('styles')
<style>
 .pedido-card-compact {
 transition: transform 0.2s, box-shadow 0.2s;
 cursor: pointer;
 }
 .pedido-card-compact:hover {
 transform: translateY(-5px);
 box-shadow: 0 5px 15px rgba(0,0,0,0.1);
 }
 .bg-opacity-10 {
 opacity: 0.1;
 background-color: currentColor !important;
 }
</style>
@endpush

<!-- Modal para Atribuir Entregador -->
<div class="modal fade" id="modalEntregador" tabindex="-1">
 <div class="modal-dialog">
 <div class="modal-content">
 <div class="modal-header">
 <h5 class="modal-title">Atribuir Entregador</h5>
 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
 </div>
 <div class="modal-body">
 <div class="mb-3">
 <label for="entregadorSelect" class="form-label">Selecione o Entregador</label>
 <select class="form-select" id="entregadorSelect">
 <option value="">Carregando...</option>
 </select>
 </div>
 <input type="hidden" id="pedidoIdEntregador">
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
 <button type="button" class="btn btn-primary" onclick="confirmarAtribuicaoEntregador()">
 <i class="fas fa-motorcycle me-2"></i>Atribuir Entregador
 </button>
 </div>
 </div>
 </div>
</div>

@push('scripts')
<script>
let autoRefreshEnabled = false;
let autoRefreshInterval = null;
document.getElementById('searchInput').addEventListener('input', filterPedidos);
document.getElementById('statusFilter').addEventListener('change', filterPedidos);
document.getElementById('mesaFilter').addEventListener('change', filterPedidos);
document.getElementById('dataFilter').addEventListener('change', filterPedidos);
function filterPedidos() {
 const searchTerm = document.getElementById('searchInput').value.toLowerCase();
 const statusFilter = document.getElementById('statusFilter').value;
 const mesaFilter = document.getElementById('mesaFilter').value;
 const dataFilter = document.getElementById('dataFilter').value;
 const items = document.querySelectorAll('.pedido-item');
 items.forEach(item => {
 const id = item.dataset.id;
 const status = item.dataset.status;
 const mesa = item.dataset.mesa;
 const data = item.dataset.data;
 let show = true;
 if (searchTerm && !id.includes(searchTerm)) {
 show = false;
 }
 if (statusFilter && status !== statusFilter) {
 show = false;
 }
 if (mesaFilter && mesa !== mesaFilter) {
 show = false;
 }
 if (dataFilter && data !== dataFilter) {
 show = false;
 }
 item.style.display = show ? 'block' : 'none';
 });
}
document.getElementById('sortSelect').addEventListener('change', function() {
 const sortBy = this.value;
 const container = document.getElementById('pedidosContainer');
 const items = Array.from(container.children);
 items.sort((a, b) => {
 switch(sortBy) {
 case 'recente':
 return parseInt(b.dataset.id) - parseInt(a.dataset.id);
 case 'antigo':
 return parseInt(a.dataset.id) - parseInt(b.dataset.id);
 case 'valor_desc':
 return parseFloat(b.dataset.valor) - parseFloat(a.dataset.valor);
 case 'valor_asc':
 return parseFloat(a.dataset.valor) - parseFloat(b.dataset.valor);
 case 'status':
 return a.dataset.status.localeCompare(b.dataset.status);
 default:
 return 0;
 }
 });
 items.forEach(item => container.appendChild(item));
});
function alterarStatus(id, novoStatus) {
 let mensagem = '';
 switch(novoStatus) {
 case 'em_preparo':
 mensagem = 'iniciar o preparo deste pedido';
 break;
 case 'pronto':
 mensagem = 'marcar este pedido como pronto';
 break;
 case 'entregue':
 mensagem = 'marcar este pedido como entregue';
 break;
 case 'finalizado':
 mensagem = 'finalizar este pedido';
 break;
 case 'cancelado':
 mensagem = 'cancelar este pedido';
 break;
 }
 if (confirm(`Deseja ${mensagem}?`)) {
 console.log('Alterar status pedido:', id, 'para', novoStatus);
 updatePedidoStatus(id, novoStatus);
 }
}
function updatePedidoStatus(id, novoStatus) {
 // Criar form e submeter
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/garcom/pedidos/${id}/status`;
 
 // CSRF Token
 const csrfInput = document.createElement('input');
 csrfInput.type = 'hidden';
 csrfInput.name = '_token';
 csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
 form.appendChild(csrfInput);
 
 // Method PATCH
 const methodInput = document.createElement('input');
 methodInput.type = 'hidden';
 methodInput.name = '_method';
 methodInput.value = 'PATCH';
 form.appendChild(methodInput);
 
 // Status
 const statusInput = document.createElement('input');
 statusInput.type = 'hidden';
 statusInput.name = 'status';
 statusInput.value = novoStatus;
 form.appendChild(statusInput);
 
 // Adicionar ao body e submeter
 document.body.appendChild(form);
 form.submit();
}
function toggleAutoRefresh() {
 autoRefreshEnabled = !autoRefreshEnabled;
 const btn = document.getElementById('autoRefreshBtn');
 if (autoRefreshEnabled) {
 btn.innerHTML = '<i class="fas fa-sync-alt me-2 fa-spin"></i>Auto-Refresh: ON';
 btn.classList.remove('btn-outline-info');
 btn.classList.add('btn-info');
 autoRefreshInterval = setInterval(() => {
 refreshData();
 }, 30000);
 } else {
 btn.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Auto-Refresh: OFF';
 btn.classList.remove('btn-info');
 btn.classList.add('btn-outline-info');
 if (autoRefreshInterval) {
 clearInterval(autoRefreshInterval);
 }
 }
}
function refreshData() {
 location.reload();
}
document.getElementById('dataFilter').value = new Date().toISOString().split('T')[0];
function atribuirEntregador(pedidoId) {
 document.getElementById('pedidoIdEntregador').value = pedidoId;
 fetch('/api/entregadores/disponiveis')
 .then(response => response.json())
 .then(data => {
 const select = document.getElementById('entregadorSelect');
 select.innerHTML = '<option value="">Selecione um entregador...</option>';
 data.entregadores.forEach(entregador => {
 const option = document.createElement('option');
 option.value = entregador.id;
 option.textContent = `${entregador.nome} - ${entregador.tipo_veiculo}`;
 select.appendChild(option);
 });
 new bootstrap.Modal(document.getElementById('modalEntregador')).show();
 })
 .catch(error => {
 console.error('Erro ao buscar entregadores:', error);
 alert('Erro ao buscar entregadores dispon�veis');
 });
}
function confirmarAtribuicaoEntregador() {
 const pedidoId = document.getElementById('pedidoIdEntregador').value;
 const entregadorId = document.getElementById('entregadorSelect').value;
 if (!entregadorId) {
 alert('Por favor, selecione um entregador');
 return;
 }
 const formData = new FormData();
 formData.append('entregador_id', entregadorId);
 formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
 fetch(`/pedidos/${pedidoId}/atribuir-entregador`, {
 method: 'POST',
 body: formData
 })
 .then(response => response.json())
 .then(data => {
 if (data.success) {
 bootstrap.Modal.getInstance(document.getElementById('modalEntregador')).hide();
 location.reload();
 } else {
 alert(data.message || 'Erro ao atribuir entregador');
 }
 })
 .catch(error => {
 console.error('Erro:', error);
 alert('Erro ao atribuir entregador');
 });
}
</script>
@endpush
