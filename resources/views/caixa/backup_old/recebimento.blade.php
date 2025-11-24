<!DOCTYPE html>
<html lang="pt-BR">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <title>Recebimento de Pagamento - MyD Bar & Restaurantes</title>
 <!-- Bootstrap CSS -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <!-- Bootstrap Icons -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
 <!-- Font Awesome -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
 <style>
 body {
 background-color: #f8f9fa;
 }
 .card {
 border-radius: 15px;
 box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
 }
 .btn-custom {
 border-radius: 10px;
 padding: 12px 24px;
 font-weight: 600;
 }
 .bg-gradient-primary {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 }
 .bg-gradient-success {
 background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
 }
 .bg-gradient-info {
 background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
 }
 </style>
</head>
<body>
<div class="container-fluid">
 <div class="row">
 <div class="col-12">
 <div class="d-flex justify-content-between align-items-center mb-4">                <h2 class="text-primary fw-bold">
 <i class="fas fa-cash-register me-2"></i>
 Recebimento de Pagamento
 </h2>
 <a href="{{ route('caixa.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-arrow-left me-1"></i>
 Voltar ao Caixa
 </a>
 </div>
 <!-- Card do Pedido -->
 <div class="card mb-4 shadow-sm">
 <div class="card-header bg-gradient-primary text-white">
 <h5 class="card-title mb-0">
 <i class="fas fa-receipt me-2"></i>
 Pedido #{{ $pedido->id }} - Mesa {{ $pedido->mesa->numero ?? 'Balcão' }}
 </h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-6">
 <h6 class="text-muted">Itens do Pedido:</h6>                            <ul class="list-unstyled">
 @foreach($pedido->itens as $item)
 <li class="d-flex justify-content-between">
 <span>{{ $item->quantidade }}x {{ $item->produto->nome }}</span>
 <span class="text-success fw-bold">R$ {{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}</span>
 </li>
 @endforeach
 </ul>
 </div>
 <div class="col-md-6">                                <div class="card bg-light">
 <div class="card-body text-center">
 <h4 class="text-primary mb-0">Total a Pagar</h4>
 <h2 class="text-success fw-bold" id="total-pedido">R$ {{ number_format($pedido->total, 2, ',', '.') }}</h2>
 @if($pedido->pagamentos->where('status', 'confirmado')->sum('valor') > 0)
 <p class="text-muted mb-0">
 Pago: R$ {{ number_format($pedido->pagamentos->where('status', 'confirmado')->sum('valor'), 2, ',', '.') }}
 </p>
 <h5 class="text-warning">
 Restante: R$ {{ number_format($pedido->total - $pedido->pagamentos->where('status', 'confirmado')->sum('valor'), 2, ',', '.') }}
 </h5>
 @endif
 </div>
 </div>
 </div>
 </div>
 </div>            </div>            <!-- Formulário de Pagamento (processa via API JavaScript) -->
 <form id="form-pagamento" onsubmit="return false;">
 @csrf
 <div class="row">
 <!-- Coluna Esquerda - Formas de Pagamento -->
 <div class="col-md-8">
 <div class="card shadow-sm">
 <div class="card-header bg-gradient-success text-white">
 <h5 class="mb-0">
 <i class="fas fa-credit-card me-2"></i>
 Formas de Pagamento
 </h5>
 </div>
 <div class="card-body">
 <!-- Seleção da Forma de Pagamento -->
 <div class="row mb-4">
 <div class="col-md-6">
 <label class="form-label fw-bold">Forma de Pagamento:</label>
 <select name="forma_pagamento" class="form-select form-select-lg" id="forma-pagamento" required>
 <option value="">Selecione...</option>
 <option value="dinheiro">💵 Dinheiro</option>
 <option value="cartao_credito">💳 Cartão de Crédito</option>
 <option value="cartao_debito">💳 Cartão de Débito</option>
 <option value="pix">📱 PIX</option>
 <option value="vale_refeicao">🎫 Vale Refeição</option>
 </select>
 </div>
 <div class="col-md-6">
 <label class="form-label fw-bold">Valor do Pagamento:</label>                                        <div class="input-group input-group-lg">
 <span class="input-group-text">R$</span>
 <input type="number" name="valor" class="form-control" id="valor-pagamento" 
 step="0.01" min="0.01" max="{{ $pedido->total }}" 
 value="{{ $pedido->total }}" required>
 </div>
 </div>
 </div>
 <!-- Campo específico para Dinheiro -->
 <div id="campos-dinheiro" class="row mb-3" style="display: none;">
 <div class="col-md-6">
 <label class="form-label fw-bold">Valor Recebido:</label>
 <div class="input-group input-group-lg">
 <span class="input-group-text">R$</span>
 <input type="number" name="valor_recebido" class="form-control" 
 id="valor-recebido" step="0.01" min="0">
 </div>
 </div>
 <div class="col-md-6">
 <label class="form-label fw-bold">Troco:</label>
 <div class="input-group input-group-lg">
 <span class="input-group-text">R$</span>
 <input type="text" class="form-control bg-light" id="troco" readonly>
 </div>
 </div>
 </div>
 <!-- Observações -->
 <div class="mb-3">
 <label class="form-label fw-bold">Observações (Opcional):</label>
 <textarea name="observacoes" class="form-control" rows="2" 
 placeholder="Ex: Cliente pagou com nota de R$ 100,00"></textarea>
 </div>
 <!-- Atalhos de Valores para Dinheiro -->
 <div id="atalhos-dinheiro" style="display: none;">
 <label class="form-label fw-bold">Atalhos de Valores:</label>
 <div class="row">
 <div class="col-md-12">
 <div class="btn-group flex-wrap" role="group">
 <button type="button" class="btn btn-outline-primary atalho-valor" data-valor="10">R$ 10</button>
 <button type="button" class="btn btn-outline-primary atalho-valor" data-valor="20">R$ 20</button>
 <button type="button" class="btn btn-outline-primary atalho-valor" data-valor="50">R$ 50</button>
 <button type="button" class="btn btn-outline-primary atalho-valor" data-valor="100">R$ 100</button>
 <button type="button" class="btn btn-outline-primary atalho-valor" data-valor="200">R$ 200</button>
 <button type="button" class="btn btn-outline-success" id="valor-exato">Valor Exato</button>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Coluna Direita - Resumo e Ações -->
 <div class="col-md-4">
 <div class="card shadow-sm">
 <div class="card-header bg-gradient-info text-white">
 <h5 class="mb-0">
 <i class="fas fa-calculator me-2"></i>
 Resumo do Pagamento
 </h5>
 </div>
 <div class="card-body">
 <div class="d-grid gap-2">                                    <div class="p-3 bg-light rounded">
 <div class="d-flex justify-content-between mb-2">
 <span>Valor do Pedido:</span>
 <strong class="text-primary">R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong>
 </div>
 <div class="d-flex justify-content-between mb-2">
 <span>Valor a Pagar:</span>
 <strong class="text-success" id="resumo-valor">R$ 0,00</strong>
 </div>
 <div id="resumo-recebido" class="d-flex justify-content-between mb-2" style="display: none;">
 <span>Valor Recebido:</span>
 <strong class="text-info">R$ 0,00</strong>
 </div>
 <div id="resumo-troco" class="d-flex justify-content-between" style="display: none;">
 <span>Troco:</span>
 <strong class="text-warning">R$ 0,00</strong>
 </div>
 </div>
 <button type="submit" class="btn btn-success btn-lg" id="btn-processar">
 <i class="fas fa-check me-2"></i>
 Processar Pagamento
 </button>
 <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalMultiplosPagamentos">
 <i class="fas fa-layer-group me-2"></i>
 Múltiplas Formas
 </button>
 <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-outline-info">
 <i class="fas fa-eye me-2"></i>
 Ver Detalhes
 </a>                                    <a href="{{ route('caixa.index') }}" class="btn btn-outline-secondary">
 <i class="fas fa-times me-2"></i>
 Cancelar
 </a>
 </div>
 </div>
 </div>
 <!-- Card de Pagamentos Existentes -->
 @if($pedido->pagamentos->count() > 0)
 <div class="card shadow-sm mt-3">
 <div class="card-header bg-gradient-warning text-dark">
 <h6 class="mb-0">
 <i class="fas fa-history me-2"></i>
 Pagamentos Realizados
 </h6>
 </div>
 <div class="card-body">                                    @foreach($pedido->pagamentos as $pagamento)
 <div class="d-flex justify-content-between align-items-center mb-2">
 <small>
 <strong>{{ ucfirst(str_replace('_', ' ', $pagamento->forma_pagamento)) }}</strong><br>
 <span class="text-muted">{{ $pagamento->created_at->format('H:i') }}</span>
 </small>
 <span class="badge bg-{{ $pagamento->status == 'confirmado' ? 'success' : 'warning' }}">
 R$ {{ number_format($pagamento->valor, 2, ',', '.') }}
 </span>
 </div>
 @endforeach
 </div>
 </div>
 @endif
 </div>
 </div>
 </form>
 </div>
 </div>
</div>
<!-- Modal para Múltiplos Pagamentos -->
<div class="modal fade" id="modalMultiplosPagamentos" tabindex="-1">
 <div class="modal-dialog modal-lg">
 <div class="modal-content">
 <div class="modal-header bg-gradient-warning text-dark">
 <h5 class="modal-title">
 <i class="fas fa-layer-group me-2"></i>
 Múltiplas Formas de Pagamento
 </h5>
 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
 </div>
 <div class="modal-body">
 <p class="text-muted">
 Utilize esta opção quando o cliente quiser pagar com mais de uma forma 
 (ex: parte em dinheiro e parte no cartão).
 </p>
 <div id="formas-multiplas">
 <!-- Será preenchido via JavaScript -->
 </div>
 <!-- Debug Info -->
 <div class="alert alert-info mt-3">
 <h6><i class="fas fa-info-circle me-2"></i>Debug Info:</h6>
 <small>
 <strong>Valor Total:</strong> R$ {{ number_format($pedido->total, 2, ',', '.') }}<br>
 <strong>Status do Pedido:</strong> {{ $pedido->status }}<br>
 <strong>jQuery:</strong> <span id="jquery-status">Verificando...</span><br>
 <strong>Eventos:</strong> <span id="eventos-status">Verificando...</span>
 </small>
 </div>
 <button type="button" class="btn btn-outline-primary" id="adicionar-forma">
 <i class="fas fa-plus me-1"></i>
 Adicionar Forma de Pagamento
 </button>
 <!-- Botão de Teste -->
 <button type="button" class="btn btn-outline-info mt-2" id="testar-conectividade">
 <i class="fas fa-wifi me-1"></i>
 Testar Conectividade
 </button>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-success" id="confirmar-multiplos">
 <i class="fas fa-check me-1"></i>
 Processar Pagamentos
 </button>
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
 </div>
 </div>
 </div>
</div>
</body>
</html>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
 const valorTotal = {{ $pedido->total }};
 console.log('Sistema de recebimento carregado');
 console.log('Valor total do pedido:', valorTotal);
 if (typeof $ === 'undefined') {
 console.error('jQuery não está carregado!');
 alert('Erro: jQuery não está carregado. Recarregue a página.');
 return;
 }
 console.log('jQuery carregado com sucesso');
 document.getElementById('jquery-status').textContent = 'OK ✅';
 const adicionarFormaBtn = document.getElementById('adicionar-forma');
 const confirmarMultiplosBtn = document.getElementById('confirmar-multiplos');
 if (adicionarFormaBtn) {
 document.getElementById('eventos-status').textContent = 'Elementos encontrados ✅';
 console.log('Elemento adicionar-forma encontrado');
 } else {
 document.getElementById('eventos-status').textContent = 'Elemento adicionar-forma NÃO encontrado ❌';
 console.error('Elemento adicionar-forma não encontrado!');
 }
 $('#forma-pagamento').change(function() {
 const forma = $(this).val();
 if (forma === 'dinheiro') {
 $('#campos-dinheiro').show();
 $('#atalhos-dinheiro').show();
 $('#valor-recebido').prop('required', true);
 } else {
 $('#campos-dinheiro').hide();
 $('#atalhos-dinheiro').hide();
 $('#valor-recebido').prop('required', false);
 $('#resumo-recebido').hide();
 $('#resumo-troco').hide();
 }
 atualizarResumo();
 });
 $('#valor-pagamento').on('input', function() {
 atualizarResumo();
 });
 $('#valor-recebido').on('input', function() {
 calcularTroco();
 atualizarResumo();
 });
 $('.atalho-valor').click(function() {
 const valor = $(this).data('valor');
 $('#valor-recebido').val(valor);
 calcularTroco();
 atualizarResumo();
 });
 $('#valor-exato').click(function() {
 const valorPagamento = parseFloat($('#valor-pagamento').val()) || 0;
 $('#valor-recebido').val(valorPagamento.toFixed(2));
 calcularTroco();
 atualizarResumo();
 });
 function calcularTroco() {
 const valorPagamento = parseFloat($('#valor-pagamento').val()) || 0;
 const valorRecebido = parseFloat($('#valor-recebido').val()) || 0;
 const troco = valorRecebido - valorPagamento;
 $('#troco').val(troco >= 0 ? troco.toFixed(2) : '0.00');
 if (valorRecebido < valorPagamento) {
 $('#valor-recebido').addClass('is-invalid');
 $('#btn-processar').prop('disabled', true);
 } else {
 $('#valor-recebido').removeClass('is-invalid');
 $('#btn-processar').prop('disabled', false);
 }
 }
 function atualizarResumo() {
 const valorPagamento = parseFloat($('#valor-pagamento').val()) || 0;
 const valorRecebido = parseFloat($('#valor-recebido').val()) || 0;
 const forma = $('#forma-pagamento').val();
 $('#resumo-valor').text('R$ ' + valorPagamento.toLocaleString('pt-BR', {
 minimumFractionDigits: 2,
 maximumFractionDigits: 2
 }));
 if (forma === 'dinheiro') {
 $('#resumo-recebido').show().find('strong').text('R$ ' + valorRecebido.toLocaleString('pt-BR', {
 minimumFractionDigits: 2,
 maximumFractionDigits: 2
 }));
 const troco = Math.max(0, valorRecebido - valorPagamento);
 $('#resumo-troco').show().find('strong').text('R$ ' + troco.toLocaleString('pt-BR', {
 minimumFractionDigits: 2,
 maximumFractionDigits: 2
 }));
 }
 }
 $('#form-pagamento').submit(function(e) {
 e.preventDefault();
 const forma = $('#forma-pagamento').val();
 const valorPagamento = parseFloat($('#valor-pagamento').val()) || 0;
 console.log('🚀 [API UNIFICADA] Processando pagamento único:', forma, valorPagamento);
 if (!forma) {
 alert('Selecione uma forma de pagamento!');
 return false;
 }
 if (valorPagamento <= 0) {
 alert('O valor do pagamento deve ser maior que zero!');
 return false;
 }
 let valorRecebido = valorPagamento;
 if (forma === 'dinheiro') {
 valorRecebido = parseFloat($('#valor-recebido').val()) || 0;
 if (valorRecebido < valorPagamento) {
 alert('O valor recebido não pode ser menor que o valor do pagamento!');
 return false;
 }
 }
 if (!confirm('Confirma o processamento do pagamento via API unificada?')) {
 return false;
 }
 console.log('🚀 [API UNIFICADA] Enviando pagamento único...');
 const btn = $('#btn-processar');
 const originalText = btn.html();
 btn.html('<i class="fas fa-spinner fa-spin me-1"></i>Processando via API...').prop('disabled', true);
 const dadosAPI = {
 forma_pagamento: forma,
 valor: valorPagamento,
 observacoes: $('#observacoes').val() || 'Pagamento via modo caixa'
 };
 if (forma === 'dinheiro') {
 dadosAPI.valor_recebido = valorRecebido;
 }
 console.log('Dados para API:', dadosAPI);
 fetch(`/api/pagamentos-teste/pedido/{{ $pedido->id }}`, {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'Accept': 'application/json',
 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 },
 body: JSON.stringify(dadosAPI)
 })
 .then(response => {
 console.log('🚀 [API UNIFICADA] Status:', response.status);
 if (!response.ok) {
 throw new Error(`HTTP error! status: ${response.status}`);
 }
 return response.json();
 })
 .then(data => {
 console.log('🚀 [API UNIFICADA] Resposta:', data);
 if (data.success) {
 const pagamento = data.data.pagamentos[0];
 const troco = pagamento.troco || 0;
 let resumo = `✅ Pagamento processado com sucesso!\n\n` +
 `💰 Valor: R$ ${pagamento.valor.toFixed(2).replace('.', ',')}\n` +
 `💳 Forma: ${pagamento.forma_pagamento}`;
 if (forma === 'dinheiro' && troco > 0) {
 resumo += `\n💵 Troco: R$ ${troco.toFixed(2).replace('.', ',')}`;
 }
 alert(resumo);
 window.location.href = '{{ route("caixa.index") }}';
 } else {
 alert('❌ Erro ao processar pagamento via API: ' + (data.message || 'Erro desconhecido'));
 }        })
 .catch(error => {
 console.error('❌ Erro ao processar pagamento via API:', error);
 alert('❌ Erro ao processar pagamento via API. Verifique sua conexão e tente novamente.');
 })
 .finally(() => {
 btn.html(originalText).prop('disabled', false);
 });
 });// Múltiplas formas de pagamento
 let contadorFormas = 0;
 console.log('Configurando múltiplos pagamentos...');
 const btnAdicionar = $('#adicionar-forma');
 if (btnAdicionar.length === 0) {
 console.error('ERRO: Botão adicionar-forma não encontrado!');
 alert('ERRO: Botão adicionar-forma não encontrado!');
 return;
 } else {
 console.log('Botão adicionar-forma encontrado:', btnAdicionar);
 }
 $('#adicionar-forma').click(function() {
 console.log('Clique em adicionar forma detectado');
 alert('Botão funcionando! Adicionando forma...');
 contadorFormas++;
 const html = `
 <div class="card mb-3 forma-pagamento-item" data-index="${contadorFormas}">
 <div class="card-body">
 <div class="row">
 <div class="col-md-6">
 <label class="form-label">Forma de Pagamento:</label>
 <select class="form-select forma-multipla" name="formas_multiplas[${contadorFormas}][forma_pagamento]" required>
 <option value="">Selecione...</option>
 <option value="dinheiro">💵 Dinheiro</option>
 <option value="cartao_credito">💳 Cartão de Crédito</option>
 <option value="cartao_debito">💳 Cartão de Débito</option>
 <option value="pix">📱 PIX</option>
 <option value="vale_refeicao">🎫 Vale Refeição</option>
 </select>
 </div>
 <div class="col-md-5">
 <label class="form-label">Valor:</label>
 <div class="input-group">
 <span class="input-group-text">R$</span>
 <input type="number" class="form-control valor-multiplo" 
 name="formas_multiplas[${contadorFormas}][valor]" 
 step="0.01" min="0.01" required>
 </div>
 </div>
 <div class="col-md-1">
 <label class="form-label">&nbsp;</label>
 <button type="button" class="btn btn-outline-danger w-100 remover-forma">
 <i class="fas fa-trash"></i>
 </button>
 </div>
 </div>
 </div>
 </div>
 `;
 $('#formas-multiplas').append(html);
 console.log('Forma de pagamento adicionada, contador:', contadorFormas);
 calcularTotalMultiplo();
 });
 $(document).on('click', '.remover-forma', function() {
 $(this).closest('.forma-pagamento-item').remove();
 calcularTotalMultiplo();
 });
 $(document).on('input', '.valor-multiplo', function() {
 calcularTotalMultiplo();
 });
 function calcularTotalMultiplo() {
 let totalInformado = 0;
 $('.valor-multiplo').each(function() {
 totalInformado += parseFloat($(this).val()) || 0;
 });
 const restante = valorTotal - totalInformado;
 $('.alert-info-total').remove();
 if (restante > 0) {
 $('#formas-multiplas').append(`
 <div class="alert alert-info alert-info-total">
 <strong>Restante a pagar: R$ ${restante.toLocaleString('pt-BR', {
 minimumFractionDigits: 2,
 maximumFractionDigits: 2
 })}</strong>
 </div>
 `);
 } else if (restante < 0) {
 $('#formas-multiplas').append(`
 <div class="alert alert-warning alert-info-total">
 <strong>Valor excedido em: R$ ${Math.abs(restante).toLocaleString('pt-BR', {
 minimumFractionDigits: 2,
 maximumFractionDigits: 2
 })}</strong>
 </div>
 `);
 } else {
 $('#formas-multiplas').append(`
 <div class="alert alert-success alert-info-total">
 <strong>Total correto! Valor: R$ ${totalInformado.toLocaleString('pt-BR', {
 minimumFractionDigits: 2,
 maximumFractionDigits: 2
 })}</strong>
 </div>
 `);
 }
 const btnConfirmar = $('#confirmar-multiplos');
 if (Math.abs(restante) < 0.01 && totalInformado > 0) {
 btnConfirmar.prop('disabled', false);
 } else {
 btnConfirmar.prop('disabled', true);
 }
 }
 $('#confirmar-multiplos').click(function() {
 console.log('🚀 [API UNIFICADA] Iniciando múltiplos pagamentos');
 const formas = [];
 let totalInformado = 0;
 $('.forma-pagamento-item').each(function() {
 const forma = $(this).find('.forma-multipla').val();
 const valor = parseFloat($(this).find('.valor-multiplo').val()) || 0;
 console.log('Processando forma:', forma, 'valor:', valor);
 if (forma && valor > 0) {
 formas.push({
 forma_pagamento: forma,
 valor: valor,
 observacoes: `Múltiplos pagamentos - ${forma}`
 });
 totalInformado += valor;
 }
 });
 console.log('Formas coletadas:', formas);
 console.log('Total informado:', totalInformado);
 console.log('Valor total do pedido:', valorTotal);
 if (formas.length === 0) {
 console.error('Nenhuma forma de pagamento válida');
 alert('Adicione pelo menos uma forma de pagamento!');
 return;
 }
 if (Math.abs(totalInformado - valorTotal) > 0.01) {
 console.error('Total não confere:', totalInformado, 'vs', valorTotal);
 alert('O total dos pagamentos deve ser igual ao valor do pedido!');
 return;
 }
 if (!confirm(`Confirma o processamento de ${formas.length} pagamentos no valor total de R$ ${totalInformado.toFixed(2).replace('.', ',')}?`)) {
 console.log('Usuário cancelou confirmação');
 return;
 }
 console.log('🚀 [API UNIFICADA] Enviando para API...');
 const btn = $(this);
 const originalText = btn.html();
 btn.html('<i class="fas fa-spinner fa-spin me-1"></i>Processando via API...').prop('disabled', true);
 const dadosAPI = {
 multiplos_pagamentos: JSON.stringify(formas)
 };
 console.log('Dados para API:', dadosAPI);
 fetch(`/api/pagamentos-teste/pedido/{{ $pedido->id }}`, {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'Accept': 'application/json',
 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 },
 body: JSON.stringify(dadosAPI)
 })
 .then(response => {
 console.log('🚀 [API UNIFICADA] Status da resposta:', response.status);
 if (!response.ok) {
 throw new Error(`HTTP error! status: ${response.status}`);
 }
 return response.json();
 })
 .then(data => {
 console.log('🚀 [API UNIFICADA] Resposta:', data);
 if (data.success) {
 const resumo = `✅ Múltiplos pagamentos processados com sucesso!\n\n` +
 `💰 Total processado: R$ ${data.data.total_processado.toFixed(2).replace('.', ',')}\n` +
 `🧾 Quantidade de pagamentos: ${data.data.pagamentos.length}\n` +
 `📋 Pedido totalmente pago: ${data.data.pedido_totalmente_pago ? 'Sim' : 'Não'}\n` +
 `💳 Saldo restante: R$ ${data.data.saldo_restante.toFixed(2).replace('.', ',')}`;
 alert(resumo);
 window.location.href = '{{ route("caixa.index") }}';
 } else {
 alert('❌ Erro ao processar pagamentos via API: ' + (data.message || 'Erro desconhecido'));
 }
 })        .catch(error => {
 console.error('🚀 [API UNIFICADA] Erro:', error);
 alert('❌ Erro ao processar pagamentos via API: ' + error.message);
 })
 .finally(() => {            console.log('🏁 Finalizando processamento...');
 btn.html(originalText).prop('disabled', false);
 });
 });
 });
</script>