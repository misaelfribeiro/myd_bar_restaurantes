

<?php $__env->startSection('title', 'Transações de Pagamento'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-money-bill-wave"></i> Transações de Pagamento
        </h1>
        <a href="<?php echo e(route('admin.financeiro.pagamentos.dashboard')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filtros
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.financeiro.pagamentos.lista')); ?>">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">Todos</option>
                                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pendente</option>
                                <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Aprovado</option>
                                <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejeitado</option>
                                <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelado</option>
                                <option value="refunded" <?php echo e(request('status') == 'refunded' ? 'selected' : ''); ?>>Estornado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Restaurante</label>
                            <input type="text" name="tenant" class="form-control" 
                                   placeholder="Código do restaurante"
                                   value="<?php echo e(request('tenant')); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Método</label>
                            <select name="metodo" class="form-control">
                                <option value="">Todos</option>
                                <option value="pix" <?php echo e(request('metodo') == 'pix' ? 'selected' : ''); ?>>PIX</option>
                                <option value="credit_card" <?php echo e(request('metodo') == 'credit_card' ? 'selected' : ''); ?>>Crédito</option>
                                <option value="debit_card" <?php echo e(request('metodo') == 'debit_card' ? 'selected' : ''); ?>>Débito</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Data Inicial</label>
                            <input type="date" name="data_inicio" class="form-control" 
                                   value="<?php echo e(request('data_inicio')); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Data Final</label>
                            <input type="date" name="data_fim" class="form-control" 
                                   value="<?php echo e(request('data_fim')); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="<?php echo e(route('admin.financeiro.pagamentos.lista')); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Pagamentos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Listagem de Transações
            </h6>
            <span class="badge badge-info"><?php echo e($pagamentos->total()); ?> registros</span>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-sm table-bordered table-hover" style="font-size: 0.85rem; min-width: 100%;">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th style="width: 120px;">Data</th>
                            <th style="width: 70px;">Pedido</th>
                            <th style="width: 130px;">Restaurante</th>
                            <th style="width: 80px;">Método</th>
                            <th style="width: 90px;">Valor</th>
                            <th style="width: 100px;">Estornos</th>
                            <th style="width: 80px;">Taxa</th>
                            <th style="width: 90px;">Líquido</th>
                            <th style="width: 90px;">Status</th>
                            <th style="width: 60px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pagamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>#<?php echo e($pag->id); ?></td>
                            <td><?php echo e($pag->created_at->format('d/m/Y H:i')); ?></td>
                            <td>
                                <a href="#" data-toggle="modal" data-target="#detalhesModal<?php echo e($pag->id); ?>">
                                    #<?php echo e($pag->numero_pedido); ?>

                                </a>
                            </td>
                            <td>
                                <strong><?php echo e($pag->tenant_code); ?></strong>
                                <?php if($pag->pedido && $pag->pedido->empresa): ?>
                                <br><small class="text-muted" style="font-size: 0.75rem;"><?php echo e(Str::limit($pag->pedido->empresa->nome, 15)); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($pag->payment_method == 'pix'): ?>
                                <span class="badge badge-primary">PIX</span>
                                <?php elseif($pag->payment_method == 'credit_card'): ?>
                                <span class="badge badge-success">Crédito</span>
                                <?php elseif($pag->payment_method == 'debit_card'): ?>
                                <span class="badge badge-info">Débito</span>
                                <?php else: ?>
                                <?php echo e($pag->payment_method); ?>

                                <?php endif; ?>
                            </td>
                            <td>R$ <?php echo e(number_format($pag->amount, 2, ',', '.')); ?></td>
                            <td>
                                <?php
                                    $totalEstornos = $pag->pedido && $pag->pedido->estornos 
                                        ? $pag->pedido->estornos->where('status', 'aprovado')->sum('valor') 
                                        : 0;
                                    $estornosPendentes = $pag->pedido && $pag->pedido->estornos 
                                        ? $pag->pedido->estornos->where('status', 'pendente')->count() 
                                        : 0;
                                ?>
                                <?php if($totalEstornos > 0): ?>
                                    <span class="badge badge-danger badge-sm">
                                        -R$ <?php echo e(number_format($totalEstornos, 2, ',', '.')); ?>

                                    </span>
                                    <?php if($estornosPendentes > 0): ?>
                                    <br><span class="badge badge-warning badge-sm mt-1"><?php echo e($estornosPendentes); ?> pend.</span>
                                    <?php endif; ?>
                                <?php elseif($estornosPendentes > 0): ?>
                                    <span class="badge badge-warning badge-sm"><?php echo e($estornosPendentes); ?> pend.</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-info">R$ <?php echo e(number_format($pag->platform_fee, 2, ',', '.')); ?></td>
                            <td class="text-success">
                                <?php
                                    $liquidoReal = $pag->net_amount - $totalEstornos;
                                ?>
                                <strong>R$ <?php echo e(number_format($liquidoReal, 2, ',', '.')); ?></strong>
                            </td>
                            <td>
                                <?php if($pag->status == 'pending'): ?>
                                <span class="badge badge-warning">Pendente</span>
                                <?php elseif($pag->status == 'approved'): ?>
                                <span class="badge badge-success">Aprovado</span>
                                <?php elseif($pag->status == 'rejected'): ?>
                                <span class="badge badge-danger">Rejeitado</span>
                                <?php elseif($pag->status == 'cancelled'): ?>
                                <span class="badge badge-secondary">Cancelado</span>
                                <?php elseif($pag->status == 'refunded'): ?>
                                <span class="badge badge-dark">Estornado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" data-toggle="modal" 
                                        data-target="#detalhesModal<?php echo e($pag->id); ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if($pag->status == 'approved'): ?>
                                <button class="btn btn-sm btn-danger" 
                                        onclick="confirmarEstorno(<?php echo e($pag->id); ?>)">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- Modal Detalhes -->
                        <div class="modal fade" id="detalhesModal<?php echo e($pag->id); ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detalhes do Pagamento #<?php echo e($pag->id); ?></h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                            $totalEstornosModal = $pag->pedido && $pag->pedido->estornos 
                                                ? $pag->pedido->estornos->where('status', 'aprovado')->sum('valor') 
                                                : 0;
                                        ?>
                                        
                                        <?php if($totalEstornosModal > 0): ?>
                                        <div class="alert alert-danger">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                                                </div>
                                                <div class="col">
                                                    <h5 class="alert-heading mb-1">
                                                        <i class="fas fa-building"></i> Estorno EatsFood
                                                    </h5>
                                                    <p class="mb-0">
                                                        <strong>Valor estornado ao cliente:</strong> 
                                                        <span class="h4 text-danger">R$ <?php echo e(number_format($totalEstornosModal, 2, ',', '.')); ?></span>
                                                    </p>
                                                    <small>
                                                        Líquido após estorno: 
                                                        <strong>R$ <?php echo e(number_format($pag->net_amount - $totalEstornosModal, 2, ',', '.')); ?></strong>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="font-weight-bold">Informações do Pagamento</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td><strong>ID:</strong></td>
                                                        <td>#<?php echo e($pag->id); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Pedido:</strong></td>
                                                        <td>#<?php echo e($pag->numero_pedido); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>MP ID:</strong></td>
                                                        <td><?php echo e($pag->mp_payment_id); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Status:</strong></td>
                                                        <td>
                                                            <?php if($pag->status == 'approved'): ?>
                                                            <span class="badge badge-success">✓ Aprovado</span>
                                                            <?php elseif($pag->status == 'pending'): ?>
                                                            <span class="badge badge-warning text-dark">⏳ Pendente</span>
                                                            <?php elseif($pag->status == 'rejected'): ?>
                                                            <span class="badge badge-danger">✗ Rejeitado</span>
                                                            <?php elseif($pag->status == 'cancelled'): ?>
                                                            <span class="badge badge-secondary">⊘ Cancelado</span>
                                                            <?php elseif($pag->status == 'refunded'): ?>
                                                            <span class="badge badge-dark">↺ Estornado</span>
                                                            <?php else: ?>
                                                            <span class="badge badge-secondary"><?php echo e(ucfirst($pag->status)); ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Criado em:</strong></td>
                                                        <td><?php echo e($pag->created_at->format('d/m/Y H:i:s')); ?></td>
                                                    </tr>
                                                    <?php if($pag->paid_at): ?>
                                                    <tr>
                                                        <td><strong>Pago em:</strong></td>
                                                        <td><?php echo e($pag->paid_at->format('d/m/Y H:i:s')); ?></td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="font-weight-bold">Valores</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td><strong>Valor Total:</strong></td>
                                                        <td>R$ <?php echo e(number_format($pag->amount, 2, ',', '.')); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Taxa Gestora:</strong></td>
                                                        <td class="text-info">R$ <?php echo e(number_format($pag->platform_fee, 2, ',', '.')); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Taxa Entrega:</strong></td>
                                                        <td class="text-warning">R$ <?php echo e(number_format($pag->delivery_fee ?? 0, 2, ',', '.')); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Taxa Gateway:</strong></td>
                                                        <td>R$ <?php echo e(number_format($pag->gateway_fee, 2, ',', '.')); ?></td>
                                                    </tr>
                                                    <tr class="bg-light">
                                                        <td><strong>Líquido Restaurante:</strong></td>
                                                        <td class="text-success">
                                                            <strong>R$ <?php echo e(number_format($pag->net_amount, 2, ',', '.')); ?></strong>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <h6 class="font-weight-bold mt-3">Restaurante</h6>
                                                <p class="mb-0">
                                                    <strong><?php echo e($pag->tenant_code); ?></strong><br>
                                                    <?php if($pag->pedido && $pag->pedido->empresa): ?>
                                                    <?php echo e($pag->pedido->empresa->nome); ?>

                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>

                                        <?php if($pag->refund_reason): ?>
                                        <hr>
                                        <div class="alert alert-warning">
                                            <strong>Motivo do Estorno:</strong><br>
                                            <?php echo e($pag->refund_reason); ?>

                                        </div>
                                        <?php endif; ?>

                                        <?php if($pag->pedido && $pag->pedido->estornos && $pag->pedido->estornos->count() > 0): ?>
                                        <hr>
                                        <h6 class="font-weight-bold">
                                            <i class="fas fa-undo text-danger"></i> 
                                            Estornos do Pedido (<?php echo e($pag->pedido->estornos->count()); ?>)
                                        </h6>
                                        <div class="alert alert-warning mb-3">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Importante:</strong> Estes estornos foram processados pela <strong>EatsFood</strong> após reclamação do cliente.
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Tipo</th>
                                                        <th>Valor</th>
                                                        <th>Status</th>
                                                        <th>Motivo</th>
                                                        <th>Solicitado</th>
                                                        <th>Processado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $pag->pedido->estornos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estorno): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr class="<?php echo e($estorno->status == 'aprovado' ? 'table-danger' : ($estorno->status == 'pendente' ? 'table-warning' : '')); ?>">
                                                        <td>
                                                            <span class="badge badge-<?php echo e($estorno->tipo == 'parcial' ? 'info' : 'warning'); ?>">
                                                                <?php echo e(ucfirst($estorno->tipo)); ?>

                                                            </span>
                                                        </td>
                                                        <td class="text-danger font-weight-bold">
                                                            R$ <?php echo e(number_format($estorno->valor, 2, ',', '.')); ?>

                                                        </td>
                                                        <td>
                                                            <?php
                                                                $statusConfig = [
                                                                    'pendente' => ['badge' => 'warning', 'icon' => 'clock', 'text' => 'Pendente'],
                                                                    'aprovado' => ['badge' => 'success', 'icon' => 'check-circle', 'text' => 'Aprovado'],
                                                                    'rejeitado' => ['badge' => 'danger', 'icon' => 'times-circle', 'text' => 'Rejeitado']
                                                                ];
                                                                $config = $statusConfig[$estorno->status] ?? ['badge' => 'secondary', 'icon' => 'question', 'text' => $estorno->status];
                                                            ?>
                                                            <span class="badge badge-<?php echo e($config['badge']); ?>">
                                                                <i class="fas fa-<?php echo e($config['icon']); ?>"></i>
                                                                <?php echo e($config['text']); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small><?php echo e(Str::limit($estorno->motivo, 40)); ?></small>
                                                        </td>
                                                        <td>
                                                            <small>
                                                                <strong><?php echo e($estorno->solicitante->name ?? 'N/A'); ?></strong><br>
                                                                <?php echo e($estorno->solicitado_em->format('d/m/Y H:i')); ?>

                                                            </small>
                                                        </td>
                                                        <td>
                                                            <?php if($estorno->aprovador): ?>
                                                            <small>
                                                                <strong><?php echo e($estorno->aprovador->name); ?></strong><br>
                                                                <?php echo e($estorno->processado_em ? $estorno->processado_em->format('d/m/Y H:i') : '-'); ?>

                                                                <?php if($estorno->observacoes_aprovacao): ?>
                                                                <br><em class="text-muted">"<?php echo e(Str::limit($estorno->observacoes_aprovacao, 30)); ?>"</em>
                                                                <?php endif; ?>
                                                            </small>
                                                            <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-dark">
                                                        <td colspan="2" class="text-right"><strong>Total Estornado (Aprovado):</strong></td>
                                                        <td colspan="4">
                                                            <strong class="text-danger">
                                                                R$ <?php echo e(number_format($pag->pedido->estornos->where('status', 'aprovado')->sum('valor'), 2, ',', '.')); ?>

                                                            </strong>
                                                            <small class="text-muted ml-2">
                                                                (Será debitado do repasse ao restaurante)
                                                            </small>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="modal-footer">
                                        <?php if($pag->status == 'approved'): ?>
                                        <button type="button" class="btn btn-danger" 
                                                onclick="confirmarEstorno(<?php echo e($pag->id); ?>)">
                                            <i class="fas fa-undo"></i> Estornar Pagamento
                                        </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                Nenhuma transação encontrada com os filtros selecionados
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center">
                <?php echo e($pagamentos->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>
</div>

<!-- Form Estorno (oculto) -->
<form id="formEstorno" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="motivo" id="motivoEstorno">
</form>

<style>
/* Tabela compacta e responsiva */
.table-sm {
    font-size: 0.85rem;
}

.table-sm td, .table-sm th {
    padding: 0.4rem;
    vertical-align: middle;
}

.table .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.4rem;
    margin: 1px;
}

