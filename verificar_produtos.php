<?php
require_once 'vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as DB;

$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost', 
    'database' => 'myd_bar_restaurantes',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$produtos = DB::table('produtos')->whereNotNull('codigo')->get(['nome', 'codigo', 'tipo_preparo']);
echo 'Produtos com código: ' . $produtos->count() . "\n";
foreach($produtos as $p) {
    echo "- {$p->nome} ({$p->codigo}) - {$p->tipo_preparo}\n";
}
