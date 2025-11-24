
<?php $__env->startSection('title', 'Dashboard Master - EatsFood'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4" style="background: #f5f7fa; min-height: 100vh; padding-top: 20px;">
 <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">👑 Dashboard Master - EatsFood</h1>
 <p class="text-muted mb-0">Visão geral da plataforma</p>
 </div>
 <div>
 <span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
 <i class="fas fa-crown me-2"></i>Master Account
 </span>
 </div>
 </div>
 <!-- Cards de Estatísticas Principais -->
 <div class="row g-3 mb-4">
 <!-- Empresas Cadastradas -->
 <div class="col-xl-3 col-md-6">
 <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
 <div class="card-body text-white">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <div class="text-white-50 mb-2">Total de Empresas</div>
 <h2 class="mb-0"><?php echo e($stats['total_empresas'] ?? 0); ?></h2>
 <small><i class="fas fa-check-circle me-1"></i><?php echo e($stats['empresas_ativas'] ?? 0); ?> ativas</small>
 </div>
 <div class="bg-white bg-opacity-25 p-3 rounded">
 <i class="fas fa-building fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Receita Mensal -->
 <div class="col-xl-3 col-md-6">
 <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
 <div class="card-body text-white">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <div class="text-white-50 mb-2">Receita Mensal</div>
 <h2 class="mb-0">R$ <?php echo e(number_format($stats['receita_mensalidades'] ?? 0, 0, ',', '.')); ?></h2>
 <small><i class="fas fa-coins me-1"></i>Mensalidades</small>
 </div>
 <div class="bg-white bg-opacity-25 p-3 rounded">
 <i class="fas fa-dollar-sign fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Pedidos do Mês -->
 <div class="col-xl-3 col-md-6">
 <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
 <div class="card-body text-white">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <div class="text-white-50 mb-2">Pedidos no Mês</div>
 <h2 class="mb-0"><?php echo e(number_format($stats['pedidos_mes'] ?? 0, 0, ',', '.')); ?></h2>
 <small><i class="fas fa-calendar me-1"></i><?php echo e($stats['pedidos_hoje'] ?? 0); ?> hoje</small>
 </div>
 <div class="bg-white bg-opacity-25 p-3 rounded">
 <i class="fas fa-shopping-cart fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Usuários Ativos -->
 <div class="col-xl-3 col-md-6">
 <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
 <div class="card-body text-white">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <div class="text-white-50 mb-2">Usuários Total</div>
 <h2 class="mb-0"><?php echo e($stats['total_usuarios_operacionais'] ?? 0); ?></h2>
 <small><i class="fas fa-user-check me-1"></i><?php echo e($stats['usuarios_ativos_hoje'] ?? 0); ?> ativos hoje</small>
 </div>
 <div class="bg-white bg-opacity-25 p-3 rounded">
 <i class="fas fa-users fa-2x"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Linha 2: Clientes e Entregadores -->
 <div class="row g-3 mb-4">
 <!-- Total Clientes -->
 <div class="col-xl-3 col-md-6">
 <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
 <div class="card-body text-dark">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <div class="text-muted mb-2">Total de Clientes</div>
 <h2 class="mb-0"><?php echo e(number_format($stats['total_clientes'] ?? 0, 0, ',', '.')); ?></h2>
 <small class="text-success">
 <i class="fas fa-arrow-up me-1"></i><?php echo e($stats['clientes_novos_mes'] ?? 0); ?> novos este mês
 </small>
 </div>
 <div class="bg-white bg-opacity-50 p-3 rounded">
 <i class="fas fa-user-friends fa-2x text-info"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Total Entregadores -->
 <div class="col-xl-3 col-md-6">
 <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
 <div class="card-body text-dark">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <div class="text-muted mb-2">Total Entregadores</div>
 <h2 class="mb-0"><?php echo e($stats['total_entregadores'] ?? 0); ?></h2>
 <small class="text-success">
 <i class="fas fa-check-circle me-1"></i><?php echo e($stats['entregadores_ativos'] ?? 0); ?> ativos
 </small>
 </div>
 <div class="bg-white bg-opacity-50 p-3 rounded">
 <i class="fas fa-motorcycle fa-2x text-warning"></i>
 </div>
 </div>
 <?php if(isset($stats['entregadores_pendentes']) && $stats['entregadores_pendentes'] > 0): ?>
 <div class="mt-2">
 <a href="<?php echo e(route('entregadores.index', ['status' => 'pendente'])); ?>" class="btn btn-sm btn-warning w-100">
 <i class="fas fa-clock me-1"></i><?php echo e($stats['entregadores_pendentes']); ?> Pendentes de Aprovação
 </a>
 </div>
 <?php endif; ?>
 </div>
 </div>
 </div>
 <!-- Ticket Médio -->
 <div class="col-xl-3 col-md-6">
 <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);">
 <div class="card-body text-dark">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <div class="text-muted mb-2">Ticket Médio</div>
 <h2 class="mb-0">R$ <?php echo e($stats['pedidos_mes'] > 0 ? number_format($stats['valor_total_mes'] / $stats['pedidos_mes'], 2, ',', '.') : '0,00'); ?></h2>
 <small class="text-muted">
 <i class="fas fa-receipt me-1"></i>Por pedido
 </small>
 </div>
 <div class="bg-white bg-opacity-50 p-3 rounded">
 <i class="fas fa-chart-bar fa-2x text-primary"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- GMV Total -->
 <div class="col-xl-3 col-md-6">
 <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%);">
 <div class="card-body text-dark">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <div class="text-muted mb-2">GMV do Mês</div>
 <h2 class="mb-0">R$ <?php echo e(number_format($stats['valor_total_mes'], 0, ',', '.')); ?></h2>
 <small class="text-muted">
 <i class="fas fa-shopping-bag me-1"></i>Volume bruto
 </small>
 </div>
 <div class="bg-white bg-opacity-50 p-3 rounded">
 <i class="fas fa-money-bill-wave fa-2x text-success"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Linha 3: Alertas e Métricas -->
 <div class="row g-3 mb-4">
 <!-- Trial -->
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-warning bg-opacity-10 text-warning p-3 rounded">
 <i class="fas fa-clock fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0"><?php echo e($stats['empresas_trial']); ?></h3>
 <small class="text-muted">Em Trial</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Suspensas -->
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-danger bg-opacity-10 text-danger p-3 rounded">
 <i class="fas fa-ban fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0"><?php echo e($stats['empresas_suspensas']); ?></h3>
 <small class="text-muted">Suspensas</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Novas este mês -->
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-success bg-opacity-10 text-success p-3 rounded">
 <i class="fas fa-plus-circle fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0"><?php echo e($stats['novas_empresas_mes']); ?></h3>
 <small class="text-muted">Novas este mês</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Taxas do Mês -->
 <div class="col-md-3">
 <div class="card border-0 shadow-sm">
 <div class="card-body">
 <div class="d-flex align-items-center">
 <div class="flex-shrink-0 me-3">
 <div class="bg-info bg-opacity-10 text-info p-3 rounded">
 <i class="fas fa-percentage fa-2x"></i>
 </div>
 </div>
 <div>
 <h3 class="mb-0">R$ <?php echo e(number_format($stats['taxa_mes_estimada'], 0, ',', '.')); ?></h3>
 <small class="text-muted">Taxas do mês</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="row g-3">
 <!-- Distribuição por Plano -->
 <div class="col-lg-4">
 <div class="card border-0 shadow-sm">
 <div class="card-header bg-white border-0 py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-chart-pie me-2 text-primary"></i>Distribuição por Plano
 </h5>
 </div>
 <div class="card-body">
 <?php $__currentLoopData = $por_plano; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plano): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <div class="mb-3">
 <div class="d-flex justify-content-between mb-1">
 <span class="badge 
 <?php if($plano->plano == 'enterprise'): ?> bg-primary
 <?php elseif($plano->plano == 'premium'): ?> bg-success
 <?php elseif($plano->plano == 'profissional'): ?> bg-info
 <?php else: ?> bg-secondary
 <?php endif; ?>">
 <?php echo e(strtoupper($plano->plano)); ?>

 </span>
 <strong><?php echo e($plano->total); ?></strong>
 </div>
 <div class="progress" style="height: 8px;">
 <div class="progress-bar 
 <?php if($plano->plano == 'enterprise'): ?> bg-primary
 <?php elseif($plano->plano == 'premium'): ?> bg-success
 <?php elseif($plano->plano == 'profissional'): ?> bg-info
 <?php else: ?> bg-secondary
 <?php endif; ?>" 
 style="width: <?php echo e(($plano->total / $stats['total_empresas']) * 100); ?>%">
 </div>
 </div>
 </div>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </div>
 </div>
 </div>
 <!-- Top Empresas por Pedidos -->
 <div class="col-lg-8">
 <div class="card border-0 shadow-sm">
 <div class="card-header bg-white border-0 py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-trophy me-2 text-warning"></i>Top 5 Empresas do Mês
 </h5>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover align-middle">
 <thead>
 <tr>
 <th>#</th>
 <th>Empresa</th>
 <th class="text-center">Pedidos</th>
 <th class="text-end">Faturamento</th>
 </tr>
 </thead>
 <tbody>
 <?php $__empty_1 = true; $__currentLoopData = $topEmpresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
 <tr>
 <td>
 <?php if($index == 0): ?>
 <i class="fas fa-crown text-warning"></i>
 <?php else: ?>
 <?php echo e($index + 1); ?>

 <?php endif; ?>
 </td>
 <td>
 <strong><?php echo e($empresa->nome_fantasia); ?></strong><br>
 <small class="text-muted"><?php echo e($empresa->tenant_code); ?></small>
 </td>
 <td class="text-center">
 <span class="badge bg-primary"><?php echo e($empresa->total_pedidos); ?></span>
 </td>
 <td class="text-end">
 <strong class="text-success">R$ <?php echo e(number_format($empresa->valor_total, 2, ',', '.')); ?></strong>
 </td>
 </tr>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
 <tr>
 <td colspan="4" class="text-center text-muted py-4">
 <i class="fas fa-info-circle me-2"></i>Nenhum pedido registrado este mês
 </td>
 </tr>
 <?php endif; ?>
 </tbody>
 </table>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Contratos Vencendo -->
 <?php if($vencendo->count() > 0): ?>
 <div class="row g-3 mt-3">
 <div class="col-12">
 <div class="card border-warning border-2 shadow-sm">
 <div class="card-header bg-warning bg-opacity-10 border-0 py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
 Contratos Vencendo nos Próximos 30 Dias
 </h5>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover align-middle">
 <thead>
 <tr>
 <th>Empresa</th>
 <th>Plano</th>
 <th>Vencimento</th>
 <th>Dias Restantes</th>
 <th class="text-end">Valor</th>
 <th class="text-center">Ação</th>
 </tr>
 </thead>
 <tbody>
 <?php $__currentLoopData = $vencendo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <tr>
 <td>
 <strong><?php echo e($empresa->nome_fantasia); ?></strong><br>
 <small class="text-muted"><?php echo e($empresa->tenant_code); ?></small>
 </td>
 <td>
 <span class="badge bg-info"><?php echo e(strtoupper($empresa->plano)); ?></span>
 </td>
 <td><?php echo e(\Carbon\Carbon::parse($empresa->data_fim_contrato)->format('d/m/Y')); ?></td>
 <td>
 <span class="badge 
 <?php if($empresa->dias_restantes_contrato <= 7): ?> bg-danger
 <?php elseif($empresa->dias_restantes_contrato <= 15): ?> bg-warning
 <?php else: ?> bg-info
 <?php endif; ?>">
 <?php echo e($empresa->dias_restantes_contrato); ?> dias
 </span>
 </td>
 <td class="text-end">R$ <?php echo e(number_format($empresa->valor_mensalidade, 2, ',', '.')); ?></td>
 <td class="text-center">
 <a href="<?php echo e(route('admin.tenants.show', $empresa->id)); ?>" class="btn btn-sm btn-primary">
 <i class="fas fa-eye"></i> Ver
 </a>
 </td>
 </tr>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </tbody>
 </table>
 </div>
 </div>
 </div>
 </div>
 </div>
 <?php endif; ?>
 <!-- Ações Rápidas -->
 <div class="row g-3 mt-3 mb-4">
 <div class="col-12">
 <div class="card border-0 shadow-sm">
 <div class="card-header bg-white border-0 py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-bolt me-2 text-warning"></i>Ações Rápidas
 </h5>
 </div>
 <div class="card-body">
 <div class="row g-2">
 <div class="col-md-3">
 <a href="<?php echo e(route('admin.tenants.list')); ?>" class="btn btn-outline-primary w-100">
 <i class="fas fa-list me-2"></i>Listar Todas Empresas
 </a>
 </div>
 <div class="col-md-3">
 <a href="<?php echo e(route('entregadores.index', ['status' => 'pendente'])); ?>" class="btn btn-outline-warning w-100">
 <i class="fas fa-user-clock me-2"></i>Aprovar Entregadores
 <?php if(isset($stats['entregadores_pendentes']) && $stats['entregadores_pendentes'] > 0): ?>
 <span class="badge bg-danger ms-1"><?php echo e($stats['entregadores_pendentes']); ?></span>
 <?php endif; ?>
 </a>
 </div>
 <div class="col-md-3">
 <a href="<?php echo e(route('admin.tenants.financial')); ?>" class="btn btn-outline-success w-100">
 <i class="fas fa-chart-line me-2"></i>Relatório Financeiro
 </a>
 </div>
 <div class="col-md-3">
 <a href="<?php echo e(route('empresas.create')); ?>" class="btn btn-outline-info w-100">
 <i class="fas fa-plus me-2"></i>Nova Empresa
 </a>
 </div>
 <div class="col-md-3">
 <button class="btn btn-outline-secondary w-100">
 <i class="fas fa-cog me-2"></i>Configurações
 </button>
 </div>
 <div class="col-md-3">
 <a href="<?php echo e(route('admin.carla')); ?>" class="btn btn-outline-purple w-100" style="border-color: #667eea; color: #667eea;">
 <i class="fas fa-robot me-2"></i>Painel Carla IA
 </a>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>

 <!-- Seção Carla IA -->
 <div class="row g-3 mt-3 mb-4">
 <div class="col-12">
 <div class="card border-0 shadow-sm" style="border-left: 4px solid #667eea !important;">
 <div class="card-header py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
 <h5 class="card-title mb-0 text-white">
 <i class="fas fa-brain me-2"></i>Carla IA - Status em Tempo Real
 </h5>
 </div>
 <div class="card-body">
 <div class="row g-3" id="carlaStats">
 <div class="col-md-3">
 <div class="text-center p-3 bg-light rounded">
 <div class="text-muted small mb-1">Neurônios</div>
 <h3 class="mb-0" id="carla_neurons">-</h3>
 <small class="text-primary">Rede Neural</small>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center p-3 bg-light rounded">
 <div class="text-muted small mb-1">Sinapses</div>
 <h3 class="mb-0" id="carla_synapses">-</h3>
 <small class="text-success">Conexões</small>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center p-3 bg-light rounded">
 <div class="text-muted small mb-1">Contextos</div>
 <h3 class="mb-0" id="carla_contexts">-</h3>
 <small class="text-info">Conhecimento</small>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center p-3 bg-light rounded">
 <div class="text-muted small mb-1">Taxa de Acerto</div>
 <h3 class="mb-0" id="carla_accuracy">-</h3>
 <small class="text-warning">Performance</small>
 </div>
 </div>
 </div>
 <div class="row mt-3">
 <div class="col-md-6">
 <div class="p-3 bg-light rounded">
 <div class="d-flex justify-content-between align-items-center mb-2">
 <span class="text-muted">Confiança Média</span>
 <strong id="carla_confidence_text">-</strong>
 </div>
 <div class="progress" style="height: 20px;">
 <div id="carla_confidence_bar" class="progress-bar bg-info" role="progressbar" style="width: 0%"></div>
 </div>
 </div>
 </div>
 <div class="col-md-6">
 <div class="p-3 bg-light rounded">
 <div class="d-flex justify-content-between align-items-center mb-2">
 <span class="text-muted">Dados Treinados</span>
 <strong><span id="carla_trained">-</span> / <span id="carla_total">-</span></strong>
 </div>
 <div class="progress" style="height: 20px;">
 <div id="carla_trained_bar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
 </div>
 </div>
 </div>
 </div>
 <div class="text-center mt-3">
 <a href="<?php echo e(route('admin.carla')); ?>" class="btn btn-primary">
 <i class="fas fa-chart-line me-2"></i>Abrir Painel Completo
 </a>
 <button class="btn btn-success ms-2" onclick="trainCarla()">
 <i class="fas fa-graduation-cap me-2"></i>Treinar Agora
 </button>
 </div>
 </div>
 </div>
 </div>
 </div>

