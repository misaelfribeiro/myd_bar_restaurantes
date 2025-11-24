<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AILearningService;
use App\Models\AIConversationSession;
use Illuminate\Support\Facades\DB;

$ai = new AILearningService();
$sessionToken = 'test-flow-' . time();

// Criar sessão inicial
DB::table('ai_conversation_sessions')->insert([
    'session_token' => $sessionToken,
    'user_id' => 3,
    'last_activity' => now(),
    'entities' => json_encode(['cart' => [['item' => 'test']], 'payment_method' => 'pix']),
    'context_stack' => json_encode([]),
    'created_at' => now(),
    'updated_at' => now()
]);

echo "=== PASSO 1: Finalizar Pedido ===\n";
$result1 = $ai->processMessage("finalizar pedido", $sessionToken);
echo "Resposta: {$result1['response']}\n";
echo "show_summary: " . ($result1['show_summary'] ? 'SIM' : 'NÃO') . "\n\n";

// Verificar entities na sessão
$session = AIConversationSession::where('session_token', $sessionToken)->first();
echo "Entities após 'finalizar pedido':\n";
print_r($session->entities);
echo "\n";

// Desconectar modelo para forçar nova consulta
unset($session);
unset($ai);

// Recriar instância
$ai = new AILearningService();

echo "=== PASSO 2: Confirmar com 'sim' ===\n";
$result2 = $ai->processMessage("sim", $sessionToken);
echo "Resposta: {$result2['response']}\n";
echo "navigate_to: " . ($result2['navigate_to'] ?? 'NENHUM') . "\n\n";

// Verificar entities após confirmação
$session->refresh();
echo "Entities após 'sim':\n";
print_r($session->entities);
