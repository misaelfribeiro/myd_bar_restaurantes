<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Mesa;
class MesasSeeder extends Seeder
{
 public function run()
 {
 $mesasExistentes = Mesa::count();
 if ($mesasExistentes > 0) {
 echo "⚠️  Já existem {$mesasExistentes} mesas na tabela. Pulando criação.\n";
 return;
 }
 for ($i = 1; $i <= 10; $i++) {
 Mesa::create([
 'numero' => $i,
 'identificador' => 'MESA-' . str_pad($i, 2, '0', STR_PAD_LEFT),
 'lugares' => rand(2, 6),
 'capacidade' => rand(2, 6),
 'status' => 'disponivel'
 ]);
 }
 echo "✅ 10 mesas criadas com sucesso!\n";
 }
}