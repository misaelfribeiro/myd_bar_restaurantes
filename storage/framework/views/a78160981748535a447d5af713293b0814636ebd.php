
<?php $__env->startSection('title', 'Detalhes da Entrega #' . $delivery->id); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1><i class="fas fa-shipping-fast me-2"></i>Entrega #<?php echo e($delivery->id); ?></h1>
 <div>
 <a href="<?php echo e(route('deliveries.index')); ?>" class="btn btn-secondary me-2">
 <i class="fas fa-arrow-left me-2"></i>Voltar
 </a>
 <?php if(in_array($delivery->status, ['pendente', 'confirmado'])): ?>
 <a href="<?php echo e(route('deliveries.edit', $delivery->id)); ?>" class="btn btn-primary">
 <i class="fas fa-edit me-2"></i>Editar
 </a>
 <?php endif; ?>
 </div>
 </div>
 <div class="row">
 <!-- Informações Principais -->
 <div class="col-lg-8">
 <div class="card mb-4">
 <div class="card-header d-flex justify-content-between align-items-center">
 <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações da Entrega</h5>
 <?php echo $delivery->status_badge; ?>

 </div>
 <div class="card-body">
 <div class="row">
 <!-- Dados do Cliente -->
 <div class="col-md-6">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-user me-2"></i>Cliente
 </h6>
 <table class="table table-borderless">
 <tr>
 <th width="120">Nome:</th>
 <td><?php echo e($delivery->cliente_nome); ?></td>
 </tr>
 <tr>
 <th>Telefone:</th>
 <td>
 <a href="tel:<?php echo e($delivery->cliente_telefone); ?>" class="text-decoration-none">
 <i class="fas fa-phone me-1"></i><?php echo e($delivery->cliente_telefone); ?>

 </a>
 </td>
 </tr>
 </table>
 </div>
 <!-- Endereço -->
 <div class="col-md-6">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-map-marker-alt me-2"></i>Endereço
 </h6>
 <table class="table table-borderless">
 <tr>
 <th width="120">Endereço:</th>
 <td><?php echo e($delivery->endereco_rua); ?>, <?php echo e($delivery->endereco_numero); ?></td>
 </tr>
 <tr>
 <th>Bairro:</th>
 <td><?php echo e($delivery->endereco_bairro); ?></td>
 </tr>
 <tr>
 <th>Cidade:</th>
 <td><?php echo e($delivery->endereco_cidade); ?></td>
 </tr>
 <?php if($delivery->endereco_cep): ?>
 <tr>
 <th>CEP:</th>
 <td><?php echo e($delivery->endereco_cep); ?></td>
 </tr>
 <?php endif; ?>
 <?php if($delivery->endereco_complemento): ?>
 <tr>
 <th>Complemento:</th>
 <td><?php echo e($delivery->endereco_complemento); ?></td>
 </tr>
 <?php endif; ?>
 </table>
 </div>
 </div>
 <!-- Detalhes da Entrega -->
 <div class="row mt-4">
 <div class="col-12">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-shipping-fast me-2"></i>Detalhes da Entrega
 </h6>
 </div>
 <div class="col-md-6">
 <table class="table table-borderless">
 <tr>
 <th width="150">Taxa de Entrega:</th>
 <td><strong>R$ <?php echo e(number_format($delivery->taxa_entrega, 2, ',', '.')); ?></strong></td>
 </tr>
 <tr>
 <th>Tempo Estimado:</th>
 <td><?php echo e($delivery->tempo_estimado); ?> minutos</td>
 </tr>
 <?php if($delivery->pedido): ?>
 <tr>
 <th>Pedido Vinculado:</th>
 <td>
 <a href="<?php echo e(route('pedidos.show', $delivery->pedido->id)); ?>" class="text-decoration-none">
 <i class="fas fa-receipt me-1"></i>Pedido #<?php echo e($delivery->pedido->id); ?>

 </a>
 </td>
 </tr>
 <?php endif; ?>
 </table>
 </div>
 <div class="col-md-6">
 <table class="table table-borderless">
 <tr>
 <th width="150">Data de Criação:</th>
 <td><?php echo e($delivery->created_at->format('d/m/Y H:i')); ?></td>
 </tr>
 <?php if($delivery->data_confirmacao): ?>
 <tr>
 <th>Confirmado em:</th>
 <td><?php echo e($delivery->data_confirmacao->format('d/m/Y H:i')); ?></td>
 </tr>
 <?php endif; ?>
 <?php if($delivery->data_entrega): ?>
 <tr>
 <th>Entregue em:</th>
 <td><?php echo e($delivery->data_entrega->format('d/m/Y H:i')); ?></td>
 </tr>
 <?php endif; ?>
 </table>
 </div>
 </div>
 <!-- Observações -->
 <?php if($delivery->observacoes): ?>
 <div class="row mt-4">
 <div class="col-12">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-comment me-2"></i>Observações
 </h6>
 <p class="mb-0"><?php echo e($delivery->observacoes); ?></p>
 </div>
 </div>
 <?php endif; ?>
 </div>
 </div>
 <!-- Pedido Vinculado -->
 <?php if($delivery->pedido): ?>
 <div class="card">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Pedido Vinculado #<?php echo e($delivery->pedido->id); ?></h5>
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-6">
 <strong>Status do Pedido:</strong>
 <span class="badge bg-<?php echo e($delivery->pedido->status == 'finalizado' ? 'success' : 'warning'); ?> ms-2">
 <?php echo e(ucfirst($delivery->pedido->status)); ?>

 </span>
 </div>
 <div class="col-md-6">
 <strong>Total do Pedido:</strong>
 <span class="fs-5 fw-bold text-success ms-2">
 R$ <?php echo e(number_format($delivery->pedido->total, 2, ',', '.')); ?>

 </span>
 </div>
 </div>
 <?php if($delivery->pedido->itens->count() > 0): ?>
 <div class="table-responsive">
 <table class="table table-sm">
 <thead class="table-light">
 <tr>
 <th>Produto</th>
 <th>Qtd</th>
 <th>Preço Unit.</th>
 <th>Subtotal</th>
 </tr>
 </thead>
 <tbody>
 <?php $__currentLoopData = $delivery->pedido->itens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <tr>
 <td><?php echo e($item->produto ? $item->produto->nome : 'Produto não encontrado'); ?></td>
 <td><?php echo e($item->quantidade); ?></td>
 <td>R$ <?php echo e(number_format($item->preco, 2, ',', '.')); ?></td>
 <td>R$ <?php echo e(number_format($item->subtotal, 2, ',', '.')); ?></td>
 </tr>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </tbody>
 </table>
 </div>
 <?php endif; ?>
 <div class="text-end">
 <a href="<?php echo e(route('pedidos.show', $delivery->pedido->id)); ?>" class="btn btn-outline-primary">
 <i class="fas fa-eye me-2"></i>Ver Pedido Completo
 </a>
 </div>
 </div>
 </div>
 <?php endif; ?>
 </div>
 <!-- Painel de Ações -->
 <div class="col-lg-4">
 <div class="card sticky-top" style="top: 20px;">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Ações da Entrega</h5>
 </div>
 <div class="card-body">
 <!-- Atribuir Entregador (quando entrega está confirmada, preparando, pronta ou em trânsito) -->
 <?php if(in_array($delivery->status, ['confirmado', 'preparando', 'pronto', 'saiu_entrega'])): ?>
 <div class="mb-4">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-motorcycle me-2"></i>Entregador
 </h6>
 <?php if($delivery->pedido && $delivery->pedido->entregador): ?>
 <!-- Entregador já atribuído -->
 <div class="alert alert-success mb-2">
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <strong><?php echo e($delivery->pedido->entregador->nome); ?></strong>
 <?php if($delivery->tipo_entrega == 'plataforma'): ?>
 <span class="badge bg-success ms-2">Plataforma</span>
 <?php else: ?>
 <span class="badge bg-primary ms-2">Fixo</span>
 <?php endif; ?>
 <br>
 <small class="text-muted">
 <i class="fas fa-phone me-1"></i><?php echo e($delivery->pedido->entregador->telefone); ?><br>
 <i class="fas fa-motorcycle me-1"></i><?php echo e(ucfirst($delivery->pedido->entregador->tipo_veiculo)); ?>

 <?php if($delivery->pedido->entregador->placa_veiculo): ?>
 - <?php echo e($delivery->pedido->entregador->placa_veiculo); ?>

 <?php endif; ?>
 <?php if($delivery->valor_entregador): ?>
 <br><i class="fas fa-dollar-sign me-1"></i>R$ <?php echo e(number_format($delivery->valor_entregador, 2, ',', '.')); ?>

 <?php endif; ?>
 </small>
 </div>
 <?php if($delivery->status == 'pronto' || $delivery->status == 'preparando'): ?>
 <button type="button" class="btn btn-sm btn-outline-danger" 
 onclick="removerEntregador()">
 <i class="fas fa-times"></i>
 </button>
 <?php endif; ?>
 </div>
 </div>
 <?php elseif($delivery->disponivel_plataforma && (!$delivery->pedido || !$delivery->pedido->entregador)): ?>
 <!-- Disponível na plataforma aguardando aceite -->
 <div class="alert alert-warning mb-2">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <strong><i class="fas fa-globe me-2"></i>Disponível na Plataforma</strong><br>
 <small class="text-muted">
 <i class="fas fa-search me-1"></i>Buscando entregadores automaticamente<br>
 <i class="fas fa-dollar-sign me-1"></i>Valor: R$ <?php echo e(number_format($delivery->valor_entregador, 2, ',', '.')); ?><br>
 <i class="fas fa-clock me-1"></i>Desde: <?php echo e($delivery->disponibilizado_em->format('d/m/Y H:i')); ?><br>
 <?php if($delivery->tentativas_notificacao > 0): ?>
 <i class="fas fa-bell me-1"></i><?php echo e($delivery->tentativas_notificacao); ?> tentativa(s) de notificação<br>
 <i class="fas fa-map-marked-alt me-1"></i>Raio de busca: <?php echo e($delivery->raio_busca_km); ?>km
 <?php endif; ?>
 </small>
 </div>
 <button type="button" class="btn btn-sm btn-outline-danger" 
 onclick="cancelarDisponibilizacao()">
 <i class="fas fa-times"></i> Cancelar
 </button>
 </div>
 </div>
 <?php else: ?>
 <!-- Selecionar tipo de entrega -->
 <div class="alert alert-info mb-3">
 <i class="fas fa-info-circle me-2"></i>
 <strong>Pedido Confirmado!</strong><br>
 Escolha como será realizada a entrega.
 </div>
 
 <div class="mb-4">
 <label class="form-label"><strong>Tipo de Entrega:</strong></label>
 <div class="btn-group w-100 mb-3" role="group">
 <input type="radio" class="btn-check" name="tipo_entrega" id="tipo_fixo" value="fixo" checked>
 <label class="btn btn-outline-primary" for="tipo_fixo">
 <i class="fas fa-user-check me-2"></i>Entregador Fixo
 </label>
 
 <input type="radio" class="btn-check" name="tipo_entrega" id="tipo_plataforma" value="plataforma">
 <label class="btn btn-outline-success" for="tipo_plataforma">
 <i class="fas fa-globe me-2"></i>Plataforma
 </label>
 </div>
 </div>
 
 <!-- Seção Entregador Fixo -->
 <div id="secao_entregador_fixo" class="tipo-entrega-secao">
 <div class="mb-3">
 <label class="form-label"><strong>Selecionar Entregador Disponível:</strong></label>
 <select class="form-select form-select-lg" id="entregador_id">
 <option value="">Escolha um entregador...</option>
 <?php $__currentLoopData = $entregadoresDisponiveis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entregador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <option value="<?php echo e($entregador->id); ?>">
 <?php echo e($entregador->nome); ?> - 
 <?php echo e(ucfirst($entregador->tipo_veiculo)); ?> - 
 ⭐ <?php echo e(number_format($entregador->avaliacao_media, 1)); ?>

 (<?php echo e($entregador->entregas_realizadas); ?> entregas)
 </option>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select>
 </div>
 <button type="button" class="btn btn-success btn-lg w-100 mb-3" id="btnAtribuirEntregador"
 onclick="atribuirEntregador()" disabled>
 <i class="fas fa-user-check me-2"></i>Atribuir Entregador Fixo
 </button>
 </div>
 
 <!-- Seção Plataforma -->
 <div id="secao_plataforma" class="tipo-entrega-secao" style="display: none;">
 <div class="alert alert-warning mb-3">
 <i class="fas fa-info-circle me-2"></i>
 A entrega ficará disponível no aplicativo para que entregadores da plataforma possam aceitá-la.
 </div>
 <div class="mb-3">
 <label class="form-label"><strong>Valor para o Entregador:</strong></label>
 <div class="input-group input-group-lg">
 <span class="input-group-text">R$</span>
 <input type="number" class="form-control" id="valor_entregador" 
 value="<?php echo e(number_format($delivery->taxa_entrega * 0.7, 2, '.', '')); ?>" 
 step="0.01" min="0">
 </div>
 <small class="text-muted">
 Taxa de entrega: R$ <?php echo e(number_format($delivery->taxa_entrega, 2, ',', '.')); ?> 
 (Sugestão: 70% = R$ <?php echo e(number_format($delivery->taxa_entrega * 0.7, 2, ',', '.')); ?>)
 </small>
 </div>
 <button type="button" class="btn btn-success btn-lg w-100 mb-3" 
 onclick="disponibilizarParaPlataforma()">
 <i class="fas fa-globe me-2"></i>Disponibilizar na Plataforma
 </button>
 </div>
 <?php endif; ?>
 </div>
 <?php endif; ?>
 
 <?php if($delivery->status == 'confirmado' && !$delivery->disponivel_plataforma): ?>
 <form method="POST" action="<?php echo e(route('deliveries.iniciar-preparo', $delivery->id)); ?>" class="mb-2">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PATCH'); ?>
 <button type="submit" class="btn btn-primary w-100">
 <i class="fas fa-play me-2"></i>Iniciar Preparo
 </button>
 </form>
 <form method="POST" action="<?php echo e(route('deliveries.cancelar', $delivery->id)); ?>" class="mb-2">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PATCH'); ?>
 <button type="submit" class="btn btn-danger w-100" 
 onclick="return confirm('Tem certeza que deseja cancelar esta entrega?')">
 <i class="fas fa-times me-2"></i>Cancelar Entrega
 </button>
 </form>
 <?php endif; ?>
 <?php if($delivery->status == 'preparando'): ?>
 <form method="POST" action="<?php echo e(route('deliveries.marcar-pronto', $delivery->id)); ?>" class="mb-2">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PATCH'); ?>
 <button type="submit" class="btn btn-info w-100">
 <i class="fas fa-check-circle me-2"></i>Marcar como Pronto
 </button>
 </form>
 <?php endif; ?>
 <?php if($delivery->status == 'pronto' && !$delivery->pedido->entregador_id): ?>
 <!-- Saiu para Entrega - Apenas se NÃO houver entregador parceiro -->
 <form method="POST" action="<?php echo e(route('deliveries.sair-entrega', $delivery->id)); ?>" class="mb-2">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PATCH'); ?>
 <button type="submit" class="btn btn-warning w-100">
 <i class="fas fa-truck me-2"></i>Saiu para Entrega (Estabelecimento)
 </button>
 </form>
 <?php endif; ?>
 <?php if($delivery->status == 'saiu_entrega'): ?>
 <form method="POST" action="<?php echo e(route('deliveries.marcar-entregue', $delivery->id)); ?>" class="mb-2">
 <?php echo csrf_field(); ?>
 <?php echo method_field('PATCH'); ?>
 <button type="submit" class="btn btn-success w-100">
 <i class="fas fa-flag-checkered me-2"></i>Marcar como Entregue
 </button>
 </form>
 <?php endif; ?>
 <?php if(in_array($delivery->status, ['entregue', 'cancelado'])): ?>
 <div class="alert alert-info text-center mb-0">
 <i class="fas fa-info-circle me-2"></i>
 <?php if($delivery->status == 'entregue'): ?>
 Entrega finalizada com sucesso!
 <?php else: ?>
 Esta entrega foi cancelada.
 <?php endif; ?>
 </div>
 <?php endif; ?>
 <!-- Informações de Tempo -->
 <hr>
 <h6 class="text-muted"><i class="fas fa-clock me-2"></i>Timeline</h6>
 <ul class="list-unstyled mb-0">
 <li class="mb-2">
 <small class="text-muted">Criado:</small><br>
 <strong><?php echo e($delivery->created_at->format('d/m/Y H:i')); ?></strong>
 </li>
 <?php if($delivery->data_confirmacao): ?>
 <li class="mb-2">
 <small class="text-muted">Confirmado:</small><br>
 <strong><?php echo e($delivery->data_confirmacao->format('d/m/Y H:i')); ?></strong>
 </li>
 <?php endif; ?>
 <?php if($delivery->data_entrega): ?>
 <li class="mb-2">
 <small class="text-muted">Entregue:</small><br>
 <strong><?php echo e($delivery->data_entrega->format('d/m/Y H:i')); ?></strong>
 </li>
 <?php endif; ?>
 </ul>
 </div>
 </div>
 </div>
 </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectEntregador = document.getElementById('entregador_id');
    const btnAtribuir = document.getElementById('btnAtribuirEntregador');
    
    if (selectEntregador && btnAtribuir) {
        selectEntregador.addEventListener('change', function() {
            btnAtribuir.disabled = !this.value;
        });
    }
    
    // Controle dos tipos de entrega
    const radioFixo = document.getElementById('tipo_fixo');
    const radioPlataforma = document.getElementById('tipo_plataforma');
    const secaoFixo = document.getElementById('secao_entregador_fixo');
    const secaoPlataforma = document.getElementById('secao_plataforma');
    
    if (radioFixo && radioPlataforma) {
        radioFixo.addEventListener('change', function() {
            if (this.checked) {
                secaoFixo.style.display = 'block';
                secaoPlataforma.style.display = 'none';
            }
        });
        
        radioPlataforma.addEventListener('change', function() {
            if (this.checked) {
                secaoFixo.style.display = 'none';
                secaoPlataforma.style.display = 'block';
            }
        });
    }
});

