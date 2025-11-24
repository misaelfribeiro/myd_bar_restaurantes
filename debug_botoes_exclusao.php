<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pedido;
use App\Models\Usuario;

echo "=== DEBUG: VERIFICAÇÃO DE PERMISSÕES ===\n";

// Verificar o pedido 52
$pedido = Pedido::with(['itens.produto', 'mesa'])->find(52);
if (!$pedido) {
    echo "❌ Pedido 52 não encontrado\n";
    exit;
}

echo "✅ Pedido #52 encontrado:\n";
echo "Status: {$pedido->status}\n";
echo "Total de itens: {$pedido->itens->count()}\n";

// Verificar se o status permite exclusão
if (in_array($pedido->status, ['entregue', 'cancelado'])) {
    echo "❌ PROBLEMA: Status '{$pedido->status}' não permite exclusão\n";
} else {
    echo "✅ Status permite exclusão\n";
}

// Verificar usuário admin
$admin = Usuario::where('role', 'admin')->first();
if (!$admin) {
    echo "❌ Usuário admin não encontrado\n";
    exit;
}

echo "✅ Usuário admin encontrado: {$admin->nome}\n";
echo "Role: {$admin->role}\n";

// Simular as condições do Blade
$currentUser = $admin;
$isUserValid = isset($currentUser);
$hasCorrectRole = in_array($currentUser->role, ['admin', 'gerente']);
$hasValidStatus = !in_array($pedido->status, ['entregue', 'cancelado']);

echo "\n=== CONDIÇÕES DO BLADE ===\n";
echo "isset(\$currentUser): " . ($isUserValid ? 'true' : 'false') . "\n";
echo "in_array(\$currentUser->role, ['admin', 'gerente']): " . ($hasCorrectRole ? 'true' : 'false') . "\n";
echo "!in_array(\$pedido->status, ['entregue', 'cancelado']): " . ($hasValidStatus ? 'true' : 'false') . "\n";

$shouldShowButton = $isUserValid && $hasCorrectRole && $hasValidStatus;
echo "RESULTADO FINAL: " . ($shouldShowButton ? 'BOTÃO DEVE APARECER ✅' : 'BOTÃO NÃO DEVE APARECER ❌') . "\n";

// Gerar HTML de teste
if ($shouldShowButton) {
    echo "\n=== HTML QUE DEVE SER GERADO ===\n";
    foreach ($pedido->itens as $item) {
        echo "<button type=\"button\" \n";
        echo "        class=\"btn btn-danger btn-sm\" \n";
        echo "        onclick=\"removeItemFromDetails({$item->id}, '{$item->produto->nome}')\"\n";
        echo "        title=\"Excluir item (Admin/Gerente)\">\n";
        echo "    <i class=\"fas fa-trash\"></i>\n";
        echo "</button>\n\n";
    }
}

echo "\n=== POSSÍVEIS PROBLEMAS ===\n";
echo "1. Usuário não está logado?\n";
echo "2. Usuário não tem role admin/gerente?\n";
echo "3. Cache de view não foi limpo?\n";
echo "4. Sessão de autenticação não está funcionando?\n";

echo "\n=== SOLUÇÕES ===\n";
echo "1. Verificar se está logado como admin\n";
echo "2. Limpar cache: php artisan view:clear\n";
echo "3. Verificar autenticação na sessão\n";