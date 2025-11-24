<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tenant = 'RESTAURANTE0001';

// Simular sessão
$session = new \App\Models\AIConversationSession();
$session->last_intent = 'search_bebida';

echo "Last intent: {$session->last_intent}\n";

// Extrair termo
if (preg_match('/search_(.+)/', $session->last_intent, $matches)) {
    $term = $matches[1];
    echo "Termo extraído: {$term}\n";
    
    $categories = ['bebida', 'comida', 'lanche', 'sobremesa', 'entrada'];
    if (in_array($term, $categories)) {
        echo "É categoria genérica!\n";
        
        $categoria = \App\Models\Categoria::where('nome', 'LIKE', "%{$term}%")->first();
        
        if ($categoria) {
            echo "Categoria encontrada: {$categoria->nome} (ID: {$categoria->id})\n";
            
            $produtos = \App\Models\Produto::where('tenant_code', $tenant)
                ->where('ativo', true)
                ->where('categoria_id', $categoria->id)
                ->orderBy('preco', 'asc')
                ->get();
            
            echo "Produtos encontrados: " . $produtos->count() . "\n";
            foreach($produtos as $p) {
                echo "  - {$p->nome} - R$ {$p->preco}\n";
            }
        } else {
            echo "Categoria NÃO encontrada!\n";
        }
    } else {
        echo "Não é categoria genérica - buscar por termo\n";
    }
}
