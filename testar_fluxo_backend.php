<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Services\AILearningService;
use App\Models\User;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE FLUXO BACKEND ===\n\n";

// Buscar usuário Maria
$user = User::where('email', 'maria@hotmail.com')->first();
if (!$user) {
    echo "❌ Usuário não encontrado\n";
    exit;
}

$service = new AILearningService();

// Limpar sessão anterior
DB::table('ai_conversation_sessions')
    ->where('user_id', $user->id)
    ->delete();

echo "👤 Usuário: {$user->name} (ID: {$user->id})\n";
echo "🏢 Tenant: {$user->tenant_code}\n\n";

// PASSO 1: Buscar produto
echo "--- PASSO 1: Buscar Produto ---\n";
echo "🗣️ Usuário: quero coca cola\n";
$response1 = $service->processMessage('quero coca cola', null, $user->id, $user->tenant_code);
echo "🤖 Carla: {$response1['response']}\n";
echo "📦 Produtos retornados: " . count($response1['products'] ?? []) . "\n";
if (!empty($response1['products'])) {
    foreach ($response1['products'] as $p) {
        echo "   • {$p['nome']} - R$ {$p['preco']}\n";
    }
}
$sessionToken = $response1['session_token']; // Guardar token
echo "\n";

// Verificar se produtos foram salvos na sessão
$session = DB::table('ai_conversation_sessions')
    ->where('user_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->first();

if ($session) {
    $entities = json_decode($session->entities, true);
    echo "💾 Produtos na sessão: " . count($entities['last_products'] ?? []) . "\n";
    echo "\n";
}

// PASSO 2: Adicionar ao carrinho
echo "--- PASSO 2: Adicionar ao Carrinho ---\n";
echo "🗣️ Usuário: quero essa\n";
$response2 = $service->processMessage('quero essa', $sessionToken, $user->id, $user->tenant_code);
echo "🤖 Carla: {$response2['response']}\n";
echo "🛒 Itens no carrinho: " . count($response2['cart'] ?? []) . "\n";
if (!empty($response2['cart'])) {
    foreach ($response2['cart'] as $item) {
        echo "   • {$item['quantity']}x {$item['nome']} - R$ " . ($item['preco'] * $item['quantity']) . "\n";
    }
}
echo "\n";

// PASSO 3: Ver carrinho
echo "--- PASSO 3: Ver Carrinho ---\n";
echo "🗣️ Usuário: mostra o carrinho\n";
$response3 = $service->processMessage('mostra o carrinho', $sessionToken, $user->id, $user->tenant_code);
echo "🤖 Carla: {$response3['response']}\n\n";

// PASSO 4: Finalizar pedido
echo "--- PASSO 4: Finalizar Pedido ---\n";
echo "🗣️ Usuário: finalizar pedido\n";
$response4 = $service->processMessage('finalizar pedido', $sessionToken, $user->id, $user->tenant_code);
echo "🤖 Carla: {$response4['response']}\n";

if (isset($response4['pedido_id'])) {
    echo "✅ Pedido #{$response4['pedido_id']} criado com sucesso!\n";
    
    // Verificar itens do pedido
    $pedido = DB::table('pedidos')->find($response4['pedido_id']);
    $itens = DB::table('item_pedidos')->where('pedido_id', $response4['pedido_id'])->get();
    
    echo "\n📝 Detalhes do pedido:\n";
    echo "   Status: {$pedido->status}\n";
    echo "   Total: R$ {$pedido->total}\n";
    echo "   Itens: " . $itens->count() . "\n";
    
    foreach ($itens as $item) {
        echo "      • {$item->quantidade}x Produto #{$item->produto_id} - R$ {$item->preco_unitario}\n";
    }
}

echo "\n=== TESTE CONCLUÍDO ===\n";
