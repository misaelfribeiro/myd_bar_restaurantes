
<?php $__env->startSection('title', 'Delivery - Gestão de Entregas'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1><i class="fas fa-shipping-fast me-2"></i>Gestão de Entregas</h1>
 <div>
 <a href="<?php echo e(route('pedidos.create')); ?>" class="btn btn-success me-2">
 <i class="fas fa-plus me-2"></i>Novo Pedido Delivery
 </a>
 <a href="<?php echo e(route('cozinha.monitor')); ?>" class="btn btn-info" target="_blank">
 <i class="fas fa-tv me-2"></i>Monitor Cozinha
 </a>
 </div>
 </div>
 <!-- Filtros -->
 <div class="row mb-4">
 <div class="col-md-12">
 <div class="card">
 <div class="card-body">
 <form method="GET" class="row g-3">
 <div class="col-md-3">
 <label class="form-label">Status</label>
 <select name="status" class="form-select">
 <option value="">Todos os Status</option>
 <option value="em_preparo" <?php echo e(request('status') == 'em_preparo' ? 'selected' : ''); ?>>Em Preparo</option>
 <option value="pronto" <?php echo e(request('status') == 'pronto' ? 'selected' : ''); ?>>Pronto</option>
 <option value="entregue" <?php echo e(request('status') == 'entregue' ? 'selected' : ''); ?>>Entregue</option>
 </select>
 </div>
 <div class="col-md-3">
 <label class="form-label">Cliente</label>
 <input type="text" name="cliente" class="form-control" value="<?php echo e(request('cliente')); ?>" placeholder="Nome do cliente">
 </div>
 <div class="col-md-3">
 <label class="form-label">Telefone</label>
 <input type="text" name="telefone" class="form-control" value="<?php echo e(request('telefone')); ?>" placeholder="Telefone">
 </div>
 <div class="col-md-3">
 <div class="d-flex align-items-end h-100">
 <button type="submit" class="btn btn-outline-primary me-2">
 <i class="fas fa-search me-2"></i>Filtrar
 </button>
 <a href="<?php echo e(route('deliveries.index')); ?>" class="btn btn-outline-secondary">
 <i class="fas fa-times"></i>
 </a>
 </div>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
 <!-- Cards de Status -->
 <div class="row mb-4">
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-warning">
 <div class="card-body text-center">
 <h6 class="card-title">Em Preparo</h6>
 <h3><?php echo e($estatisticas['em_preparo'] ?? 0); ?></h3>
 <small>Aguardando cozinha</small>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-danger">
 <div class="card-body text-center">
 <h6 class="card-title">Sem Entregador</h6>
 <h3><?php echo e($estatisticas['aguardando_entregador'] ?? 0); ?></h3>
 <small>Precisa atribuir</small>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-primary">
 <div class="card-body text-center">
 <h6 class="card-title">Prontos</h6>
 <h3><?php echo e($estatisticas['prontos'] ?? 0); ?></h3>
 <small>Aguardando entrega</small>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-info">
 <div class="card-body text-center">
 <h6 class="card-title">Em Rota</h6>
 <h3><?php echo e($estatisticas['em_rota'] ?? 0); ?></h3>
 <small>Saíram para entrega</small>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-success">
 <div class="card-body text-center">
 <h6 class="card-title">Entregues</h6>
 <h3><?php echo e($estatisticas['entregues'] ?? 0); ?></h3>
 <small>Hoje</small>
 </div>
 </div>
 </div>
 <div class="col-lg-2 col-md-4 mb-3">
 <div class="card text-white bg-dark">
 <div class="card-body text-center">
 <h6 class="card-title">Faturamento</h6>
 <h3>R$ <?php echo e(number_format($estatisticas['faturamento'] ?? 0, 0, ',', '.')); ?></h3>
 <small>Hoje</small>
 </div>
 </div>
 </div>
 </div>
 <!-- Tabela de Entregas -->
 <div class="row">
 <div class="col-md-12">
 <div class="card">
 <div class="card-body">
 <?php if($deliveries->isEmpty()): ?>
 <div class="text-center py-5">
 <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
 <h5 class="text-muted">Nenhum pedido de delivery em preparo</h5>
 <p class="text-muted">Pedidos de delivery aparecerão aqui quando estiverem com status "Em Preparo".</p>
 <a href="<?php echo e(route('pedidos.create')); ?>" class="btn btn-success">
 <i class="fas fa-plus me-2"></i>Criar Novo Pedido Delivery
 </a>
 </div>
 <?php else: ?>
 <div class="table-responsive">
 <table class="table table-hover">
 <thead class="table-dark">
 <tr>
 <th>Pedido</th>
 <th>Cliente</th>
 <th>Telefone</th>
 <th>Endereço</th>
 <th>Itens</th>
 <th>Total</th>
 <th>Entregador</th>
 <th>Status</th>
 <th>Ações</th>
 </tr>
 </thead>
 <tbody>
 <?php $__currentLoopData = $deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pedido): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <tr>
 <td>
 <strong>#<?php echo e($pedido->id); ?></strong>
 <br>
 <small class="text-muted"><?php echo e($pedido->created_at->format('d/m H:i')); ?></small>
 </td>
 <td><?php echo e($pedido->delivery->cliente_nome ?? 'N/A'); ?></td>
 <td><?php echo e($pedido->delivery->cliente_telefone ?? 'N/A'); ?></td>
 <td>
 <small>
 <?php echo e($pedido->delivery->endereco_rua ?? ''); ?> 
 <?php echo e($pedido->delivery->endereco_numero ?? ''); ?><br>
 <?php echo e($pedido->delivery->endereco_bairro ?? ''); ?>

 </small>
 </td>
 <td><?php echo e($pedido->itens->count()); ?> itens</td>
 <td><strong>R$ <?php echo e(number_format($pedido->total, 2, ',', '.')); ?></strong></td>
 <td>
 <?php if($pedido->entregador): ?>
 <span class="badge bg-success">
 <i class="fas fa-user-check"></i> <?php echo e($pedido->entregador->nome); ?>

 </span>
 <?php elseif($pedido->delivery && $pedido->delivery->disponivel_plataforma): ?>
 <span class="badge bg-warning text-dark">
 <i class="fas fa-search"></i> Buscando na plataforma
 </span>
 <?php else: ?>
 <span class="badge bg-danger">
 <i class="fas fa-user-times"></i> Sem entregador
 </span>
 <?php endif; ?>
 </td>
 <td>
 <?php if($pedido->status == 'em_preparo'): ?>
 <span class="badge bg-warning">
 <i class="fas fa-clock"></i> Em Preparo
 </span>
 <?php elseif($pedido->status == 'pronto'): ?>
 <span class="badge bg-primary">
 <i class="fas fa-check-circle"></i> Pronto
 </span>
 <?php elseif($pedido->status == 'entregue'): ?>
 <span class="badge bg-success">
 <i class="fas fa-check-double"></i> Entregue
 </span>
 <?php endif; ?>
 </td>
 <td>
 <div class="btn-group" role="group">
 <a href="<?php echo e(route('deliveries.show', $pedido->delivery->id)); ?>" 
 class="btn btn-sm btn-outline-primary" title="Ver detalhes">
 <i class="fas fa-eye"></i>
 </a>
 
 <?php if($pedido->status == 'em_preparo' && !$pedido->entregador_id && (!$pedido->delivery || !$pedido->delivery->disponivel_plataforma)): ?>
 <button type="button" class="btn btn-sm btn-success" 
 data-bs-toggle="modal" 
 data-bs-target="#modalAtribuirEntregador<?php echo e($pedido->id); ?>"
 title="Atribuir Entregador">
 <i class="fas fa-user-plus"></i>
 </button>
 <?php endif; ?>
 
 <?php if($pedido->entregador_id && $pedido->status == 'em_preparo'): ?>
 <form method="POST" action="<?php echo e(route('pedidos.remover-entregador', $pedido->id)); ?>" class="d-inline">
 <?php echo csrf_field(); ?>
 <button type="submit" class="btn btn-sm btn-warning" title="Remover Entregador"
 onclick="return confirm('Remover entregador deste pedido?')">
 <i class="fas fa-user-times"></i>
 </button>
 </form>
 <?php endif; ?>
 </div>
 </td>
 </tr>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </tbody>
 </table>
 </div>
 <?php endif; ?>
 </div>
 </div>
 </div>
 </div>
