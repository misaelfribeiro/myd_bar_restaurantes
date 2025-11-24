<?php
/**
 * Script para criar múltiplos caixas no mesmo dia para testar a separação
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Caixa;
use App\Models\Usuario;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\Mesa;
use Carbon\Carbon;

echo "=== Criando múltiplos caixas no mesmo dia ===\n\n";

try {
    // Buscar ou criar usuário
    $usuario = Usuario::first();
    if (!$usuario) {
        $usuario = Usuario::create([
            'nome' => 'Operador Teste',
            'email' => 'operador@teste.com',
            'password' => bcrypt('123456'),
            'tipo' => 'caixa'
        ]);
        echo "✓ Usuário criado\n";
    }

    // Buscar ou criar mesa
    $mesa = Mesa::first();
    if (!$mesa) {
        $mesa = Mesa::create([
            'numero' => 1,
            'capacidade' => 4,
            'status' => 'livre'
        ]);
        echo "✓ Mesa criada\n";
    }

    $hoje = Carbon::today();

    // Limpar caixas existentes do dia
    Caixa::whereDate('data_abertura', $hoje)->delete();
    echo "✓ Caixas anteriores removidos\n";

    // Caixa da Manhã (8:00 - 14:00)
    $caixa1 = Caixa::create([
        'usuario_id' => $usuario->id,
        'data_abertura' => $hoje->copy()->setTime(8, 0),
        'data_fechamento' => $hoje->copy()->setTime(14, 0),
        'saldo_inicial' => 100.00,
        'saldo_final' => 350.00,
        'status' => 'fechado',
        'total_vendas' => 250.00,
        'total_dinheiro' => 150.00,
        'total_cartao' => 100.00,
        'total_pix' => 0.00,
        'total_vale' => 0.00,
        'observacoes_abertura' => 'Turno da manhã'
    ]);

    // Criar pagamentos para o caixa da manhã
    for ($i = 1; $i <= 3; $i++) {
        $pedido = Pedido::create([
            'mesa_id' => $mesa->id,
            'usuario_id' => $usuario->id,
            'observacoes' => "Pedido manhã $i",
            'status' => 'finalizado',
            'total' => 50.00 + ($i * 20)
        ]);

        Pagamento::create([
            'pedido_id' => $pedido->id,
            'usuario_id' => $usuario->id,
            'forma_pagamento' => $i <= 2 ? 'dinheiro' : 'cartao',
            'valor' => 50.00 + ($i * 20),
            'valor_recebido' => 50.00 + ($i * 20),
            'troco' => 0.00,
            'status' => 'confirmado',
            'data_pagamento' => $hoje->copy()->setTime(8 + $i, 30)
        ]);
    }

    echo "✓ Caixa da manhã criado (8:00-14:00) - R$ 250,00\n";

    // Caixa da Tarde (14:30 - 22:00)
    $caixa2 = Caixa::create([
        'usuario_id' => $usuario->id,
        'data_abertura' => $hoje->copy()->setTime(14, 30),
        'data_fechamento' => $hoje->copy()->setTime(22, 0),
        'saldo_inicial' => 80.00,
        'saldo_final' => 580.00,
        'status' => 'fechado',
        'total_vendas' => 500.00,
        'total_dinheiro' => 200.00,
        'total_cartao' => 200.00,
        'total_pix' => 100.00,
        'total_vale' => 0.00,
        'observacoes_abertura' => 'Turno da tarde'
    ]);

    // Criar pagamentos para o caixa da tarde
    for ($i = 1; $i <= 5; $i++) {
        $pedido = Pedido::create([
            'mesa_id' => $mesa->id,
            'usuario_id' => $usuario->id,
            'observacoes' => "Pedido tarde $i",
            'status' => 'finalizado',
            'total' => 80.00 + ($i * 20)
        ]);

        $forma = match($i) {
            1, 2 => 'dinheiro',
            3, 4 => 'cartao',
            5 => 'pix'
        };

        Pagamento::create([
            'pedido_id' => $pedido->id,
            'usuario_id' => $usuario->id,
            'forma_pagamento' => $forma,
            'valor' => 80.00 + ($i * 20),
            'valor_recebido' => 80.00 + ($i * 20),
            'troco' => 0.00,
            'status' => 'confirmado',
            'data_pagamento' => $hoje->copy()->setTime(15 + $i, 0)
        ]);
    }

    echo "✓ Caixa da tarde criado (14:30-22:00) - R$ 500,00\n";

    // Caixa da Madrugada (22:30 - 02:00 do dia seguinte) - ABERTO
    $caixa3 = Caixa::create([
        'usuario_id' => $usuario->id,
        'data_abertura' => $hoje->copy()->setTime(22, 30),
        'data_fechamento' => null,
        'saldo_inicial' => 150.00,
        'saldo_final' => 0.00,
        'status' => 'aberto',
        'total_vendas' => 180.00,
        'total_dinheiro' => 80.00,
        'total_cartao' => 100.00,
        'total_pix' => 0.00,
        'total_vale' => 0.00,
        'observacoes_abertura' => 'Turno da madrugada - ABERTO'
    ]);

    // Criar alguns pagamentos para o caixa aberto
    for ($i = 1; $i <= 2; $i++) {
        $pedido = Pedido::create([
            'mesa_id' => $mesa->id,
            'usuario_id' => $usuario->id,
            'observacoes' => "Pedido madrugada $i",
            'status' => 'finalizado',
            'total' => 90.00
        ]);

        Pagamento::create([
            'pedido_id' => $pedido->id,
            'usuario_id' => $usuario->id,
            'forma_pagamento' => $i == 1 ? 'dinheiro' : 'cartao',
            'valor' => 90.00,
            'valor_recebido' => 90.00,
            'troco' => 0.00,
            'status' => 'confirmado',
            'data_pagamento' => $hoje->copy()->setTime(23, 0 + ($i * 30))
        ]);
    }

    echo "✓ Caixa da madrugada criado (22:30-ABERTO) - R$ 180,00\n";

    echo "\n=== RESUMO ===\n";
    echo "Total de caixas criados: 3\n";
    echo "Caixa #$caixa1->id (Manhã): R$ 250,00 - FECHADO\n";
    echo "Caixa #$caixa2->id (Tarde): R$ 500,00 - FECHADO\n";
    echo "Caixa #$caixa3->id (Madrugada): R$ 180,00 - ABERTO\n";
    echo "\n✅ Dados criados com sucesso!\n";
    echo "\n📋 Agora você pode testar:\n";
    echo "- Histórico: http://localhost:8000/caixa/historico\n";
    echo "- Relatório Manhã: http://localhost:8000/caixa/relatorio/$caixa1->id\n";
    echo "- Relatório Tarde: http://localhost:8000/caixa/relatorio/$caixa2->id\n";
    echo "- Relatório Madrugada: http://localhost:8000/caixa/relatorio/$caixa3->id\n";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n";
