<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "===== TESTE RECUSA DE ENTREGA =====\n\n";

// Pegar primeira entrega disponível
$delivery = App\Models\Delivery::where('disponivel_plataforma', true)
    ->whereNull('entregador_id')
    ->first();

if (!$delivery) {
    echo "❌ Nenhuma entrega disponível para testar\n";
    exit;
}

echo "📦 Entrega #{$delivery->id}\n";
echo "Cliente: {$delivery->cliente_nome}\n";
echo "Status: {$delivery->status}\n";
echo "Notificados antes: " . json_encode($delivery->entregadores_notificados ?? []) . "\n";
echo "Recusados antes: " . json_encode($delivery->entregadores_recusados ?? []) . "\n\n";

// Simular recusa do entregador 1
$entregadorId = 1;
echo "🚫 Simulando recusa do entregador #{$entregadorId}...\n";

$recusados = $delivery->entregadores_recusados ?? [];
if (!in_array($entregadorId, $recusados)) {
    $recusados[] = $entregadorId;
    $delivery->entregadores_recusados = $recusados;
    $delivery->save();
}

echo "✅ Recusa registrada\n";
echo "Recusados depois: " . json_encode($delivery->entregadores_recusados) . "\n\n";

// Buscar outros entregadores
echo "🔍 Buscando outros entregadores disponíveis...\n";
$entregadores = $delivery->buscarEntregadoresProximos();

echo "Total encontrado: " . $entregadores->count() . "\n\n";

foreach ($entregadores as $e) {
    echo "  - ID: {$e->id} | Nome: {$e->nome} | Disponível: " . ($e->disponivel ? 'Sim' : 'Não') . "\n";
}

echo "\n✅ Teste concluído!\n";
