<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\AILearningService;

// Ativar log do Laravel
\Illuminate\Support\Facades\Log::getLogger()->pushHandler(
    new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG)
);

$ai = new AILearningService();
$clienteId = DB::table('clientes')->value('id');

echo "=== TESTE COM LOG ATIVADO ===\n\n";
echo "Mensagem: 'pagar com dinheiro'\n\n";

$result = $ai->processMessage('pagar com dinheiro', null, $clienteId);

echo "\n=== RESULTADO ===\n";
echo "Response: {$result['response']}\n";
echo "Intent: {$result['intent']}\n";
echo "Confidence: {$result['confidence']}\n";
echo "Action: " . ($result['action'] ?? 'null') . "\n";
