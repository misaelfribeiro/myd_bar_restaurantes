<?php
// Teste simples da API de informações de pagamento da mesa
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Mesa;

header('Content-Type: application/json');

try {
    $mesaId = $_GET['mesa_id'] ?? 2;
    $mesa = Mesa::with(['pedidos.usuario', 'pedidos.itens.produto'])->find($mesaId);
    
    if (!$mesa) {
        throw new Exception("Mesa não encontrada");
    }
    
    $pedidos = $mesa->pedidos()
        ->with(['itens.produto', 'usuario'])
        ->where('status', 'finalizado')
        ->get()
        ->filter(function($pedido) {
            return !$pedido->isPago();
        });
    
    $totalMesa = $pedidos->sum('total');
    
    $response = [
        'success' => true,
        'data' => [
            'mesa' => [
                'id' => $mesa->id,
                'numero' => $mesa->numero,
                'identificador' => $mesa->identificador,
                'capacidade' => $mesa->capacidade,
                'lugares' => $mesa->lugares,
                'status' => $mesa->status,
                'total_geral' => $totalMesa
            ],
            'pedidos' => $pedidos->map(function($pedido) {
                return [
                    'id' => $pedido->id,
                    'total' => $pedido->total,
                    'created_at' => $pedido->created_at,
                    'status' => $pedido->status,
                    'usuario' => [
                        'id' => $pedido->usuario->id,
                        'nome' => $pedido->usuario->nome
                    ]
                ];
            }),
            'total_mesa' => $totalMesa
        ]
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>