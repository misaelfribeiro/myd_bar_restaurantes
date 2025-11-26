<?php
namespace App\Http\Controllers;
use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ProdutoController extends Controller
{
 public function index()
 {
 $produtos = Produto::with('categoria')
 ->orderBy('nome')
 ->get();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($produtos);
 }
 return view('produtos.index', compact('produtos'));
 }

 public function create()
 {
 $categorias = Categoria::orderBy('nome')->get();
 return view('produtos.create', compact('categorias'));
 }

 public function store(Request $request)
 {
 $request->validate([
 'nome' => 'required|string|max:255|unique:produtos,nome',
 'descricao' => 'nullable|string|max:1000',
 'preco' => 'required|numeric|min:0|max:9999.99',
 'categoria_id' => 'required|exists:categorias,id',
 'ativo' => 'sometimes|boolean'
 ], [
 'nome.required' => 'O nome do produto é obrigatório.',
 'nome.unique' => 'Já existe um produto com este nome.',
 'preco.required' => 'O preço é obrigatório.',
 'preco.numeric' => 'O preço deve ser um número válido.',
 'preco.min' => 'O preço não pode ser negativo.',
 'preco.max' => 'O preço máximo é R$ 9.999,99.',
 'categoria_id.required' => 'A categoria é obrigatória.',
 'categoria_id.exists' => 'A categoria selecionada não existe.'
 ]);
 DB::beginTransaction();
 try {
 $produto = Produto::create([
 'nome' => $request->nome,
 'descricao' => $request->descricao,
 'preco' => $request->preco,
 'categoria_id' => $request->categoria_id,
 'ativo' => $request->has('ativo') ? true : false
 ]);
 DB::commit();
 if ($request->expectsJson()) {
 return response()->json([
 'success' => true,
 'message' => 'Produto criado com sucesso!',
 'produto' => $produto->load('categoria')
 ], 201);
 }
 return redirect()->route('produtos.index')
 ->with('success', 'Produto criado com sucesso!');
 } catch (\Exception $e) {
 DB::rollback();
 if ($request->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao criar produto: ' . $e->getMessage()
 ], 500);
 }
 return redirect()->back()
 ->withInput()
 ->withErrors(['error' => 'Erro ao criar produto.']);
 }
 }
 public function show(Produto $produto)
 {
 $produto->load('categoria');
 if (request()->expectsJson()) {
 return response()->json([
 'success' => true,
 'produto' => $produto
 ]);
 }
 return view('produtos.show', compact('produto'));
 }
 public function edit(Produto $produto)
 {
 $categorias = Categoria::orderBy('nome')->get();
 return view('produtos.edit', compact('produto', 'categorias'));
 }
 public function update(Request $request, Produto $produto)
 {
 $request->validate([
 'nome' => 'required|string|max:255|unique:produtos,nome,' . $produto->id,
 'descricao' => 'nullable|string|max:1000',
 'preco' => 'required|numeric|min:0|max:9999.99',
 'categoria_id' => 'required|exists:categorias,id',
 'ativo' => 'sometimes|boolean'
 ], [
 'nome.required' => 'O nome do produto é obrigatório.',
 'nome.unique' => 'Já existe um produto com este nome.',
 'preco.required' => 'O preço é obrigatório.',
 'preco.numeric' => 'O preço deve ser um número válido.',
 'preco.min' => 'O preço não pode ser negativo.',
 'preco.max' => 'O preço máximo é R$ 9.999,99.',
 'categoria_id.required' => 'A categoria é obrigatória.',
 'categoria_id.exists' => 'A categoria selecionada não existe.'
 ]);
 DB::beginTransaction();
 try {
 $produto->update([
 'nome' => $request->nome,
 'descricao' => $request->descricao,
 'preco' => $request->preco,
 'categoria_id' => $request->categoria_id,
 'ativo' => $request->has('ativo') ? true : false
 ]);
 DB::commit();
 if ($request->expectsJson()) {
 return response()->json([
 'success' => true,
 'message' => 'Produto atualizado com sucesso!',
 'produto' => $produto->load('categoria')
 ]);
 }
 return redirect()->route('produtos.index')
 ->with('success', 'Produto atualizado com sucesso!');
 } catch (\Exception $e) {
 DB::rollback();
 if ($request->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao atualizar produto: ' . $e->getMessage()
 ], 500);
 }
 return redirect()->back()
 ->withInput()
 ->withErrors(['error' => 'Erro ao atualizar produto.']);
 }
 }
 public function destroy(Produto $produto)
 {
 DB::beginTransaction();
 try {
 $itensCount = $produto->itens()->count();
 if ($itensCount > 0) {
 if (request()->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Não é possível excluir este produto pois ele está sendo usado em pedidos.'
 ], 400);
 }
 return redirect()->back()
 ->withErrors(['error' => 'Não é possível excluir este produto pois ele está sendo usado em pedidos.']);
 }
 $produto->delete();
 DB::commit();
 if (request()->expectsJson()) {
 return response()->json([
 'success' => true,
 'message' => 'Produto excluído com sucesso!'
 ]);
 }
 return redirect()->route('produtos.index')
 ->with('success', 'Produto excluído com sucesso!');
 } catch (\Exception $e) {
 DB::rollback();
 if (request()->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao excluir produto: ' . $e->getMessage()
 ], 500);
 }
 return redirect()->back()
 ->withErrors(['error' => 'Erro ao excluir produto.']);
 }
 }
 public function webIndex()
 {
 $produtos = Produto::with('categoria')
 ->orderBy('nome')
 ->get();
 return view('produtos.index', compact('produtos'));
 }
 public function toggleStatus(Produto $produto)
 {
 try {
 $produto->update(['ativo' => !$produto->ativo]);
 $status = $produto->ativo ? 'ativado' : 'desativado';
 if (request()->expectsJson()) {
 return response()->json([
 'success' => true,
 'message' => "Produto {$status} com sucesso!",
 'produto' => $produto->load('categoria')
 ]);
 }
 return redirect()->back()
 ->with('success', "Produto {$status} com sucesso!");
 } catch (\Exception $e) {
 if (request()->expectsJson()) {
 return response()->json([
 'success' => false,
 'message' => 'Erro ao alterar status do produto.'
 ], 500);
 }
 return redirect()->back()
 ->withErrors(['error' => 'Erro ao alterar status do produto.']);
 }
 }
 public function getCacheData()
 {
 $produtos = Produto::with('categoria')
 ->where('disponivel', true)
 ->get();
 return response()->json([
 'success' => true,
 'produtos' => $produtos
 ]);
 }
 
 /**
  * API para App Cliente - Lista produtos com filtros
  */
 public function appProdutos(Request $request)
 {
     $query = Produto::with('categoria')
         ->where('ativo', true);
     
     // Filtrar por tenant_code (query string ou header)
     if ($request->has('tenant_code')) {
         $query->where('tenant_code', $request->tenant_code);
     } elseif ($request->header('X-Tenant-Code')) {
         $query->where('tenant_code', $request->header('X-Tenant-Code'));
     }
     
     if ($request->has('categoria_id')) {
         $query->where('categoria_id', $request->categoria_id);
     }
     
     if ($request->has('search')) {
         $search = $request->search;
         $query->where(function($q) use ($search) {
             $q->where('nome', 'like', "%{$search}%")
               ->orWhere('descricao', 'like', "%{$search}%");
         });
     }
     
     $limit = $request->input('limit', 20);
     $produtos = $query->orderBy('nome')->limit($limit)->get();
     
     return response()->json([
         'success' => true,
         'data' => $produtos
     ]);
 }
 
 /**
  * API para App Cliente - Produtos em destaque
  */
 public function destaques(Request $request)
 {
     $produtos = Produto::with('categoria')
         ->where('ativo', true)
         ->where('destaque', true)
         ->where('tenant_code', $request->header('X-Tenant-Code', 'RESTAURANTE0001'))
         ->orderBy('nome')
         ->limit(6)
         ->get();
     
     return response()->json([
         'success' => true,
         'data' => $produtos
     ]);
 }
}
