<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Categoria;
use App\Models\Produto;
use App\Models\ItemPedido;

class ItemPedidoApiTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario;
    protected $mesa;
    protected $categoria;
    protected $produto;
    protected $pedido;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar dados de teste
        $this->usuario = Usuario::create([
            'nome' => 'João Silva',
            'email' => 'joao@teste.com',
            'password' => bcrypt('123456'),
            'perfil' => 'garcom'
        ]);
        
        $this->mesa = Mesa::create([
            'identificador' => '01',
            'lugares' => 4
        ]);
        
        $this->categoria = Categoria::create([
            'nome' => 'Lanches',
            'descricao' => 'Categoria de lanches'
        ]);
        
        $this->produto = Produto::create([
            'nome' => 'Hambúrguer Teste',
            'descricao' => 'Hambúrguer para teste',
            'preco' => 25.50,
            'categoria_id' => $this->categoria->id
        ]);
        
        $this->pedido = Pedido::create([
            'mesa_id' => $this->mesa->id,
            'usuario_id' => $this->usuario->id,
            'total' => 0,
            'status' => 'pendente'
        ]);
    }

    /**
     * Teste básico de criação de item via model
     */
    public function test_pode_criar_item_via_model()
    {
        $item = ItemPedido::create([
            'pedido_id' => $this->pedido->id,
            'produto_id' => $this->produto->id,
            'quantidade' => 2,
            'preco_unitario' => $this->produto->preco,
            'subtotal' => 2 * $this->produto->preco,
            'observacoes' => 'Teste de observação'
        ]);

        $this->assertDatabaseHas('item_pedidos', [
            'pedido_id' => $this->pedido->id,
            'produto_id' => $this->produto->id,
            'quantidade' => 2,
            'preco_unitario' => 25.50,
            'subtotal' => 51.00,
            'observacoes' => 'Teste de observação'
        ]);

        $this->assertEquals(2, $item->quantidade);
        $this->assertEquals(51.00, $item->subtotal);
    }

    /**
     * Teste de relacionamentos
     */
    public function test_relacionamentos_funcionam()
    {
        $item = ItemPedido::create([
            'pedido_id' => $this->pedido->id,
            'produto_id' => $this->produto->id,
            'quantidade' => 1,
            'preco_unitario' => $this->produto->preco,
            'subtotal' => $this->produto->preco
        ]);

        // Testar relacionamento com produto
        $this->assertEquals('Hambúrguer Teste', $item->produto->nome);
        
        // Testar relacionamento com pedido
        $this->assertEquals($this->pedido->id, $item->pedido->id);
        
        // Testar relacionamento inverso
        $this->assertTrue($this->pedido->itens()->exists());
        $this->assertEquals(1, $this->pedido->itens()->count());
    }

    /**
     * Teste de validação de cálculos
     */
    public function test_calculo_subtotal_correto()
    {
        $quantidade = 3;
        $precoUnitario = 25.50;
        $subtotalEsperado = $quantidade * $precoUnitario;

        $item = ItemPedido::create([
            'pedido_id' => $this->pedido->id,
            'produto_id' => $this->produto->id,
            'quantidade' => $quantidade,
            'preco_unitario' => $precoUnitario,
            'subtotal' => $subtotalEsperado
        ]);

        $this->assertEquals($subtotalEsperado, $item->subtotal);
        $this->assertEquals(76.50, $item->subtotal);
    }

    /**
     * Teste de múltiplos itens no mesmo pedido
     */
    public function test_multiplos_itens_mesmo_pedido()
    {
        // Criar segundo produto
        $produto2 = Produto::create([
            'nome' => 'Coca-Cola',
            'descricao' => 'Refrigerante',
            'preco' => 4.50,
            'categoria_id' => $this->categoria->id
        ]);

        // Criar dois itens
        ItemPedido::create([
            'pedido_id' => $this->pedido->id,
            'produto_id' => $this->produto->id,
            'quantidade' => 1,
            'preco_unitario' => $this->produto->preco,
            'subtotal' => $this->produto->preco
        ]);

        ItemPedido::create([
            'pedido_id' => $this->pedido->id,
            'produto_id' => $produto2->id,
            'quantidade' => 2,
            'preco_unitario' => $produto2->preco,
            'subtotal' => 2 * $produto2->preco
        ]);

        // Verificar se ambos itens estão no pedido
        $this->assertEquals(2, $this->pedido->itens()->count());
        
        $totalEsperado = 25.50 + (2 * 4.50); // 25.50 + 9.00 = 34.50
        $totalItens = $this->pedido->itens()->sum('subtotal');
        
        $this->assertEquals($totalEsperado, $totalItens);
    }

    /**
     * Teste de busca por pedido específico
     */
    public function test_busca_itens_por_pedido()
    {
        // Criar segundo pedido
        $pedido2 = Pedido::create([
            'mesa_id' => $this->mesa->id,
            'usuario_id' => $this->usuario->id,
            'total' => 0,
            'status' => 'pendente'
        ]);

        // Criar itens em pedidos diferentes
        ItemPedido::create([
            'pedido_id' => $this->pedido->id,
            'produto_id' => $this->produto->id,
            'quantidade' => 1,
            'preco_unitario' => $this->produto->preco,
            'subtotal' => $this->produto->preco
        ]);

        ItemPedido::create([
            'pedido_id' => $pedido2->id,
            'produto_id' => $this->produto->id,
            'quantidade' => 2,
            'preco_unitario' => $this->produto->preco,
            'subtotal' => 2 * $this->produto->preco
        ]);

        // Verificar que cada pedido tem seus itens
        $this->assertEquals(1, $this->pedido->itens()->count());
        $this->assertEquals(1, $pedido2->itens()->count());
        
        // Verificar quantidade correta em cada pedido
        $this->assertEquals(1, $this->pedido->itens()->first()->quantidade);
        $this->assertEquals(2, $pedido2->itens()->first()->quantidade);
    }

    /**
     * Teste de atualização de item
     */
    public function test_atualizacao_item()
    {
        $item = ItemPedido::create([
            'pedido_id' => $this->pedido->id,
            'produto_id' => $this->produto->id,
            'quantidade' => 1,
            'preco_unitario' => $this->produto->preco,
            'subtotal' => $this->produto->preco,
            'observacoes' => 'Observação inicial'
        ]);

        // Atualizar item
        $item->update([
            'quantidade' => 3,
            'subtotal' => 3 * $this->produto->preco,
            'observacoes' => 'Observação atualizada'
        ]);

        $this->assertEquals(3, $item->fresh()->quantidade);
        $this->assertEquals(76.50, $item->fresh()->subtotal);
        $this->assertEquals('Observação atualizada', $item->fresh()->observacoes);
    }

    /**
     * Teste de remoção de item
     */
    public function test_remocao_item()
    {
        $item = ItemPedido::create([
            'pedido_id' => $this->pedido->id,
            'produto_id' => $this->produto->id,
            'quantidade' => 1,
            'preco_unitario' => $this->produto->preco,
            'subtotal' => $this->produto->preco
        ]);

        $itemId = $item->id;
        $item->delete();

        $this->assertDatabaseMissing('item_pedidos', [
            'id' => $itemId
        ]);

        $this->assertEquals(0, $this->pedido->itens()->count());
    }
}
