<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ai = new App\Services\AILearningService();

// Pegar cliente_id válido
$clienteId = DB::table('clientes')->value('id');
echo "Usando cliente_id: $clienteId\n\n";

// Teste 1: Finalizar pedido
echo "=== TESTE 1: 'finalizar pedido' ===\n";
$r1 = $ai->processMessage('finalizar pedido', null, $clienteId);
print_r(['msg' => $r1['response'], 'nav' => $r1['navigate_to'] ?? 'null']);

echo "\n\n";

// Teste 2: Confirmar pedido
echo "=== TESTE 2: 'confirmar' ===\n";
$r2 = $ai->processMessage('confirmar', null, $clienteId);
print_r(['msg' => $r2['response'], 'nav' => $r2['navigate_to'] ?? 'null']);

echo "\n\n=== RESUMO ===\n";
echo "✅ Teste 1 deve retornar: navigate_to = 'checkout'\n";
echo "✅ Teste 2 deve retornar: navigate_to = 'confirm_order'\n";
