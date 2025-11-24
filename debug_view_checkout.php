<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\AILearningService;
use App\Models\User;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG VIEW CART E CHECKOUT ===\n\n";

$user = User::where('email', 'maria@hotmail.com')->first();
$service = new AILearningService();

// Criar sessão com carrinho
DB::table('ai_conversation_sessions')
    ->where('user_id', $user->id)
    ->delete();

// Adicionar produto ao carrinho
$response1 = $service->processMessage('quero coca cola', null, $user->id, $user->tenant_code);
$sessionToken = $response1['session_token'];

$response2 = $service->processMessage('quero essa', $sessionToken, $user->id, $user->tenant_code);
echo "Produto adicionado. Carrinho: " . count($response2['cart']) . " item(s)\n\n";

// Testar view cart
echo "--- VIEW CART ---\n";
$response3 = $service->processMessage('mostra o carrinho', $sessionToken, $user->id, $user->tenant_code);
echo "Intent: {$response3['intent']}\n";
echo "Action: {$response3['action']}\n";
echo "Confidence: {$response3['confidence']}\n";
echo "Response: {$response3['response']}\n\n";

// Testar checkout
echo "--- CHECKOUT ---\n";
$response4 = $service->processMessage('finalizar pedido', $sessionToken, $user->id, $user->tenant_code);
echo "Intent: {$response4['intent']}\n";
echo "Action: {$response4['action']}\n";
echo "Confidence: {$response4['confidence']}\n";
echo "Response: {$response4['response']}\n";
if (isset($response4['pedido_id'])) {
    echo "Pedido ID: {$response4['pedido_id']}\n";
}
