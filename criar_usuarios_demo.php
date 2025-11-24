<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar ambiente Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

echo "🔧 Criando usuários de demonstração...\n\n";

try {
    // Limpar usuários existentes (opcional)
    Usuario::whereIn('email', [
        'admin@exemplo.com',
        'gerente@exemplo.com', 
        'garcom@exemplo.com',
        'caixa@exemplo.com'
    ])->delete();

    // Criar usuários de demonstração
    $usuarios = [
        [
            'nome' => 'Administrador',
            'email' => 'admin@exemplo.com',
            'password' => Hash::make('123456'),
            'role' => 'admin'
        ],
        [
            'nome' => 'Gerente Demo',
            'email' => 'gerente@exemplo.com',
            'password' => Hash::make('123456'),
            'role' => 'gerente'
        ],
        [
            'nome' => 'Garçom Demo',
            'email' => 'garcom@exemplo.com',
            'password' => Hash::make('123456'),
            'role' => 'garcom'
        ],
        [
            'nome' => 'Operador Caixa',
            'email' => 'caixa@exemplo.com',
            'password' => Hash::make('123456'),
            'role' => 'caixa'
        ]
    ];

    foreach ($usuarios as $userData) {
        $usuario = Usuario::create($userData);
        echo "✅ Usuário criado: {$usuario->nome} ({$usuario->email}) - Role: {$usuario->role}\n";
    }

    echo "\n🎉 Usuários de demonstração criados com sucesso!\n";
    echo "📝 Todos os usuários têm a senha: 123456\n\n";

    echo "🔐 Para fazer login:\n";
    echo "- Admin: admin@exemplo.com / 123456\n";
    echo "- Gerente: gerente@exemplo.com / 123456\n";
    echo "- Garçom: garcom@exemplo.com / 123456\n";
    echo "- Caixa: caixa@exemplo.com / 123456\n\n";

} catch (Exception $e) {
    echo "❌ Erro ao criar usuários: " . $e->getMessage() . "\n";
}

echo "✨ Script finalizado!\n";
