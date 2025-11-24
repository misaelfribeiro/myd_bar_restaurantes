<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Produto;
use App\Models\User;
use App\Models\Pedido;
use App\Models\PedidoItem;

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🧪 TESTE COMPLETO: Produto → Carrinho → Pedido\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// 1. SELECIONAR PRODUTO
echo "1️⃣ SELECIONANDO PRODUTO\n";
echo "───────────────────────────────────────\n";

$produto = Produto::where('tenant_code', 'RESTAURANTE0001')
    ->where('ativo', true)
    ->first();

if (!$produto) {
    die("❌ Nenhum produto encontrado para RESTAURANTE0001\n");
}

echo "✅ Produto selecionado:\n";
echo "   ID: {$produto->id}\n";
echo "   Nome: {$produto->nome}\n";
echo "   Preço: R$ " . number_format($produto->preco, 2, ',', '.') . "\n";
echo "   Tenant: {$produto->tenant_code}\n\n";

// 2. BUSCAR/CRIAR USUÁRIO
echo "2️⃣ VERIFICANDO USUÁRIO\n";
echo "───────────────────────────────────────\n";

$user = User::where('email', 'teste@cliente.com')->first();

if (!$user) {
    echo "⚠️ Usuário não encontrado. Criando...\n";
    $user = User::create([
        'name' => 'Cliente Teste',
        'email' => 'teste@cliente.com',
        'password' => bcrypt('123456'),
        'tipo_usuario' => 'cliente',
        'telefone' => '11999999999',
        'status' => 'ativo'
    ]);
    echo "✅ Usuário criado!\n";
} else {
    echo "✅ Usuário encontrado!\n";
}

echo "   ID: {$user->id}\n";
echo "   Nome: {$user->name}\n";
echo "   Email: {$user->email}\n\n";

// 3. CRIAR CARRINHO (simulado)
echo "3️⃣ MONTANDO CARRINHO\n";
echo "───────────────────────────────────────\n";

$carrinho = [
    [
        'produto_id' => $produto->id,
        'quantidade' => 2,
        'preco_unitario' => $produto->preco,
        'observacoes' => 'Teste via backend'
    ]
];

$subtotal = 0;
foreach ($carrinho as $item) {
    $total_item = $item['quantidade'] * $item['preco_unitario'];
    $subtotal += $total_item;
    echo "✅ Item adicionado:\n";
    echo "   Produto: {$produto->nome}\n";
    echo "   Quantidade: {$item['quantidade']}\n";
    echo "   Preço unitário: R$ " . number_format($item['preco_unitario'], 2, ',', '.') . "\n";
    echo "   Total: R$ " . number_format($total_item, 2, ',', '.') . "\n\n";
}

echo "💰 Subtotal: R$ " . number_format($subtotal, 2, ',', '.') . "\n";
echo "🚚 Taxa de entrega: R$ 5,00\n";

$taxa_entrega = 5.00;
$total = $subtotal + $taxa_entrega;

echo "💵 TOTAL: R$ " . number_format($total, 2, ',', '.') . "\n\n";

// 4. CRIAR PEDIDO
echo "4️⃣ FINALIZANDO PEDIDO\n";
echo "───────────────────────────────────────\n";

try {
    DB::beginTransaction();
    
    $pedido = Pedido::create([
        'usuario_id' => $user->id,
        'tenant_code' => $produto->tenant_code,
        'status' => 'pendente',
        'subtotal' => $subtotal,
        'taxa_entrega' => $taxa_entrega,
        'total' => $total,
        'forma_pagamento' => 'dinheiro',
        'tipo_entrega' => 'delivery',
        'endereco_entrega' => json_encode([
            'rua' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01000-000'
        ]),
        'observacoes' => 'Pedido teste via backend'
    ]);
    
    echo "✅ Pedido #{$pedido->id} criado!\n\n";
    
    // 5. ADICIONAR ITENS AO PEDIDO
    echo "5️⃣ ADICIONANDO ITENS AO PEDIDO\n";
    echo "───────────────────────────────────────\n";
    
    foreach ($carrinho as $item) {
        $pedidoItem = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $item['produto_id'],
            'quantidade' => $item['quantidade'],
            'preco_unitario' => $item['preco_unitario'],
            'subtotal' => $item['quantidade'] * $item['preco_unitario'],
            'observacoes' => $item['observacoes']
        ]);
        
        echo "✅ Item #{$pedidoItem->id} adicionado ao pedido\n";
    }
    
    DB::commit();
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ PEDIDO FINALIZADO COM SUCESSO!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "📋 RESUMO DO PEDIDO:\n";
    echo "   Pedido ID: #{$pedido->id}\n";
    echo "   Cliente: {$user->name}\n";
    echo "   Restaurante: {$produto->tenant_code}\n";
    echo "   Status: {$pedido->status}\n";
    echo "   Itens: " . count($carrinho) . "\n";
    echo "   Subtotal: R$ " . number_format($pedido->subtotal, 2, ',', '.') . "\n";
    echo "   Taxa entrega: R$ " . number_format($pedido->taxa_entrega, 2, ',', '.') . "\n";
    echo "   TOTAL: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    echo "   Pagamento: {$pedido->forma_pagamento}\n";
    echo "   Tipo: {$pedido->tipo_entrega}\n";
    
    echo "\n🔍 Verificando no banco de dados:\n";
    
    $pedidoVerificado = Pedido::with('itens.produto')->find($pedido->id);
    
    if ($pedidoVerificado) {
        echo "   ✅ Pedido encontrado no banco\n";
        echo "   ✅ {$pedidoVerificado->itens->count()} itens vinculados\n";
        
        foreach ($pedidoVerificado->itens as $item) {
            echo "      • {$item->produto->nome} x{$item->quantidade} = R$ " 
                . number_format($item->subtotal, 2, ',', '.') . "\n";
        }
    }
    
    echo "\n✅ TESTE CONCLUÍDO COM SUCESSO! 🎉\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERRO ao finalizar pedido:\n";
    echo "   {$e->getMessage()}\n";
    echo "   Arquivo: {$e->getFile()}:{$e->getLine()}\n";
}
