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
use App\Models\Pagamento;

echo "=== PREPARANDO TESTE DE MÚLTIPLOS PAGAMENTOS ===\n\n";

try {
    // 1. Garantir que há caixa aberto
    $caixaAberto = Caixa::where('status', 'aberto')->first();
    if (!$caixaAberto) {
        $usuario = Usuario::first();
        if (!$usuario) {
            echo "❌ Nenhum usuário encontrado\n";
            exit(1);
        }
        
        $caixaAberto = Caixa::create([
            'usuario_id' => $usuario->id,
            'data_abertura' => now(),
            'saldo_inicial' => 100,
            'status' => 'aberto'
        ]);
        echo "✅ Caixa criado - ID: {$caixaAberto->id}\n";
    } else {
        echo "✅ Caixa já existe - ID: {$caixaAberto->id}\n";
    }

    // 2. Limpar pedidos de teste anteriores
    Pedido::where('total', 120.00)->delete();

    // 3. Criar pedido específico para teste
    $mesa = Mesa::first();
    $produto = Produto::first();
    $usuario = Usuario::first();

    if (!$mesa || !$produto || !$usuario) {
        echo "❌ Dados básicos não encontrados (mesa/produto/usuario)\n";
        exit(1);
    }

    $pedidoTeste = Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => $usuario->id,
        'total' => 120.00,
        'status' => 'finalizado'
    ]);

    ItemPedido::create([
        'pedido_id' => $pedidoTeste->id,
        'produto_id' => $produto->id,
        'quantidade' => 2,
        'preco_unitario' => 60.00
    ]);

    echo "✅ Pedido de teste criado:\n";
    echo "   - ID: {$pedidoTeste->id}\n";
    echo "   - Mesa: {$mesa->numero}\n";
    echo "   - Total: R$ " . number_format($pedidoTeste->total, 2, ',', '.') . "\n";
    echo "   - Status: {$pedidoTeste->status}\n";

    // 4. Verificar métodos do pedido
    echo "\n--- VERIFICAÇÕES ---\n";
    echo "isPago(): " . ($pedidoTeste->isPago() ? 'SIM' : 'NÃO') . "\n";
    echo "total_pago: R$ " . number_format($pedidoTeste->total_pago, 2, ',', '.') . "\n";
    echo "saldo_restante: R$ " . number_format($pedidoTeste->saldo_restante, 2, ',', '.') . "\n";

    // 5. URL para teste
    echo "\n--- TESTE MÚLTIPLOS PAGAMENTOS ---\n";
    echo "URL: http://localhost:8000/caixa/recebimento/{$pedidoTeste->id}\n";
    echo "\nCenário de teste:\n";
    echo "1. Abra a URL acima\n";
    echo "2. Clique em 'Múltiplas Formas'\n";
    echo "3. Adicione 2 formas de pagamento:\n";
    echo "   - Dinheiro: R$ 70,00\n";
    echo "   - Cartão: R$ 50,00\n";
    echo "4. Clique em 'Processar Pagamentos'\n";
    echo "5. Verifique o console do navegador (F12) para logs\n";

    echo "\nTotal esperado: R$ 120,00\n";
    echo "Soma teste: R$ 70,00 + R$ 50,00 = R$ 120,00 ✅\n";

    echo "\n✅ PREPARAÇÃO CONCLUÍDA!\n";

} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
