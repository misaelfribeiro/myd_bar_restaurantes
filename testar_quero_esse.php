<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\AILearningService;
use App\Models\User;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE 'QUERO ESSE' AUTOMÁTICO ===\n\n";

$user = User::where('email', 'maria@hotmail.com')->first();
$service = new AILearningService();

// Limpar sessão
DB::table('ai_conversation_sessions')
    ->where('user_id', $user->id)
    ->delete();

echo "👤 Usuário: {$user->name}\n\n";

// PASSO 1: Buscar produto
echo "--- PASSO 1: Buscar Produto ---\n";
echo "🗣️ Usuário: quero coca cola\n";
$response1 = $service->processMessage('quero coca cola', null, $user->id, $user->tenant_code);
$sessionToken = $response1['session_token'];

echo "🤖 Carla: {$response1['response']}\n";
echo "📦 Produtos: " . count($response1['products'] ?? []) . "\n";

if (!empty($response1['products'])) {
    foreach ($response1['products'] as $p) {
        echo "   • {$p['nome']} - R$ {$p['preco']}\n";
    }
}
echo "\n";

// PASSO 2: Adicionar automaticamente com "quero esse"
echo "--- PASSO 2: Adicionar Automaticamente ---\n";
echo "🗣️ Usuário: quero esse\n";
$response2 = $service->processMessage('quero esse', $sessionToken, $user->id, $user->tenant_code);

echo "🤖 Carla: {$response2['response']}\n";
echo "Intent: {$response2['intent']}\n";
echo "Action: {$response2['action']}\n";

if (isset($response2['add_to_cart_product'])) {
    $produto = $response2['add_to_cart_product'];
    echo "\n✅ Produto para adicionar automaticamente:\n";
    echo "   ID: {$produto['id']}\n";
    echo "   Nome: {$produto['nome']}\n";
    echo "   Preço: R$ {$produto['preco']}\n";
    echo "\n🎯 O frontend vai chamar: addToCart({$produto['id']})\n";
    echo "🛒 Produto será adicionado ao appState.cart automaticamente!\n";
} else {
    echo "❌ Nenhum produto para adicionar\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
