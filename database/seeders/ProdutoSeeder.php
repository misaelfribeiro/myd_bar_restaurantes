<?php
namespace Database\Seeders;
use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Database\Seeder;
class ProdutoSeeder extends Seeder
{
 public function run()
 {
 $bebidas = Categoria::where('nome', 'Bebidas')->first()->id;
 $pratos = Categoria::where('nome', 'Pratos Principais')->first()->id;
 $sobremesas = Categoria::where('nome', 'Sobremesas')->first()->id;
 $petiscos = Categoria::where('nome', 'Petiscos')->first()->id;
 $drinks = Categoria::where('nome', 'Drinks')->first()->id;
 $produtos = [
 [
 'nome' => 'Coca-Cola 350ml',
 'descricao' => 'Refrigerante gelado',
 'preco' => 5.50,
 'categoria_id' => $bebidas,
 'ativo' => true
 ],
 [
 'nome' => 'Suco de Laranja',
 'descricao' => 'Suco natural da fruta',
 'preco' => 8.00,
 'categoria_id' => $bebidas,
 'ativo' => true
 ],
 [
 'nome' => 'Água Mineral 500ml',
 'descricao' => 'Água sem gás gelada',
 'preco' => 3.00,
 'categoria_id' => $bebidas,
 'ativo' => true
 ],
 [
 'nome' => 'Cerveja Heineken',
 'descricao' => 'Cerveja long neck gelada',
 'preco' => 8.50,
 'categoria_id' => $bebidas,
 'ativo' => true
 ],
 [
 'nome' => 'Hambúrguer Artesanal',
 'descricao' => 'Pão artesanal, carne 150g, queijo, alface e tomate',
 'preco' => 25.90,
 'categoria_id' => $pratos,
 'ativo' => true
 ],
 [
 'nome' => 'Pizza Margherita',
 'descricao' => 'Molho de tomate, mussarela e manjericão',
 'preco' => 32.50,
 'categoria_id' => $pratos,
 'ativo' => true
 ],
 [
 'nome' => 'Filé à Parmegiana',
 'descricao' => 'Filé empanado com molho e queijo, acompanha arroz e batata',
 'preco' => 45.00,
 'categoria_id' => $pratos,
 'ativo' => true
 ],
 [
 'nome' => 'Lasanha Bolonhesa',
 'descricao' => 'Massa, molho bolonhesa e queijo',
 'preco' => 28.90,
 'categoria_id' => $pratos,
 'ativo' => true
 ],
 [
 'nome' => 'Batata Frita',
 'descricao' => 'Porção de batata frita crocante',
 'preco' => 12.00,
 'categoria_id' => $petiscos,
 'ativo' => true
 ],
 [
 'nome' => 'Salada Caesar',
 'descricao' => 'Alface, croutons, parmesão e molho caesar',
 'preco' => 18.50,
 'categoria_id' => $petiscos,
 'ativo' => true
 ],
 [
 'nome' => 'Porção de Mandioca',
 'descricao' => 'Mandioca frita com alho',
 'preco' => 14.00,
 'categoria_id' => $petiscos,
 'ativo' => true
 ],
 [
 'nome' => 'Pudim de Leite',
 'descricao' => 'Pudim caseiro com calda de caramelo',
 'preco' => 9.90,
 'categoria_id' => $sobremesas,
 'ativo' => true
 ],
 [
 'nome' => 'Brigadeiro Gourmet',
 'descricao' => 'Brigadeiro artesanal (unidade)',
 'preco' => 4.50,
 'categoria_id' => $sobremesas,
 'ativo' => true
 ],
 [
 'nome' => 'Torta de Chocolate',
 'descricao' => 'Fatia de torta de chocolate com cobertura',
 'preco' => 12.90,
 'categoria_id' => $sobremesas,
 'ativo' => true
 ],
 [
 'nome' => 'Caipirinha',
 'descricao' => 'Cachaça, limão e açúcar',
 'preco' => 15.00,
 'categoria_id' => $drinks,
 'ativo' => true
 ],
 [
 'nome' => 'Mojito',
 'descricao' => 'Rum, hortelã, limão e açúcar',
 'preco' => 18.50,
 'categoria_id' => $drinks,
 'ativo' => true
 ]
 ];
 foreach ($produtos as $produto) {
 Produto::create($produto);
 }
 }
}