<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;

class ItemPedidoSeeder extends Seeder
{
    public function run()
    {
        echo "Criando itens de pedido...\n";

        // Verificar se existem pedidos e produtos
        $pedidos = Pedido::all();
        $produtos = Produto::all();

        if ($pedidos->isEmpty() || $produtos->isEmpty()) {
            echo "⚠️ Não há pedidos ou produtos cadastrados.\n";
            return;
        }

        // Limpar itens existentes
        ItemPedido::truncate();

        // Buscar produtos específicos
        $cocaCola = Produto::where('nome', 'like', '%Coca-Cola%')->first();
        $cerveja = Produto::where('nome', 'like', '%Cerveja%')->first();
        $pudim = Produto::where('nome', 'like', '%Pudim%')->first();
        $suco = Produto::where('nome', 'like', '%Suco%')->first();
        
        // Pegar alguns pedidos para adicionar itens
        $pedidosParaItens = $pedidos->take(4);

        foreach ($pedidosParaItens as $index => $pedido) {
            switch ($index % 4) {
                case 0:
                    if ($cerveja) {
                        $this->criarItem($pedido, $cerveja, 2, 'Bem gelada');
                    }
                    break;
                    
                case 1:
                    if ($cocaCola) {
                        $this->criarItem($pedido, $cocaCola, 2, null);
                    }
                    if ($pudim) {
                        $this->criarItem($pedido, $pudim, 1, 'Sem açúcar extra');
                    }
                    break;
                    
                case 2:
                    if ($cerveja) {
                        $this->criarItem($pedido, $cerveja, 1, null);
                    }
                    if ($suco) {
                        $this->criarItem($pedido, $suco, 1, 'Natural');
                    }
                    break;
                    
                case 3:
                    if ($cocaCola) {
                        $this->criarItem($pedido, $cocaCola, 1, null);
                    }
                    break;
            }
        }

        echo "Recalculando totais dos pedidos...\n";
        
        // Recalcular totais
        foreach (Pedido::all() as $pedido) {
            $total = $pedido->itens()->sum('subtotal');
            $pedido->update(['total' => $total]);
            echo "✓ Pedido {$pedido->id}: R$ " . number_format($total, 2, ',', '.') . "\n";
        }

        $totalItens = ItemPedido::count();
        echo "✅ {$totalItens} itens de pedido criados!\n";
    }

    private function criarItem($pedido, $produto, $quantidade, $observacoes = null)
    {
        $subtotal = $produto->preco * $quantidade;
        
        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
            'preco_unitario' => $produto->preco,
            'subtotal' => $subtotal,
            'observacoes' => $observacoes
        ]);

        echo "✓ Item criado: {$produto->nome} x{$quantidade} - Pedido {$pedido->id}\n";
    }
}
