<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Mesa;
use App\Models\Pedido;

echo "🔍 Verificando informações da Mesa 2...\n\n";

// Buscar mesa
$mesa = Mesa::find(2);

if (!$mesa) {
    echo "❌ Mesa 2 não encontrada!\n";
    exit;
}

echo "📋 Informações da Mesa:\n";
echo "- ID: {$mesa->id}\n";
echo "- Identificador: {$mesa->identificador}\n";
echo "- Número: " . ($mesa->numero ?? 'null') . "\n";
echo "- Capacidade: " . ($mesa->capacidade ?? 'null') . "\n";
echo "- Lugares: " . ($mesa->lugares ?? 'null') . "\n";
echo "- Status: " . ($mesa->status ?? 'null') . "\n";

// Buscar pedidos da mesa
$pedidos = $mesa->pedidos()->with('usuario')->get();

echo "\n📄 Pedidos da Mesa:\n";
foreach ($pedidos as $pedido) {
    echo "- Pedido #{$pedido->id}\n";
    echo "  Status: {$pedido->status}\n";
    echo "  Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    echo "  Usuário: " . ($pedido->usuario->nome ?? 'N/A') . "\n";
    echo "  Criado: {$pedido->created_at->format('d/m/Y H:i')}\n";
    echo "  Tempo decorrido: {$pedido->created_at->diffForHumans()}\n\n";
}

echo "✅ Verificação concluída!\n";
?>