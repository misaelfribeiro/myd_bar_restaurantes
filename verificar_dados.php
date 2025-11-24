<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Verificando dados do sistema...\n\n";

// Verificar contadores
echo "📊 Contadores:\n";
echo "- Caixas: " . App\Models\Caixa::count() . "\n";
echo "- Pagamentos: " . App\Models\Pagamento::count() . "\n";
echo "- Pedidos: " . App\Models\Pedido::count() . "\n";
echo "- Usuários: " . App\Models\Usuario::count() . "\n\n";

// Verificar caixas
echo "💰 Caixas existentes:\n";
$caixas = App\Models\Caixa::with('usuario')->get();
foreach ($caixas as $caixa) {
    $status = $caixa->data_fechamento ? 'Fechado' : 'Aberto';
    echo "- Caixa #{$caixa->id} - {$caixa->data_abertura->format('d/m/Y H:i')} - $status\n";
}

// Verificar pagamentos
echo "\n💳 Pagamentos existentes:\n";
$pagamentos = App\Models\Pagamento::with('pedido')->get();
foreach ($pagamentos as $pagamento) {
    echo "- Pedido #{$pagamento->pedido_id} - {$pagamento->forma_pagamento} - R$ " . number_format($pagamento->valor, 2, ',', '.') . " - {$pagamento->status}\n";
}

echo "\n✅ Verificação concluída!\n";
