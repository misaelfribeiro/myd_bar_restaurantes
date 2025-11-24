
<?php $__env->startSection('title', 'Detalhes do Pedido #' . ($pedido->numero_pedido ?? $pedido->id)); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
 <div class="page-header">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <h1 class="page-title">
 <i class="fas fa-receipt me-2"></i>
 Detalhes do Pedido #<?php echo e($pedido->numero_pedido ?? $pedido->id); ?>

 </h1>
 <p class="page-subtitle">
 <?php if($pedido->mesa): ?>
 Mesa <?php echo e($pedido->mesa->identificador); ?> -
 <?php elseif($pedido->delivery): ?>
 Delivery (<?php echo e($pedido->delivery->cliente_nome); ?>) -
 <?php else: ?>
 Balcão -
 <?php endif; ?>
 Criado em <?php echo e($pedido->created_at->format('d/m/Y H:i')); ?>

 </p>
 </div>
 <div class="btn-group">
 <a href="<?php echo e(route('pedidos.index')); ?>" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-2"></i>
 Voltar
 </a>
 <a href="<?php echo e(route('pedidos.edit', $pedido->id)); ?>" class="btn btn-primary">
 <i class="fas fa-edit me-2"></i>
 Editar
 </a>
 <button class="btn btn-info" onclick="window.print()">
 <i class="fas fa-print me-2"></i>
 Imprimir
 </button>
 </div>
 </div>
 </div>
 
 <!-- Alerta para delivery sem entregador -->
 <?php if($pedido->delivery && $pedido->status == 'em_preparo' && !$pedido->entregador_id && !$pedido->delivery->disponivel_plataforma): ?>
 <div class="alert alert-warning alert-dismissible fade show" role="alert">
 <div class="d-flex align-items-center">
 <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
 <div class="flex-grow-1">
 <h6 class="alert-heading mb-1">Atenção: Pedido sem entregador!</h6>
 <p class="mb-0">Este pedido de delivery está em preparo mas ainda não tem entregador atribuído.</p>
 </div>
 <div class="d-flex gap-2 ms-3">
 <form method="POST" action="<?php echo e(route('deliveries.disponibilizar-plataforma', $pedido->delivery->id)); ?>" class="d-inline">
 <?php echo csrf_field(); ?>
 <button type="submit" class="btn btn-primary btn-sm">
 <i class="fas fa-globe me-1"></i>
 Buscar na Plataforma
 </button>
 </form>
 <a href="<?php echo e(route('deliveries.index')); ?>" class="btn btn-warning btn-sm">
 <i class="fas fa-user-plus me-1"></i>
 Atribuir Manualmente
 </a>
 </div>
 </div>
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 <?php endif; ?>

 <!-- Alerta para delivery buscando na plataforma -->
 <?php if($pedido->delivery && $pedido->status == 'em_preparo' && !$pedido->entregador_id && $pedido->delivery->disponivel_plataforma): ?>
 <div class="alert alert-info alert-dismissible fade show" role="alert">
 <div class="d-flex align-items-center">
 <i class="fas fa-search fa-2x me-3 fa-spin"></i>
 <div class="flex-grow-1">
 <h6 class="alert-heading mb-1">🔍 Buscando entregadores na plataforma...</h6>
 <p class="mb-2">O sistema está notificando entregadores disponíveis próximos ao restaurante.</p>
 <?php if($pedido->delivery->entregadores_notificados && count($pedido->delivery->entregadores_notificados) > 0): ?>
 <small class="text-muted">
 <i class="fas fa-bell me-1"></i>
 <?php echo e(count($pedido->delivery->entregadores_notificados)); ?> <?php echo e(count($pedido->delivery->entregadores_notificados) == 1 ? 'entregador notificado' : 'entregadores notificados'); ?>

 </small>
 <?php endif; ?>
 </div>
 <form method="POST" action="<?php echo e(route('deliveries.cancelar-plataforma', $pedido->delivery->id)); ?>" class="d-inline ms-3">
 <?php echo csrf_field(); ?>
 <button type="submit" class="btn btn-outline-danger btn-sm" title="Cancelar busca e atribuir manualmente">
 <i class="fas fa-times me-1"></i>
 Cancelar Busca
 </button>
 </form>
 </div>
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 <?php endif; ?>
 
 <?php if($pedido->delivery && $pedido->status == 'pronto' && $pedido->entregador_id): ?>
 <div class="alert alert-warning alert-dismissible fade show" role="alert">
 <div class="d-flex align-items-center">
 <i class="fas fa-motorcycle fa-2x me-3"></i>
 <div class="flex-grow-1">
 <h6 class="alert-heading mb-1">Aguardando Entregador Coletar</h6>
 <p class="mb-0">
 <strong><?php echo e($pedido->entregador->nome); ?></strong> está a caminho para buscar o pedido.
 <br>
 <span class="badge bg-dark fs-5 mt-2 px-4 py-2" style="letter-spacing: 5px; font-family: 'Courier New', monospace;">
 <?php echo e(strtoupper(substr(md5($pedido->id), 0, 6))); ?>

 </span>
 <br>
 <small class="text-muted mt-1 d-block">Código de Retirada - Informe ao entregador</small>
 </p>
 </div>
 </div>
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 <?php endif; ?>
 
 <div class="row">
 <!-- Informações Principais -->
 <div class="col-lg-8">
 <!-- Status e Informações Básicas -->
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="card-title mb-0">
 <i class="fas fa-info-circle me-2"></i>
 Informações do Pedido
 </h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-3">
 <div class="text-center mb-3">
 <h6 class="text-muted">Status</h6>
 <span class="badge bg-<?php echo e($pedido->status === 'pendente' ? 'warning' : ($pedido->status === 'em_preparo' ? 'info' : ($pedido->status === 'pronto' ? 'success' : ($pedido->status === 'em_rota' ? 'primary' : ($pedido->status === 'entregue' ? 'success' : 'danger'))))); ?> fs-6 px-3 py-2">
 <?php echo e(ucfirst(str_replace('_', ' ', $pedido->status))); ?>

 </span>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center mb-3">
 <?php if($pedido->mesa): ?>
 <h6 class="text-muted">Mesa</h6>
 <div class="fw-bold"><?php echo e($pedido->mesa->identificador); ?></div>
 <small class="text-muted"><?php echo e($pedido->mesa->lugares); ?> lugares</small>
 <?php elseif($pedido->delivery): ?>
 <h6 class="text-muted">Delivery</h6>
 <div class="fw-bold"><?php echo e($pedido->delivery->cliente_nome); ?></div>
 <small class="text-muted"><?php echo e($pedido->delivery->endereco_completo); ?></small>
 <?php else: ?>
 <h6 class="text-muted">Tipo</h6>
 <div class="fw-bold">Balcão</div>
 <small class="text-muted">Retirada no local</small>
 <?php endif; ?>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center mb-3">
 <h6 class="text-muted">Total de Itens</h6>
 <div class="fw-bold"><?php echo e($pedido->itens->count()); ?> <?php echo e($pedido->itens->count() === 1 ? 'item' : 'itens'); ?></div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center mb-3">
 <h6 class="text-muted">Valor Total</h6>
 <div class="fw-bold text-success h5 mb-0">
 R$ <?php echo e(number_format($pedido->itens->sum(function($item) { return $item->preco_unitario * $item->quantidade; }), 2, ',', '.')); ?>

 </div>
 </div>
 </div>
 </div>
 <?php if($pedido->observacoes): ?>
 <div class="mt-3 p-3 bg-light rounded">
 <h6 class="mb-2">
 <i class="fas fa-comment text-muted me-2"></i>
 Observações do Pedido
 </h6>
 <p class="mb-0"><?php echo e($pedido->observacoes); ?></p>
 </div>
 <?php endif; ?>
 </div>
 </div>
 <!-- Itens do Pedido -->
 <div class="card mb-4">
 <div class="card-header d-flex justify-content-between align-items-center">
 <h5 class="card-title mb-0">
 <i class="fas fa-shopping-cart me-2"></i>
 Itens do Pedido
 </h5>
 <span class="badge bg-primary"><?php echo e($pedido->itens->count()); ?> <?php echo e($pedido->itens->count() === 1 ? 'item' : 'itens'); ?></span>
 </div>
 <div class="card-body p-0">
 <?php $__empty_1 = true; $__currentLoopData = $pedido->itens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
 <div class="border-bottom p-4 <?php echo e($loop->last ? '' : 'border-bottom'); ?>">
 <div class="row align-items-center">
 <div class="col-md-6">
 <h6 class="mb-2">
 <?php if($item->tipo_item === 'combo'): ?>
 <i class="fas fa-fire text-warning me-1"></i>
 <?php endif; ?>
 <?php echo e($item->nome_item); ?>

 <?php if($item->tipo_item === 'combo'): ?>
 <span class="badge bg-warning text-dark ms-1">Combo</span>
 <?php endif; ?>
 </h6>
 <div class="text-muted">
 <small>
 <?php if($item->tipo_item === 'combo'): ?>
 <i class="fas fa-box-open me-1"></i>
 <?php echo e($item->combo->produtos->count()); ?> produtos inclusos
 <?php else: ?>
 <i class="fas fa-tag me-1"></i>
 <?php echo e($item->produto->categoria->nome ?? 'Sem categoria'); ?>

 <?php endif; ?>
 </small>
 </div>
 <?php if($item->observacoes): ?>
 <div class="mt-2 p-2 bg-light rounded">
 <small class="text-muted">
 <i class="fas fa-sticky-note me-1"></i>
 <?php echo e($item->observacoes); ?>

 </small>
 </div>
 <?php endif; ?>
 </div>
 <div class="col-md-2 text-center">
 <div class="text-muted small">Preço Unit.</div>
 <div class="fw-bold">R$ <?php echo e(number_format($item->preco_unitario, 2, ',', '.')); ?></div>
 </div>
 <div class="col-md-2 text-center">
 <div class="text-muted small">Quantidade</div>
 <div class="fw-bold"><?php echo e($item->quantidade); ?>x</div>
 </div>
 <div class="col-md-2 text-end">
 <div class="text-muted small">Subtotal</div>
 <div class="fw-bold text-success">
 R$ <?php echo e(number_format($item->preco_unitario * $item->quantidade, 2, ',', '.')); ?>

 </div>
 
 <?php if(auth()->check() && in_array(auth()->user()->role, ['admin', 'gerente']) && !in_array($pedido->status, ['entregue', 'cancelado'])): ?>
 <div class="mt-2 text-end">
 <button type="button" 
 class="btn btn-outline-danger btn-sm" 
 onclick="removeItemFromDetails(<?php echo e($item->id); ?>, '<?php echo e(addslashes($item->nome_item)); ?>', <?php echo e($item->quantidade); ?>, <?php echo e($item->preco_unitario); ?>)"
 title="Excluir item">
 <i class="fas fa-trash"></i>
 </button>
 </div>
 <?php endif; ?>
 </div>
 </div>
 </div>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
 <div class="text-center text-muted py-5">
 <i class="fas fa-shopping-cart fa-3x mb-3"></i>
 <h5>Nenhum item no pedido</h5>
 <p>Este pedido não possui itens cadastrados.</p>
 </div>
 <?php endif; ?>
 </div>
 <?php if($pedido->itens->count() > 0): ?>
 <div class="card-footer bg-light">
 <div class="row align-items-center">
 <div class="col-md-8">
 <div class="fw-bold">
 Total de <?php echo e($pedido->itens->sum('quantidade')); ?> 
 <?php echo e($pedido->itens->sum('quantidade') === 1 ? 'produto' : 'produtos'); ?>

 </div>
 </div>
 <div class="col-md-4 text-end">
 <h4 class="mb-0 text-success">
 R$ <?php echo e(number_format($pedido->itens->sum(function($item) { return $item->preco_unitario * $item->quantidade; }), 2, ',', '.')); ?>

 </h4>
 </div>
 </div>
 </div>
 <?php endif; ?>
 </div>
 <!-- Ações Rápidas -->
 <?php if($pedido->status !== 'cancelado' && $pedido->status !== 'entregue'): ?>
 <div class="card">
 <div class="card-header">
 <h5 class="card-title mb-0">
 <i class="fas fa-bolt me-2"></i>
 Ações Rápidas
 </h5>
 </div>
 <div class="card-body">
 <div class="row">
 <?php if($pedido->status === 'pendente'): ?>
 <div class="col-md-4 mb-3">
 <form method="POST" action="<?php echo e(route('pedidos.update', $pedido->id)); ?>" class="d-inline">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PUT'); ?>
 <input type="hidden" name="status" value="em_preparo">
 <button type="submit" class="btn btn-info w-100">
 <i class="fas fa-fire me-2"></i>
 Iniciar Preparo
 </button>
 </form>
 </div>
 <?php endif; ?>
 <?php if($pedido->status === 'em_preparo'): ?>
 <div class="col-md-4 mb-3">
 <form method="POST" action="<?php echo e(route('pedidos.update', $pedido->id)); ?>" class="d-inline">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PUT'); ?>
 <input type="hidden" name="status" value="pronto">
 <button type="submit" class="btn btn-warning w-100">
 <i class="fas fa-check me-2"></i>
 Marcar como Pronto
 </button>
 </form>
 </div>
 <?php endif; ?>
 <?php if($pedido->status === 'pronto' && $pedido->delivery): ?>
 <div class="col-md-4 mb-3">
 <?php if($pedido->delivery->disponivel_plataforma): ?>
 
 <div class="alert alert-info mb-0">
 <i class="fas fa-search me-2 fa-spin"></i>
 <strong>Aguardando entregador da plataforma</strong><br>
 <small>O sistema está buscando um entregador disponível</small>
 <?php if($pedido->delivery->entregadores_notificados && count($pedido->delivery->entregadores_notificados) > 0): ?>
 <br><small class="text-muted">
 <i class="fas fa-bell me-1"></i>
 <?php echo e(count($pedido->delivery->entregadores_notificados)); ?> notificado(s)
 </small>
 <?php endif; ?>
 <form method="POST" action="<?php echo e(route('deliveries.cancelar-plataforma', $pedido->delivery->id)); ?>" class="mt-2">
 <?php echo csrf_field(); ?>
 <button type="submit" class="btn btn-outline-danger btn-sm w-100">
 <i class="fas fa-times me-1"></i>Cancelar Busca
 </button>
 </form>
 </div>
 <?php elseif(!$pedido->delivery->entregador_id): ?>
 
 <div class="alert alert-warning mb-0">
 <i class="fas fa-exclamation-triangle me-2"></i>
 <strong>Nenhum entregador atribuído</strong><br>
 <small>Busque na plataforma ou atribua manualmente</small>
 <form method="POST" action="<?php echo e(route('deliveries.disponibilizar-plataforma', $pedido->delivery->id)); ?>" class="mt-2">
 <?php echo csrf_field(); ?>
 <button type="submit" class="btn btn-primary btn-sm w-100">
 <i class="fas fa-globe me-1"></i>Buscar na Plataforma
 </button>
 </form>
 <a href="<?php echo e(route('deliveries.index')); ?>" class="btn btn-outline-secondary btn-sm w-100 mt-1">
 <i class="fas fa-user-plus me-1"></i>Atribuir Manualmente
 </a>
 </div>
 <?php else: ?>
 
 <form method="POST" action="<?php echo e(route('pedidos.update', $pedido->id)); ?>" class="d-inline w-100">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PUT'); ?>
 <input type="hidden" name="status" value="em_rota">
 <button type="submit" class="btn btn-primary w-100">
 <i class="fas fa-shipping-fast me-2"></i>
 Pedido Saiu para Entrega
 </button>
 </form>
 <?php endif; ?>
 </div>
 <?php endif; ?>
 <?php if($pedido->status === 'pronto' && !$pedido->delivery): ?>
 
 <div class="col-md-4 mb-3">
 <form method="POST" action="<?php echo e(route('pedidos.update', $pedido->id)); ?>" class="d-inline">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PUT'); ?>
 <input type="hidden" name="status" value="entregue">
 <button type="submit" class="btn btn-success w-100">
 <i class="fas fa-check-circle me-2"></i>
 Marcar como Entregue
 </button>
 </form>
 </div>
 <?php endif; ?>
 <?php if($pedido->status === 'em_rota'): ?>
 <div class="col-md-4 mb-3">
 <?php if($pedido->delivery && $pedido->delivery->entregador_id): ?>
 
 <div class="alert alert-primary mb-0">
 <i class="fas fa-info-circle me-2"></i>
 <strong>Aguardando confirmação do entregador</strong><br>
 <small>Entregador: <?php echo e($pedido->delivery->entregador->nome ?? 'N/A'); ?></small>
 </div>
 <?php else: ?>
 
 <form method="POST" action="<?php echo e(route('pedidos.update', $pedido->id)); ?>" class="d-inline">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PUT'); ?>
 <input type="hidden" name="status" value="entregue">
 <button type="submit" class="btn btn-success w-100">
 <i class="fas fa-check-circle me-2"></i>
 Confirmar Entrega
 </button>
 </form>
 <?php endif; ?>
 </div>
 <?php endif; ?>
 <?php if($pedido->status === 'pendente'): ?>
 <div class="col-md-4 mb-3">
 <form method="POST" action="<?php echo e(route('pedidos.update', $pedido->id)); ?>" class="d-inline" onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?')">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PUT'); ?>
 <input type="hidden" name="status" value="cancelado">
 <button type="submit" class="btn btn-danger w-100">
 <i class="fas fa-times me-2"></i>
 Cancelar Pedido
 </button>
 </form>
 </div>
 <?php endif; ?>
 </div>
 </div>
 </div>
 <?php endif; ?>
 </div>
 <!-- Informações Laterais -->
 <div class="col-lg-4">
 <!-- Timeline de Status -->
 <div class="card mb-4">
 <div class="card-header">
 <h6 class="card-title mb-0">
 <i class="fas fa-history me-2"></i>
 Histórico do Pedido
 </h6>
 </div>
 <div class="card-body">
 <div class="timeline">
 <div class="timeline-item <?php echo e($pedido->created_at ? 'completed' : ''); ?>">
 <div class="timeline-marker bg-primary"></div>
 <div class="timeline-content">
 <h6 class="mb-1">Pedido Criado</h6>
 <small class="text-muted">
 <?php echo e($pedido->created_at->format('d/m/Y H:i:s')); ?>

 </small>
 </div>
 </div>
 <div class="timeline-item <?php echo e(in_array($pedido->status, ['em_preparo', 'pronto', 'entregue']) ? 'completed' : ''); ?>">
 <div class="timeline-marker <?php echo e(in_array($pedido->status, ['em_preparo', 'pronto', 'entregue']) ? 'bg-info' : 'bg-secondary'); ?>"></div>
 <div class="timeline-content">
 <h6 class="mb-1">Em Preparo</h6>
 <small class="text-muted">
 <?php if(in_array($pedido->status, ['em_preparo', 'pronto', 'entregue'])): ?>
 <?php echo e($pedido->updated_at->format('d/m/Y H:i:s')); ?>

 <?php else: ?>
 Aguardando...
 <?php endif; ?>
 </small>
 <?php if($pedido->delivery && in_array($pedido->status, ['em_preparo', 'pronto', 'entregue'])): ?>
 <div class="mt-2">
 <?php if($pedido->entregador): ?>
 <span class="badge bg-success">
 <i class="fas fa-user-check me-1"></i>
 Entregador: <?php echo e($pedido->entregador->nome); ?>

 </span>
 <?php else: ?>
 <span class="badge bg-warning text-dark">
 <i class="fas fa-exclamation-triangle me-1"></i>
 Aguardando atribuição de entregador
 </span>
 <?php endif; ?>
 </div>
 <?php endif; ?>
 </div>
 </div>
 <div class="timeline-item <?php echo e(in_array($pedido->status, ['pronto', 'entregue']) ? 'completed' : ''); ?>">
 <div class="timeline-marker <?php echo e(in_array($pedido->status, ['pronto', 'entregue']) ? 'bg-warning' : 'bg-secondary'); ?>"></div>
 <div class="timeline-content">
 <h6 class="mb-1">Pronto</h6>
 <small class="text-muted">
 <?php if(in_array($pedido->status, ['pronto', 'entregue'])): ?>
 <?php echo e($pedido->updated_at->format('d/m/Y H:i:s')); ?>

 <?php else: ?>
 Aguardando...
 <?php endif; ?>
 </small>
 </div>
 </div>
 <div class="timeline-item <?php echo e($pedido->status === 'entregue' ? 'completed' : ''); ?>">
 <div class="timeline-marker <?php echo e($pedido->status === 'entregue' ? 'bg-success' : 'bg-secondary'); ?>"></div>
 <div class="timeline-content">
 <h6 class="mb-1">Entregue</h6>
 <small class="text-muted">
 <?php if($pedido->status === 'entregue'): ?>
 <?php echo e($pedido->updated_at->format('d/m/Y H:i:s')); ?>

 <?php else: ?>
 Aguardando...
 <?php endif; ?>
 </small>
 </div>
 </div>
 <?php if($pedido->status === 'cancelado'): ?>
 <div class="timeline-item completed">
 <div class="timeline-marker bg-danger"></div>
 <div class="timeline-content">
 <h6 class="mb-1">Cancelado</h6>
 <small class="text-muted"><?php echo e($pedido->updated_at->format('d/m/Y H:i:s')); ?></small>
 </div>
 </div>
 <?php endif; ?>
 </div>
 </div>
 </div>
 <!-- Informações da Mesa -->
 <div class="card mb-4">
 <div class="card-header">
 <h6 class="card-title mb-0">
 <i class="fas fa-chair me-2"></i>
 Informações da Mesa
 </h6>
 </div>
 <div class="card-body">
 <div class="text-center">
 <?php if($pedido->mesa): ?>
 <div class="mb-3">
 <i class="fas fa-chair fa-3x text-primary"></i>
 </div>
 <h5 class="mb-3">Mesa <?php echo e($pedido->mesa->identificador); ?></h5>
 <div class="row">
 <div class="col-6">
 <div class="border-end">
 <div class="fw-bold"><?php echo e($pedido->mesa->lugares); ?></div>
 <div class="text-muted small">Lugares</div>
 </div>
 </div>
 <div class="col-6">
 <div class="fw-bold">
 <?php
 $mesaStatus = isset($pedido->mesa->disponivel) 
 ? $pedido->mesa->disponivel 
 : ($pedido->status === 'finalizado' || $pedido->status === 'entregue');
 ?>
 <span class="badge bg-<?php echo e($mesaStatus ? 'success' : 'warning'); ?>">
 <?php echo e($mesaStatus ? 'Livre' : 'Ocupada'); ?>

 </span>
 </div>
 <div class="text-muted small">Status</div>
 </div>
 </div>
 <?php elseif($pedido->delivery): ?>
 <div class="mb-3">
 <i class="fas fa-motorcycle fa-3x icon-delivery"></i>
 </div>
 <h5 class="mb-3">Delivery</h5>
 <div class="text-muted">
 <div><strong>Cliente:</strong> <?php echo e($pedido->delivery->cliente_nome); ?></div>
 <div><strong>Telefone:</strong> <?php echo e($pedido->delivery->cliente_telefone); ?></div>
 <div><strong>Endereço:</strong> <?php echo e($pedido->delivery->endereco_completo); ?></div>
 </div>
 <?php else: ?>
 <div class="mb-3">
 <i class="fas fa-coffee fa-3x icon-balcao"></i>
 </div>
 <h5 class="mb-3">Balcão</h5>
 <div class="text-muted">
 <div>Retirada no local</div>
 </div>
 <?php endif; ?>
 </div>
 </div>
 </div>
 <!-- Resumo Financeiro -->
 <div class="card">
 <div class="card-header bg-success text-white">
 <h6 class="card-title mb-0">
 <i class="fas fa-calculator me-2"></i>
 Resumo Financeiro
 </h6>
 </div>
 <div class="card-body">
 <?php
 $subtotal = $pedido->itens->sum(function($item) { 
 return $item->preco_unitario * $item->quantidade; 
 });
 $desconto = 0;
 $total = $subtotal - $desconto;
 ?>
 <div class="d-flex justify-content-between mb-2">
 <span>Subtotal:</span>
 <span>R$ <?php echo e(number_format($subtotal, 2, ',', '.')); ?></span>
 </div>
 <?php if($desconto > 0): ?>
 <div class="d-flex justify-content-between mb-2 text-success">
 <span>Desconto:</span>
 <span>- R$ <?php echo e(number_format($desconto, 2, ',', '.')); ?></span>
 </div>
 <?php endif; ?>
 <hr>
 <div class="d-flex justify-content-between">
 <strong>Total:</strong>
 <strong class="text-success">R$ <?php echo e(number_format($total, 2, ',', '.')); ?></strong>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
