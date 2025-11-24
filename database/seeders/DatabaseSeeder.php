<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
 public function run()
 {
 $this->call([
 CategoriaSeeder::class,
 MesaSeeder::class,
 UsuarioSeeder::class,
 PedidoSeeder::class,
 ItemPedidoSeeder::class,
 ]);
 }
}