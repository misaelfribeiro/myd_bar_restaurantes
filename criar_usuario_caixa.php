<?php
require_once 'vendor/autoload.php';

// Carregar o ambiente Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Usuario;

echo "=== CRIANDO USUÁRIO PADRÃO PARA CAIXA ===\n";

// Verificar se já existe usuário
$usuarios = Usuario::count();
echo "Usuários existentes: $usuarios\n";

if ($usuarios == 0) {
    echo "Criando usuário padrão...\n";
    
    $usuario = Usuario::create([
        'nome' => 'Operador Caixa',
        'email' => 'caixa@restaurante.com',
        'password' => bcrypt('123456'),
        'tipo' => 'funcionario'
    ]);
    
    echo "Usuário criado com ID: {$usuario->id}\n";
} else {
    echo "Usuários já existem:\n";
    Usuario::all(['id', 'nome', 'email'])->each(function($u) {
        echo "  - ID {$u->id}: {$u->nome} ({$u->email})\n";
    });
}

echo "\n=== CONCLUÍDO ===\n";
