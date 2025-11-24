<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Delivery;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

echo "\n=== TESTE DE NOTIFICAÇÃO COM USER_ID CORRETO ===\n\n";

// User ID que tem o token
$userId = 2;

echo "1. Usando user_id: $userId (que tem o token FCM)\n";

// Criar pedido com cliente_id = user_id correto
$pedido = Pedido::create([
    'usuario_id' => 1, // Funcionário que criou o pedido
    'cliente_id' => $userId, // Cliente que receberá a notificação
    'tipo' => 'delivery',
    'status' => 'pendente',
    'total' => 75.00,
    'observacoes' => 'Teste com user_id correto'
]);
echo "2. Pedido criado (ID: {$pedido->id}) com cliente_id = $userId\n";

// Criar delivery
$delivery = Delivery::create([
    'pedido_id' => $pedido->id,
    'cliente_nome' => 'Cliente Teste',
    'cliente_telefone' => '11999999999',
    'endereco_rua' => 'Rua Teste',
    'endereco_numero' => '456',
    'endereco_bairro' => 'Bairro Teste',
    'endereco_cidade' => 'São Paulo',
    'endereco_cep' => '01000-000',
    'status' => 'pendente',
    'taxa_entrega' => 8.00
]);
echo "3. Delivery criado (ID: {$delivery->id})\n\n";

// Testar notificações
echo "4. Marcando delivery como PRONTO...\n";
$delivery->marcarPronto();
echo "   ✓ Status: {$delivery->status}\n";
echo "   🔔 Notificação 'Pedido Pronto' enviada!\n\n";

sleep(3);

echo "5. Marcando delivery como SAIU PARA ENTREGA...\n";
$delivery->sairParaEntrega('José Entregador');
echo "   ✓ Status: {$delivery->status}\n";
echo "   🔔 Notificação 'Seu Pedido Está a Caminho' enviada!\n\n";

sleep(3);

echo "6. Marcando delivery como ENTREGUE...\n";
$delivery->marcarEntregue(5, 'Entrega perfeita!');
echo "   ✓ Status: {$delivery->status}\n";
echo "   🔔 Notificação 'Pedido Entregue' enviada!\n\n";

echo "=== TESTE CONCLUÍDO ===\n";
echo "Verifique se recebeu 3 notificações no celular!\n";
echo "Se não recebeu, o app está logado com user_id diferente de $userId\n\n";
