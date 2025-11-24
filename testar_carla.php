<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE DA IA CARLA - BACKEND ===\n\n";

// Simular diferentes comandos de voz
$comandos = [
    "mostre o cardápio",
    "adicione pizza ao carrinho",
    "qual é o preço da pizza",
    "faça meu pedido",
    "onde está meu pedido",
    "quero uma coca cola",
    "remove pizza do carrinho",
    "quanto está meu carrinho",
    "quero fazer um pedido",
    "oi carla"
];

echo "🤖 Testando processamento de comandos da Carla...\n\n";

foreach ($comandos as $index => $comando) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Teste " . ($index + 1) . "/" . count($comandos) . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "👤 Usuário: \"$comando\"\n\n";
    
    try {
        // Chamar API da Carla
        $response = \Illuminate\Support\Facades\Http::post('http://localhost/api/ai/process', [
            'message' => $comando,
            'session_token' => 'test_session_' . time()
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            
            echo "🤖 Carla: " . ($data['response'] ?? 'Sem resposta') . "\n";
            
            if (isset($data['action'])) {
                echo "⚙️ Ação: " . $data['action'] . "\n";
            }
            
            if (isset($data['data'])) {
                echo "📊 Dados: " . json_encode($data['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            }
            
            if (isset($data['intent'])) {
                echo "🎯 Intenção detectada: " . $data['intent'] . "\n";
            }
            
            if (isset($data['confidence'])) {
                echo "📈 Confiança: " . ($data['confidence'] * 100) . "%\n";
            }
        } else {
            echo "❌ Erro na API: " . $response->status() . "\n";
            echo "   Mensagem: " . $response->body() . "\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Erro ao processar: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    sleep(1); // Pequeno delay entre comandos
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ TESTE CONCLUÍDO\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Testar estatísticas
echo "📊 ESTATÍSTICAS DA CARLA\n\n";

try {
    $statsResponse = \Illuminate\Support\Facades\Http::get('http://localhost/api/ai/stats');
    
    if ($statsResponse->successful()) {
        $stats = $statsResponse->json();
        
        echo "Total de conversas: " . ($stats['total_conversations'] ?? 0) . "\n";
        echo "Total de comandos: " . ($stats['total_commands'] ?? 0) . "\n";
        echo "Taxa de sucesso: " . ($stats['success_rate'] ?? 0) . "%\n";
        echo "Comando mais usado: " . ($stats['most_used_command'] ?? 'N/A') . "\n";
        
        if (isset($stats['intents'])) {
            echo "\nIntenções detectadas:\n";
            foreach ($stats['intents'] as $intent => $count) {
                echo "  - $intent: $count vezes\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "⚠️ Não foi possível obter estatísticas\n";
}

echo "\n=== FIM DO TESTE ===\n";
