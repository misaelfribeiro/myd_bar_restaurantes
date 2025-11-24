<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoControllerAtivos
{
    public function pedidosAtivos()
    {
        try {
            $pedidos = Pedido::whereIn('status', ['aberto', 'pendente', 'em_preparo', 'pronto'])
                ->with([
                    'mesa:id,identificador,numero',
                    'usuario:id,nome',
                    'itens:id,pedido_id,produto_id,combo_id,quantidade,observacoes',
                    'itens.produto:id,nome,tipo_preparo',
                    'itens.combo:id,nome'
                ])
                ->select('id', 'mesa_id', 'usuario_id', 'status', 'observacoes', 'created_at')
                ->orderByRaw("FIELD(status, 'aberto', 'pendente', 'em_preparo', 'pronto')")
                ->orderBy('created_at', 'asc')
                ->limit(50)
                ->get();
            
            // Adicionar nome_item
            $pedidos->each(function($pedido) {
                $pedido->itens->each(function($item) {
                    if ($item->produto) {
                        $item->nome_item = $item->produto->nome;
                    } elseif ($item->combo) {
                        $item->nome_item = $item->combo->nome;
                    }
                });
            });
            
            return response()->json([
                'success' => true,
                'pedidos' => $pedidos
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar pedidos ativos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar pedidos: ' . $e->getMessage()
            ], 500);
        }
    }
}
