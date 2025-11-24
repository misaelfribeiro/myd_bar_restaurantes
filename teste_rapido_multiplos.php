<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Caixa;
use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\ItemPedido;

echo "=== TESTE RÁPIDO DE MÚLTIPLOS PAGAMENTOS ===\n\n";

try {
    // 1. Garantir caixa aberto
    $caixaAberto = Caixa::where('status', 'aberto')->first();
    if (!$caixaAberto) {
        $usuario = Usuario::first();
        $caixaAberto = Caixa::create([
            'usuario_id' => $usuario->id,
            'data_abertura' => now(),
            'saldo_inicial' => 100,
            'status' => 'aberto'
        ]);
        echo "✅ Caixa criado: {$caixaAberto->id}\n";
    } else {
        echo "✅ Caixa encontrado: {$caixaAberto->id}\n";
    }

    // 2. Buscar pedido para teste
    $pedido = Pedido::where('status', 'finalizado')
        ->whereDoesntHave('pagamentos', function($query) {
            $query->where('status', 'confirmado');
        })
        ->first();

    if (!$pedido) {
        echo "❌ Nenhum pedido disponível para teste\n";
        exit();
    }

    echo "✅ Pedido encontrado: {$pedido->id} - Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";

    // 3. Testar dados que seriam enviados
    $multiplosPagamentos = [
        ['forma_pagamento' => 'dinheiro', 'valor' => round($pedido->total * 0.6, 2)],
        ['forma_pagamento' => 'cartao_credito', 'valor' => round($pedido->total * 0.4, 2)]
    ];

    echo "\n--- DADOS PARA TESTE ---\n";
    echo "URL: http://localhost:8000/caixa/processar-pagamento/{$pedido->id}\n";
    echo "Método: POST\n";
    echo "Dados:\n";
    echo "  _token: [token_csrf]\n";
    echo "  multiplos_pagamentos: " . json_encode($multiplosPagamentos) . "\n";

    $totalTest = array_sum(array_column($multiplosPagamentos, 'valor'));
    echo "Total teste: R$ " . number_format($totalTest, 2, ',', '.') . "\n";
    echo "Diferença: R$ " . number_format(abs($pedido->total - $totalTest), 2, ',', '.') . "\n";

    // 4. URL para teste manual
    echo "\n--- TESTE MANUAL ---\n";
    echo "1. Abra: http://localhost:8000/caixa/recebimento/{$pedido->id}\n";
    echo "2. Abra F12 → Console\n";
    echo "3. Clique em 'Múltiplas Formas'\n";
    echo "4. Adicione:\n";
    echo "   - {$multiplosPagamentos[0]['forma_pagamento']}: R$ " . number_format($multiplosPagamentos[0]['valor'], 2, ',', '.') . "\n";
    echo "   - {$multiplosPagamentos[1]['forma_pagamento']}: R$ " . number_format($multiplosPagamentos[1]['valor'], 2, ',', '.') . "\n";
    echo "5. Observe os logs no console e no Laravel\n";

    echo "\n✅ PREPARAÇÃO CONCLUÍDA!\n";

} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
?>
