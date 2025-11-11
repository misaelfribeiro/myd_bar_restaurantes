<?php
// Teste final - Dashboard sem duplicação de Mesa

require 'vendor/autoload.php';

$app = new Illuminate\Foundation\Application(realpath(__DIR__));
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mesa;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

echo "🧪 TESTE FINAL - VERIFICAÇÃO DASHBOARD\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Limpar dados existentes
    DB::table('item_pedidos')->truncate();
    DB::table('pedidos')->truncate();
    DB::table('mesas')->truncate();
    
    echo "🗑️ Dados anteriores limpos\n\n";
    
    // Criar mesas com diferentes formatos de identificador
    $mesas = [
        ['numero' => 1, 'identificador' => 'Mesa 01'],
        ['numero' => 2, 'identificador' => 'Mesa VIP'],
        ['numero' => 3, 'identificador' => 'Terraço'],
        ['numero' => 4, 'identificador' => null], // Sem identificador
        ['numero' => 5, 'identificador' => 'Salão Principal 05']
    ];
    
    foreach ($mesas as $mesaData) {
        Mesa::create([
            'numero' => $mesaData['numero'],
            'identificador' => $mesaData['identificador'],
            'capacidade' => 4,
            'disponivel' => true
        ]);
        
        $nome = $mesaData['identificador'] ?? 'Mesa ' . $mesaData['numero'];
        echo "✅ Criada: {$nome}\n";
    }
    
    echo "\n";
    
    // Criar alguns pedidos para testar
    $pedido1 = Pedido::create([
        'usuario_id' => 1,
        'mesa_id' => 1, // Mesa 01
        'total' => 50.00,
        'status' => 'aberto',
        'observacoes' => 'Teste Mesa 01'
    ]);
    
    $pedido2 = Pedido::create([
        'usuario_id' => 1,
        'mesa_id' => 2, // Mesa VIP
        'total' => 75.00,
        'status' => 'aberto',
        'observacoes' => 'Teste Mesa VIP'
    ]);
    
    $pedido3 = Pedido::create([
        'usuario_id' => 1,
        'mesa_id' => 4, // Mesa sem identificador
        'total' => 25.00,
        'status' => 'finalizado',
        'observacoes' => 'Teste Mesa sem identificador'
    ]);
    
    echo "📋 Pedidos criados:\n";
    echo "  - Pedido #{$pedido1->id} - Mesa 01 (aberto)\n";
    echo "  - Pedido #{$pedido2->id} - Mesa VIP (aberto)\n";
    echo "  - Pedido #{$pedido3->id} - Mesa 4 (finalizado)\n";
    
    echo "\n🎯 TESTE DE EXIBIÇÃO:\n\n";
    
    // Testar lógica de exibição
    $mesasTeste = Mesa::all();
    foreach ($mesasTeste as $mesa) {
        $nomeExibicao = $mesa->identificador ?? 'Mesa ' . $mesa->numero;
        echo "ID {$mesa->id}: numero={$mesa->numero}, identificador='{$mesa->identificador}' → Exibe: '{$nomeExibicao}'\n";
    }
    
    echo "\n✅ DADOS DE TESTE CRIADOS!\n";
    echo "🌐 Acesse: http://localhost:8000/garcom/dashboard\n";
    echo "👀 Verifique se não há duplicação 'Mesa Mesa'\n\n";
    
    echo "🔍 VERIFICAÇÕES A FAZER:\n";
    echo "  1. Mesa 01 deve aparecer como 'Mesa 01' (não 'Mesa Mesa 01')\n";
    echo "  2. Mesa VIP deve aparecer como 'Mesa VIP' (não 'Mesa Mesa VIP')\n";
    echo "  3. Terraço deve aparecer como 'Terraço' (não 'Mesa Terraço')\n";
    echo "  4. Mesa 4 deve aparecer como 'Mesa 4' (usando fallback)\n";
    echo "  5. Mesa 5 deve aparecer como 'Salão Principal 05'\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
