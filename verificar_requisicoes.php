<?php
// Script para verificar se as requisições estão chegando

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICANDO REQUISIÇÕES DE TOKEN ===\n\n";

// 1. Verificar últimas linhas do log
echo "1. Últimas entradas do log Laravel:\n";
echo str_repeat("-", 50) . "\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -20);
    foreach ($lastLines as $line) {
        if (stripos($line, 'token') !== false || stripos($line, 'notificacao') !== false) {
            echo $line;
        }
    }
} else {
    echo "Log não encontrado!\n";
}

echo "\n2. Tokens na tabela fcm_tokens:\n";
echo str_repeat("-", 50) . "\n";
$tokens = DB::table('fcm_tokens')->get();
if ($tokens->count() > 0) {
    foreach ($tokens as $token) {
        echo "ID: {$token->id}\n";
        echo "User ID: {$token->user_id}\n";
        echo "Token: " . substr($token->token, 0, 50) . "...\n";
        echo "Device: {$token->device_type}\n";
        echo "Ativo: " . ($token->ativo ? 'Sim' : 'Não') . "\n";
        echo "Criado em: {$token->created_at}\n";
        echo str_repeat("-", 50) . "\n";
    }
} else {
    echo "❌ Nenhum token encontrado!\n";
}

echo "\n3. Testando endpoint manualmente:\n";
echo str_repeat("-", 50) . "\n";

// Simular requisição
$testToken = "teste_token_" . time();
$testUserId = 26;

try {
    DB::table('fcm_tokens')->updateOrInsert(
        ['user_id' => $testUserId],
        [
            'token' => $testToken,
            'device_type' => 'android',
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]
    );
    echo "✅ Token de teste inserido com sucesso!\n";
    echo "User ID: $testUserId\n";
    echo "Token: $testToken\n";
    
    // Verificar se foi inserido
    $inserted = DB::table('fcm_tokens')->where('user_id', $testUserId)->first();
    if ($inserted) {
        echo "✅ Token confirmado no banco!\n";
    } else {
        echo "❌ Token NÃO foi inserido!\n";
    }
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
