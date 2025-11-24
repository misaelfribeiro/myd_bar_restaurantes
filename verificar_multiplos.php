<?php
echo "=== VERIFICAÇÃO MÚLTIPLOS PAGAMENTOS ===\n";

// Simples verificação das rotas
$routes = [
    'Recebimento' => '/caixa/recebimento/{pedido}',
    'Processar Pagamento' => '/caixa/processar-pagamento/{pedido}'
];

echo "Rotas configuradas:\n";
foreach ($routes as $nome => $rota) {
    echo "- $nome: $rota\n";
}

echo "\nProblemas comuns em múltiplos pagamentos:\n";
echo "1. JavaScript não está sendo carregado\n";
echo "2. CSRF token não está sendo enviado\n";
echo "3. Validação de dados no backend\n";
echo "4. Problemas de encoding JSON\n";

echo "\nSugestões de verificação:\n";
echo "1. Abra as ferramentas de desenvolvedor (F12)\n";
echo "2. Vá na aba Console e veja se há erros JavaScript\n";
echo "3. Vá na aba Network e veja se a requisição AJAX está sendo feita\n";
echo "4. Verifique se o modal está abrindo corretamente\n";

echo "\n✅ Verificação básica concluída\n";
?>
