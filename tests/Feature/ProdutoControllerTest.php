<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProdutoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar categoria de teste
        $this->categoria = Categoria::create(['nome' => 'Teste']);
    }

    public function test_pode_listar_produtos()
    {
        // Criar produtos de teste
        Produto::create([
            'nome' => 'Produto 1',
            'descricao' => 'Descrição do produto 1',
            'preco' => 10.50,
            'categoria_id' => $this->categoria->id,
            'ativo' => true
        ]);

        Produto::create([
            'nome' => 'Produto 2',
            'descricao' => 'Descrição do produto 2',
            'preco' => 15.90,
            'categoria_id' => $this->categoria->id,
            'ativo' => false
        ]);

        $response = $this->get('/produtos');

        $response->assertStatus(200);
        $response->assertSee('Produto 1');
        $response->assertSee('Produto 2');
        $response->assertSee('R$ 10,50');
        $response->assertSee('R$ 15,90');
    }

    public function test_pode_criar_produto()
    {
        $response = $this->get('/produtos/create');
        $response->assertStatus(200);
        $response->assertSee('Novo Produto');
    }

    public function test_pode_armazenar_produto()
    {
        $dados = [
            'nome' => 'Produto Teste',
            'descricao' => 'Descrição do produto teste',
            'preco' => 25.90,
            'categoria_id' => $this->categoria->id,
            'ativo' => true
        ];

        $response = $this->post('/produtos', $dados);

        $response->assertRedirect('/produtos');
        $this->assertDatabaseHas('produtos', [
            'nome' => 'Produto Teste',
            'preco' => 25.90
        ]);
    }

    public function test_pode_visualizar_produto()
    {
        $produto = Produto::create([
            'nome' => 'Produto Teste',
            'descricao' => 'Descrição do produto teste',
            'preco' => 25.90,
            'categoria_id' => $this->categoria->id,
            'ativo' => true
        ]);

        $response = $this->get("/produtos/{$produto->id}");

        $response->assertStatus(200);
        $response->assertSee('Produto Teste');
        $response->assertSee('R$ 25,90');
    }

    public function test_pode_editar_produto()
    {
        $produto = Produto::create([
            'nome' => 'Produto Teste',
            'descricao' => 'Descrição do produto teste',
            'preco' => 25.90,
            'categoria_id' => $this->categoria->id,
            'ativo' => true
        ]);

        $response = $this->get("/produtos/{$produto->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Editar Produto');
        $response->assertSee('Produto Teste');
    }

    public function test_pode_atualizar_produto()
    {
        $produto = Produto::create([
            'nome' => 'Produto Teste',
            'descricao' => 'Descrição do produto teste',
            'preco' => 25.90,
            'categoria_id' => $this->categoria->id,
            'ativo' => true
        ]);

        $dadosAtualizados = [
            'nome' => 'Produto Teste Atualizado',
            'descricao' => 'Nova descrição',
            'preco' => 30.00,
            'categoria_id' => $this->categoria->id,
            'ativo' => false
        ];

        $response = $this->put("/produtos/{$produto->id}", $dadosAtualizados);

        $response->assertRedirect('/produtos');
        $this->assertDatabaseHas('produtos', [
            'id' => $produto->id,
            'nome' => 'Produto Teste Atualizado',
            'preco' => 30.00,
            'ativo' => false
        ]);
    }

    public function test_pode_excluir_produto()
    {
        $produto = Produto::create([
            'nome' => 'Produto Teste',
            'descricao' => 'Descrição do produto teste',
            'preco' => 25.90,
            'categoria_id' => $this->categoria->id,
            'ativo' => true
        ]);

        $response = $this->delete("/produtos/{$produto->id}");

        $response->assertRedirect('/produtos');
        $this->assertDatabaseMissing('produtos', [
            'id' => $produto->id
        ]);
    }

    public function test_validacao_nome_obrigatorio()
    {
        $dados = [
            'nome' => '',
            'preco' => 25.90,
            'categoria_id' => $this->categoria->id
        ];

        $response = $this->post('/produtos', $dados);

        $response->assertSessionHasErrors('nome');
    }

    public function test_validacao_preco_obrigatorio()
    {
        $dados = [
            'nome' => 'Produto Teste',
            'preco' => '',
            'categoria_id' => $this->categoria->id
        ];

        $response = $this->post('/produtos', $dados);

        $response->assertSessionHasErrors('preco');
    }

    public function test_validacao_categoria_obrigatoria()
    {
        $dados = [
            'nome' => 'Produto Teste',
            'preco' => 25.90,
            'categoria_id' => ''
        ];

        $response = $this->post('/produtos', $dados);

        $response->assertSessionHasErrors('categoria_id');
    }

    public function test_pode_alternar_status_produto()
    {
        $produto = Produto::create([
            'nome' => 'Produto Teste',
            'descricao' => 'Descrição do produto teste',
            'preco' => 25.90,
            'categoria_id' => $this->categoria->id,
            'ativo' => true
        ]);

        $response = $this->patch("/produtos/{$produto->id}/toggle-status");

        $response->assertRedirect();
        $produto->refresh();
        $this->assertFalse($produto->ativo);
    }
}
