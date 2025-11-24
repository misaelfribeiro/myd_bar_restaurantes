<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE DE BUSCA DE CLIENTES ===\n\n";

// Buscar clientes com "maria"
$termo = 'maria';
echo "🔍 Buscando clientes com termo: '$termo'\n\n";

$clientes = \App\Models\Cliente::where('nome', 'like', "%{$termo}%")
    ->orWhere('telefone', 'like', "%{$termo}%")
    ->get();

echo "📊 Total de clientes encontrados: " . $clientes->count() . "\n\n";

if ($clientes->count() > 0) {
    foreach ($clientes as $cliente) {
        echo "✅ Cliente ID: {$cliente->id}\n";
        echo "   Nome: {$cliente->nome}\n";
        echo "   Telefone: {$cliente->telefone}\n";
        echo "   Endereço: " . ($cliente->endereco ?? 'N/A') . "\n";
        echo "   Criado em: {$cliente->created_at}\n";
        echo "\n";
    }
} else {
    echo "❌ Nenhum cliente encontrado\n";
}

echo "\n=== LISTANDO TODOS OS CLIENTES ===\n\n";

$todosClientes = \App\Models\Cliente::all();
echo "📊 Total de clientes no sistema: " . $todosClientes->count() . "\n\n";

foreach ($todosClientes as $cliente) {
    echo "ID: {$cliente->id} - {$cliente->nome} - {$cliente->telefone}\n";
}

echo "\n=== FIM DO TESTE ===\n";
