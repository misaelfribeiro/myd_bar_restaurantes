<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ADICIONAR CONTEXTO: FINALIZAR PEDIDO ===\n\n";

// Inserir contexto confirm_order
DB::table('ai_contexts')->insert([
    'key' => 'confirm_order',
    'category' => 'order',
    'pattern' => '*(finalizar|concluir|confirmar)*(pedido|compra)*',
    'response_template' => 'Vou finalizar seu pedido agora!',
    'action' => 'confirmOrder',
    'confidence_threshold' => 0.75,
    'active' => 1,
    'created_at' => now(),
    'updated_at' => now()
]);

echo "✅ Contexto 'confirm_order' adicionado!\n";
echo "   Pattern: *(finalizar|concluir|confirmar)*(pedido|compra)*\n";
echo "   Action: confirmOrder\n";
echo "   Threshold: 0.75\n";
