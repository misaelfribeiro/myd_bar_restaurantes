<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== LIMPEZA E TESTE DE AUTENTICAÇÃO ===\n";

// Limpar arquivos de sessão
$sessionPath = storage_path('framework/sessions');
if (is_dir($sessionPath)) {
    $files = glob($sessionPath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ Arquivos de sessão limpos\n";
}

// Verificar configuração de sessão
echo "\n=== CONFIGURAÇÕES DE SESSÃO ===\n";
echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Lifetime: " . config('session.lifetime') . " minutos\n";
echo "Session Domain: " . (config('session.domain') ?: 'null') . "\n";
echo "App URL: " . config('app.url') . "\n";

// Verificar se existe usuário admin
$admin = App\Models\Usuario::where('role', 'admin')->first();
if ($admin) {
    echo "\n✅ Usuário admin encontrado: {$admin->nome} ({$admin->email})\n";
} else {
    echo "\n❌ Nenhum usuário admin encontrado\n";
}

echo "\n=== PRÓXIMOS PASSOS ===\n";
echo "1. Acesse: http://localhost:8000/login-admin-teste\n";
echo "2. Verifique status: http://localhost:8000/status-auth\n";
echo "3. Acesse pedido: http://localhost:8000/pedidos/52/detalhes\n";

// Verificar pedido de teste
$pedido = App\Models\Pedido::with('itens')->find(52);
if ($pedido) {
    echo "\n✅ Pedido #52 encontrado com {$pedido->itens->count()} itens\n";
    echo "Status: {$pedido->status}\n";
} else {
    echo "\n⚠️ Pedido #52 não encontrado\n";
}

echo "\n=== CONFIGURAÇÃO CONCLUÍDA ===\n";