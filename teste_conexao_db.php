<?php
// Teste de conexão com banco de dados
try {
    echo "<h1>🔍 Teste de Conexão com Banco de Dados</h1>";
    
    // Teste direto com PDO
    echo "<h2>1. Teste direto com PDO:</h2>";
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=myd_bar_restaurantes', 'root', '');
    echo "✅ Conexão PDO: <strong>SUCESSO</strong><br>";
    
    // Verificar se a base existe
    $stmt = $pdo->query("SELECT DATABASE()");
    $database = $stmt->fetchColumn();
    echo "📊 Database ativo: <strong>$database</strong><br>";
    
    // Verificar tabelas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Tabelas encontradas: <strong>" . count($tables) . "</strong><br>";
    
    if (count($tables) > 0) {
        echo "<ul>";
        foreach($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>2. Teste com Laravel:</h2>";
    
    // Carregar Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Testar conexão Laravel
    $connection = DB::connection();
    $databaseName = $connection->getDatabaseName();
    echo "✅ Laravel DB: <strong>CONECTADO</strong><br>";
    echo "📊 Database Laravel: <strong>$databaseName</strong><br>";
    
    // Testar query simples
    $usuarios = DB::table('usuarios')->count();
    echo "👥 Usuários na base: <strong>$usuarios</strong><br>";
    
    $mesas = DB::table('mesas')->count();
    echo "🪑 Mesas na base: <strong>$mesas</strong><br>";
    
    echo "<div style='background: #d4edda; padding: 10px; margin: 20px 0; border-radius: 5px; color: #155724;'>";
    echo "<strong>✅ SUCESSO!</strong> Todas as conexões estão funcionando corretamente.";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 10px; margin: 20px 0; border-radius: 5px; color: #721c24;'>";
    echo "<strong>❌ ERRO:</strong> " . $e->getMessage();
    echo "<br><strong>Arquivo:</strong> " . $e->getFile();
    echo "<br><strong>Linha:</strong> " . $e->getLine();
    echo "</div>";
    
    echo "<h3>🔧 Possíveis soluções:</h3>";
    echo "<ul>";
    echo "<li>Verificar se o XAMPP MySQL está rodando</li>";
    echo "<li>Verificar se a database 'myd_bar_restaurantes' existe</li>";
    echo "<li>Verificar credenciais no arquivo .env</li>";
    echo "<li>Executar: php artisan config:clear</li>";
    echo "<li>Executar: php artisan migrate</li>";
    echo "</ul>";
}
?>
