<?php

require_once 'vendor/autoload.php';

// Inicializar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Criando pedido de teste para API de pagamentos...\n";

try {
    // Buscar uma mesa disponível
    $mesa = \App\Models\Mesa::first();
    if (!$mesa) {
        echo "Erro: Nenhuma mesa encontrada!\n";
        exit(1);
    }
    
    // Buscar um usuário
    $usuario = \App\Models\Usuario::first();
    if (!$usuario) {
        echo "Erro: Nenhum usuário encontrado!\n";
        exit(1);
    }
    
    // Criar pedido
    $pedido = \App\Models\Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => $usuario->id,
        'total' => 75.50,
        'status' => 'finalizado',
        'observacoes' => 'Pedido de teste para API unificada'
    ]);
    
    echo "✅ Pedido criado com sucesso!\n";
    echo "   ID: {$pedido->id}\n";
    echo "   Mesa: {$mesa->numero}\n";
    echo "   Total: R$ {$pedido->total}\n";
    echo "   Status: {$pedido->status}\n";
    
    // Verificar se está pago
    $pago = $pedido->isPago() ? 'SIM' : 'NÃO';
    echo "   Pago: {$pago}\n";
    
    echo "\n🧪 Agora você pode testar a API com o pedido ID {$pedido->id}!\n";
    
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

?>
