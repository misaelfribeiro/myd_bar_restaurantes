<?php

echo "=== TESTE DIRETO DE MÚLTIPLOS PAGAMENTOS ===\n\n";

// Dados para teste
$pedido_id = 6; // Use um ID de pedido válido
$url = "http://localhost:8000/caixa/processar-pagamento/{$pedido_id}";

// Dados de múltiplos pagamentos
$multiplosPagamentos = [
    ['forma_pagamento' => 'dinheiro', 'valor' => 12.90],
    ['forma_pagamento' => 'cartao_credito', 'valor' => 8.60]
];

echo "URL: {$url}\n";
echo "Dados de teste:\n";
foreach ($multiplosPagamentos as $i => $pag) {
    echo "  " . ($i + 1) . ". {$pag['forma_pagamento']}: R$ " . number_format($pag['valor'], 2, ',', '.') . "\n";
}

$total = array_sum(array_column($multiplosPagamentos, 'valor'));
echo "Total: R$ " . number_format($total, 2, ',', '.') . "\n";

echo "\nPara testar:\n";
echo "1. Abra http://localhost:8000/caixa\n";
echo "2. Encontre um pedido pendente\n";
echo "3. Clique em 'Receber Pagamento'\n";
echo "4. Abra as ferramentas de desenvolvedor (F12)\n";
echo "5. Vá na aba Console\n";
echo "6. Clique em 'Múltiplas Formas'\n";
echo "7. Adicione formas de pagamento\n";
echo "8. Observe os logs no console\n";

echo "\nSe houver problemas, verifique:\n";
echo "- Se jQuery está carregando\n";
echo "- Se há erros JavaScript no console\n";
echo "- Se o modal está abrindo corretamente\n";
echo "- Se o botão 'Adicionar Forma' funciona\n";
echo "- Se a requisição AJAX está sendo enviada\n";

echo "\n✅ Instruções preparadas!\n";
?>
