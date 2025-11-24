<?php

require_once 'vendor/autoload.php';

// Inicializar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testando controller da API de pagamentos...\n";

try {
    // Testar instanciação do controller
    $controller = new \App\Http\Controllers\Api\PagamentoController();
    echo "✅ Controller criado com sucesso!\n";
    
    // Verificar se os models existem
    $pedido = \App\Models\Pedido::find(19);
    if ($pedido) {
        echo "✅ Pedido 19 encontrado: Mesa {$pedido->mesa_id}, Total R$ {$pedido->total}\n";
    } else {
        echo "❌ Pedido 19 não encontrado!\n";
        exit(1);
    }
    
    // Verificar caixa aberto
    $caixa = \App\Models\Caixa::where('status', 'aberto')->first();
    if ($caixa) {
        echo "✅ Caixa aberto encontrado: ID {$caixa->id}\n";
    } else {
        echo "❌ Nenhum caixa aberto!\n";
        exit(1);
    }
    
    // Testar criação de request simulado
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'forma_pagamento' => 'cartao_credito',
        'valor' => 50.00,
        'observacoes' => 'Teste direto do controller'
    ]);
    
    echo "✅ Request criado com sucesso!\n";
    echo "📝 Dados do request: forma_pagamento={$request->forma_pagamento}, valor={$request->valor}\n";
    
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "🔍 Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>
