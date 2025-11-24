<?php

/**
 * Script de Teste para Notificações Push
 * 
 * Execute: php testar_notificacoes_teste.php
 * 
 * Substitua TOKEN_AQUI com seu token FCM real
 */

$server = 'http://192.168.15.9';

echo "\n=== TESTE DE NOTIFICAÇÕES PUSH ===\n\n";

// Função auxiliar para fazer requisições
function testarApi($endpoint, $dados, $titulo) {
    global $server;
    
    echo "📤 $titulo\n";
    echo "─────────────────────────────\n";
    
    $url = $server . '/api/notificacao/' . $endpoint;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: $httpCode\n";
    echo "Resposta: " . $response . "\n\n";
}

// TESTES

// 1. Salvar Token
testarApi('salvar-token', [
    'token' => 'TOKEN_AQUI',
    'usuario_id' => 1,
    'cliente_id' => 1
], '1️⃣ Salvando Token FCM');

// 2. Enviar Notificação Simples
testarApi('enviar', [
    'token' => 'TOKEN_AQUI',
    'titulo' => 'Teste de Notificação',
    'mensagem' => 'Esta é uma notificação de teste!',
    'pedido_id' => '123',
    'action' => 'teste'
], '2️⃣ Enviando Notificação Simples');

// 3. Pedido Pronto
testarApi('pedido-pronto', [
    'token' => 'TOKEN_AQUI',
    'pedido_id' => '123',
    'numero_mesa' => 5
], '3️⃣ Notificando Pedido Pronto');

// 4. Delivery Aceito
testarApi('delivery-aceito', [
    'token' => 'TOKEN_AQUI',
    'pedido_id' => '123',
    'motorista' => 'João Silva'
], '4️⃣ Notificando Delivery Aceito');

// 5. Delivery Entregue
testarApi('delivery-entregue', [
    'token' => 'TOKEN_AQUI',
    'pedido_id' => '123'
], '5️⃣ Notificando Delivery Entregue');

// 6. Múltiplos Tokens
testarApi('enviar-multipla', [
    'tokens' => ['TOKEN_1', 'TOKEN_2', 'TOKEN_3'],
    'titulo' => 'Promoção Especial!',
    'mensagem' => 'Novo cupom disponível',
    'action' => 'promocao'
], '6️⃣ Enviando para Múltiplos Dispositivos');

echo "✅ Testes Concluídos!\n";
echo "\n⚠️ Lembre-se:\n";
echo "   - Substitua 'TOKEN_AQUI' com seu token FCM real\n";
echo "   - Configure sua Server Key do Firebase no NotificacaoController.php\n";
echo "   - Atualize o IP (192.168.15.9) conforme necessário\n\n";
?>
