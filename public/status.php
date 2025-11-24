<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status do Sistema</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .status-card {
            background: white;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status-ok { border-left: 4px solid #28a745; }
        .status-error { border-left: 4px solid #dc3545; }
        .status-warning { border-left: 4px solid #ffc107; }
        h1 { color: #333; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px;
        }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🔍 Status do Sistema</h1>
    
    <div class="status-card status-ok">
        <h3>✅ Servidor Laravel</h3>
        <p><strong>Status:</strong> Online</p>
        <p><strong>URL:</strong> http://127.0.0.1:8080</p>
        <p><strong>Hora:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
    </div>
    
    <div class="status-card <?php echo file_exists(__DIR__ . '/sw.js') ? 'status-error' : 'status-ok'; ?>">
        <h3><?php echo file_exists(__DIR__ . '/sw.js') ? '⚠️' : '✅'; ?> Service Worker</h3>
        <p><strong>Status:</strong> <?php echo file_exists(__DIR__ . '/sw.js') ? 'ATIVO (pode causar problemas)' : 'Desativado'; ?></p>
        <?php if (file_exists(__DIR__ . '/sw.js')): ?>
            <p style="color: #dc3545;">O Service Worker está ativo e pode estar interceptando requisições!</p>
        <?php endif; ?>
    </div>
    
    <div class="status-card status-ok">
        <h3>📊 Informações do PHP</h3>
        <pre>Versão PHP: <?php echo PHP_VERSION; ?>
Memória Limite: <?php echo ini_get('memory_limit'); ?>
Upload Max: <?php echo ini_get('upload_max_filesize'); ?>
Post Max: <?php echo ini_get('post_max_size'); ?></pre>
    </div>
    
    <div class="status-card status-ok">
        <h3>🗄️ Conexão com Banco de Dados</h3>
        <?php
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=myd_bar_restaurantes', 'root', '');
            echo '<p style="color: #28a745;">✅ Conectado com sucesso!</p>';
            
            $stmt = $pdo->query('SELECT COUNT(*) as total FROM users');
            $result = $stmt->fetch();
            echo '<p>Total de usuários Master: ' . $result['total'] . '</p>';
            
            $stmt = $pdo->query('SELECT COUNT(*) as total FROM usuarios');
            $result = $stmt->fetch();
            echo '<p>Total de funcionários: ' . $result['total'] . '</p>';
            
        } catch (PDOException $e) {
            echo '<p style="color: #dc3545;">❌ Erro: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
    
    <div class="status-card status-ok">
        <h3>🔗 Links Rápidos</h3>
        <a href="/admin/login" class="btn">Login Admin</a>
        <a href="/login" class="btn">Login Sistema</a>
        <a href="/" class="btn">Dashboard</a>
    </div>
    
    <div class="status-card status-warning">
        <h3>🧹 Limpar Cache do Navegador</h3>
        <p>Se ainda tiver problemas de conexão:</p>
        <ol>
            <li>Pressione <strong>F12</strong> para abrir DevTools</li>
            <li>Vá na aba <strong>Application</strong></li>
            <li>Na lateral esquerda, clique em <strong>Service Workers</strong></li>
            <li>Clique em <strong>Unregister</strong> em todos os workers</li>
            <li>Ainda no DevTools, clique em <strong>Clear storage</strong></li>
            <li>Marque todas as opções e clique em <strong>Clear site data</strong></li>
            <li>Feche o navegador completamente e abra novamente</li>
        </ol>
    </div>
    
    <script>
        // Verificar se há Service Workers ativos
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(registrations => {
                const container = document.createElement('div');
                container.className = 'status-card status-error';
                container.innerHTML = '<h3>⚠️ Service Workers Ativos no Navegador</h3>';
                
                if (registrations.length > 0) {
                    container.innerHTML += '<p style="color: #dc3545;"><strong>ATENÇÃO:</strong> Existem ' + registrations.length + ' Service Worker(s) ativo(s)!</p>';
                    container.innerHTML += '<button onclick="unregisterAll()" class="btn">Desativar Todos</button>';
                    registrations.forEach((reg, index) => {
                        container.innerHTML += '<pre>SW ' + (index + 1) + ': ' + reg.scope + '</pre>';
                    });
                } else {
                    container.className = 'status-card status-ok';
                    container.innerHTML += '<p style="color: #28a745;">✅ Nenhum Service Worker ativo no navegador</p>';
                }
                
                document.body.insertBefore(container, document.body.children[2]);
            });
        }
        
        function unregisterAll() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(registrations => {
                    Promise.all(registrations.map(reg => reg.unregister())).then(() => {
                        alert('Todos os Service Workers foram desativados! Recarregando página...');
                        location.reload();
                    });
                });
            }
        }
    </script>
</body>
</html>
