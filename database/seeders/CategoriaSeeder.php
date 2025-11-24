<?php
namespace Database\Seeders;
use App\Models\Categoria;
use Illuminate\Database\Seeder;
class CategoriaSeeder extends Seeder
{
 public function run()
 {
 $categorias = [
 ['nome' => 'Bebidas'],
 ['nome' => 'Pratos Principais'],
 ['nome' => 'Sobremesas'],
 ['nome' => 'Petiscos'],
 ['nome' => 'Drinks']
 ];
 foreach ($categorias as $categoria) {
 Categoria::create($categoria);
 }
 }
}