function atribuirEntregador() {
    const entregadorId = document.getElementById('entregador_id').value;
    
    if (!entregadorId) {
        alert('Por favor, selecione um entregador.');
        return;
    }
    
    if (confirm('Deseja atribuir este entregador à entrega?')) {
        const url = '<?php echo e(route("deliveries.atribuir-fixo", $delivery->id)); ?>';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                entregador_id: entregadorId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Entregador atribuído com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Não foi possível atribuir o entregador'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao atribuir entregador: ' + error.message);
        });
    }
}

function disponibilizarParaPlataforma() {
    const valorEntregador = document.getElementById('valor_entregador').value;
    
    if (!valorEntregador || parseFloat(valorEntregador) <= 0) {
        alert('Por favor, informe um valor válido para o entregador.');
        return;
    }
    
    if (confirm('Deseja disponibilizar esta entrega na plataforma por R$ ' + parseFloat(valorEntregador).toFixed(2) + '?')) {
        const url = '<?php echo e(route("deliveries.disponibilizar-plataforma", $delivery->id)); ?>';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                valor_entregador: parseFloat(valorEntregador)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Entrega disponibilizada na plataforma com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Não foi possível disponibilizar a entrega'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao disponibilizar entrega: ' + error.message);
        });
    }
}

function cancelarDisponibilizacao() {
    if (!confirm('Tem certeza que deseja cancelar a disponibilização na plataforma?')) {
        return;
    }
    
    const url = '<?php echo e(route("deliveries.cancelar-plataforma", $delivery->id)); ?>';
    console.log('Cancelando em:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => {
        console.log('Status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Erro HTTP ' + response.status + ': ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Resposta:', data);
        if (data.success) {
            alert('Disponibilização cancelada com sucesso!');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível cancelar'));
        }
    })
    .catch(error => {
        console.error('Erro completo:', error);
        alert('Erro ao cancelar disponibilização: ' + error.message);
    });
}

function removerEntregador() {
    if (!confirm('Tem certeza que deseja remover este entregador?')) {
        return;
    }
    
    fetch('<?php echo e(route("deliveries.remover-entregador", $delivery->id)); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Entregador removido com sucesso!');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Não foi possível remover o entregador'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao remover entregador');
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\myd_bar_restaurantes\resources\views/deliveries/show.blade.php ENDPATH**/ ?>