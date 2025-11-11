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

class EditarItemPedidoTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $mesa;
    protected $produto1;
    protected $produto2;
    protected $pedido;
    protected $itemPedido;

    protected function setUp(): void
    {
        parent::setUp();        // Criar dados de teste
        $this->user = User::factory()->create([
            'name' => 'Garçom Teste',
            'email' => 'garcom@teste.com'
        ]);

        Categoria::create([
            'nome' => 'Bebidas',
            'descricao' => 'Categoria de bebidas'
        ]);        $this->mesa = Mesa::create([
            'identificador' => 'Mesa 01',
            'lugares' => 4,
            'status' => 'disponivel'
        ]);

        $this->produto1 = Produto::create([
            'nome' => 'Coca-Cola',
            'descricao' => 'Refrigerante Coca-Cola 350ml',
            'preco' => 5.00,
            'categoria_id' => 1,
            'ativo' => true
        ]);

        $this->produto2 = Produto::create([
            'nome' => 'Pepsi',
            'descricao' => 'Refrigerante Pepsi 350ml',
            'preco' => 4.50,
            'categoria_id' => 1,
            'ativo' => true
        ]);

        $this->pedido = Pedido::create([
            'mesa_id' => $this->mesa->id,
            'usuario_id' => $this->user->id,
            'status' => 'pendente',
            'total' => 10.00
        ]);

        $this->itemPedido = ItemPedido::create([
            'pedido_id' => $this->pedido->id,
            'produto_id' => $this->produto1->id,
            'quantidade' => 2,
            'preco_unitario' => 5.00,
            'subtotal' => 10.00,
            'observacoes' => 'Sem gelo'
        ]);
    }

    /** @test */
    public function pode_buscar_detalhes_de_um_item_especifico()
    {
        $response = $this->getJson("/api/item-pedidos-public/{$this->itemPedido->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'item' => [
                         'id' => $this->itemPedido->id,
                         'pedido_id' => $this->pedido->id,
                         'produto_id' => $this->produto1->id,
                         'quantidade' => 2,
                         'preco_unitario' => '5.00',
                         'subtotal' => '10.00',
                         'observacoes' => 'Sem gelo'
                     ]
                 ]);
    }

    /** @test */
    public function pode_editar_quantidade_do_item()
    {
        $dadosEdicao = [
            'quantidade' => 3
        ];

        $response = $this->putJson("/api/item-pedidos-public/{$this->itemPedido->id}", $dadosEdicao);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Item atualizado com sucesso!'
                 ]);

        // Verificar se foi atualizado no banco
        $this->assertDatabaseHas('item_pedidos', [
            'id' => $this->itemPedido->id,
            'quantidade' => 3,
            'subtotal' => 15.00 // 3 * 5.00
        ]);

        // Verificar se o total do pedido foi recalculado
        $this->pedido->refresh();
        $this->assertEquals(15.00, $this->pedido->total);
    }

    /** @test */
    public function pode_editar_produto_do_item()
    {
        $dadosEdicao = [
            'produto_id' => $this->produto2->id,
            'quantidade' => 2
        ];

        $response = $this->putJson("/api/item-pedidos-public/{$this->itemPedido->id}", $dadosEdicao);

        $response->assertStatus(200);

        // Verificar se foi atualizado no banco
        $this->assertDatabaseHas('item_pedidos', [
            'id' => $this->itemPedido->id,
            'produto_id' => $this->produto2->id,
            'quantidade' => 2,
            'preco_unitario' => 4.50, // Preço do produto2
            'subtotal' => 9.00 // 2 * 4.50
        ]);

        // Verificar se o total do pedido foi recalculado
        $this->pedido->refresh();
        $this->assertEquals(9.00, $this->pedido->total);
    }

    /** @test */
    public function pode_editar_observacoes_do_item()
    {
        $dadosEdicao = [
            'observacoes' => 'Com gelo e limão'
        ];

        $response = $this->putJson("/api/item-pedidos-public/{$this->itemPedido->id}", $dadosEdicao);

        $response->assertStatus(200);

        // Verificar se foi atualizado no banco
        $this->assertDatabaseHas('item_pedidos', [
            'id' => $this->itemPedido->id,
            'observacoes' => 'Com gelo e limão'
        ]);
    }

    /** @test */
    public function pode_editar_multiplos_campos_simultaneamente()
    {
        $dadosEdicao = [
            'produto_id' => $this->produto2->id,
            'quantidade' => 4,
            'observacoes' => 'Extra gelado'
        ];

        $response = $this->putJson("/api/item-pedidos-public/{$this->itemPedido->id}", $dadosEdicao);

        $response->assertStatus(200);

        // Verificar se tudo foi atualizado no banco
        $this->assertDatabaseHas('item_pedidos', [
            'id' => $this->itemPedido->id,
            'produto_id' => $this->produto2->id,
            'quantidade' => 4,
            'preco_unitario' => 4.50,
            'subtotal' => 18.00, // 4 * 4.50
            'observacoes' => 'Extra gelado'
        ]);

        // Verificar se o total do pedido foi recalculado
        $this->pedido->refresh();
        $this->assertEquals(18.00, $this->pedido->total);
    }

    /** @test */
    public function nao_pode_editar_item_de_pedido_entregue()
    {
        // Alterar status do pedido para entregue
        $this->pedido->update(['status' => 'entregue']);

        $dadosEdicao = [
            'quantidade' => 3
        ];

        $response = $this->putJson("/api/item-pedidos-public/{$this->itemPedido->id}", $dadosEdicao);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Não é possível modificar pedido com status: entregue'
                 ]);
    }

    /** @test */
    public function nao_pode_editar_item_de_pedido_cancelado()
    {
        // Alterar status do pedido para cancelado
        $this->pedido->update(['status' => 'cancelado']);

        $dadosEdicao = [
            'quantidade' => 3
        ];

        $response = $this->putJson("/api/item-pedidos-public/{$this->itemPedido->id}", $dadosEdicao);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Não é possível modificar pedido com status: cancelado'
                 ]);
    }

    /** @test */
    public function valida_quantidade_minima()
    {
        $dadosEdicao = [
            'quantidade' => 0
        ];

        $response = $this->putJson("/api/item-pedidos-public/{$this->itemPedido->id}", $dadosEdicao);

        $response->assertStatus(422);
    }

    /** @test */
    public function valida_produto_existente()
    {
        $dadosEdicao = [
            'produto_id' => 9999 // ID que não existe
        ];

        $response = $this->putJson("/api/item-pedidos-public/{$this->itemPedido->id}", $dadosEdicao);

        $response->assertStatus(422);
    }

    /** @test */
    public function retorna_erro_para_item_inexistente()
    {
        $response = $this->getJson("/api/item-pedidos-public/9999");

        $response->assertStatus(404);
    }
}
