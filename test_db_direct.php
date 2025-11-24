<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Services\AILearningService;

$ai = new AILearningService();
$sessionToken = 'test-direct-' . time();

// Criar sessão direto no banco
DB::table('ai_conversation_sessions')->insert([
    'session_token' => $sessionToken,
    'user_id' => 3,
    'last_activity' => now(),
    'entities' => json_encode(['cart' => [['item' => 'test']]]),
    'context_stack' => json_encode([]),
    'created_at' => now(),
    'updated_at' => now()
]);

echo "Sessão criada\n\n";

// Chamar processMessage
echo "=== Executando: 'finalizar pedido' ===\n";
$result = $ai->processMessage("finalizar pedido", $sessionToken);
echo "Resposta: {$result['response']}\n\n";

// Verificar direto no banco
echo "=== Verificando banco DIRETO ===\n";
$sessionDB = DB::table('ai_conversation_sessions')->where('session_token', $sessionToken)->first();
echo "Entities no banco: " . $sessionDB->entities . "\n";
