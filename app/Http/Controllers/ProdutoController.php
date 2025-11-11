<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = Produto::with('categoria')
                          ->orderBy('nome')
                          ->get();
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => true,
                'produtos' => $produtos
            ]);
        }
        
        // Se for uma requisição web, retorna a view
        return view('produtos.index', compact('produtos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nome')->get();
        
        return view('produtos.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produto $produto)
    {
        $categorias = Categoria::orderBy('nome')->get();
        
        return view('produtos.edit', compact('produto', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        DB::beginTransaction();
        
        try {
            // Verificar se o produto está sendo usado em pedidos
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

    /**
     * Show the listing page for web interface
     */
    public function webIndex()
    {
        $produtos = Produto::with('categoria')
                          ->orderBy('nome')
                          ->get();
        
        return view('produtos.index', compact('produtos'));
    }

    /**
     * Toggle product active status
     */
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
}
