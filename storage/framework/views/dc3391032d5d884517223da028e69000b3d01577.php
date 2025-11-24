
<?php $__env->startSection('title', 'Novo Pedido'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-plus-circle me-2"></i>
 Novo Pedido
 </h1>
 <p class="page-subtitle">Crie um novo pedido para o restaurante</p>
 </div>
 <a href="<?php echo e(route('pedidos.index')); ?>" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 </div>
 </div>
 <!-- Progress Steps -->
 <div class="card mb-4">
 <div class="card-body">
 <div class="row text-center">
 <div class="col-md-3">
 <div class="border-end">
 <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">1</div>
 <div class="mt-2">
 <strong>Tipo de Pedido</strong>
 <br><small class="text-muted">Mesa ou Delivery</small>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="border-end">
 <div class="step-number bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">2</div>
 <div class="mt-2">
 <strong>Mesa/Cliente</strong>
 <br><small class="text-muted">Escolha a mesa ou dados do cliente</small>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="border-end">
 <div class="step-number bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">3</div>
 <div class="mt-2">
 <strong>Adicionar Itens</strong>
 <br><small class="text-muted">Selecione produtos do cardápio</small>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="step-number bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">4</div>
 <div class="mt-2">
 <strong>Finalizar</strong>
 <br><small class="text-muted">Confirmar e criar o pedido</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <form id="pedidoForm">
 <?php echo csrf_field(); ?>
 <!-- Passo 1: Tipo de Pedido -->
 <div class="card mb-4" id="step1">
 <div class="card-header">
 <h5 class="card-title mb-0">
 <i class="fas fa-clipboard-list me-2"></i>
 Passo 1: Tipo de Pedido
 </h5>
 </div>
 <div class="card-body">
 <div class="row justify-content-center">
 <div class="col-md-6">
 <div class="row">
 <div class="col-6">
 <label class="form-check-label w-100" for="tipoMesa">
 <div class="card h-100 tipo-pedido-option border-primary" style="cursor: pointer;">
 <div class="card-body text-center">
 <i class="fas fa-chair fa-3x mb-3 text-primary"></i>
 <h5 class="card-title">Mesa</h5>
 <p class="card-text text-muted">Pedido para consumo no local</p>
 <input type="radio" 
 class="form-check-input position-absolute top-0 end-0 m-2" 
 name="tipo_pedido" 
 id="tipoMesa" 
 value="mesa"
 onchange="selecionarTipoPedido('mesa')">
 </div>
 </div>
 </label>
 </div>
 <div class="col-6">
 <label class="form-check-label w-100" for="tipoDelivery">
 <div class="card h-100 tipo-pedido-option border-info" style="cursor: pointer;">
 <div class="card-body text-center">
 <i class="fas fa-truck fa-3x mb-3 text-info"></i>
 <h5 class="card-title">Delivery</h5>
 <p class="card-text text-muted">Pedido para entrega</p>
 <input type="radio" 
 class="form-check-input position-absolute top-0 end-0 m-2" 
 name="tipo_pedido" 
 id="tipoDelivery" 
 value="delivery"
 onchange="selecionarTipoPedido('delivery')">
 </div>
 </div>
 </label>
 </div>
 </div>
 </div>
 </div>
 <div class="text-end mt-4">
 <button type="button" class="btn btn-primary" id="nextStep1" onclick="nextStep(2)" disabled>
 Próximo Passo
 <i class="fas fa-arrow-right ms-2"></i>
 </button>
 </div>
 </div>
 </div>
 <!-- Passo 2: Seleção de Mesa ou Dados do Cliente -->
 <div class="card mb-4 d-none" id="step2">
 <div class="card-header">
 <h5 class="card-title mb-0">
 <i class="fas fa-chair me-2"></i>
 Passo 2: <span id="step2Title">Selecionar Mesa</span>
 </h5>
 </div>
 <div class="card-body">
 <!-- Seção de Mesas (visível quando tipo_pedido = mesa) -->
 <div id="secaoMesas">
 <?php if($errors->has('mesa_id')): ?>
 <div class="alert alert-danger">
 <i class="fas fa-exclamation-triangle me-2"></i>
 <?php echo e($errors->first('mesa_id')); ?>

 </div>
 <?php endif; ?>
 <div class="row" id="mesasGrid">
 <?php $__empty_1 = true; $__currentLoopData = $mesas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mesa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
 <div class="col-md-3 col-sm-6 mb-3">
 <label class="form-check-label w-100" for="mesa<?php echo e($mesa->id); ?>">
 <div class="card h-100 mesa-option <?php echo e($mesa->pedidos->count() > 0 ? 'border-warning' : 'border-success'); ?>" style="cursor: pointer;">
 <div class="card-body text-center">
 <i class="fas fa-chair fa-2x mb-3 <?php echo e($mesa->pedidos->count() > 0 ? 'text-warning' : 'text-success'); ?>"></i>
 <h6 class="card-title"><?php echo e($mesa->identificador); ?></h6>
 <div class="mb-2">
 <span class="badge bg-<?php echo e($mesa->pedidos->count() > 0 ? 'warning' : 'success'); ?>">
 <?php echo e($mesa->pedidos->count() > 0 ? 'Ocupada' : 'Livre'); ?>

 </span>
 </div>
 <div class="text-muted">
 <i class="fas fa-users me-1"></i>
 <?php echo e($mesa->lugares); ?> lugares
 </div>
 <input type="radio" 
 class="form-check-input position-absolute top-0 end-0 m-2" 
 name="mesa_id" 
 id="mesa<?php echo e($mesa->id); ?>" 
 value="<?php echo e($mesa->id); ?>" 
 <?php echo e(old('mesa_id') == $mesa->id ? 'checked' : ''); ?>

 onchange="updateStepIndicator(2)">
 </div>
 </div>
 </label>
 </div>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
 <div class="col-12">
 <div class="text-center py-4 text-muted">
 <i class="fas fa-table fa-2x mb-3"></i>
 <p>Nenhuma mesa cadastrada ainda</p>
 <a href="<?php echo e(route('mesas.create')); ?>" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Cadastrar Mesa
 </a>
 </div>
 </div>
 <?php endif; ?>
 </div>
 </div>
 <!-- Seção de Dados do Cliente (visível quando tipo_pedido = delivery) -->
 <div id="secaoCliente" style="display: none;">
 <!-- Busca de Cliente -->
 <div class="mb-4">
 <label for="busca_cliente_pedido" class="form-label">
 <i class="fas fa-search me-2"></i>Buscar Cliente *
 </label>
 <input type="text" class="form-control" id="busca_cliente_pedido" 
 placeholder="Digite nome ou telefone (mínimo 3 caracteres)..." autocomplete="off">
 <!-- Resultados da busca -->
 <div id="resultados_cliente" class="list-group position-absolute w-100" 
 style="z-index: 1000; display: none; max-height: 300px; overflow-y: auto; margin-top: 2px;"></div>
 </div>
 <!-- Cliente selecionado -->
 <div id="cliente_selecionado_info" class="alert alert-success d-none">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <h6 class="mb-1"><i class="fas fa-user me-2"></i><strong id="cliente_sel_nome"></strong></h6>
 <small class="text-muted d-block">
 <i class="fas fa-phone me-1"></i><span id="cliente_sel_telefone"></span>
 </small>
 <small class="text-muted d-block">
 <i class="fas fa-map-marker-alt me-1"></i><span id="cliente_sel_endereco"></span>
 </small>
 </div>
 <button type="button" class="btn btn-sm btn-outline-danger" onclick="limparClientePedido()">
 <i class="fas fa-times"></i> Limpar
 </button>
 </div>
 </div>
 <!-- Alerta: Cliente sem endereço -->
 <div id="alerta_sem_endereco" class="alert alert-warning d-none">
 <i class="fas fa-exclamation-triangle me-2"></i>
 <strong>Atenção!</strong> Este cliente não possui endereço cadastrado. 
 É necessário cadastrar o endereço antes de continuar o pedido de delivery.
 <a href="<?php echo e(route('clientes.index')); ?>" target="_blank" class="alert-link ms-2">
 <i class="fas fa-external-link-alt me-1"></i>Cadastrar endereço agora
 </a>
 </div>
 <!-- Campos hidden para envio -->
 <input type="hidden" id="cliente_id" name="cliente_id">
 <input type="hidden" id="cliente_nome" name="cliente_nome">
 <input type="hidden" id="cliente_telefone" name="cliente_telefone">
 <input type="hidden" id="cliente_endereco" name="cliente_endereco">
 <input type="hidden" id="cliente_bairro" name="cliente_bairro">
 <!-- Link para cadastrar novo cliente -->
 <div class="alert alert-info">
 <i class="fas fa-info-circle me-2"></i>
 Cliente não encontrado? 
 <a href="<?php echo e(route('clientes.create')); ?>" target="_blank" class="alert-link">
 Cadastrar novo cliente
 </a>
 </div>
 <div class="row mt-3">
 <div class="col-md-12">
 <label for="observacoes_delivery" class="form-label">Observações para Entrega</label>
 <textarea class="form-control" id="observacoes_delivery" name="observacoes_delivery" 
 rows="2" placeholder="Pontos de referência, instruções especiais..."></textarea>
 </div>
 </div>
 </div>
 <div class="text-end mt-4">
 <button type="button" class="btn btn-secondary me-2" onclick="prevStep(1)">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </button>
 <button type="button" class="btn btn-primary" id="nextStep2" onclick="nextStep(3)" disabled>
 Próximo Passo
 <i class="fas fa-arrow-right ms-2"></i>
 </button>
 </div>
 </div>
 </div>
 <!-- Passo 3: Seleção de Itens -->
 <div class="card mb-4 d-none" id="step3">
 <div class="card-header">
 <h5 class="card-title mb-0">
 <i class="fas fa-shopping-cart me-2"></i>
 Passo 3: Adicionar Itens
 </h5>
 </div>
 <div class="card-body">
 <!-- Tabs: Produtos e Combos -->
 <ul class="nav nav-tabs mb-4" id="itemsTabs" role="tablist">
 <li class="nav-item" role="presentation">
 <button class="nav-link active" id="produtos-tab" data-bs-toggle="tab" data-bs-target="#produtos-panel" type="button" role="tab">
 <i class="fas fa-box me-2"></i>Produtos
 </button>
 </li>
 <li class="nav-item" role="presentation">
 <button class="nav-link" id="combos-tab" data-bs-toggle="tab" data-bs-target="#combos-panel" type="button" role="tab">
 <i class="fas fa-fire me-2"></i>Combos <span class="badge bg-danger ms-1">Promoção</span>
 </button>
 </li>
 </ul>

 <div class="tab-content" id="itemsTabContent">
 <!-- Tab Produtos -->
 <div class="tab-pane fade show active" id="produtos-panel" role="tabpanel">
 <div class="row">
 <div class="col-lg-8">
 <!-- Filtro por Categoria -->
 <div class="mb-4">
 <label class="form-label">Filtrar por Categoria:</label>
 <select class="form-select" id="categoriaFilter" onchange="filtrarProdutos()">
 <option value="">Todas as Categorias</option>
 <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <option value="<?php echo e($categoria->id); ?>"><?php echo e($categoria->nome); ?></option>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select>
 </div>
 <!-- Lista de Produtos -->
 <div id="produtosContainer">
 <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <?php if($categoria->produtos->count() > 0): ?>
 <div class="categoria-section mb-4" data-categoria="<?php echo e($categoria->id); ?>">
 <h6 class="text-primary border-bottom pb-2">
 <i class="fas fa-tag me-2"></i>
 <?php echo e($categoria->nome); ?>

 </h6>
 <div class="row">
 <?php $__currentLoopData = $categoria->produtos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <?php if($produto->ativo): ?>
 <div class="col-md-6 mb-3">
 <div class="card produto-card h-100">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-start">
 <div class="flex-grow-1">
 <h6 class="card-title"><?php echo e($produto->nome); ?></h6>
 <?php if($produto->descricao): ?>
 <p class="card-text text-muted small"><?php echo e(Str::limit($produto->descricao, 60)); ?></p>
 <?php endif; ?>
 <div class="fw-bold text-success">
 R$ <?php echo e(number_format($produto->preco, 2, ',', '.')); ?>

 </div>
 </div>
 <div class="text-end">
 <button type="button" 
 class="btn btn-outline-primary btn-sm" 
 onclick="adicionarItem('<?php echo e($produto->id); ?>', '<?php echo e($produto->nome); ?>', '<?php echo e($produto->preco); ?>')">
 <i class="fas fa-plus"></i>
 </button>
 </div>
 </div>
 </div>
 </div>
 </div>
 <?php endif; ?>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </div>
 </div>
 <?php endif; ?>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </div>
 </div>
 </div>
 </div>
 <!-- Tab Combos -->
 <div class="tab-pane fade" id="combos-panel" role="tabpanel">
 <?php if($combos->count() > 0): ?>
 <div class="row">
 <?php $__currentLoopData = $combos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $combo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <div class="col-md-6 mb-3">
 <div class="card combo-card h-100 border-warning">
 <div class="card-body">
 <?php if($combo->imagem): ?>
 <img src="<?php echo e(asset('storage/' . $combo->imagem)); ?>" class="card-img-top mb-2" alt="<?php echo e($combo->nome); ?>" style="height: 120px; object-fit: cover; border-radius: 8px;">
 <?php endif; ?>
 <div class="d-flex justify-content-between align-items-start mb-2">
 <div class="flex-grow-1">
 <h6 class="card-title">
 <i class="fas fa-fire text-warning me-1"></i>
 <?php echo e($combo->nome); ?>

 <?php if($combo->destaque): ?>
 <span class="badge bg-danger ms-1">Destaque</span>
 <?php endif; ?>
 </h6>
 <?php if($combo->descricao): ?>
 <p class="card-text text-muted small"><?php echo e(Str::limit($combo->descricao, 80)); ?></p>
 <?php endif; ?>
 </div>
 </div>
 
 <!-- Produtos inclusos -->
 <div class="bg-light p-2 rounded mb-2">
 <small class="text-muted fw-bold d-block mb-1">Produtos inclusos:</small>
 <ul class="list-unstyled mb-0 small">
 <?php $__currentLoopData = $combo->produtos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <li><i class="fas fa-check text-success me-1"></i> <?php echo e($produto->pivot->quantidade); ?>x <?php echo e($produto->nome); ?></li>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </ul>
 </div>

 <!-- Preços -->
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <small class="text-muted text-decoration-line-through">
 De R$ <?php echo e(number_format($combo->preco_original, 2, ',', '.')); ?>

 </small>
 <div class="fw-bold text-success fs-5">
 R$ <?php echo e(number_format($combo->preco_combo, 2, ',', '.')); ?>

 </div>
 <span class="badge bg-danger"><?php echo e($combo->desconto); ?>% OFF</span>
 <span class="badge bg-success">Economia R$ <?php echo e(number_format($combo->economia, 2, ',', '.')); ?></span>
 </div>
 <button type="button" 
 class="btn btn-warning btn-sm" 
 onclick="adicionarCombo(<?php echo e($combo->id); ?>, '<?php echo e(addslashes($combo->nome)); ?>', <?php echo e($combo->preco_combo); ?>)">
 <i class="fas fa-plus-circle"></i>
 Adicionar
 </button>
 </div>
 </div>
 </div>
 </div>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </div>
 <?php else: ?>
 <div class="alert alert-info">
 <i class="fas fa-info-circle me-2"></i>
 Nenhum combo disponível no momento.
 </div>
 <?php endif; ?>
 </div>
 </div>
 <!-- Fecha tab-content -->
 
 <!-- Carrinho (aparece abaixo das tabs) -->
 <div class="card mt-4">
 <div class="card-header bg-primary text-white">
 <h6 class="card-title mb-0">
 <i class="fas fa-shopping-bag me-2"></i>
 Itens do Pedido
 </h6>
 </div>
 <div class="card-body">
 <div id="itensPedido">
 <div class="text-center text-muted py-3">
 <i class="fas fa-shopping-cart fa-2x mb-2"></i>
 <p>Nenhum item adicionado</p>
 </div>
 </div>
 <div id="totalPedido" class="border-top pt-3 mt-3 d-none">
 <div class="d-flex justify-content-between align-items-center">
 <strong>Total:</strong>
 <h5 class="text-success mb-0" id="valorTotal">R$ 0,00</h5>
 </div>
 </div>
 </div>
 </div>
 
 <div class="text-end mt-4">
 <button type="button" class="btn btn-secondary me-2" onclick="prevStep(2)">
 <i class="fas fa-arrow-left me-2"></i>
 Passo Anterior
 </button>
 <button type="button" class="btn btn-primary" id="nextStep3" onclick="nextStep(4)" disabled>
 Próximo Passo
 <i class="fas fa-arrow-right ms-2"></i>
 </button>
 </div>
 </div>
 </div>
 <!-- Passo 4: Finalização -->
 <div class="card mb-4 d-none" id="step4">
 <div class="card-header">
 <h5 class="card-title mb-0">
 <i class="fas fa-check me-2"></i>
 Passo 4: Finalizar Pedido
 </h5>
 </div>
 <div class="card-body">
 <!-- Forma de Pagamento -->
 <div class="mb-4">
 <label class="form-label">
 <i class="fas fa-credit-card me-2"></i>
 Forma de Pagamento *
 </label>
 <div class="row g-3">
 <div class="col-md-3 col-6">
 <input type="radio" class="btn-check" name="forma_pagamento" id="pgto_dinheiro" value="dinheiro" autocomplete="off">
 <label class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="pgto_dinheiro">
 <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
 <span>Dinheiro</span>
 </label>
 </div>
 <div class="col-md-3 col-6">
 <input type="radio" class="btn-check" name="forma_pagamento" id="pgto_credito" value="credito" autocomplete="off">
 <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="pgto_credito">
 <i class="fas fa-credit-card fa-2x mb-2"></i>
 <span>Cartão Crédito</span>
 </label>
 </div>
 <div class="col-md-3 col-6">
 <input type="radio" class="btn-check" name="forma_pagamento" id="pgto_debito" value="debito" autocomplete="off">
 <label class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="pgto_debito">
 <i class="fas fa-credit-card fa-2x mb-2"></i>
 <span>Cartão Débito</span>
 </label>
 </div>
 <div class="col-md-3 col-6">
 <input type="radio" class="btn-check" name="forma_pagamento" id="pgto_pix" value="pix" autocomplete="off">
 <label class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="pgto_pix">
 <i class="fas fa-qrcode fa-2x mb-2"></i>
 <span>PIX</span>
 </label>
 </div>
 </div>
 </div>

 <!-- Observações -->
 <div class="mb-4">
 <label for="observacoes" class="form-label">
 <i class="fas fa-comment me-1"></i>
 Observações do Pedido (opcional)
 </label>
 <textarea class="form-control" 
 id="observacoes" 
 name="observacoes" 
 rows="3"
 placeholder="Digite observações especiais para este pedido..."><?php echo e(old('observacoes')); ?></textarea>
 </div>

 <!-- Resumo do Pedido -->
 <div class="card bg-light border-0">
 <div class="card-body">
 <h6 class="card-title mb-3">
 <i class="fas fa-clipboard-list me-2"></i>
 Resumo do Pedido
 </h6>
 <div id="resumoPedido">
 <div class="row mb-2">
 <div class="col-6"><strong>Mesa/Cliente:</strong></div>
 <div class="col-6 text-end"><span id="resumoMesa">-</span></div>
 </div>
 <div class="row mb-2">
 <div class="col-6"><strong>Quantidade de Itens:</strong></div>
 <div class="col-6 text-end"><span id="resumoItens">0</span></div>
 </div>
 <div class="row mb-2">
 <div class="col-6"><strong>Forma de Pagamento:</strong></div>
 <div class="col-6 text-end"><span id="resumoPagamento" class="text-muted">Não selecionada</span></div>
 </div>
 <hr>
 <div class="row">
 <div class="col-6"><strong class="fs-5">TOTAL:</strong></div>
 <div class="col-6 text-end"><span id="resumoTotal" class="text-success fw-bold fs-4">R$ 0,00</span></div>
 </div>
 </div>
 </div>
 </div>

 <!-- Opções de Impressão -->
 <div class="mt-4">
 <div class="form-check form-switch">
 <input class="form-check-input" type="checkbox" id="imprimir_comanda" name="imprimir_comanda" checked>
 <label class="form-check-label" for="imprimir_comanda">
 <i class="fas fa-print me-2"></i>
 Imprimir comanda para cozinha após criar pedido
 </label>
 </div>
 </div>

 <div class="text-end mt-4">
 <button type="button" class="btn btn-secondary me-2" onclick="prevStep(3)">
 <i class="fas fa-arrow-left me-2"></i>
 Passo Anterior
 </button>
 <button type="submit" class="btn btn-success btn-lg" id="finalizarPedido">
 <i class="fas fa-check me-2"></i>
 Criar Pedido
 </button>
 </div>
 </div>
 </div>
 <!-- Inputs Hidden para Itens -->
 <div id="hiddenInputs"></div>
 </form>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
let currentStep = 1;
let itensPedido = [];
let totalPedido = 0;
let tipoPedido = '';
function selecionarTipoPedido(tipo) {
 tipoPedido = tipo;
 document.getElementById('nextStep1').disabled = false;
 if (tipo === 'mesa') {
 document.getElementById('step2Title').textContent = 'Selecionar Mesa';
 document.getElementById('secaoMesas').style.display = 'block';
 document.getElementById('secaoCliente').style.display = 'none';
 } else {
 document.getElementById('step2Title').textContent = 'Dados do Cliente';
 document.getElementById('secaoMesas').style.display = 'none';
 document.getElementById('secaoCliente').style.display = 'block';
 }
}
function validarDadosCliente() {
 if (tipoPedido !== 'delivery') return true;
 const clienteId = document.getElementById('cliente_id').value;
 const nome = document.getElementById('cliente_nome').value;
 const telefone = document.getElementById('cliente_telefone').value;
 const endereco = document.getElementById('cliente_endereco').value;
 const bairro = document.getElementById('cliente_bairro').value;
 return clienteId && nome && telefone && endereco && bairro;
}
function atualizarBotaoPasso2() {
 const botao = document.getElementById('nextStep2');
 if (tipoPedido === 'mesa') {
 const mesaSelecionada = document.querySelector('input[name="mesa_id"]:checked');
 botao.disabled = !mesaSelecionada;
 } else {
 botao.disabled = !validarDadosCliente();
 }
}
let debounceTimerCliente = null;
document.addEventListener('DOMContentLoaded', function() {
 console.log('🚀 DOM loaded - Inicializando busca de clientes');
 const buscaInput = document.getElementById('busca_cliente_pedido');
 console.log('📝 Input de busca encontrado:', buscaInput);
 if (buscaInput) {
 buscaInput.addEventListener('input', function() {
 const termo = this.value.trim();
 console.log('⌨️ Digitado:', termo, '- Length:', termo.length);
 if (termo.length < 3) {
 document.getElementById('resultados_cliente').style.display = 'none';
 return;
 }
 clearTimeout(debounceTimerCliente);
 debounceTimerCliente = setTimeout(() => {
 console.log('⏰ Debounce completado, iniciando busca...');
 buscarClientesPedido(termo);
 }, 300);
 });
 }
});
async function buscarClientesPedido(termo) {
 try {
 console.log('🔍 Buscando clientes com termo:', termo);
 const response = await fetch(`/api/clientes/search?search=${encodeURIComponent(termo)}`, {
 method: 'GET',
 headers: {
 'Accept': 'application/json',
 'Content-Type': 'application/json'
 },
 credentials: 'same-origin'
 });
 
 if (!response.ok) {
 console.error('❌ Erro na resposta:', response.status, response.statusText);
 throw new Error(`HTTP error! status: ${response.status}`);
 }
 
 const data = await response.json();
 console.log('📡 Busca de clientes:', data);
 const resultados = document.getElementById('resultados_cliente');
 resultados.innerHTML = '';
 if (data.success && data.data.length > 0) {
 data.data.forEach(cliente => {
 const item = document.createElement('button');
 item.type = 'button';
 item.className = 'list-group-item list-group-item-action';
 item.innerHTML = `
 <div class="d-flex justify-content-between">
 <div>
 <strong>${cliente.nome}</strong><br>
 <small class="text-muted">${cliente.telefone}</small>
 </div>
 <div class="text-end">
 <small class="text-muted">${cliente.endereco_completo || 'Sem endereço'}</small>
 </div>
 </div>
 `;
 item.onclick = () => selecionarClientePedido(cliente);
 resultados.appendChild(item);
 });
 resultados.style.display = 'block';
 } else {
 resultados.innerHTML = '<div class="list-group-item text-muted">Nenhum cliente encontrado</div>';
 resultados.style.display = 'block';
 }
 } catch (error) {
 console.error('❌ Erro na busca de clientes:', error);
 }
}
function selecionarClientePedido(cliente) {
 console.log('✅ Cliente selecionado:', cliente);
 document.getElementById('cliente_id').value = cliente.id;
 document.getElementById('cliente_nome').value = cliente.nome;
 document.getElementById('cliente_telefone').value = cliente.telefone;
 if (cliente.endereco_completo) {
 document.getElementById('cliente_endereco').value = cliente.endereco_completo;
 } else {
 const enderecoCompleto = [
 cliente.endereco_rua,
 cliente.endereco_numero,
 cliente.endereco_bairro
 ].filter(Boolean).join(', ');
 document.getElementById('cliente_endereco').value = enderecoCompleto;
 }
 document.getElementById('cliente_bairro').value = cliente.endereco_bairro || '';
 document.getElementById('cliente_sel_nome').textContent = cliente.nome;
 document.getElementById('cliente_sel_telefone').textContent = cliente.telefone;
 document.getElementById('cliente_sel_endereco').textContent = cliente.endereco_completo || 'Sem endereço cadastrado';
 document.getElementById('cliente_selecionado_info').classList.remove('d-none');
 const alertaSemEndereco = document.getElementById('alerta_sem_endereco');
 if (!cliente.endereco_completo || !cliente.endereco_bairro) {
 alertaSemEndereco.classList.remove('d-none');
 } else {
 alertaSemEndereco.classList.add('d-none');
 }
 document.getElementById('resultados_cliente').style.display = 'none';
 document.getElementById('busca_cliente_pedido').value = '';
 atualizarBotaoPasso2();
}
function limparClientePedido() {
 document.getElementById('cliente_id').value = '';
 document.getElementById('cliente_nome').value = '';
 document.getElementById('cliente_telefone').value = '';
 document.getElementById('cliente_endereco').value = '';
 document.getElementById('cliente_bairro').value = '';
 document.getElementById('cliente_selecionado_info').classList.add('d-none');
 document.getElementById('busca_cliente_pedido').value = '';
 atualizarBotaoPasso2();
 console.log('🗑️ Cliente limpo');
}
document.addEventListener('click', function(e) {
 if (!e.target.closest('#busca_cliente_pedido') && !e.target.closest('#resultados_cliente')) {
 const resultados = document.getElementById('resultados_cliente');
 if (resultados) {
 resultados.style.display = 'none';
 }
 }
});

function prevStep(step) {
 document.getElementById(`step${currentStep}`).classList.add('d-none');
 document.getElementById(`step${step}`).classList.remove('d-none');
 updateStepIndicator(step);
 currentStep = step;
}

function updateStepIndicator(step) {
 for (let i = 1; i <= 4; i++) {
 const indicators = document.querySelectorAll('.step-number');
 const indicator = indicators[i - 1];
 if (indicator) {
 if (i <= step) {
 indicator.classList.remove('bg-secondary');
 indicator.classList.add('bg-primary');
 } else {
 indicator.classList.remove('bg-primary');
 indicator.classList.add('bg-secondary');
 }
 }
 }
}

// Event listeners para formas de pagamento
document.querySelectorAll('input[name="forma_pagamento"]').forEach(radio => {
 radio.addEventListener('change', function() {
 const botaoFinalizar = document.getElementById('finalizarPedido');
 const resumoPagamento = document.getElementById('resumoPagamento');
 
 if (this.checked) {
 botaoFinalizar.disabled = false;
 const formasPagamento = {
 'dinheiro': 'Dinheiro',
 'credito': 'Cartão de Crédito',
 'debito': 'Cartão de Débito',
 'pix': 'PIX'
 };
 resumoPagamento.textContent = formasPagamento[this.value];
 resumoPagamento.classList.remove('text-muted');
 resumoPagamento.classList.add('text-success', 'fw-bold');
 }
 });
});

// Atualizar resumo ao chegar no step 4
function nextStep(step) {
 document.getElementById(`step${currentStep}`).classList.add('d-none');
 document.getElementById(`step${step}`).classList.remove('d-none');
 updateStepIndicator(step);
 currentStep = step;
 
 // Se chegou no step 4, atualizar resumo completo
 if (step === 4) {
 atualizarResumoFinal();
 }
}

function atualizarResumoFinal() {
 // Atualizar mesa/cliente
 if (tipoPedido === 'mesa') {
 const mesaSelecionada = document.querySelector('input[name="mesa_id"]:checked');
 if (mesaSelecionada) {
 const mesaLabel = mesaSelecionada.closest('.card').querySelector('.card-title').textContent;
 document.getElementById('resumoMesa').textContent = mesaLabel;
 }
 } else {
 const clienteNome = document.getElementById('cliente_nome')?.value || 
 document.getElementById('cliente_sel_nome')?.textContent;
 document.getElementById('resumoMesa').textContent = clienteNome || 'Cliente não selecionado';
 }
 
 // Atualizar itens e total
 document.getElementById('resumoItens').textContent = itensPedido.length;
 document.getElementById('resumoTotal').textContent = `R$ ${totalPedido.toFixed(2).replace('.', ',')}`;
}

document.querySelectorAll('input[name="mesa_id"]').forEach(input => {
 input.addEventListener('change', function() {
 atualizarBotaoPasso2();
 const mesaLabel = this.closest('.card').querySelector('.card-title').textContent;
 document.getElementById('resumoMesa').textContent = mesaLabel;
 });
});
document.addEventListener('DOMContentLoaded', function() {
 const camposCliente = ['cliente_nome', 'cliente_telefone', 'cliente_endereco', 'cliente_bairro'];
 camposCliente.forEach(campo => {
 const element = document.getElementById(campo);
 if (element) {
 element.addEventListener('input', atualizarBotaoPasso2);
 }
 });
});
function filtrarProdutos() {
 const categoriaId = document.getElementById('categoriaFilter').value;
 const sections = document.querySelectorAll('.categoria-section');
 sections.forEach(section => {
 if (!categoriaId || section.dataset.categoria === categoriaId) {
 section.style.display = 'block';
 } else {
 section.style.display = 'none';
 }
 });
}
function adicionarItem(produtoId, nome, preco) {
 const existingItem = itensPedido.find(item => item.tipo_item === 'produto' && item.produto_id === produtoId);
 if (existingItem) {
 existingItem.quantidade++;
 existingItem.subtotal = existingItem.quantidade * existingItem.preco_unitario;
 } else {
 itensPedido.push({
 tipo_item: 'produto',
 produto_id: produtoId,
 combo_id: null,
 nome: nome,
 preco_unitario: parseFloat(preco),
 quantidade: 1,
 subtotal: parseFloat(preco)
 });
 }
 atualizarCarrinho();
}

function adicionarCombo(comboId, nome, preco) {
 const existingItem = itensPedido.find(item => item.tipo_item === 'combo' && item.combo_id === comboId);
 if (existingItem) {
 existingItem.quantidade++;
 existingItem.subtotal = existingItem.quantidade * existingItem.preco_unitario;
 } else {
 itensPedido.push({
 tipo_item: 'combo',
 produto_id: null,
 combo_id: comboId,
 nome: nome,
 preco_unitario: parseFloat(preco),
 quantidade: 1,
 subtotal: parseFloat(preco)
 });
 }
 atualizarCarrinho();
}

function removerItem(index) {
 itensPedido.splice(index, 1);
 atualizarCarrinho();
}
function alterarQuantidade(index, quantidade) {
 const item = itensPedido[index];
 if (item) {
 item.quantidade = parseInt(quantidade);
 item.subtotal = item.quantidade * item.preco_unitario;
 if (item.quantidade <= 0) {
 removerItem(index);
 return;
 }
 atualizarCarrinho();
 }
}
function atualizarCarrinho() {
 const container = document.getElementById('itensPedido');
 const totalContainer = document.getElementById('totalPedido');
 if (itensPedido.length === 0) {
 container.innerHTML = `
 <div class="text-center text-muted py-3">
 <i class="fas fa-shopping-cart fa-2x mb-2"></i>
 <p>Nenhum item adicionado</p>
 </div>
 `;
 totalContainer.classList.add('d-none');
 document.getElementById('nextStep3').disabled = true;
 return;
 }
 container.innerHTML = itensPedido.map((item, index) => `
 <div class="border rounded p-2 mb-2 ${item.tipo_item === 'combo' ? 'border-warning' : ''}">
 <div class="d-flex justify-content-between align-items-start">
 <div class="flex-grow-1">
 <h6 class="mb-1">
 ${item.tipo_item === 'combo' ? '<i class="fas fa-fire text-warning me-1"></i>' : ''}
 ${item.nome}
 ${item.tipo_item === 'combo' ? '<span class="badge bg-warning text-dark ms-1">Combo</span>' : ''}
 </h6>
 <div class="input-group input-group-sm" style="width: 120px;">
 <button class="btn btn-outline-secondary" type="button" onclick="alterarQuantidade(${index}, ${item.quantidade - 1})">-</button>
 <input type="number" class="form-control text-center" value="${item.quantidade}" onchange="alterarQuantidade(${index}, this.value)" min="1">
 <button class="btn btn-outline-secondary" type="button" onclick="alterarQuantidade(${index}, ${item.quantidade + 1})">+</button>
 </div>
 </div>
 <div class="text-end">
 <div class="fw-bold">R$ ${item.subtotal.toFixed(2).replace('.', ',')}</div>
 <button class="btn btn-danger btn-sm" onclick="removerItem(${index})">
 <i class="fas fa-trash"></i>
 </button>
 </div>
 </div>
 </div>
 `).join('');
 totalPedido = itensPedido.reduce((sum, item) => sum + item.subtotal, 0);
 document.getElementById('valorTotal').textContent = `R$ ${totalPedido.toFixed(2).replace('.', ',')}`;
 document.getElementById('resumoTotal').textContent = `R$ ${totalPedido.toFixed(2).replace('.', ',')}`;
 document.getElementById('resumoItens').textContent = itensPedido.length;
 totalContainer.classList.remove('d-none');
 document.getElementById('nextStep3').disabled = false;
 criarHiddenInputs();
}
function criarHiddenInputs() {
 const container = document.getElementById('hiddenInputs');
 container.innerHTML = itensPedido.map((item, index) => `
 <input type="hidden" name="itens[${index}][produto_id]" value="${item.produto_id}">
 <input type="hidden" name="itens[${index}][quantidade]" value="${item.quantidade}">
 <input type="hidden" name="itens[${index}][preco_unitario]" value="${item.preco_unitario}">
 `).join('');
}
document.getElementById('pedidoForm').addEventListener('submit', function(e) {
 e.preventDefault();
 
 console.log('=== INICIANDO FINALIZAÇÃO DO PEDIDO ===');
 console.log('Tipo de pedido:', tipoPedido);
 console.log('Itens no pedido:', itensPedido);
 
 if (itensPedido.length === 0) {
 mostrarAlerta('Adicione pelo menos um item ao pedido!', 'warning');
 return;
 }
 
 const formaPagamento = document.querySelector('input[name="forma_pagamento"]:checked');
 console.log('Forma de pagamento selecionada:', formaPagamento);
 
 if (!formaPagamento) {
 mostrarAlerta('Selecione uma forma de pagamento!', 'warning');
 return;
 }
 
 if (tipoPedido === 'mesa') {
 const mesaSelecionada = document.querySelector('input[name="mesa_id"]:checked');
 console.log('Mesa selecionada:', mesaSelecionada);
 
 if (!mesaSelecionada) {
 mostrarAlerta('Selecione uma mesa para o pedido!', 'warning');
 return;
 }
 } else if (tipoPedido === 'delivery') {
 console.log('Validando dados do cliente...');
 if (!validarDadosCliente()) {
 mostrarAlerta('Preencha todos os dados obrigatórios do cliente!', 'warning');
 return;
 }
 }
 
 console.log('Todas as validações passaram, enviando pedido...');
 enviarPedidoViaAPI();
});
async function enviarPedidoViaAPI() {
 console.log('=== FUNÇÃO enviarPedidoViaAPI INICIADA ===');
 
 const formaPagamento = document.querySelector('input[name="forma_pagamento"]:checked').value;
 const imprimirComanda = document.getElementById('imprimir_comanda').checked;
 const observacoes = document.querySelector('textarea[name="observacoes"]')?.value || '';
 
 console.log('Forma pagamento:', formaPagamento);
 console.log('Imprimir comanda:', imprimirComanda);
 console.log('Observações:', observacoes);
 
 let dadosPedido;
 
 if (tipoPedido === 'mesa') {
 const mesaId = document.querySelector('input[name="mesa_id"]:checked').value;
 dadosPedido = {
 mesa_id: parseInt(mesaId),
 forma_pagamento: formaPagamento,
 imprimir_comanda: imprimirComanda,
 observacoes: observacoes,
 itens: itensPedido.map(item => ({
 tipo_item: item.tipo_item,
 produto_id: item.produto_id ? parseInt(item.produto_id) : null,
 combo_id: item.combo_id ? parseInt(item.combo_id) : null,
 quantidade: parseInt(item.quantidade),
 preco_unitario: parseFloat(item.preco_unitario),
 observacoes: null
 }))
 };
 } else {
 dadosPedido = {
 tipo_pedido: 'delivery',
 cliente_id: parseInt(document.getElementById('cliente_id').value),
 cliente_nome: document.getElementById('cliente_nome').value,
 cliente_telefone: document.getElementById('cliente_telefone').value,
 cliente_endereco: document.getElementById('cliente_endereco').value,
 cliente_bairro: document.getElementById('cliente_bairro').value,
 forma_pagamento: formaPagamento,
 imprimir_comanda: imprimirComanda,
 observacoes: observacoes,
 itens: itensPedido.map(item => ({
 tipo_item: item.tipo_item,
 produto_id: item.produto_id ? parseInt(item.produto_id) : null,
 combo_id: item.combo_id ? parseInt(item.combo_id) : null,
 quantidade: parseInt(item.quantidade),
 preco_unitario: parseFloat(item.preco_unitario),
 observacoes: null
 }))
 };
 }
 
 console.log('Dados do pedido montados:', JSON.stringify(dadosPedido, null, 2));
 
 const btnSubmit = document.querySelector('button[type="submit"]');
 const originalText = btnSubmit.innerHTML;
 btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Criando Pedido...';
 btnSubmit.disabled = true;
 
 try {
 console.log('Enviando requisição para /api/pedidos-public...');
 
 const response = await fetch('/api/pedidos-public', {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'Accept': 'application/json',
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
 },
 body: JSON.stringify(dadosPedido)
 });
 
 console.log('Response status:', response.status);
 console.log('Response ok:', response.ok);
 
 const result = await response.json();
 console.log('Response data:', result);
 
 if (response.ok) {
 mostrarAlerta(`Pedido #${result.pedido.id} criado com sucesso!`, 'success');
 
 // Se houver URL da comanda, abre em nova janela
 if (result.comanda_url) {
 console.log('Abrindo comanda:', result.comanda_url);
 window.open(result.comanda_url, '_blank', 'width=400,height=600');
 }
 
 setTimeout(() => {
 window.location.href = `/pedidos/${result.pedido.id}`;
 }, 2000);
 } else {
 console.error('Erro da API:', result);
 if (result.errors) {
 const errosFormatados = Object.values(result.errors).flat().join('\n');
 mostrarAlerta(`Erro de validação:\n${errosFormatados}`, 'danger');
 } else {
 mostrarAlerta(result.message || 'Erro ao criar pedido. Tente novamente.', 'danger');
 }
 }
 } catch (error) {
 console.error('Erro de rede ou parsing:', error);
 mostrarAlerta('Erro de conexão. Verifique sua internet e tente novamente.', 'danger');
 } finally {
 btnSubmit.innerHTML = originalText;
 btnSubmit.disabled = false;
 }
}
function mostrarAlerta(mensagem, tipo = 'info') {
 const alertasExistentes = document.querySelectorAll('.alert-dinamico');
 alertasExistentes.forEach(alerta => alerta.remove());
 const alerta = document.createElement('div');
 alerta.className = `alert alert-${tipo} alert-dismissible fade show alert-dinamico`;
 alerta.style.position = 'fixed';
 alerta.style.top = '80px';
 alerta.style.right = '20px';
 alerta.style.zIndex = '9999';
 alerta.style.minWidth = '300px';
 alerta.innerHTML = `
 <div class="d-flex align-items-center">
 <i class="fas fa-${tipo === 'success' ? 'check-circle' : tipo === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
 <div style="white-space: pre-line;">${mensagem}</div>
 </div>
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 `;
 document.body.appendChild(alerta);
 if (tipo !== 'success') {
 setTimeout(() => {
 if (alerta.parentNode) {
 alerta.remove();
 }
 }, 5000);
 }
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\myd_bar_restaurantes\resources\views/pedidos/create.blade.php ENDPATH**/ ?>