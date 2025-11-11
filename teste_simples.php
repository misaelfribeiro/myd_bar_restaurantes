<?php

echo "Iniciando teste simples...\n";

try {
    require_once __DIR__.'/vendor/autoload.php';
    echo "Autoload carregado\n";
    
    $app = require_once __DIR__.'/bootstrap/app.php';
    echo "App carregado\n";
    
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    echo "Kernel inicializado\n";
    
    $count = App\Models\Mesa::count();
    echo "Mesas encontradas: $count\n";
    
    $produto = App\Models\Produto::first();
    if ($produto) {
        echo "Primeiro produto: {$produto->nome}\n";
    } else {
        echo "Nenhum produto encontrado\n";
    }
    
    echo "Teste simples concluído com sucesso!\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}
