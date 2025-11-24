<?php

// Script simples para criar dados básicos para teste do caixa

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Caixa;
use App\Models\Usuario;
use App\Models\Pagamento;
use Carbon\Carbon;

echo "🔄 Criando dados básicos para teste...\n\n";

try {
    // 1. Criar usuário se não existir
    $usuario = Usuario::firstOrCreate(
        ['email' => 'caixa@teste.com'],
        [
            'nome' => 'Operador Caixa',
            'password' => bcrypt('123456'),
            'tipo' => 'caixa'
        ]
    );
    echo "✅ Usuário: {$usuario->nome} (ID: {$usuario->id})\n";

    // 2. Criar alguns caixas com datas válidas
    $hoje = Carbon::now();
    $ontem = Carbon::yesterday();
    $anteontem = Carbon::yesterday()->subDay();

    $caixas = [
        [
            'usuario_id' => $usuario->id,
            'data_abertura' => $anteontem->setTime(8, 0, 0),
            'data_fechamento' => $anteontem->setTime(18, 30, 0),
            'saldo_inicial' => 100.00,
            'status' => 'fechado'
        ],
        [
            'usuario_id' => $usuario->id,
            'data_abertura' => $ontem->setTime(8, 0, 0),
            'data_fechamento' => $ontem->setTime(19, 0, 0),
            'saldo_inicial' => 100.00,
            'status' => 'fechado'
        ],
        [
            'usuario_id' => $usuario->id,
            'data_abertura' => $hoje->setTime(8, 0, 0),
            'data_fechamento' => null,
            'saldo_inicial' => 100.00,
            'status' => 'aberto'
        ]
    ];

    foreach ($caixas as $dadosCaixa) {
        $caixa = Caixa::create($dadosCaixa);
        echo "✅ Caixa #{$caixa->id} criado - Status: {$caixa->status}\n";
        
        // Criar alguns pagamentos para cada caixa
        if ($caixa->data_abertura) {
            $numPagamentos = rand(3, 8);
            for ($i = 1; $i <= $numPagamentos; $i++) {
                $valor = rand(15, 80) + (rand(0, 99) / 100); // Valores entre 15.00 e 80.99
                
                Pagamento::create([
                    'pedido_id' => rand(1, 100), // ID fictício
                    'usuario_id' => $usuario->id,
                    'forma_pagamento' => ['dinheiro', 'cartao_credito', 'pix'][rand(0, 2)],
                    'valor' => $valor,
                    'valor_recebido' => $valor,
                    'troco' => 0,
                    'status' => 'confirmado',
                    'data_pagamento' => $caixa->data_abertura->copy()->addHours(rand(1, 9))
                ]);
            }
            echo "  💰 {$numPagamentos} pagamentos criados\n";
        }
    }

    echo "\n✅ Dados básicos criados com sucesso!\n";
    echo "🔗 Teste agora: http://localhost:8000/caixa/historico\n\n";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📍 Trace: " . $e->getTraceAsString() . "\n";
}
