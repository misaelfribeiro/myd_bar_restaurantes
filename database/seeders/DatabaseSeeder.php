<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategoriaSeeder::class,
            // ProdutoSeeder::class, // Comentado temporariamente
            MesaSeeder::class,
            UsuarioSeeder::class,
            PedidoSeeder::class,
            ItemPedidoSeeder::class,
        ]);
    }
}
