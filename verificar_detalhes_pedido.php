<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pedido;
use App\Models\Usuario;

echo "=== VERIFICAÇÃO PÁGINA DE DETALHES ===\n";

// Buscar pedidos com itens para teste
$pedidos = Pedido::with(['itens.produto', 'mesa'])->has('itens')->latest()->take(5)->get();

if ($pedidos->count() === 0) {
    echo "❌ Nenhum pedido com itens encontrado\n";
    exit;
}

echo "✅ Pedidos disponíveis para teste:\n";
foreach ($pedidos as $pedido) {
    echo sprintf(
        "- Pedido #%d: %d itens, Status: %s, Mesa: %s\n",
        $pedido->id,
        $pedido->itens->count(),
        $pedido->status,
        $pedido->mesa ? $pedido->mesa->identificador : 'Balcão/Delivery'
    );
}

// Verificar usuários admin/gerente
$adminUsers = Usuario::whereIn('role', ['admin', 'gerente'])->get(['nome', 'email', 'role']);

echo "\n=== USUÁRIOS COM PERMISSÃO ===\n";
foreach ($adminUsers as $user) {
    echo "- {$user->nome} ({$user->email}) - {$user->role}\n";
}

// Pegar o primeiro pedido para teste detalhado
$pedidoTeste = $pedidos->first();

echo "\n=== TESTE DETALHADO - PEDIDO #{$pedidoTeste->id} ===\n";
echo "Status: {$pedidoTeste->status}\n";
echo "Total de itens: {$pedidoTeste->itens->count()}\n";

if (in_array($pedidoTeste->status, ['entregue', 'cancelado'])) {
    echo "⚠️ AVISO: Pedido com status '{$pedidoTeste->status}' - botões de exclusão não aparecerão\n";
} else {
    echo "✅ Status permite exclusão para admin/gerente\n";
}

echo "\nItens do pedido:\n";
foreach ($pedidoTeste->itens as $item) {
    echo sprintf(
        "- Item %d: %s (Qtd: %d, R$ %s)\n",
        $item->id,
        $item->produto->nome,
        $item->quantidade,
        number_format($item->preco_unitario, 2, ',', '.')
    );
}

echo "\n=== URLs DE TESTE ===\n";
echo "📍 Detalhes: http://127.0.0.1:8000/pedidos/{$pedidoTeste->id}/detalhes\n";
echo "📍 Edição: http://127.0.0.1:8000/pedidos/{$pedidoTeste->id}/edit\n";

echo "\n=== INSTRUÇÕES ===\n";
echo "1. Faça login como admin/gerente\n";
echo "2. Acesse a URL de detalhes acima\n";
echo "3. Verifique se os botões de lixeira aparecem nos itens\n";
echo "4. Teste a exclusão de um item\n";
echo "5. Verifique se o total é recalculado automaticamente\n";