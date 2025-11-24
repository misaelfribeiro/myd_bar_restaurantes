<?php
/**
 * Testa comandos de endereço e pagamento
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIConversationSession;
use App\Services\AILearningService;

echo "=== TESTE DE ENDEREÇO E PAGAMENTO ===\n\n";

$empresaId = 'RESTAURANTE0001';
$userId = 3; // admin@eatsfood.com.br

// Criar sessão
$session = AIConversationSession::create([
    'session_token' => 'test_' . uniqid(),
    'usuario_id' => $userId,
    'tenant_code' => $empresaId,
    'context' => [],
    'entities' => [],
    'last_activity' => now(),
    'expires_at' => now()->addHours(2)
]);

$service = new AILearningService();

// Teste 1: Confirmar endereço
echo "👤 \"confirma meu endereço\"\n";
$result1 = $service->processMessage('confirma meu endereço', $session->session_token, $userId, $empresaId);
echo "🤖 Intent: {$result1['intent']}\n";
echo "🤖 Action: {$result1['action']}\n";
echo "🤖 Resposta: {$result1['response']}\n";
if (!empty($result1['address_data'])) {
    echo "✅ Endereço retornado\n";
    print_r($result1['address_data']);
}
echo "\n";

// Teste 2: Mostrar formas de pagamento
echo "👤 \"quais as formas de pagamento\"\n";
$result2 = $service->processMessage('quais as formas de pagamento', $session->session_token, $userId, $empresaId);
echo "🤖 Intent: {$result2['intent']}\n";
echo "🤖 Action: {$result2['action']}\n";
echo "🤖 Resposta: {$result2['response']}\n";
if (!empty($result2['payment_methods'])) {
    echo "✅ Formas de pagamento:\n";
    foreach ($result2['payment_methods'] as $key => $name) {
        echo "   - {$key}: {$name}\n";
    }
}
echo "\n";

// Teste 3: Selecionar PIX
echo "👤 \"pagar via pix\"\n";
$result3 = $service->processMessage('pagar via pix', $session->session_token, $userId, $empresaId);
echo "🤖 Intent: {$result3['intent']}\n";
echo "🤖 Action: {$result3['action']}\n";
echo "🤖 Resposta: {$result3['response']}\n";
if (!empty($result3['payment_selected'])) {
    echo "✅ Pagamento selecionado: {$result3['payment_selected']['name']} ({$result3['payment_selected']['method']})\n";
}
echo "\n";

// Teste 4: Alterar endereço
echo "👤 \"mudar endereço\"\n";
$result4 = $service->processMessage('mudar endereço', $session->session_token, $userId, $empresaId);
echo "🤖 Intent: {$result4['intent']}\n";
echo "🤖 Action: {$result4['action']}\n";
echo "🤖 Resposta: {$result4['response']}\n";
if (!empty($result4['navigate_to'])) {
    echo "✅ Navegar para: {$result4['navigate_to']}\n";
}
echo "\n";

echo "=== RESUMO ===\n";
echo "1️⃣ Confirmar endereço: " . ($result1['action'] === 'confirmAddress' ? '✅' : '❌') . "\n";
echo "2️⃣ Mostrar pagamentos: " . ($result2['action'] === 'showPaymentMethods' ? '✅' : '❌') . "\n";
echo "3️⃣ Selecionar PIX: " . ($result3['action'] === 'selectPayment' ? '✅' : '❌') . "\n";
echo "4️⃣ Mudar endereço: " . ($result4['action'] === 'changeAddress' ? '✅' : '❌') . "\n";

// Limpar
$session->delete();

echo "\n✅ Teste completo!\n";