<!-- Modal para confirmação de exclusão de item -->
<div class="modal fade" id="modalExcluirItem" tabindex="-1" aria-labelledby="modalExcluirItemLabel" aria-hidden="true">
 <div class="modal-dialog">
 <div class="modal-content">
 <div class="modal-header">
 <h5 class="modal-title" id="modalExcluirItemLabel">
 <i class="fas fa-trash text-danger me-2"></i>
 Confirmar Exclusão de Item
 </h5>
 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <div class="modal-body">
 <div class="alert alert-warning">
 <i class="fas fa-exclamation-triangle me-2"></i>
 <strong>Atenção!</strong> Esta ação irá excluir permanentemente o item do pedido.
 </div>
 <div class="mb-3">
 <strong>Item a ser excluído:</strong>
 <div class="p-2 bg-light rounded mt-1" id="itemExclusaoInfo">
 <!-- Preenchido via JavaScript -->
 </div>
 </div>
 <div class="mb-3" id="quantidadeContainer">
 <label for="quantidadeExcluir" class="form-label">
 <strong>Quantidade a excluir <span class="text-danger">*</span></strong>
 </label>
 <div class="row">
 <div class="col-6">
 <input type="number" class="form-control" id="quantidadeExcluir" min="1" required>
 <div class="form-text">Máximo: <span id="quantidadeMaxima"></span></div>
 <div class="invalid-feedback">
 Informe uma quantidade válida.
 </div>
 </div>
 <div class="col-6">
 <div class="form-check mt-2">
 <input class="form-check-input" type="checkbox" id="excluirCompleto">
 <label class="form-check-label" for="excluirCompleto">
 Excluir item completo
 </label>
 </div>
 </div>
 </div>
 </div>
 <div class="mb-3">
 <label for="motivoExclusao" class="form-label">
 <strong>Motivo da exclusão <span class="text-danger">*</span></strong>
 </label>
 <select class="form-select" id="motivoExclusao" required>
 <option value="">Selecione um motivo</option>
 <option value="Pedido incorreto">Pedido incorreto</option>
 <option value="Item errado">Item errado</option>
 <option value="Ajuste no pedido">Ajuste no pedido</option>
 <option value="Cancelamento pelo cliente">Cancelamento pelo cliente</option>
 <option value="Produto indisponível">Produto indisponível</option>
 <option value="Erro de lançamento">Erro de lançamento</option>
 <option value="Solicitação do garçom">Solicitação do garçom</option>
 <option value="Problema na cozinha">Problema na cozinha</option>
 <option value="Outros">Outros motivos</option>
 </select>
 <div class="invalid-feedback" id="motivoError">
 Por favor, selecione um motivo para a exclusão.
 </div>
 </div>
 <div class="mb-3" id="motivoOutrosContainer" style="display: none;">
 <label for="motivoOutros" class="form-label">Especifique o motivo:</label>
 <textarea class="form-control" id="motivoOutros" rows="2" maxlength="500" 
 placeholder="Descreva o motivo da exclusão..."></textarea>
 <div class="form-text">Máximo 500 caracteres</div>
 </div>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
 <i class="fas fa-times me-1"></i>Cancelar
 </button>
 <button type="button" class="btn btn-danger" id="confirmarExclusaoBtn">
 <i class="fas fa-trash me-1"></i>Confirmar Exclusão
 </button>
 </div>
 </div>
 </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
