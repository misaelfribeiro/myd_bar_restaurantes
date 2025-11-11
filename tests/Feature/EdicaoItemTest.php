<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdicaoItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pode_editar_item_via_api()
    {
        // Criar dados básicos
        $user = User::factory()->create();
        
        $categoria = Categoria::create([
            'nome' => 'Bebidas',
            'descricao' => 'Categoria de bebidas'
        ]);

        $mesa = Mesa::create([
            'identificador' => 'Mesa 01',
            'lugares' => 4,
            'status' => 'disponivel'
        ]);

        $produto = Produto::create([
            'nome' => 'Coca-Cola',
            'descricao' => 'Refrigerante Coca-Cola 350ml',
            'preco' => 5.00,
            'categoria_id' => $categoria->id,
            'ativo' => true
        ]);

        $pedido = Pedido::create([
            'mesa_id' => $mesa->id,
            'usuario_id' => $user->id,
            'status' => 'pendente',
            'total' => 10.00
        ]);

        $item = ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'preco_unitario' => 5.00,
            'subtotal' => 10.00,
            'observacoes' => 'Sem gelo'
        ]);

        // Testar edição
        $dadosEdicao = [
            'quantidade' => 3,
            'observacoes' => 'Com gelo'
        ];

        $response = $this->putJson("/api/item-pedidos-public/{$item->id}", $dadosEdicao);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Item atualizado com sucesso!'
                 ]);

        // Verificar se foi atualizado no banco
        $this->assertDatabaseHas('item_pedidos', [
            'id' => $item->id,
            'quantidade' => 3,
            'observacoes' => 'Com gelo',
            'subtotal' => 15.00 // 3 * 5.00
        ]);
    }

    /** @test */
    public function pode_buscar_item_especifico()
    {
        // Criar dados básicos
        $user = User::factory()->create();
        
        $categoria = Categoria::create([
            'nome' => 'Bebidas',
            'descricao' => 'Categoria de bebidas'
        ]);

        $mesa = Mesa::create([
            'identificador' => 'Mesa 01',
            'lugares' => 4,
            'status' => 'disponivel'
        ]);

        $produto = Produto::create([
            'nome' => 'Coca-Cola',
            'descricao' => 'Refrigerante Coca-Cola 350ml',
            'preco' => 5.00,
            'categoria_id' => $categoria->id,
            'ativo' => true
        ]);

        $pedido = Pedido::create([
            'mesa_id' => $mesa->id,
            'usuario_id' => $user->id,
            'status' => 'pendente',
            'total' => 5.00
        ]);

        $item = ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 1,
            'preco_unitario' => 5.00,
            'subtotal' => 5.00,
            'observacoes' => 'Teste'
        ]);

        // Testar busca
        $response = $this->getJson("/api/item-pedidos-public/{$item->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'item' => [
                         'id' => $item->id,
                         'quantidade' => 1,
                         'observacoes' => 'Teste'
                     ]
                 ]);
    }
}