.table .badge-sm {
    font-size: 0.7rem;
    padding: 0.2rem 0.35rem;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table-bordered td,
.table-bordered th {
    border: 1px solid #dee2e6 !important;
}

.table thead th {
    background-color: #6c757d;
    color: white;
    font-weight: 600;
    border-color: #5a6268 !important;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table tbody tr {
    background-color: #fff;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.modal-dialog {
    max-width: 900px;
}

/* Ajuste de larguras específicas */
.table td:nth-child(1) { width: 50px; text-align: center; } /* ID */
.table td:nth-child(2) { width: 120px; font-size: 0.8rem; } /* Data */
.table td:nth-child(3) { width: 70px; text-align: center; } /* Pedido */
.table td:nth-child(4) { width: 130px; } /* Restaurante */
.table td:nth-child(5) { width: 80px; text-align: center; } /* Método */
.table td:nth-child(6) { width: 90px; text-align: right; } /* Valor */
.table td:nth-child(7) { width: 100px; text-align: center; } /* Estornos */
.table td:nth-child(8) { width: 80px; text-align: right; } /* Taxa */
.table td:nth-child(9) { width: 90px; text-align: right; } /* Líquido */
.table td:nth-child(10) { width: 90px; text-align: center; } /* Status */
.table td:nth-child(11) { width: 60px; text-align: center; } /* Ações */
</style>

<script>
function confirmarEstorno(paymentId) {
    Swal.fire({
        title: 'Confirmar Estorno',
        text: 'Informe o motivo do estorno:',
        input: 'textarea',
        inputPlaceholder: 'Digite o motivo do estorno...',
        showCancelButton: true,
        confirmButtonText: 'Estornar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        inputValidator: (value) => {
            if (!value) {
                return 'Você precisa informar um motivo!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('formEstorno');
            form.action = `/admin/financeiro/pagamentos/${paymentId}/estornar`;
            document.getElementById('motivoEstorno').value = result.value;
            form.submit();
        }
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\myd_bar_restaurantes\resources\views/admin/financeiro/pagamentos-lista.blade.php ENDPATH**/ ?>