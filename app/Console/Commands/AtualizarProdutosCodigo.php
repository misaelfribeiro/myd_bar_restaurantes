<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
class AtualizarProdutosCodigo extends Command
{
 protected $signature = 'produtos:atualizar-codigo';
 protected $description = 'Atualizar produtos com códigos e tipo de preparo';
 public function __construct()
 {
 parent::__construct();
 }
 public function handle()
 {
 $this->info('Atualizando produtos com códigos e tipo de preparo...');
 $produtos_config = [
 'Coca-Cola 350ml' => ['codigo' => 'BEB001', 'tipo_preparo' => 'pronto'],
 'Suco de Laranja' => ['codigo' => 'BEB002', 'tipo_preparo' => 'preparo'],
 'Água Mineral 500ml' => ['codigo' => 'BEB003', 'tipo_preparo' => 'pronto'],
 'Cerveja Heineken' => ['codigo' => 'BEB004', 'tipo_preparo' => 'pronto'],
 'Hambúrguer Artesanal' => ['codigo' => 'PRA001', 'tipo_preparo' => 'preparo'],
 'Pizza Margherita' => ['codigo' => 'PRA002', 'tipo_preparo' => 'preparo'],
 'Filé à Parmegiana' => ['codigo' => 'PRA003', 'tipo_preparo' => 'preparo'],
 'Lasanha Bolonhesa' => ['codigo' => 'PRA004', 'tipo_preparo' => 'preparo'],
 'Batata Frita' => ['codigo' => 'PET001', 'tipo_preparo' => 'preparo'],
 'Salada Caesar' => ['codigo' => 'PET002', 'tipo_preparo' => 'preparo'],
 'Porção de Mandioca' => ['codigo' => 'PET003', 'tipo_preparo' => 'preparo'],
 'Pudim de Leite' => ['codigo' => 'SOB001', 'tipo_preparo' => 'pronto'],
 'Brigadeiro Gourmet' => ['codigo' => 'SOB002', 'tipo_preparo' => 'pronto'],
 'Torta de Chocolate' => ['codigo' => 'SOB003', 'tipo_preparo' => 'pronto'],
 'Caipirinha' => ['codigo' => 'DRK001', 'tipo_preparo' => 'preparo'],
 'Mojito' => ['codigo' => 'DRK002', 'tipo_preparo' => 'preparo']
 ];
 $contador = 0;
 foreach ($produtos_config as $nome => $config) {
 $produto = \App\Models\Produto::where('nome', $nome)->first();
 if ($produto) {
 $produto->update([
 'codigo' => $config['codigo'],
 'tipo_preparo' => $config['tipo_preparo']
 ]);
 $this->line("✅ {$nome} - Código: {$config['codigo']} - Preparo: {$config['tipo_preparo']}");
 $contador++;
 } else {
 $this->error("❌ Produto '{$nome}' não encontrado");
 }
 }
 $this->info("\n🎉 {$contador} produtos atualizados com sucesso!");
 $this->info("\n📊 RESUMO POR TIPO DE PREPARO:");
 $pronto = \App\Models\Produto::where('tipo_preparo', 'pronto')->count();
 $preparo = \App\Models\Produto::where('tipo_preparo', 'preparo')->count();
 $this->table(
 ['Tipo de Preparo', 'Quantidade'],
 [
 ['Pronto (não precisa preparo)', $pronto],
 ['Preparo (requer preparação)', $preparo]
 ]
 );
 $this->info("\n📝 TIPOS DE PREPARO:");
 $this->line("• pronto: Produtos que não precisam de preparo (bebidas industrializadas, sobremesas prontas)");
 $this->line("• preparo: Produtos que requerem algum tipo de preparação (cozinha, bar, etc.)");
 return 0;
 }
}