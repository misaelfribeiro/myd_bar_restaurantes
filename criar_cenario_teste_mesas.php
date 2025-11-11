<?php

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\ItemPedido;

echo "=== CRIANDO CENÁRIO DE TESTE ===\n";

// Primeiro, verificar se já existe pedido aberto
$pedidoExistente = Pedido::where('status', 'aberto')->first();
if ($pedidoExistente) {
    echo "Já existe pedido aberto: ID {$pedidoExistente->id} na mesa {$pedidoExistente->mesa_id}\n";
} else {
    echo "Nenhum pedido aberto encontrado. Criando um...\n";
    
    // Buscar primeira mesa
    $mesa = Mesa::first();
    if (!$mesa) {
        echo "Erro: Nenhuma mesa encontrada!\n";
        exit;
    }
    
    // Criar pedido de teste
    $pedido = Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => 1,
        'total' => 35.80,
        'status' => 'aberto',
        'observacoes' => 'Pedido de teste para verificar duplicação'
    ]);
    
    echo "Pedido criado: ID {$pedido->id} na Mesa {$mesa->numero}\n";
}

// Verificar estado atual
$totalMesas = Mesa::count();
$mesasOcupadas = Mesa::whereHas('pedidos', function($query) { 
    $query->where('status', 'aberto'); 
})->count();
$mesasLivres = $totalMesas - $mesasOcupadas;

echo "\n=== ESTADO ATUAL ===\n";
echo "Total de mesas: {$totalMesas}\n";
echo "Mesas ocupadas: {$mesasOcupadas}\n";
echo "Mesas livres: {$mesasLivres}\n";

echo "\n=== LISTAGEM DETALHADA ===\n";
$mesasComStatus = Mesa::with(['pedidos' => function($query) { 
    $query->where('status', 'aberto'); 
}])->get();

foreach ($mesasComStatus as $mesa) {
    $nome = $mesa->identificador ?: "Mesa {$mesa->numero}";
    $status = $mesa->pedidos->count() > 0 ? "OCUPADA" : "LIVRE";
    $pedidoInfo = $mesa->pedidos->count() > 0 ? " (Pedido: {$mesa->pedidos->first()->id})" : "";
    echo "- {$nome}: {$status}{$pedidoInfo}\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
