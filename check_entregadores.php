<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ENTREGADORES CADASTRADOS ===\n\n";

$entregadores = App\Models\Entregador::all();

if ($entregadores->count() === 0) {
    echo "Nenhum entregador cadastrado!\n";
} else {
    foreach($entregadores as $e) {
        echo "ID: {$e->id}\n";
        echo "Nome: {$e->nome}\n";
        echo "Email: " . ($e->email ?? 'NÃO TEM EMAIL') . "\n";
        echo "Tem senha: " . ($e->senha ? 'SIM' : 'NÃO') . "\n";
        echo "Status: {$e->status}\n";
        echo "Tipo: {$e->tipo}\n";
        echo "Telefone: {$e->telefone}\n";
        echo "---\n";
    }
}

echo "\n=== SUGESTÃO DE LOGIN ===\n";
$primeiro = $entregadores->first();
if ($primeiro) {
    $email = $primeiro->email ?? ($primeiro->nome ? strtolower(str_replace(' ', '.', $primeiro->nome)) . '@email.com' : null);
    echo "Email: {$email}\n";
    echo "Senha padrão sugerida: 123456\n";
    
    if (!$primeiro->email) {
        echo "\n⚠️ PROBLEMA: Entregador não tem email cadastrado!\n";
        echo "Executar comando para adicionar email:\n";
        echo "php artisan tinker\n";
        echo "\$e = App\\Models\\Entregador::find({$primeiro->id});\n";
        echo "\$e->email = '{$email}';\n";
        echo "\$e->senha = Hash::make('123456');\n";
        echo "\$e->save();\n";
    }
}
