<?php

// Script simples para criar dados básicos apenas para histórico

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Caixa;
use App\Models\Usuario;
use Carbon\Carbon;

echo "🔄 Criando dados simples para histórico...\n\n";

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

    // 2. Criar alguns caixas apenas com dados básicos
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
    }

    echo "\n✅ Dados simples criados com sucesso!\n";
    echo "🔗 Teste agora: http://localhost:8000/caixa/historico\n\n";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
