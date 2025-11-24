<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DADOS DO SISTEMA ===\n";
echo "Produtos: " . App\Models\Produto::count() . "\n";
echo "Pedidos: " . App\Models\Pedido::count() . "\n";
echo "Usuários: " . App\Models\Usuario::count() . "\n";
echo "Mesas: " . App\Models\Mesa::count() . "\n";

// Verificar se existe algum pedido com itens
$pedidoComItens = App\Models\Pedido::with('itens')->has('itens')->first();
if ($pedidoComItens) {
    echo "\nPedido de teste encontrado: ID {$pedidoComItens->id} com {$pedidoComItens->itens->count()} itens\n";
} else {
    echo "\nNenhum pedido com itens encontrado\n";
}

// Verificar usuários admin/gerente
$admins = App\Models\Usuario::whereIn('role', ['admin', 'gerente'])->get(['id', 'nome', 'email', 'role']);
echo "\n=== USUÁRIOS ADMIN/GERENTE ===\n";
foreach ($admins as $admin) {
    echo "- {$admin->nome} ({$admin->email}) - {$admin->role}\n";
}