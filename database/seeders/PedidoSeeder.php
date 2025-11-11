<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Usuario;

class PedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Criando pedidos...\n";

        // Buscar mesas e usuários existentes
        $mesas = Mesa::all();
        $usuarios = Usuario::where('role', 'garcom')->get();
        
        if ($mesas->isEmpty()) {
            echo "⚠️ Nenhuma mesa encontrada. Execute o MesaSeeder primeiro.\n";
            return;
        }

        if ($usuarios->isEmpty()) {
            echo "⚠️ Nenhum garçom encontrado. Execute o UsuarioSeeder primeiro.\n";
            return;
        }

        $statusPossivel = ['pendente', 'preparando', 'pronto', 'entregue'];
        
        // Criar alguns pedidos de exemplo
        for ($i = 1; $i <= 5; $i++) {
            $mesa = $mesas->random();
            $usuario = $usuarios->random();
            $status = $statusPossivel[array_rand($statusPossivel)];
            
            $pedido = Pedido::create([
                'mesa_id' => $mesa->id,
                'usuario_id' => $usuario->id,
                'total' => 0, // Será calculado quando adicionarmos itens
                'status' => $status,
                'created_at' => now()->subHours(rand(0, 24))
            ]);
            
            echo "✓ Pedido criado: #{$pedido->id} - Mesa {$mesa->identificador} - {$status}\n";
        }

        echo "✅ " . Pedido::count() . " pedidos criados com sucesso!\n";
    }
}
