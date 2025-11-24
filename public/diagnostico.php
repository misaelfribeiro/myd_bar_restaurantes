<?php
/**
 * Script de Diagnóstico do Servidor
 * Verifica configurações e problemas comuns
 */

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico do Servidor</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .ok { color: #4ec9b0; }
        .warning { color: #dcdcaa; }
        .error { color: #f48771; }
        .section { margin: 20px 0; padding: 15px; background: #252526; border-left: 3px solid #007acc; }
        h2 { color: #569cd6; margin-top: 0; }
        pre { background: #1e1e1e; padding: 10px; overflow-x: auto; border: 1px solid #3e3e42; }
        .bom-detected { background: #5a1e1e; padding: 5px; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico Completo do Servidor</h1>
    
    <div class="section">
        <h2>1. Informações do PHP</h2>
        <pre>
Versão PHP: <?php echo PHP_VERSION; ?>

Encoding padrão: <?php echo ini_get('default_charset'); ?>

Output buffering: <?php echo ini_get('output_buffering') ? 'ATIVADO (' . ini_get('output_buffering') . ')' : 'DESATIVADO'; ?>

Zend.multibyte: <?php echo ini_get('zend.multibyte') ? 'ATIVADO' : 'DESATIVADO'; ?>

mbstring.encoding_translation: <?php echo ini_get('mbstring.encoding_translation') ? 'ATIVADO' : 'DESATIVADO'; ?>

Default MIME-Type: <?php echo ini_get('default_mimetype'); ?>
        </pre>
    </div>

    <div class="section">
        <h2>2. Teste de Resposta JSON</h2>
        <?php
        ob_start();
        $testData = ['test' => 'value', 'status' => 'ok'];
        $json = json_encode($testData);
        echo $json;
        $output = ob_get_clean();
        
        $outputHex = bin2hex(substr($output, 0, 10));
        $hasBOM = (substr($output, 0, 3) === "\xEF\xBB\xBF");
        ?>
        <pre>
JSON gerado: <?php echo htmlspecialchars($json); ?>

Output capturado: <?php echo htmlspecialchars($output); ?>

Primeiros 10 bytes (hex): <?php echo $outputHex; ?>

<span class="<?php echo $hasBOM ? 'error' : 'ok'; ?>">BOM Detectado: <?php echo $hasBOM ? '❌ SIM (EF BB BF)' : '✅ NÃO'; ?></span>

Bytes extras: <?php echo strlen($output) - strlen($json); ?>

JSON válido: <?php echo json_decode($output) ? '✅ SIM' : '❌ NÃO'; ?>
        </pre>
    </div>

    <div class="section">
        <h2>3. Verificação de Arquivos com BOM</h2>
        <?php
        $filesWithBOM = [];
        $dirsToCheck = [
            __DIR__ . '/../app/Http/Controllers',
            __DIR__ . '/../app/Http/Middleware',
            __DIR__ . '/../routes'
        ];
        
        foreach ($dirsToCheck as $dir) {
            if (is_dir($dir)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir)
                );
                
                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getExtension() === 'php') {
                        $content = file_get_contents($file->getPathname());
                        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
                            $filesWithBOM[] = str_replace(__DIR__ . '/../', '', $file->getPathname());
                        }
                    }
                }
            }
        }
        ?>
        <pre>
Total de arquivos verificados: <?php echo count(iterator_to_array(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../app')))); ?>

<span class="<?php echo count($filesWithBOM) > 0 ? 'error' : 'ok'; ?>">Arquivos com BOM: <?php echo count($filesWithBOM); ?></span>

<?php if (count($filesWithBOM) > 0): ?>
<span class="bom-detected">⚠️ ARQUIVOS COM BOM DETECTADOS:</span>
<?php foreach ($filesWithBOM as $file): ?>
  - <?php echo $file; ?>

<?php endforeach; ?>
<?php else: ?>
✅ Nenhum arquivo com BOM encontrado
<?php endif; ?>
        </pre>
    </div>

    <div class="section">
        <h2>4. Headers e Output Buffer</h2>
        <?php
        ob_start();
        ?>
        <pre>
Headers já enviados: <?php echo headers_sent($file, $line) ? "❌ SIM (em $file:$line)" : '✅ NÃO'; ?>

Níveis de output buffer ativos: <?php echo ob_get_level(); ?>

<?php
        $buffers = [];
        for ($i = ob_get_level(); $i > 0; $i--) {
            $status = ob_get_status($i);
            $buffers[] = $status;
        }
        if (count($buffers) > 0) {
            echo "Buffers ativos:\n";
            foreach ($buffers as $idx => $buf) {
                echo "  Buffer #" . ($idx + 1) . ": ";
                echo "Level=" . $buf['level'] . ", ";
                echo "Type=" . ($buf['type'] ?? 'default') . ", ";
                echo "Status=" . ($buf['status'] ?? 'unknown') . "\n";
            }
        }
        ?>
        </pre>
        <?php ob_end_flush(); ?>
    </div>

    <div class="section">
        <h2>5. Teste de Rota API</h2>
        <pre>
URL de teste: <a href="/api/dashboard/stats" target="_blank">/api/dashboard/stats</a>

Para testar manualmente:
1. Faça login no sistema
2. Abra: <a href="/api/dashboard/stats" target="_blank" style="color: #4ec9b0;">http://localhost/api/dashboard/stats</a>
3. Veja se retorna JSON válido
        </pre>
    </div>

    <div class="section">
        <h2>6. Configurações do Laravel</h2>
        <?php
        $envPath = __DIR__ . '/../.env';
        $hasEnv = file_exists($envPath);
        ?>
        <pre>
Arquivo .env: <?php echo $hasEnv ? '✅ Existe' : '❌ Não encontrado'; ?>

<?php if ($hasEnv): ?>
APP_ENV: <?php echo getenv('APP_ENV') ?: 'Não definido'; ?>

APP_DEBUG: <?php echo getenv('APP_DEBUG') ?: 'Não definido'; ?>

LOG_CHANNEL: <?php echo getenv('LOG_CHANNEL') ?: 'Não definido'; ?>
<?php endif; ?>
        </pre>
    </div>

    <div class="section">
        <h2>7. Extensões PHP Necessárias</h2>
        <pre>
<?php
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'tokenizer', 'xml', 'ctype', 'fileinfo'];
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? '✅' : '❌';
    $class = $loaded ? 'ok' : 'error';
    echo "<span class='$class'>$status $ext</span>\n";
}
?>
        </pre>
    </div>

    <div class="section">
        <h2>8. Permissões de Diretórios</h2>
        <pre>
<?php
$dirsToCheck = [
    'storage/logs' => __DIR__ . '/../storage/logs',
    'storage/framework/cache' => __DIR__ . '/../storage/framework/cache',
    'storage/framework/sessions' => __DIR__ . '/../storage/framework/sessions',
    'storage/framework/views' => __DIR__ . '/../storage/framework/views',
    'bootstrap/cache' => __DIR__ . '/../bootstrap/cache'
];

foreach ($dirsToCheck as $name => $path) {
    if (is_dir($path)) {
        $writable = is_writable($path);
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $status = $writable ? '✅' : '❌';
        $class = $writable ? 'ok' : 'error';
        echo "<span class='$class'>$status $name (Permissões: $perms)</span>\n";
    } else {
        echo "<span class='error'>❌ $name - DIRETÓRIO NÃO EXISTE</span>\n";
    }
}
?>
        </pre>
    </div>

    <div class="section">
        <h2>9. Recomendações</h2>
        <pre class="<?php echo count($filesWithBOM) > 0 ? 'error' : 'ok'; ?>">
<?php if (count($filesWithBOM) > 0): ?>
❌ CRÍTICO: Arquivos com BOM detectados!
   Solução: Recodificar arquivos para UTF-8 sem BOM
   
<?php endif; ?>

<?php if (ini_get('output_buffering')): ?>
⚠️ Output buffering está ativado
   Pode causar problemas com JSON
   
<?php endif; ?>

<?php if (ob_get_level() > 1): ?>
⚠️ Múltiplos níveis de buffer ativos
   Pode causar duplicação de output
   
<?php endif; ?>

<?php if (!headers_sent()): ?>
✅ Headers ainda não foram enviados (OK)
<?php endif; ?>
        </pre>
    </div>

    <p style="margin-top: 30px; padding: 10px; background: #252526; border-left: 3px solid #4ec9b0;">
        <strong>✅ Diagnóstico concluído!</strong><br>
        Timestamp: <?php echo date('Y-m-d H:i:s'); ?>
    </p>

</body>
</html>
