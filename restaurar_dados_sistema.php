<?php

// Script para restaurar dados essenciais do sistema

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

echo "🔄 Restaurando dados essenciais do sistema...\n\n";

try {
    // 1. RESTAURAR USUÁRIOS
    echo "👥 Restaurando usuários...\n";
    
    if (!Schema::hasTable('usuarios')) {
        DB::statement('
            CREATE TABLE usuarios (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                nome varchar(255) NOT NULL,
                email varchar(255) NOT NULL,
                password varchar(255) NOT NULL,
                tipo enum("admin","garcom","caixa") NOT NULL DEFAULT "garcom",
                role varchar(255) DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY usuarios_email_unique (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }
    
    // Inserir usuário padrão se não existir
    $usuarioExiste = DB::table('usuarios')->where('email', 'admin@restaurante.com')->exists();
    if (!$usuarioExiste) {
        DB::table('usuarios')->insert([
            'nome' => 'Administrador',
            'email' => 'admin@restaurante.com', 
            'password' => bcrypt('123456'),
            'tipo' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "  ✅ Usuário admin criado\n";
    }

    // 2. RESTAURAR MESAS
    echo "\n🪑 Restaurando mesas...\n";
    
    if (!Schema::hasTable('mesas')) {
        DB::statement('
            CREATE TABLE mesas (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                numero int(11) NOT NULL,
                capacidade int(11) NOT NULL DEFAULT 4,
                status enum("disponivel","ocupada","reservada") NOT NULL DEFAULT "disponivel",
                disponivel tinyint(1) NOT NULL DEFAULT 1,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY mesas_numero_unique (numero)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }
    
    // Limpar e recriar mesas
    DB::table('mesas')->truncate();
    for ($i = 1; $i <= 10; $i++) {
        DB::table('mesas')->insert([
            'numero' => $i,
            'capacidade' => rand(2, 6),
            'status' => 'disponivel',
            'disponivel' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    echo "  ✅ 10 mesas criadas (numeradas de 1 a 10)\n";

    // 3. RESTAURAR CATEGORIAS
    echo "\n📋 Restaurando categorias...\n";
    
    if (!Schema::hasTable('categorias')) {
        DB::statement('
            CREATE TABLE categorias (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                nome varchar(255) NOT NULL,
                descricao text DEFAULT NULL,
                ativa tinyint(1) NOT NULL DEFAULT 1,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }
    
    $categorias = [
        ['nome' => 'Bebidas', 'descricao' => 'Drinks, sucos e refrigerantes'],
        ['nome' => 'Lanches', 'descricao' => 'Hambúrguers, sanduíches e petiscos'],
        ['nome' => 'Pratos Principais', 'descricao' => 'Pratos executivos e refeições'],
        ['nome' => 'Sobremesas', 'descricao' => 'Doces e sobremesas variadas'],
        ['nome' => 'Entradas', 'descricao' => 'Aperitivos e entradas']
    ];
    
    DB::table('categorias')->truncate();
    foreach ($categorias as $categoria) {
        $categoria['ativa'] = true;
        $categoria['created_at'] = now();
        $categoria['updated_at'] = now();
        DB::table('categorias')->insert($categoria);
    }
    echo "  ✅ 5 categorias criadas\n";

    // 4. RESTAURAR PRODUTOS
    echo "\n🍽️ Restaurando produtos...\n";
    
    if (!Schema::hasTable('produtos')) {
        DB::statement('
            CREATE TABLE produtos (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                nome varchar(255) NOT NULL,
                preco decimal(8,2) NOT NULL,
                categoria varchar(255) DEFAULT NULL,
                descricao text DEFAULT NULL,
                ativo tinyint(1) NOT NULL DEFAULT 1,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }
    
    $produtos = [
        ['nome' => 'Hambúrguer Artesanal', 'preco' => 25.90, 'categoria' => 'Lanches', 'descricao' => 'Pão artesanal, carne 150g, queijo, alface e tomate'],
        ['nome' => 'Pizza Margherita', 'preco' => 32.50, 'categoria' => 'Pratos Principais', 'descricao' => 'Molho de tomate, mussarela e manjericão'],
        ['nome' => 'Coca-Cola 350ml', 'preco' => 5.50, 'categoria' => 'Bebidas', 'descricao' => 'Refrigerante gelado'],
        ['nome' => 'Suco de Laranja', 'preco' => 8.00, 'categoria' => 'Bebidas', 'descricao' => 'Suco natural da fruta'],
        ['nome' => 'Batata Frita', 'preco' => 12.00, 'categoria' => 'Entradas', 'descricao' => 'Porção de batata frita crocante'],
        ['nome' => 'Cerveja Heineken', 'preco' => 8.50, 'categoria' => 'Bebidas', 'descricao' => 'Cerveja long neck gelada'],
        ['nome' => 'Pudim de Leite', 'preco' => 9.90, 'categoria' => 'Sobremesas', 'descricao' => 'Pudim caseiro com calda'],
        ['nome' => 'Filé à Parmegiana', 'preco' => 45.00, 'categoria' => 'Pratos Principais', 'descricao' => 'Filé empanado com molho e queijo'],
        ['nome' => 'Salada Caesar', 'preco' => 18.50, 'categoria' => 'Entradas', 'descricao' => 'Alface, croutons, parmesão e molho caesar'],
        ['nome' => 'Água Mineral', 'preco' => 3.00, 'categoria' => 'Bebidas', 'descricao' => 'Água sem gás 500ml']
    ];
    
    DB::table('produtos')->truncate();
    foreach ($produtos as $produto) {
        $produto['ativo'] = true;
        $produto['created_at'] = now();
        $produto['updated_at'] = now();
        DB::table('produtos')->insert($produto);
    }
    echo "  ✅ 10 produtos criados\n";

    // 5. VERIFICAR PEDIDOS
    echo "\n📝 Verificando tabela de pedidos...\n";
    
    if (!Schema::hasTable('pedidos')) {
        DB::statement('
            CREATE TABLE pedidos (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                mesa_id bigint(20) unsigned NOT NULL,
                usuario_id bigint(20) unsigned DEFAULT NULL,
                status enum("aberto","em_preparo","pronto","finalizado","cancelado") NOT NULL DEFAULT "aberto",
                data_pedido timestamp NULL DEFAULT NULL,
                valor_total decimal(8,2) DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }
    echo "  ✅ Tabela pedidos verificada\n";

    echo "\n✅ RESTAURAÇÃO CONCLUÍDA COM SUCESSO!\n";
    echo "🎯 Sistema restaurado com:\n";
    echo "   - Usuários: " . DB::table('usuarios')->count() . "\n";
    echo "   - Mesas: " . DB::table('mesas')->count() . "\n"; 
    echo "   - Categorias: " . DB::table('categorias')->count() . "\n";
    echo "   - Produtos: " . DB::table('produtos')->count() . "\n";
    echo "   - Pedidos: " . DB::table('pedidos')->count() . "\n";
    
    echo "\n🔗 Acesse: http://localhost:8000\n";
    echo "👤 Login: admin@restaurante.com / 123456\n\n";

} catch (Exception $e) {
    echo "❌ Erro durante restauração: " . $e->getMessage() . "\n";
    echo "📍 Linha: " . $e->getLine() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . "\n\n";
}
