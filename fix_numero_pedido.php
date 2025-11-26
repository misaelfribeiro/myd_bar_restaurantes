<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Verificando coluna numero_pedido ===\n\n";

// Verificar se coluna existe
$columns = \DB::select("SHOW COLUMNS FROM pedidos LIKE 'numero_pedido'");

if (empty($columns)) {
    echo "❌ PROBLEMA: Coluna 'numero_pedido' NÃO EXISTE na tabela pedidos!\n\n";
    echo "Vou criar a coluna agora...\n";
    
    \DB::statement("ALTER TABLE pedidos ADD COLUMN numero_pedido VARCHAR(255) NULL AFTER id");
    
    echo "✅ Coluna criada!\n\n";
} else {
    echo "✅ Coluna 'numero_pedido' existe\n\n";
}

// Verificar dados
$pedidos = \App\Models\Pedido::select('id', 'numero_pedido', 'total')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

echo "Últimos 5 pedidos:\n";
foreach ($pedidos as $p) {
    $numero = $p->numero_pedido ?: 'NULL';
    echo "ID: {$p->id} | Numero: {$numero} | Total: R$ {$p->total}\n";
}

// Se numero_pedido estiver vazio, preencher
$vazios = \App\Models\Pedido::whereNull('numero_pedido')->orWhere('numero_pedido', '')->count();

if ($vazios > 0) {
    echo "\n⚠️  Encontrados {$vazios} pedidos sem numero_pedido\n";
    echo "Preenchendo automaticamente...\n";
    
    \App\Models\Pedido::whereNull('numero_pedido')
        ->orWhere('numero_pedido', '')
        ->each(function($pedido) {
            $pedido->numero_pedido = str_pad($pedido->id, 3, '0', STR_PAD_LEFT);
            $pedido->save();
        });
    
    echo "✅ Números de pedido preenchidos!\n";
}

echo "\n=== Pedidos disponíveis para teste ===\n";
$pedidos = \App\Models\Pedido::select('id', 'numero_pedido', 'total', 'status')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

foreach ($pedidos as $p) {
    echo "Número: {$p->numero_pedido} | Total: R$ " . number_format($p->total, 2, ',', '.') . " | Status: {$p->status}\n";
}
