<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Delivery;
use App\Models\Pedido;

echo "\n=== TESTE COM USUARIO_ID = 2 (SEU LOGIN) ===\n\n";

// Verificar usuário
$usuario = DB::table('usuarios')->where('id', 2)->first();
if ($usuario) {
    echo "✅ Usuário encontrado:\n";
    echo "   ID: {$usuario->id}\n";
    echo "   Nome: {$usuario->nome}\n";
    echo "   Email: {$usuario->email}\n\n";
}

// Verificar token
$token = DB::table('fcm_tokens')->where('user_id', 23)->first();
if ($token) {
    echo "✅ Token FCM encontrado para user_id 23\n";
    echo "   Token: " . substr($token->token, 0, 40) . "...\n\n";
}

echo "Criando pedido para o usuário logado (usuario_id = 2)...\n\n";

// Criar pedido - O usuario_id será usado para buscar o token
$pedido = Pedido::create([
    'usuario_id' => 2, // Seu login
    'cliente_id' => 23, // Cliente que receberá notificação
    'tipo' => 'delivery',
    'status' => 'pendente',
    'total' => 99.00,
    'observacoes' => 'Teste final de notificações'
]);
echo "1. Pedido criado (ID: {$pedido->id})\n";
echo "   - usuario_id: {$pedido->usuario_id}\n";
echo "   - cliente_id: {$pedido->cliente_id}\n\n";

// Criar delivery
$delivery = Delivery::create([
    'pedido_id' => $pedido->id,
    'cliente_nome' => 'Cliente Teste Final',
    'cliente_telefone' => '11988887777',
    'endereco_rua' => 'Rua Final',
    'endereco_numero' => '999',
    'endereco_bairro' => 'Centro',
    'endereco_cidade' => 'São Paulo',
    'endereco_cep' => '01000-000',
    'status' => 'pendente',
    'taxa_entrega' => 10.00
]);
echo "2. Delivery criado (ID: {$delivery->id})\n\n";

echo "=== ENVIANDO NOTIFICAÇÕES ===\n\n";

sleep(1);
echo "3. Marcando como PRONTO...\n";
$delivery->marcarPronto();
echo "   ✅ Status: pronto\n";
echo "   🔔 Notificação 'Pedido Pronto' enviada!\n\n";

sleep(3);
echo "4. Marcando como SAIU PARA ENTREGA...\n";
$delivery->sairParaEntrega('Carlos Entregador');
echo "   ✅ Status: saiu_entrega\n";
echo "   🔔 Notificação 'Seu Pedido Está a Caminho' enviada!\n\n";

sleep(3);
echo "5. Marcando como ENTREGUE...\n";
$delivery->marcarEntregue(5, 'Entrega excelente!');
echo "   ✅ Status: entregue\n";
echo "   🔔 Notificação 'Pedido Entregue' enviada!\n\n";

echo "=== TESTE CONCLUÍDO ===\n";
echo "IMPORTANTE: O sistema busca token pelo cliente_id (23) primeiro.\n";
echo "Você deve receber as 3 notificações no celular!\n\n";
