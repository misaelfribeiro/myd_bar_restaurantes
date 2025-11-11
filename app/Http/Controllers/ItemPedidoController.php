<?php

namespace App\Http\Controllers;

use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemPedidoController extends Controller
{
    /**
     * Listar itens de um pedido específico
     */
    public function index(Request $request)
    {
        $pedidoId = $request->input('pedido_id');
        
        $query = ItemPedido::with(['produto', 'pedido']);
        
        if ($pedidoId) {
            $query->where('pedido_id', $pedidoId);
        }
        
        $itens = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'itens' => $itens
        ]);
    }

    /**
     * Adicionar item ao pedido
     */
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:1',
            'observacoes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        
        try {
            // Buscar produto e pedido
            $produto = Produto::findOrFail($request->produto_id);
            $pedido = Pedido::findOrFail($request->pedido_id);

            // Verificar se o pedido ainda pode ser modificado
            if (in_array($pedido->status, ['entregue', 'cancelado'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível modificar pedido com status: ' . $pedido->status
                ], 400);
            }

            // Verificar se o item já existe no pedido
            $itemExistente = ItemPedido::where('pedido_id', $request->pedido_id)
                                     ->where('produto_id', $request->produto_id)
                                     ->first();

            if ($itemExistente) {
                // Atualizar quantidade do item existente
                $itemExistente->quantidade += $request->quantidade;
                $itemExistente->subtotal = $itemExistente->quantidade * $produto->preco;
                $itemExistente->observacoes = $request->observacoes ?: $itemExistente->observacoes;
                $itemExistente->save();
                
                $item = $itemExistente;
            } else {
                // Criar novo item
                $item = ItemPedido::create([
                    'pedido_id' => $request->pedido_id,
                    'produto_id' => $request->produto_id,
                    'quantidade' => $request->quantidade,
                    'preco_unitario' => $produto->preco,
                    'subtotal' => $request->quantidade * $produto->preco,
                    'observacoes' => $request->observacoes
                ]);
            }

            // Recalcular total do pedido
            $this->recalcularTotalPedido($pedido);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item adicionado ao pedido com sucesso!',
                'item' => $item->load(['produto', 'pedido'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar item ao pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar item específico
     */
    public function show(ItemPedido $itemPedido)
    {
        $itemPedido->load(['produto', 'pedido']);
        
        return response()->json([
            'success' => true,
            'item' => $itemPedido
        ]);
    }

    /**
     * Atualizar item do pedido
     */
    public function update(Request $request, ItemPedido $itemPedido)
    {
        $request->validate([
            'produto_id' => 'sometimes|required|exists:produtos,id',
            'quantidade' => 'sometimes|required|numeric|min:1',
            'observacoes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        
        try {
            // Verificar se o pedido ainda pode ser modificado
            $pedido = $itemPedido->pedido;
            if (in_array($pedido->status, ['entregue', 'cancelado'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível modificar pedido com status: ' . $pedido->status
                ], 400);
            }

            // Se mudou o produto, atualizar preço unitário
            if ($request->has('produto_id')) {
                $produto = Produto::findOrFail($request->produto_id);
                $itemPedido->produto_id = $request->produto_id;
                $itemPedido->preco_unitario = $produto->preco;
            }

            // Atualizar quantidade
            if ($request->has('quantidade')) {
                $itemPedido->quantidade = $request->quantidade;
            }

            // Atualizar observações
            if ($request->has('observacoes')) {
                $itemPedido->observacoes = $request->observacoes;
            }

            // Recalcular subtotal
            $itemPedido->subtotal = $itemPedido->quantidade * $itemPedido->preco_unitario;
            $itemPedido->save();

            // Recalcular total do pedido
            $this->recalcularTotalPedido($pedido);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item atualizado com sucesso!',
                'item' => $itemPedido->load(['produto', 'pedido'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover item do pedido
     */
    public function destroy(ItemPedido $itemPedido)
    {
        DB::beginTransaction();
        
        try {
            // Verificar se o pedido ainda pode ser modificado
            $pedido = $itemPedido->pedido;
            if (in_array($pedido->status, ['entregue', 'cancelado'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível modificar pedido com status: ' . $pedido->status
                ], 400);
            }

            $itemPedido->delete();

            // Recalcular total do pedido
            $this->recalcularTotalPedido($pedido);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item removido do pedido com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar itens de um pedido específico
     */
    public function itensPorPedido($pedidoId)
    {
        $pedido = Pedido::with(['itens.produto', 'mesa', 'usuario'])->findOrFail($pedidoId);

        return response()->json([
            'success' => true,
            'pedido' => $pedido,
            'itens' => $pedido->itens,
            'total_itens' => $pedido->itens->count(),
            'valor_total' => $pedido->itens->sum('subtotal')
        ]);
    }

    /**
     * Adicionar múltiplos itens ao pedido de uma vez
     */
    public function adicionarMultiplos(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|numeric|min:1',
            'itens.*.observacoes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        
        try {
            $pedido = Pedido::findOrFail($request->pedido_id);

            // Verificar se o pedido ainda pode ser modificado
            if (in_array($pedido->status, ['entregue', 'cancelado'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível modificar pedido com status: ' . $pedido->status
                ], 400);
            }

            $itensAdicionados = [];

            foreach ($request->itens as $itemData) {
                $produto = Produto::findOrFail($itemData['produto_id']);

                // Verificar se o item já existe no pedido
                $itemExistente = ItemPedido::where('pedido_id', $request->pedido_id)
                                         ->where('produto_id', $itemData['produto_id'])
                                         ->first();

                if ($itemExistente) {
                    // Atualizar quantidade do item existente
                    $itemExistente->quantidade += $itemData['quantidade'];
                    $itemExistente->subtotal = $itemExistente->quantidade * $produto->preco;
                    $itemExistente->observacoes = $itemData['observacoes'] ?? $itemExistente->observacoes;
                    $itemExistente->save();
                    
                    $itensAdicionados[] = $itemExistente;
                } else {
                    // Criar novo item
                    $item = ItemPedido::create([
                        'pedido_id' => $request->pedido_id,
                        'produto_id' => $itemData['produto_id'],
                        'quantidade' => $itemData['quantidade'],
                        'preco_unitario' => $produto->preco,
                        'subtotal' => $itemData['quantidade'] * $produto->preco,
                        'observacoes' => $itemData['observacoes'] ?? null
                    ]);
                    
                    $itensAdicionados[] = $item;
                }
            }

            // Recalcular total do pedido
            $this->recalcularTotalPedido($pedido);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($itensAdicionados) . ' itens adicionados ao pedido com sucesso!',
                'itens' => collect($itensAdicionados)->load(['produto']),
                'pedido_total' => $pedido->fresh()->total
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar itens: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalcular total do pedido baseado nos itens
     */
    private function recalcularTotalPedido(Pedido $pedido)
    {
        $total = $pedido->itens()->sum('subtotal');
        $pedido->update(['total' => $total]);
    }

    /**
     * Relatório de itens mais vendidos
     */
    public function itensMaisVendidos()
    {
        $itens = ItemPedido::with('produto')
            ->select('produto_id')
            ->selectRaw('SUM(quantidade) as total_vendido')
            ->selectRaw('COUNT(DISTINCT pedido_id) as pedidos_count')
            ->selectRaw('SUM(subtotal) as receita_total')
            ->groupBy('produto_id')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'itens_mais_vendidos' => $itens
        ]);
    }
}
