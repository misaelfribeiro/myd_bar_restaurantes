<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\AILearningService;
use App\Models\User;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DEBUG ADD TO CART ===\n\n";

$user = User::where('email', 'maria@hotmail.com')->first();
$service = new AILearningService();

// Limpar sessão
DB::table('ai_conversation_sessions')
    ->where('user_id', $user->id)
    ->delete();

// Passo 1: Buscar produto
echo "1. Buscar produto\n";
$response1 = $service->processMessage('quero coca cola', null, $user->id, $user->tenant_code);
echo "   Intent: {$response1['intent']}\n";
echo "   Confidence: {$response1['confidence']}\n";
echo "   Produtos: " . count($response1['products'] ?? []) . "\n\n";

// Verificar sessão
$session = DB::table('ai_conversation_sessions')
    ->where('user_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->first();

$entities = json_decode($session->entities, true);
echo "   Sessão last_products: " . count($entities['last_products'] ?? []) . "\n";
echo "   Session token: {$session->session_token}\n\n";

// Passo 2: Adicionar ao carrinho (COM o session token)
echo "2. Adicionar ao carrinho\n";
$response2 = $service->processMessage('quero essa', $session->session_token, $user->id, $user->tenant_code);
echo "   Intent: {$response2['intent']}\n";
echo "   Confidence: {$response2['confidence']}\n";
echo "   Response: {$response2['response']}\n";
echo "   Cart: " . count($response2['cart'] ?? []) . "\n";

// Listar todos os matches encontrados
if (isset($response2['debug_matches'])) {
    echo "\n   Matches encontrados:\n";
    foreach ($response2['debug_matches'] as $match) {
        echo "      • {$match['intent']} ({$match['confidence']})\n";
    }
}