</div>
<style>
.card {
 transition: transform 0.2s;
}
.card:hover {
 transform: translateY(-2px);
}
.btn-outline-purple:hover {
 background-color: #667eea !important;
 border-color: #667eea !important;
 color: white !important;
}
</style>

<script>
// Carrega estatísticas da Carla
async function loadCarlaStats() {
 try {
 const response = await fetch('/api/ai/stats');
 const data = await response.json();
 
 // Atualiza cards
 document.getElementById('carla_neurons').textContent = data.total_neurons || 0;
 document.getElementById('carla_synapses').textContent = data.total_synapses || 0;
 document.getElementById('carla_contexts').textContent = data.total_contexts || 0;
 document.getElementById('carla_accuracy').textContent = (data.correct_rate || 0).toFixed(1) + '%';
 
 // Confiança média
 const confidence = (data.avg_confidence || 0) * 100;
 document.getElementById('carla_confidence_text').textContent = confidence.toFixed(1) + '%';
 document.getElementById('carla_confidence_bar').style.width = confidence + '%';
 
 // Treinados
 const trained = data.trained_count || 0;
 const total = data.total_interactions || 0;
 const trainedPercent = total > 0 ? (trained / total * 100) : 0;
 document.getElementById('carla_trained').textContent = trained;
 document.getElementById('carla_total').textContent = total;
 document.getElementById('carla_trained_bar').style.width = trainedPercent + '%';
 
 } catch (error) {
 console.error('Erro ao carregar stats da Carla:', error);
 }
}

// Treinar Carla
async function trainCarla() {
 if (!confirm('Deseja treinar a Carla agora? Isso pode levar alguns minutos.')) {
 return;
 }
 
 const btn = event.target;
 const originalText = btn.innerHTML;
 btn.disabled = true;
 btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Treinando...';
 
 try {
 const response = await fetch('/api/ai/train', {
 method: 'POST',
 headers: { 
 'Content-Type': 'application/json',
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
 },
 body: JSON.stringify({ batch: true })
 });
 
 const result = await response.json();
 alert('✅ Treinamento concluído! ' + (result.data?.trained_count || 0) + ' interações processadas.');
 loadCarlaStats();
 
 } catch (error) {
 alert('❌ Erro ao treinar a Carla');
 } finally {
 btn.disabled = false;
 btn.innerHTML = originalText;
 }
}

// Carrega ao iniciar
document.addEventListener('DOMContentLoaded', () => {
 loadCarlaStats();
 // Auto-refresh a cada 60 segundos
 setInterval(loadCarlaStats, 60000);
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\myd_bar_restaurantes\resources\views/dashboard-master.blade.php ENDPATH**/ ?>