</div>

<!-- Modais para atribuir entregador -->
<?php $__currentLoopData = $deliveries->where('status', 'em_preparo')->whereNull('entregador_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pedido): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="modalAtribuirEntregador<?php echo e($pedido->id); ?>" tabindex="-1">
 <div class="modal-dialog">
 <div class="modal-content">
 <div class="modal-header bg-primary text-white">
 <h5 class="modal-title">
 <i class="fas fa-user-plus me-2"></i>Atribuir Entregador - Pedido #<?php echo e($pedido->id); ?>

 </h5>
 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
 </div>
 <form method="POST" action="<?php echo e(route('pedidos.atribuir-entregador', $pedido->id)); ?>">
 <?php echo csrf_field(); ?>
 <div class="modal-body">
 <div class="mb-3">
 <label class="form-label">Cliente</label>
 <input type="text" class="form-control" value="<?php echo e($pedido->delivery->cliente_nome); ?>" readonly>
 </div>
 <div class="mb-3">
 <label class="form-label">Endereço</label>
 <textarea class="form-control" rows="2" readonly><?php echo e($pedido->delivery->endereco_rua); ?> <?php echo e($pedido->delivery->endereco_numero); ?>, <?php echo e($pedido->delivery->endereco_bairro); ?></textarea>
 </div>
 <div class="mb-3">
 <label class="form-label">Total do Pedido</label>
 <input type="text" class="form-control" value="R$ <?php echo e(number_format($pedido->total, 2, ',', '.')); ?>" readonly>
 </div>
 <div class="mb-3">
 <label class="form-label">Selecionar Entregador *</label>
 <select name="entregador_id" class="form-select" required>
 <option value="">Escolha um entregador...</option>
 <?php
 $entregadores = \App\Models\Entregador::where('status', 'ativo')
 ->where('disponivel', 1)
 ->where('tenant_code', auth('admin')->user()->tenant_code ?? auth()->user()->tenant_code)
 ->orderBy('nome')
 ->get();
 ?>
 <?php $__currentLoopData = $entregadores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entregador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <option value="<?php echo e($entregador->id); ?>">
 <?php echo e($entregador->nome); ?> - <?php echo e($entregador->tipo_veiculo); ?>

 </option>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select>
 <small class="text-muted">Apenas entregadores ativos e disponíveis</small>
 </div>
 <div class="alert alert-info">
 <i class="fas fa-info-circle me-2"></i>
 O entregador será notificado e deverá aceitar a entrega.
 </div>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
 <button type="submit" class="btn btn-success">
 <i class="fas fa-check me-2"></i>Atribuir Entregador
 </button>
 </div>
 </form>
 </div>
 </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\myd_bar_restaurantes\resources\views/deliveries/index.blade.php ENDPATH**/ ?>