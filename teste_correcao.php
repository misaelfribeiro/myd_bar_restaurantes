<?php
// Teste rápido da correção
echo "Testando correção do cálculo do caixa...\n";

// Simular uma consulta direta
try {
    $pdo = new PDO('mysql:host=localhost;dbname=myd_bar_restaurantes', 'root', '');
    
    // Buscar caixa mais recente
    $stmt = $pdo->query("SELECT id, total_vendas, total_cartao_credito, saldo_inicial FROM caixa ORDER BY data_abertura DESC LIMIT 1");
    $caixa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($caixa) {
        echo "Caixa #{$caixa['id']}:\n";
        echo "- Total Vendas (banco): R$ " . number_format($caixa['total_vendas'] ?? 0, 2, ',', '.') . "\n";
        echo "- Total Cartão Crédito (banco): R$ " . number_format($caixa['total_cartao_credito'] ?? 0, 2, ',', '.') . "\n";
        echo "- Saldo Inicial: R$ " . number_format($caixa['saldo_inicial'] ?? 0, 2, ',', '.') . "\n";
        
        // Buscar pagamentos deste caixa
        $stmt2 = $pdo->prepare("SELECT forma_pagamento, SUM(valor) as total FROM pagamentos WHERE caixa_id = ? AND status = 'confirmado' GROUP BY forma_pagamento");
        $stmt2->execute([$caixa['id']]);
        $pagamentos = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nPagamentos calculados:\n";
        foreach($pagamentos as $pag) {
            echo "- {$pag['forma_pagamento']}: R$ " . number_format($pag['total'], 2, ',', '.') . "\n";
        }
        
        // Total calculado
        $stmt3 = $pdo->prepare("SELECT SUM(valor) as total FROM pagamentos WHERE caixa_id = ? AND status = 'confirmado'");
        $stmt3->execute([$caixa['id']]);
        $totalCalculado = $stmt3->fetchColumn();
        
        echo "\nTotal calculado: R$ " . number_format($totalCalculado ?? 0, 2, ',', '.') . "\n";
        
        $diferenca = abs(($caixa['total_vendas'] ?? 0) - ($totalCalculado ?? 0));
        if ($diferenca > 0.01) {
            echo "⚠️ PROBLEMA: Diferença de R$ " . number_format($diferenca, 2, ',', '.') . "\n";
        } else {
            echo "✅ OK: Valores conferem!\n";
        }
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
