<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pedido;
use App\Models\Usuario;

echo "=== TESTE AUTENTICAÇÃO E PEDIDO COM ITENS ===\n";

// Encontrar um pedido com itens para edição
$pedido = Pedido::with(['itens.produto', 'mesa'])->has('itens')->first();

if (!$pedido) {
    echo "❌ Nenhum pedido com itens encontrado\n";
    exit;
}

echo "✅ Pedido ID {$pedido->id} encontrado com {$pedido->itens->count()} itens\n";
echo "Status: {$pedido->status}\n";
if ($pedido->mesa) {
    echo "Mesa: {$pedido->mesa->identificador}\n";
} else {
    echo "Tipo: Balcão/Delivery\n";
}

echo "\n=== ITENS DO PEDIDO ===\n";
foreach ($pedido->itens as $index => $item) {
    echo sprintf(
        "Item %d: %s (ID: %d) - Qtd: %d - R$ %s\n",
        $index + 1,
        $item->produto->nome,
        $item->id,
        $item->quantidade,
        number_format($item->preco_unitario, 2, ',', '.')
    );
}

echo "\n=== USUÁRIOS COM PERMISSÃO ===\n";
$adminUsers = Usuario::whereIn('role', ['admin', 'gerente'])->get(['id', 'nome', 'email', 'role']);

foreach ($adminUsers as $user) {
    echo "- {$user->nome} ({$user->email}) - {$user->role}\n";
}

if ($adminUsers->count() > 0) {
    echo "\n✅ Para testar, faça login com um dos usuários acima\n";
    echo "📍 URL de teste: http://127.0.0.1:8000/pedidos/{$pedido->id}/edit\n";
} else {
    echo "\n❌ Nenhum usuário admin/gerente encontrado\n";
}

echo "\n=== VERIFICAÇÃO DO MÉTODO DE EXCLUSÃO ===\n";

// Testar se a rota está registrada
try {
    $routes = app('router')->getRoutes();
    $routeFound = false;
    
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'pedidos/{pedido}/itens/{item}') && 
            in_array('DELETE', $route->methods())) {
            $routeFound = true;
            echo "✅ Rota de exclusão encontrada: {$route->uri()}\n";
            break;
        }
    }
    
    if (!$routeFound) {
        echo "❌ Rota de exclusão não encontrada\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar rotas: {$e->getMessage()}\n";
}

echo "\n=== PRÓXIMOS PASSOS ===\n";
echo "1. Acesse o sistema como admin: misael_ribeiro@hotmail.com\n";
echo "2. Vá para: http://127.0.0.1:8000/pedidos/{$pedido->id}/edit\n";
echo "3. Verifique se os botões de exclusão aparecem\n";
echo "4. Teste a exclusão de um item\n";