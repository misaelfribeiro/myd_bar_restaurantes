<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Delivery;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

echo "\n=== TESTE COMPLETO DE NOTIFICAÇÕES VIA DELIVERY ===\n\n";

// 1. Buscar ou criar cliente
$user = User::firstOrCreate(
    ['email' => 'cliente.delivery@teste.com'],
    [
        'name' => 'Cliente Delivery',
        'password' => bcrypt('123456'),
        'role' => 'cliente'
    ]
);
echo "1. Usuário: {$user->name} (ID: {$user->id})\n";

// 2. Salvar token FCM
DB::table('fcm_tokens')->where('user_id', $user->id)->delete();
DB::table('fcm_tokens')->insert([
    'user_id' => $user->id,
    'token' => 'czN2MULAQCmwKqSoCvuE_B:APA91bETP48PSgeHkM8lt4fvWZhIXxewGYs4-2rDDQ_YtxYtnwnrfMw-nxEy8ktK72NXYWk1xVqHJU8bTHss5qUYdvRI7OgPnu1-Qkpbmztg5L0XFq4rjw8',
    'device_type' => 'android',
    'ativo' => true,
    'created_at' => now(),
    'updated_at' => now()
]);
echo "2. Token FCM salvo\n";

// 3. Criar cliente se não existir
$cliente = Cliente::firstOrCreate(
    ['telefone' => '11999999999'],
    [
        'nome' => 'Cliente Delivery',
        'user_id' => $user->id,
        'endereco' => 'Rua Teste, 123'
    ]
);
echo "3. Cliente: {$cliente->nome} (ID: {$cliente->id})\n";

// 4. Criar pedido
$pedido = Pedido::create([
    'usuario_id' => 1,
    'cliente_id' => $cliente->id,
    'tipo' => 'delivery',
    'status' => 'pendente',
    'total' => 50.00,
    'observacoes' => 'Teste de notificações'
]);
echo "4. Pedido criado (ID: {$pedido->id})\n";

// 5. Criar delivery
$delivery = Delivery::create([
    'pedido_id' => $pedido->id,
    'cliente_nome' => $cliente->nome,
    'cliente_telefone' => $cliente->telefone,
    'endereco_rua' => 'Rua Teste',
    'endereco_numero' => '123',
    'endereco_bairro' => 'Centro',
    'endereco_cidade' => 'São Paulo',
    'endereco_cep' => '01000-000',
    'status' => 'pendente',
    'taxa_entrega' => 5.00
]);
echo "5. Delivery criado (ID: {$delivery->id})\n\n";

// 6. Testar mudança para PRONTO
echo "6. Marcando delivery como PRONTO...\n";
$delivery->marcarPronto();
echo "   Status: {$delivery->status}\n";
echo "   🔔 Deve enviar notificação 'Pedido Pronto'\n\n";

sleep(2);

// 7. Testar mudança para SAIU_ENTREGA
echo "7. Marcando delivery como SAIU PARA ENTREGA...\n";
$delivery->sairParaEntrega('João Entregador');
echo "   Status: {$delivery->status}\n";
echo "   🔔 Deve enviar notificação 'Seu Pedido Está a Caminho'\n\n";

sleep(2);

// 8. Testar mudança para ENTREGUE
echo "8. Marcando delivery como ENTREGUE...\n";
$delivery->marcarEntregue(5, 'Entrega rápida!');
echo "   Status: {$delivery->status}\n";
echo "   🔔 Deve enviar notificação 'Pedido Entregue'\n\n";

echo "=== TESTE CONCLUÍDO ===\n";
echo "Verifique se recebeu 3 notificações no celular!\n\n";
