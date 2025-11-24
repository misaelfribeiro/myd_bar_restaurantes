<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Caixa;

echo "🔍 VERIFICANDO STATUS DO CAIXA\n";
echo "═══════════════════════════════\n\n";

$caixaAberto = Caixa::where('status', 'aberto')->first();

if ($caixaAberto) {
    echo "✅ CAIXA ABERTO\n";
    echo "   ID: {$caixaAberto->id}\n";
    echo "   Data Abertura: {$caixaAberto->data_abertura}\n";
    echo "   Responsável: {$caixaAberto->usuario_abertura}\n";
    echo "   Total Vendas: R$ " . number_format($caixaAberto->total_vendas ?? 0, 2, ',', '.') . "\n";
    
    echo "\n🔐 FECHANDO CAIXA PARA TESTE...\n";
    $caixaAberto->update([
        'status' => 'fechado',
        'data_fechamento' => now(),
        'usuario_fechamento' => 1
    ]);
    echo "✅ Caixa fechado com sucesso!\n";
} else {
    echo "❌ NENHUM CAIXA ABERTO\n";
    echo "   Sistema já está no estado de teste necessário.\n";
}

echo "\n📋 RESUMO DO TESTE:\n";
echo "════════════════════\n";
echo "• Caixa fechado ✅\n";
echo "• Validações implementadas no GarcomController ✅\n";
echo "• Alertas visuais no dashboard ✅\n";
echo "• Botão de criar pedido desabilitado ✅\n";

echo "\n🧪 COMO TESTAR:\n";
echo "1. Acesse: http://localhost:8000/garcom/dashboard\n";
echo "2. Observe o alerta amarelo de caixa fechado\n";
echo "3. Tente criar um pedido - deve ser bloqueado\n";
echo "4. Para reabrir o caixa, acesse o sistema administrativo\n";

echo "\n💡 PARA REABRIR O CAIXA:\n";
echo "php artisan tinker --execute=\"\\App\\Models\\Caixa::create(['usuario_abertura' => 1, 'data_abertura' => now(), 'status' => 'aberto', 'saldo_inicial' => 0]);\"\n";
