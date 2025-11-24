<?php
// TESTE ESPECÍFICO DA MIGRAÇÃO DO MODO CAIXA
echo "<h1>🧪 TESTE DA MIGRAÇÃO - MODO CAIXA</h1>";
echo "<p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>";

// Criar um pedido de teste se necessário
$baseUrl = 'http://localhost/myd_bar_restaurantes/public';

// 1. Verificar se existe um pedido finalizado para teste
$apiUrl = "$baseUrl/api/teste-models";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

echo "<div style='background: #e7f3ff; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
echo "<h2>🔍 1. Verificação de Dados</h2>";
echo "<p><strong>Status API:</strong> " . $httpCode . "</p>";

if ($data && $data['success']) {
    echo "<p>✅ Conexão com banco OK</p>";
    echo "<p>Pedidos disponíveis: " . count($data['pedidos']) . "</p>";
    
    if (count($data['pedidos']) > 0) {
        $pedido = $data['pedidos'][0];
        echo "<p>📋 <strong>Pedido de teste: #{$pedido['id']} - R$ {$pedido['total']}</strong></p>";
    } else {
        echo "<p>⚠️ Nenhum pedido encontrado. <a href='criar_dados_teste_multiplos.php'>Criar dados de teste</a></p>";
        $pedido = ['id' => 19, 'total' => '75.00']; // Fallback
    }
} else {
    echo "<p>❌ Problema na API</p>";
    $pedido = ['id' => 19, 'total' => '75.00']; // Fallback
}
echo "</div>";

// 2. Verificar se há caixa aberto
echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
echo "<h2>💰 2. Status do Caixa</h2>";

$statusCaixa = "$baseUrl/api/pagamentos-status";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $statusCaixa);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$statusResponse = curl_exec($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($statusCode == 200) {
    echo "<p>✅ API de pagamentos funcionando</p>";
} else {
    echo "<p>❌ Problema na API de pagamentos</p>";
}
echo "</div>";

// 3. Link para testar o modo caixa
if ($pedido) {
    echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 8px; text-align: center;'>";
    echo "<h2>🚀 TESTE PRÁTICO</h2>";
    echo "<p>Agora você pode testar a migração do modo caixa!</p>";
    echo "<p><strong>📋 Pedido de teste:</strong> #{$pedido['id']} - R$ {$pedido['total']}</p>";
    echo "<p>";
    echo "<a href='$baseUrl/caixa/recebimento/{$pedido['id']}' class='btn btn-primary' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>💳 Testar Pagamento Único</a>";
    echo "</p>";
    echo "<p>";
    echo "<a href='$baseUrl/caixa/recebimento/{$pedido['id']}' class='btn btn-warning' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>💰 Testar Múltiplos Pagamentos</a>";
    echo "</p>";
    echo "</div>";

    echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
    echo "<h3>📝 Instruções para Teste</h3>";
    echo "<ul>";
    echo "<li><strong>Pagamento Único:</strong> Selecione uma forma de pagamento e processe normalmente</li>";
    echo "<li><strong>Múltiplos Pagamentos:</strong> Clique em 'Múltiplas Formas' e adicione diferentes métodos</li>";
    echo "<li><strong>Verificar Console:</strong> Abra o DevTools (F12) para ver os logs da API</li>";
    echo "<li><strong>Fallback:</strong> Se a API falhar, o sistema usa o método original automaticamente</li>";
    echo "</ul>";
    echo "</div>";

    echo "<div style='background: #e9ecef; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
    echo "<h3>🔍 O que foi migrado</h3>";
    echo "<ul>";
    echo "<li>✅ <strong>Pagamento único:</strong> Intercepta o formulário e usa API unificada</li>";
    echo "<li>✅ <strong>Múltiplos pagamentos:</strong> Converte dados e usa API unificada</li>";
    echo "<li>✅ <strong>Fallback automático:</strong> Se API falhar, usa método original</li>";
    echo "<li>✅ <strong>Logs detalhados:</strong> Console mostra todo o processo</li>";
    echo "<li>✅ <strong>Validação completa:</strong> Todas as validações mantidas</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<style>
body { 
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
    margin: 20px; 
    background: #f5f5f5; 
    line-height: 1.6;
}
h1 { 
    color: #2c3e50; 
    text-align: center; 
    margin-bottom: 30px;
}
h2, h3 { 
    color: #34495e; 
    margin-top: 0; 
}
p { margin-bottom: 10px; }
.btn {
    display: inline-block;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.3s;
}
.btn:hover {
    opacity: 0.8;
    text-decoration: none;
}
ul {
    padding-left: 20px;
}
li {
    margin-bottom: 8px;
}
</style>
