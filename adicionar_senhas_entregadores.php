<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ADICIONANDO SENHAS PARA ENTREGADORES ===\n\n";

$entregadores = App\Models\Entregador::whereNull('senha')->orWhere('senha', '')->get();

if ($entregadores->count() === 0) {
    echo "Todos os entregadores já têm senha!\n";
} else {
    foreach($entregadores as $e) {
        $e->senha = Hash::make('123456');
        $e->save();
        echo "✅ Senha '123456' criada para: {$e->nome} ({$e->email})\n";
    }
    
    echo "\n✅ Total de senhas criadas: " . $entregadores->count() . "\n";
}

echo "\n=== CREDENCIAIS DE LOGIN ===\n\n";

$todosEntregadores = App\Models\Entregador::where('status', 'ativo')->get();
foreach($todosEntregadores as $e) {
    echo "📧 Email: {$e->email}\n";
    echo "🔑 Senha: 123456\n";
    echo "👤 Nome: {$e->nome}\n";
    echo "---\n";
}
