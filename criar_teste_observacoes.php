<?php
require 'vendor/autoload.php';

// Criar uma aplicação Laravel básica
$app = new Illuminate\Foundation\Application(realpath(__DIR__));
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// Bootstrap Laravel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Criar dados de teste
use Illuminate\Support\Facades\DB;

echo "Criando pedido com observações...\n";

try {
    DB::beginTransaction();
    
    // Inserir pedido
    $pedidoId = DB::table('pedidos')->insertGetId([
        'usuario_id' => 1,
        'mesa_id' => 1,
        'total' => 50.00,
        'status' => 'finalizado',
        'observacoes' => 'Pedido para teste de observações',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "Pedido criado com ID: $pedidoId\n";
    
    // Inserir itens com observações
    DB::table('item_pedidos')->insert([
        [
            'pedido_id' => $pedidoId,
            'produto_id' => 1,
            'quantidade' => 1,
            'preco_unitario' => 25.00,
            'subtotal' => 25.00,
            'observacoes' => 'Mal passado, sem cebola',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'pedido_id' => $pedidoId,
            'produto_id' => 2,
            'quantidade' => 2,
            'preco_unitario' => 12.50,
            'subtotal' => 25.00,
            'observacoes' => 'Sem açúcar, com gelo',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ]);
    
    echo "Itens inseridos com observações!\n";
    
    DB::commit();
    echo "✅ Pedido de teste criado com sucesso!\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
