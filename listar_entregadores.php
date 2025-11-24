<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$entregadores = App\Models\Entregador::all();

echo "=== ENTREGADORES CADASTRADOS ===\n\n";

if ($entregadores->isEmpty()) {
    echo "Nenhum entregador cadastrado!\n";
} else {
    foreach ($entregadores as $ent) {
        echo "ID: {$ent->id}\n";
        echo "Nome: {$ent->nome}\n";
        echo "Tipo Veículo: " . ($ent->tipo_veiculo ?? 'Não informado') . "\n";
        echo "Status: {$ent->status}\n";
        echo "---\n";
    }
}
