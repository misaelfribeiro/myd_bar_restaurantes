<?php
/**
 * Script de Sincronizacao COBOL <-> MySQL
 * Exporta dados do Laravel para arquivos COBOL
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==========================================\n";
echo "SINCRONIZACAO COBOL - EATSFOOD\n";
echo "==========================================\n\n";

// Exportar Pedidos
echo "Exportando pedidos...\n";
$pedidos = DB::table('pedidos')
    ->join('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
    ->join('empresas', 'pedidos.tenant_code', '=', 'empresas.codigo')
    ->select(
        'pedidos.id',
        'pedidos.numero_pedido',
        'pedidos.cliente_id',
        'empresas.nome_fantasia as restaurante',
        'pedidos.status',
        'pedidos.total',
        DB::raw('DATE_FORMAT(pedidos.created_at, "%d/%m/%Y") as data'),
        'clientes.telefone'
    )
    ->whereIn('pedidos.status', ['pendente', 'confirmado', 'preparando', 'enviado'])
    ->get();

$file = fopen(__DIR__ . '/pedidos.dat', 'w');
foreach ($pedidos as $pedido) {
    $linha = sprintf(
        "%08d%-20s%08d%-100s%-20s%06.2f%-10s%-15s\n",
        $pedido->id,
        $pedido->numero_pedido,
        $pedido->cliente_id,
        substr($pedido->restaurante, 0, 100),
        strtoupper($pedido->status),
        $pedido->total,
        $pedido->data,
        $pedido->telefone
    );
    fwrite($file, $linha);
}
fclose($file);
echo "✓ " . count($pedidos) . " pedidos exportados\n\n";

// Exportar Clientes
echo "Exportando clientes...\n";
$clientes = DB::table('clientes')
    ->select('id', 'nome', 'telefone', 'email', 'endereco', 'cpf')
    ->get();

$file = fopen(__DIR__ . '/clientes.dat', 'w');
foreach ($clientes as $cliente) {
    $linha = sprintf(
        "%08d%-100s%-15s%-100s%-200s%-14s\n",
        $cliente->id,
        substr($cliente->nome, 0, 100),
        $cliente->telefone,
        substr($cliente->email ?? '', 0, 100),
        substr($cliente->endereco ?? '', 0, 200),
        $cliente->cpf ?? ''
    );
    fwrite($file, $linha);
}
fclose($file);
echo "✓ " . count($clientes) . " clientes exportados\n\n";

// Exportar Restaurantes
echo "Exportando restaurantes...\n";
$restaurantes = DB::table('empresas')
    ->select('id', 'nome_fantasia', 'codigo', 'telefone', 'status')
    ->where('status', 'ativo')
    ->get();

$file = fopen(__DIR__ . '/restaurantes.dat', 'w');
foreach ($restaurantes as $rest) {
    $linha = sprintf(
        "%08d%-100s%-20s%-15s%-20s\n",
        $rest->id,
        substr($rest->nome_fantasia, 0, 100),
        $rest->codigo,
        $rest->telefone ?? '',
        strtoupper($rest->status)
    );
    fwrite($file, $linha);
}
fclose($file);
echo "✓ " . count($restaurantes) . " restaurantes exportados\n\n";

echo "==========================================\n";
echo "SINCRONIZACAO CONCLUIDA!\n";
echo "==========================================\n";
echo "\nPara executar o sistema COBOL:\n";
echo "cd cobol-callcenter\n";
echo ".\\compilar.bat\n";
