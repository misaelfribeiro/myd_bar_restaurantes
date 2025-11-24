<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Monitor da Cozinha - <?php echo e(config('app.name')); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: #1a1a1a;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .header-monitor {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .pedido-card {
            background: #2d2d2d;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            border-left: 5px solid #ffc107;
            animation: slideIn 0.3s ease-out;
        }
        
        .pedido-card.novo {
            animation: pulse 1s ease-in-out 3;
            border-left-color: #28a745;
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.5);
        }
        
        .pedido-card.em_preparo {
            border-left-color: #17a2b8;
        }
        
        .pedido-card.pronto {
            border-left-color: #28a745;
        }
        
        @keyframes  slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes  pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }
        
        .pedido-numero {
            font-size: 3rem;
            font-weight: bold;
            color: #ffc107;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .pedido-mesa {
            font-size: 1.5rem;
            color: #17a2b8;
        }
        
        .item-pedido {
            background: #3a3a3a;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
            border-left: 3px solid #667eea;
        }
        
        .item-quantidade {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            min-width: 60px;
            display: inline-block;
        }
        
        .item-nome {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .item-combo {
            border-left-color: #ffc107;
            background: linear-gradient(135deg, #3a3a3a 0%, #4a3a2a 100%);
        }
        
        .item-observacoes {
            background: #ff6b6b;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .tempo-pedido {
            font-size: 1.2rem;
            color: #aaa;
        }
        
        .btn-acao {
            font-size: 1.2rem;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: bold;
            margin: 5px;
        }
        
        .status-badge {
            font-size: 1.2rem;
            padding: 10px 20px;
            border-radius: 25px;
        }
        
        .conexao-status {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            z-index: 1001;
        }
        
        .conexao-status.offline {
            background: #dc3545;
        }
        
        .sem-pedidos {
            text-align: center;
            padding: 100px 20px;
            color: #666;
        }
        
        .sem-pedidos i {
            font-size: 120px;
            opacity: 0.3;
        }
        
        .fullscreen-btn {
            position: fixed;
            top: 90px;
            right: 20px;
            z-index: 1001;
        }
    </style>
</head>
<body>
    <div class="header-monitor">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0">
                    <i class="fas fa-utensils me-3"></i>
                    Monitor da Cozinha
                </h1>
                <p class="mb-0 mt-2" id="dataHora"></p>
            </div>
            <div class="text-end">
                <h2 class="mb-0" id="contadorPedidos">
                    <span class="badge bg-warning text-dark" id="totalPedidos">0</span> Pedidos Ativos
                </h2>
                <small id="ultimaAtualizacao">Última atualização: agora</small>
            </div>
        </div>
    </div>
    
    <button class="btn btn-primary btn-lg fullscreen-btn" onclick="toggleFullscreen()">
        <i class="fas fa-expand"></i>
    </button>
    
    <div class="container-fluid p-4">
        <div class="row" id="pedidosContainer">
            <div class="col-12 sem-pedidos">
                <i class="fas fa-coffee"></i>
                <h2 class="mt-4">Nenhum pedido no momento</h2>
                <p>Os novos pedidos aparecerão aqui automaticamente</p>
            </div>
        </div>
    </div>
    
    <div class="conexao-status" id="conexaoStatus">
        <i class="fas fa-wifi me-2"></i>
        Conectado
    </div>
    
    <!-- Som de notificação -->
    <audio id="notificacaoSom" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGGe77OWaTRAMUKfj8LdjHAU7k9nyzXkpBSh+zPLaizsKFFu16+qnVBIKSKDh8bllHgcsg9Dx1IU+ChdmvO3kl0sNClCp4/G1YxsGPJLY8s15KQUmfMzx2ok4ChZdt+rppVMSC0ie4PG3ZRwGKoDO8dWFPQoXZbvt5JdLDQpRqOPxtmIcBjaR1vHNeCkFJXzM8dmJNwoWXbbq6aVTEgpJnuDxt2QcBSuBzvHUhj0KF2S87eSXSw0KUajj8bZjHAY3kdXxzXgqBSV8zPHZijYLFluz6umkUxIKSZ7g8bdjGwYtgc7x1IU9ChdjvOzjl0sNCFFo5PKxYhoGN5HU8c14KQUlfe/s1Yf+ChVZtuzopFITCkme3/C2ZBwGKoHO8dSFPQoXY73s45dKDQpRZ+PwtWIaBjOR1PHNeCoEKHzH69mI/AoWWLbq56RREwpDm+DwtmQcByqBzvDUhj4KF2K56+OYSg0KUGfi8LRiGgU0kNPwy3cpBSh8x+vaiPwKFle16ualUhMKQprd77ZkHAcqgc7w1IY+ChdjuuzjmEoNCk9m4+2xYhkFNJDT8Mt3KQUpfcfs2Yj8ChVXtOvmpVITCkKY3e+1ZBwHKYDN8NSGPQoXYrrs45hKDQpPZuPtsWIaBTOP0vDLdykFKH3H7NmI+woVV7Tr56VSEwo/l9zutmQcByh/zfDUhT4KF2K66+OYSg0KT2Xj7LFiGgUzj9Lvy3cpBSh9yO7ViPsKFVi06+ejUhMKP5fc7rZjHAcqgMzw1IY9ChZiuerjl0kNCk1m4+yxYRoFM4/S78t2KQUof... (truncated)" type="audio/wav">
    </audio>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let ultimosPedidos = [];
        let somNotificacao = document.getElementById('notificacaoSom');
        
        // Atualiza data e hora
        function atualizarDataHora() {
            const agora = new Date();
            const opcoes = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('dataHora').textContent = agora.toLocaleDateString('pt-BR', opcoes);
        }
        
        // Buscar pedidos
        async function buscarPedidos() {
            try {
                console.log('Buscando pedidos...');
                const response = await fetch('/api/cozinha/pedidos-ativos');
                console.log('Response status:', response.status);
                
                // Pegar o texto da resposta
                let text = await response.text();
                
                // Remover BOM (Byte Order Mark) se existir
                text = text.replace(/^\uFEFF/, '');
                
                // Fazer parse do JSON
                const data = JSON.parse(text);
                console.log('Data recebida:', data);
                console.log('Success:', data.success);
                console.log('Total de pedidos:', data.pedidos ? data.pedidos.length : 0);
                
                if (data.success) {
                    atualizarPedidos(data.pedidos);
                    document.getElementById('totalPedidos').textContent = data.pedidos.length;
                    document.getElementById('conexaoStatus').classList.remove('offline');
                    document.getElementById('conexaoStatus').innerHTML = '<i class="fas fa-wifi me-2"></i>Conectado';
                }
                
                const agora = new Date();
                document.getElementById('ultimaAtualizacao').textContent = 
                    `Última atualização: ${agora.toLocaleTimeString('pt-BR')}`;
                    
            } catch (error) {
                console.error('Erro ao buscar pedidos:', error);
                document.getElementById('conexaoStatus').classList.add('offline');
                document.getElementById('conexaoStatus').innerHTML = '<i class="fas fa-wifi-slash me-2"></i>Desconectado';
            }
        }
        
        // Atualizar lista de pedidos
        function atualizarPedidos(pedidos) {
            const container = document.getElementById('pedidosContainer');
            
            if (pedidos.length === 0) {
                container.innerHTML = `
                    <div class="col-12 sem-pedidos">
                        <i class="fas fa-coffee"></i>
                        <h2 class="mt-4">Nenhum pedido no momento</h2>
                        <p>Os novos pedidos aparecerão aqui automaticamente</p>
                    </div>
                `;
                return;
            }
            
            // Detectar novos pedidos
            const novosPedidosIds = pedidos.map(p => p.id).filter(id => !ultimosPedidos.includes(id));
            if (novosPedidosIds.length > 0) {
                tocarNotificacao();
            }
            ultimosPedidos = pedidos.map(p => p.id);
            
            container.innerHTML = pedidos.map(pedido => renderizarPedido(pedido, novosPedidosIds.includes(pedido.id))).join('');
        }
        
        // Renderizar pedido
        function renderizarPedido(pedido, isNovo) {
            const statusClass = pedido.status.replace('_', '-');
            const statusLabels = {
                'pendente': 'Pendente',
                'em_preparo': 'Em Preparo',
                'pronto': 'Pronto',
                'entregue': 'Entregue'
            };
            
            const tempo = calcularTempo(pedido.created_at);
            
            return `
                <div class="col-lg-6 col-xl-4">
                    <div class="pedido-card ${statusClass} ${isNovo ? 'novo' : ''}" data-pedido-id="${pedido.id}">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="pedido-numero">#${pedido.id}</div>
                                <div class="pedido-mesa">
                                    <i class="fas fa-${pedido.mesa ? 'chair' : 'truck'} me-2"></i>
                                    ${pedido.mesa ? 'Mesa ' + pedido.mesa.identificador : 'Delivery'}
                                </div>
                                ${pedido.usuario ? `<div class="text-muted"><i class="fas fa-user me-1"></i>${pedido.usuario.nome}</div>` : ''}
                            </div>
                            <div class="text-end">
                                <span class="status-badge badge bg-${getStatusColor(pedido.status)}">
                                    ${statusLabels[pedido.status] || pedido.status}
                                </span>
                                <div class="tempo-pedido mt-2">
                                    <i class="fas fa-clock me-1"></i>
                                    ${tempo}
                                </div>
                            </div>
                        </div>
                        
                        <div class="itens-pedido">
                            ${pedido.itens.map(item => `
                                <div class="item-pedido ${item.tipo_item === 'combo' ? 'item-combo' : ''}">
                                    <div class="d-flex align-items-start">
                                        <span class="item-quantidade">${item.quantidade}x</span>
                                        <div class="flex-grow-1">
                                            <div class="item-nome">
                                                ${item.tipo_item === 'combo' ? '<i class="fas fa-fire text-warning me-2"></i>' : ''}
                                                ${item.nome_item}
                                                ${item.tipo_item === 'combo' ? '<span class="badge bg-warning text-dark ms-2">COMBO</span>' : ''}
                                            </div>
                                            ${item.tipo_item === 'combo' && item.combo ? `
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-box-open me-1"></i>
                                                    ${item.combo.produtos.map(p => p.nome).join(', ')}
                                                </div>
                                            ` : ''}
                                            ${item.observacoes ? `
                                                <div class="item-observacoes">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    OBS: ${item.observacoes}
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        
                        ${pedido.observacoes ? `
                            <div class="alert alert-warning mt-3 mb-3">
                                <strong><i class="fas fa-comment me-2"></i>Observações do Pedido:</strong><br>
                                ${pedido.observacoes}
                            </div>
                        ` : ''}
                        
                        <div class="d-flex gap-2 mt-3">
                            ${pedido.status === 'pendente' ? `
                                <button class="btn btn-primary btn-acao flex-grow-1" onclick="iniciarPreparo(${pedido.id})">
                                    <i class="fas fa-play me-2"></i>Iniciar Preparo
                                </button>
                            ` : ''}
                            ${pedido.status === 'em_preparo' ? `
                                <button class="btn btn-success btn-acao flex-grow-1" onclick="marcarPronto(${pedido.id})">
                                    <i class="fas fa-check me-2"></i>Marcar como Pronto
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }
        
        function getStatusColor(status) {
            const colors = {
                'pendente': 'warning',
                'em_preparo': 'info',
                'pronto': 'success',
                'entregue': 'secondary'
            };
            return colors[status] || 'secondary';
        }
        
        function calcularTempo(createdAt) {
            const criacao = new Date(createdAt);
            const agora = new Date();
            const diff = Math.floor((agora - criacao) / 1000 / 60); // minutos
            
            if (diff < 1) return 'Agora';
            if (diff === 1) return '1 minuto';
            if (diff < 60) return `${diff} minutos`;
            
            const horas = Math.floor(diff / 60);
            const mins = diff % 60;
            return `${horas}h${mins > 0 ? mins + 'm' : ''}`;
        }
        
        function tocarNotificacao() {
            somNotificacao.play().catch(e => console.log('Erro ao tocar som:', e));
        }
        
        async function iniciarPreparo(pedidoId) {
            try {
                const response = await fetch(`/api/pedidos-public/${pedidoId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: 'em_preparo' })
                });
                
                if (response.ok) {
                    buscarPedidos();
                }
            } catch (error) {
                console.error('Erro ao iniciar preparo:', error);
            }
        }
        
        async function marcarPronto(pedidoId) {
            try {
                const response = await fetch(`/api/pedidos-public/${pedidoId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: 'pronto' })
                });
                
                if (response.ok) {
                    buscarPedidos();
                }
            } catch (error) {
                console.error('Erro ao marcar como pronto:', error);
            }
        }
        
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }
        
        // Inicialização
        atualizarDataHora();
        setInterval(atualizarDataHora, 1000);
        
        buscarPedidos();
        setInterval(buscarPedidos, 5000); // Atualiza a cada 5 segundos
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\myd_bar_restaurantes\resources\views/cozinha/monitor.blade.php ENDPATH**/ ?>