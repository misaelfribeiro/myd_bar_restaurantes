<?php
/**
 * Debug: Verifica se produtos estão sendo salvos na sessão
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIConversationSession;
use App\Services\AILearningService;

echo "=== DEBUG: SALVANDO PRODUTOS NA SESSÃO ===\n\n";

$empresaId = 'RESTAURANTE0001';
$userId = null;

// Criar sessão nova COM expires_at
$session = AIConversationSession::create([
    'session_token' => 'test_debug_' . uniqid(),
    'usuario_id' => null,
    'tenant_code' => $empresaId,
    'context' => [],
    'entities' => [],
    'last_activity' => now(),
    'expires_at' => now()->addHours(2) // IMPORTANTE: definir expires_at
]);

$service = new AILearningService();

echo "📍 Session Token: {$session->session_token}\n\n";

// PASSO 1: Buscar produto
echo "PASSO 1: Buscar produto\n";
echo "👤 \"quero coca cola\"\n";
$result1 = $service->processMessage('quero coca cola', $session->session_token, $userId, $empresaId);

echo "✅ Produtos retornados: " . count($result1['products']) . "\n";

// Buscar sessão novamente do banco (não usar refresh)
$sessionReloaded = AIConversationSession::where('session_token', $session->session_token)->first();
echo "📦 Entities na sessão (recarregada do banco):\n";
print_r($sessionReloaded->entities);
echo "\n";

if (isset($sessionReloaded->entities['last_products'])) {
    echo "✅ last_products salvos: " . count($sessionReloaded->entities['last_products']) . " produtos\n";
    foreach ($sessionReloaded->entities['last_products'] as $p) {
        echo "   - ID: {$p['id']} | {$p['nome']}\n";
    }
} else {
    echo "❌ last_products NÃO foram salvos na sessão\n";
}

echo "\n";

// PASSO 2: Tentar adicionar
echo "PASSO 2: Adicionar ao carrinho\n";
echo "👤 \"quero esse\"\n";
$result2 = $service->processMessage('quero esse', $session->session_token, $userId, $empresaId);

echo "Intent: {$result2['intent']}\n";
echo "Action: {$result2['action']}\n";

if (isset($result2['add_to_cart_product'])) {
    echo "✅ add_to_cart_product presente:\n";
    print_r($result2['add_to_cart_product']);
} else {
    echo "❌ add_to_cart_product ausente\n";
}

// Limpar
$session->delete();

echo "\n✅ Debug completo!\n";