<style>
@media  print {
 .btn-group, .card-header .btn, .page-header .btn {
 display: none !important;
 }
}
.timeline {
 position: relative;
 padding-left: 20px;
}
.timeline-item {
 position: relative;
 padding-bottom: 25px;
}
.timeline-item:before {
 content: '';
 position: absolute;
 left: -15px;
 top: 25px;
 bottom: -25px;
 width: 2px;
 background: #dee2e6;
}
.timeline-item:last-child:before {
 display: none;
}
.timeline-marker {
 position: absolute;
 left: -20px;
 top: 5px;
 width: 10px;
 height: 10px;
 border-radius: 50%;
 border: 2px solid white;
 box-shadow: 0 0 0 2px #dee2e6;
}
.timeline-item.completed .timeline-marker {
 box-shadow: 0 0 0 2px transparent;
}
.page-title {
 color: #495057;
 font-weight: 600;
}
.page-subtitle {
 color: #6c757d;
 margin-bottom: 0;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
 const csrfToken = document.querySelector('meta[name="csrf-token"]');
 if (!csrfToken || !csrfToken.getAttribute('content')) {
 console.error('Token CSRF não encontrado!');
 showToast('error', 'Erro de segurança. Recarregue a página.');
 }
});
let itemParaExcluir = null;
let botaoOriginal = null;
let conteudoOriginalBotao = '';
function removeItemFromDetails(itemId, produtoNome, quantidade, precoUnitario) {
 itemParaExcluir = {
 id: itemId,
 nome: produtoNome,
 quantidade: parseInt(quantidade),
 preco_unitario: parseFloat(precoUnitario)
 };
 botaoOriginal = event.target.closest('button');
 conteudoOriginalBotao = botaoOriginal.innerHTML;
 const valorUnitario = new Intl.NumberFormat('pt-BR', {
 style: 'currency',
 currency: 'BRL'
 }).format(precoUnitario);
 const valorTotal = new Intl.NumberFormat('pt-BR', {
 style: 'currency',
 currency: 'BRL'
 }).format(quantidade * precoUnitario);
 document.getElementById('itemExclusaoInfo').innerHTML = `
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <i class="fas fa-utensils text-primary me-2"></i>
 <strong>${produtoNome}</strong>
 </div>
 <div class="text-end">
 <div class="text-muted small">Quantidade atual: <strong>${quantidade}</strong></div>
 <div class="text-muted small">Valor unitário: <strong>${valorUnitario}</strong></div>
 <div class="text-success small">Total do item: <strong>${valorTotal}</strong></div>
 </div>
 </div>
 `;
 const quantidadeInput = document.getElementById('quantidadeExcluir');
 const quantidadeMaxima = document.getElementById('quantidadeMaxima');
 const excluirCompleto = document.getElementById('excluirCompleto');
 quantidadeInput.value = 1;
 quantidadeInput.max = quantidade;
 quantidadeMaxima.textContent = quantidade;
 excluirCompleto.checked = false;
 excluirCompleto.onchange = function() {
 if (this.checked) {
 quantidadeInput.value = quantidade;
 quantidadeInput.disabled = true;
 } else {
 quantidadeInput.value = 1;
 quantidadeInput.disabled = false;
 }
 };
 quantidadeInput.onchange = function() {
 if (parseInt(this.value) === quantidade) {
 excluirCompleto.checked = true;
 } else {
 excluirCompleto.checked = false;
 }
 };
 document.getElementById('motivoExclusao').value = '';
 document.getElementById('motivoOutros').value = '';
 document.getElementById('motivoOutrosContainer').style.display = 'none';
 document.getElementById('motivoExclusao').classList.remove('is-invalid');
 quantidadeInput.classList.remove('is-invalid');
 const modal = new bootstrap.Modal(document.getElementById('modalExcluirItem'));
 modal.show();
}
document.getElementById('motivoExclusao').addEventListener('change', function() {
 const motivoOutrosContainer = document.getElementById('motivoOutrosContainer');
 if (this.value === 'Outros') {
 motivoOutrosContainer.style.display = 'block';
 document.getElementById('motivoOutros').required = true;
 } else {
 motivoOutrosContainer.style.display = 'none';
 document.getElementById('motivoOutros').required = false;
 document.getElementById('motivoOutros').value = '';
 }
 this.classList.remove('is-invalid');
});
document.getElementById('confirmarExclusaoBtn').addEventListener('click', function() {
 const motivoSelect = document.getElementById('motivoExclusao');
 const motivoOutros = document.getElementById('motivoOutros');
 const quantidadeInput = document.getElementById('quantidadeExcluir');
 let hasError = false;
 if (!motivoSelect.value) {
 motivoSelect.classList.add('is-invalid');
 hasError = true;
 } else {
 motivoSelect.classList.remove('is-invalid');
 }
 const quantidade = parseInt(quantidadeInput.value);
 if (!quantidade || quantidade < 1 || quantidade > itemParaExcluir.quantidade) {
 quantidadeInput.classList.add('is-invalid');
 hasError = true;
 } else {
 quantidadeInput.classList.remove('is-invalid');
 }
 if (motivoSelect.value === 'Outros' && !motivoOutros.value.trim()) {
 motivoOutros.classList.add('is-invalid');
 motivoOutros.focus();
 hasError = true;
 }
 if (hasError) return;
 let motivoFinal = motivoSelect.value;
 if (motivoSelect.value === 'Outros') {
 motivoFinal = motivoOutros.value.trim();
 }
 const modal = bootstrap.Modal.getInstance(document.getElementById('modalExcluirItem'));
 modal.hide();
 executarExclusao(itemParaExcluir.id, motivoFinal, quantidade);
});
function executarExclusao(itemId, motivo, quantidade) {
 botaoOriginal.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
 botaoOriginal.disabled = true;
 const isExclusaoCompleta = quantidade >= itemParaExcluir.quantidade;
 const csrfToken = document.querySelector('meta[name="csrf-token"]');
 if (!csrfToken) {
 showToast('error', 'Token de segurança não encontrado. Recarregue a página.');
 botaoOriginal.innerHTML = conteudoOriginalBotao;
 botaoOriginal.disabled = false;
 return;
 }
 fetch(`/pedidos/<?php echo e($pedido->id); ?>/itens/${itemId}`, {
 method: 'DELETE',
 headers: {
 'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
 'Accept': 'application/json',
 'Content-Type': 'application/json',
 'X-Requested-With': 'XMLHttpRequest'
 },
 credentials: 'same-origin',
 body: JSON.stringify({ 
 motivo: motivo,
 quantidade: quantidade,
 _token: csrfToken.getAttribute('content')
 })
 })
 .then(response => {
 if (response.status === 419) {
 throw new Error('Token de segurança expirado. Recarregue a página.');
 }
 if (response.status === 403) {
 throw new Error('Acesso negado. Você não tem permissão para esta ação.');
 }
 if (!response.ok) {
 throw new Error(`Erro HTTP: ${response.status}`);
 }
 return response.json();
 })
 .then(data => {
 if (data.success) {
 showToast('success', data.message);
 if (isExclusaoCompleta) {
 const itemElement = botaoOriginal.closest('.border-bottom');
 itemElement.style.transition = 'opacity 0.3s ease';
 itemElement.style.opacity = '0';
 setTimeout(() => {
 itemElement.remove();
 updateItemCounts();
 updateTotal(data.novo_total);
 checkEmptyState();
 }, 300);
 } else {
 const itemElement = botaoOriginal.closest('.border-bottom');
 const quantidadeElement = itemElement.querySelector('.fw-bold');
 const valorElement = itemElement.querySelector('.text-success .fw-bold');
 const novaQuantidade = itemParaExcluir.quantidade - quantidade;
 const novoValor = novaQuantidade * itemParaExcluir.preco_unitario;
 if (quantidadeElement) {
 quantidadeElement.textContent = `${novaQuantidade}x`;
 }
 if (valorElement) {
 valorElement.textContent = new Intl.NumberFormat('pt-BR', {
 style: 'currency',
 currency: 'BRL'
 }).format(novoValor);
 }
 updateTotal(data.novo_total);
 botaoOriginal.innerHTML = conteudoOriginalBotao;
 botaoOriginal.disabled = false;
 itemParaExcluir.quantidade = novaQuantidade;
 }
 } else {
 showToast('error', data.message);
 botaoOriginal.innerHTML = conteudoOriginalBotao;
 botaoOriginal.disabled = false;
 }
 })
 .catch(error => {
 console.error('Erro ao excluir item:', error);
 let errorMessage = 'Erro ao remover item. Tente novamente.';
 if (error.message.includes('Token de segurança')) {
 errorMessage = error.message;
 } else if (error.message.includes('Acesso negado')) {
 errorMessage = error.message;
 } else if (error.message.includes('HTTP: 419')) {
 errorMessage = 'Sessão expirada. Recarregue a página e tente novamente.';
 }
 showToast('error', errorMessage);
 botaoOriginal.innerHTML = conteudoOriginalBotao;
 botaoOriginal.disabled = false;
 });
}
function updateItemCounts() {
 const remainingItems = document.querySelectorAll('.border-bottom').length;
 document.querySelectorAll('.badge').forEach(badge => {
 if (badge.textContent.includes('item')) {
 badge.textContent = `${remainingItems} ${remainingItems === 1 ? 'item' : 'itens'}`;
 }
 });
 const totalQuantity = Array.from(document.querySelectorAll('.fw-bold')).reduce((total, el) => {
 if (el.textContent.includes('x')) {
 const qty = parseInt(el.textContent.replace('x', ''));
 return total + (qty || 0);
 }
 return total;
 }, 0);
 const totalElement = document.querySelector('.card-footer .fw-bold');
 if (totalElement) {
 totalElement.textContent = `Total de ${totalQuantity} ${totalQuantity === 1 ? 'produto' : 'produtos'}`;
 }
}
function updateTotal(novoTotal) {
 document.querySelectorAll('h4.text-success, h5.text-success, .fw-bold.text-success.h5').forEach(el => {
 if (el.textContent.includes('R$')) {
 el.textContent = 'R$ ' + novoTotal.toFixed(2).replace('.', ',');
 }
 });
}
function checkEmptyState() {
 const items = document.querySelectorAll('.border-bottom');
 if (items.length === 0) {
 const cardBody = document.querySelector('.card-body.p-0');
 cardBody.innerHTML = `
 <div class="text-center text-muted py-5">
 <i class="fas fa-shopping-cart fa-3x mb-3"></i>
 <h5>Nenhum item no pedido</h5>
 <p>Todos os itens foram removidos deste pedido.</p>
 <a href="<?php echo e(route('pedidos.edit', $pedido->id)); ?>" class="btn btn-primary">
 <i class="fas fa-plus me-2"></i>
 Adicionar Itens
 </a>
 </div>
 `;
 const footer = document.querySelector('.card-footer');
 if (footer) footer.remove();
 }
}
function showToast(type, message) {
 const toast = document.createElement('div');
 toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
 toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
 toast.innerHTML = `
 <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
 ${message}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 `;
 document.body.appendChild(toast);
 setTimeout(() => {
 if (toast.parentNode) {
 toast.parentNode.removeChild(toast);
 }
 }, 5000);
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\myd_bar_restaurantes\resources\views/pedidos/detalhes.blade.php ENDPATH**/ ?>