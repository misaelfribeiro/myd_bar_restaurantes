<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AILearningService;
use Illuminate\Support\Facades\DB;

$ai = new AILearningService();
$sessionToken = 'test-sim-' . time();

// Criar sessão
DB::table('ai_conversation_sessions')->insert([
    'session_token' => $sessionToken,
    'user_id' => 3,
    'last_activity' => now(),
    'entities' => json_encode(['awaiting_order_confirmation' => true, 'cart' => [['item' => 'test']]]),
    'context_stack' => json_encode([]),
    'created_at' => now(),
    'updated_at' => now()
]);

echo "Testando comando 'sim' com awaiting_order_confirmation=true\n\n";

// Verificar antes
$session = DB::table('ai_conversation_sessions')->where('session_token', $sessionToken)->first();
echo "Entities ANTES: " . $session->entities . "\n\n";

$result = $ai->processMessage("sim", $sessionToken);

// Verificar depois
$session = DB::table('ai_conversation_sessions')->where('session_token', $sessionToken)->first();
echo "\nEntities DEPOIS: " . $session->entities . "\n\n";

echo "Intent: {$result['intent']}\n";
echo "Action: {$result['action']}\n";
echo "Resposta: {$result['response']}\n";
echo "navigate_to: " . ($result['navigate_to'] ?? 'NENHUM') . "\n";
