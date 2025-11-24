<?php

/**
 * Script para obter Server Key do Firebase
 * e atualizar o NotificacaoController.php
 */

$firebaseConfig = json_decode(file_get_contents(__DIR__ . '/firebase-config.json'), true);

echo "=== CONFIGURAÇÃO FIREBASE ===\n\n";
echo "Project ID: " . $firebaseConfig['project_id'] . "\n";
echo "Client Email: " . $firebaseConfig['client_email'] . "\n";
echo "Private Key ID: " . $firebaseConfig['private_key_id'] . "\n\n";

echo "✅ Arquivo Firebase encontrado!\n\n";

echo "⚠️ Próximas Etapas:\n";
echo "1. Acesse: https://console.firebase.google.com\n";
echo "2. Projeto: " . $firebaseConfig['project_id'] . "\n";
echo "3. Vá para: Cloud Messaging → Configurações\n";
echo "4. Copie a 'Server Key' (REST API Key)\n";
echo "5. Atualize em: app/Http/Controllers/Api/NotificacaoController.php\n\n";

echo "📋 Dados para Copiar:\n";
echo "Project ID: " . $firebaseConfig['project_id'] . "\n";
echo "Client Email: " . $firebaseConfig['client_email'] . "\n";
?>
