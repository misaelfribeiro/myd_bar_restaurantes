<?php
/**
 * Testa fluxo completo como usuário cliente
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIConversationSession;
use App\Services\AILearningService;

echo "=== TESTE DE FLUXO COMPLETO (SEM AUTENTICAÇÃO) ===\n\n";

// Dados de teste - usando tenant code correto
$empresaId = 'RESTAURANTE0001';
$userId = null;

// Criar sessão nova COM expires_at
$session = AIConversationSession::create([
    'session_token' => 'test_' . uniqid(),
    'usuario_id' => null,
    'tenant_code' => $empresaId,
    'context' => [],
    'entities' => [],
    'last_activity' => now(),
    'expires_at' => now()->addHours(2) // IMPORTANTE
]);

$service = new AILearningService();

// ==========================================
// PASSO 1: Buscar produto
// ==========================================
echo "👤 Usuário: \"quero coca cola\"\n";
$result1 = $service->processMessage('quero coca cola', $session->session_token, $userId, $empresaId);

echo "🤖 Intent: {$result1['intent']}\n";
echo "🤖 Confiança: " . number_format($result1['confidence'] * 100, 1) . "%\n";
echo "🤖 Action: " . ($result1['action'] ?? 'N/A') . "\n";

if (!empty($result1['products'])) {
    echo "🤖 Produtos encontrados: " . count($result1['products']) . "\n";
    foreach ($result1['products'] as $p) {
        echo "   - {$p['nome']} - R$ " . number_format($p['preco'], 2, ',', '.') . "\n";
    }
} else {
    echo "⚠️ Nenhum produto encontrado\n";
}

echo "\n";

// ==========================================
// PASSO 2: Adicionar ao carrinho
// ==========================================
echo "👤 Usuário: \"quero esse\"\n";
$result2 = $service->processMessage('quero esse', $session->session_token, $userId, $empresaId);

echo "🤖 Intent: {$result2['intent']}\n";
echo "🤖 Action: " . ($result2['action'] ?? 'N/A') . "\n";

if (!empty($result2['add_to_cart_product'])) {
    $product = $result2['add_to_cart_product'];
    echo "✅ add_to_cart_product:\n";
    echo "   ID: {$product['id']}\n";
    echo "   Nome: {$product['nome']}\n";
    echo "   Preço: R$ " . number_format($product['preco'], 2, ',', '.') . "\n";
    echo "🎯 Frontend chamará: addToCart({$product['id']})\n";
} else {
    echo "❌ Nenhum produto para adicionar\n";
}

echo "\n";

// ==========================================
// PASSO 3: Visualizar carrinho
// ==========================================
echo "👤 Usuário: \"mostra o carrinho\"\n";
$result3 = $service->processMessage('mostra o carrinho', $session->session_token, $userId, $empresaId);

echo "🤖 Intent: {$result3['intent']}\n";
echo "🤖 Action: " . ($result3['action'] ?? 'N/A') . "\n";
echo "🤖 Resposta: {$result3['response']}\n";

if (!empty($result3['navigate_to'])) {
    echo "✅ navigate_to: {$result3['navigate_to']}\n";
    echo "🎯 Frontend chamará: showCart()\n";
} else {
    echo "❌ Nenhuma navegação configurada\n";
}

echo "\n";

// ==========================================
// PASSO 4: Finalizar pedido
// ==========================================
echo "👤 Usuário: \"finalizar pedido\"\n";
$result4 = $service->processMessage('finalizar pedido', $session->session_token, $userId, $empresaId);

echo "🤖 Intent: {$result4['intent']}\n";
echo "🤖 Action: " . ($result4['action'] ?? 'N/A') . "\n";
echo "🤖 Resposta: {$result4['response']}\n";

if (!empty($result4['navigate_to'])) {
    echo "✅ navigate_to: {$result4['navigate_to']}\n";
    echo "🎯 Frontend chamará: proceedToCheckout()\n";
} else {
    echo "❌ Nenhuma navegação configurada\n";
}

echo "\n";

// ==========================================
// RESUMO
// ==========================================
echo "=== RESUMO DO FLUXO ===\n\n";
echo "1️⃣ Busca de produtos: " . (!empty($result1['products']) ? '✅ OK (' . count($result1['products']) . ' produtos)' : '❌ FALHOU') . "\n";
echo "2️⃣ Adicionar ao carrinho: " . (!empty($result2['add_to_cart_product']) ? '✅ OK' : '❌ FALHOU') . "\n";
echo "3️⃣ Mostrar carrinho: " . ($result3['navigate_to'] === 'cart' ? '✅ OK' : '❌ FALHOU') . "\n";
echo "4️⃣ Finalizar pedido: " . ($result4['navigate_to'] === 'checkout' ? '✅ OK' : '❌ FALHOU') . "\n";

// Limpar sessão de teste
$session->delete();

echo "\n✅ Teste completo!\n";
