<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Adicionar contexto para finalizar/confirmar pedido
DB::table('ai_contexts')->insertOrIgnore([
    'category' => 'checkout',
    'key' => 'confirm_order',
    'pattern' => '*(finalizar|confirmar|concluir)*(pedido|compra|ordem)*',
    'response_template' => 'Vou finalizar seu pedido agora! Aguarde a confirmação.',
    'action' => 'confirmOrder',
    'confidence_threshold' => 0.75,
    'requires_context' => 0,
    'active' => 1,
    'created_at' => now(),
    'updated_at' => now()
]);

echo "✅ Contexto 'confirm_order' adicionado\n";
echo "Agora você pode dizer: 'finalizar pedido', 'confirmar pedido', 'concluir compra'\n";
