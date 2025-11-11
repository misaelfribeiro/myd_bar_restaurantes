<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorias = Categoria::with('produtos')->get();
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($categorias);
        }
        
        // Se for uma requisição web, retorna a view
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:categorias',
            'descricao' => 'nullable|string|max:500'
        ], [
            'nome.required' => 'O nome da categoria é obrigatório.',
            'nome.unique' => 'Já existe uma categoria com este nome.',
            'nome.max' => 'O nome não pode ter mais que 255 caracteres.',
            'descricao.max' => 'A descrição não pode ter mais que 500 caracteres.'
        ]);

        $categoria = Categoria::create($request->all());
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($categoria, 201);
        }
        
        // Se for uma requisição web, redireciona
        return redirect()->route('categorias.index')->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function show(Categoria $categoria)
    {
        $categoria->load('produtos');
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($categoria);
        }
        
        // Se for uma requisição web, retorna a view
        return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nome' => 'sometimes|string|max:255|unique:categorias,nome,' . $categoria->id,
            'descricao' => 'nullable|string|max:500'
        ], [
            'nome.unique' => 'Já existe uma categoria com este nome.',
            'nome.max' => 'O nome não pode ter mais que 255 caracteres.',
            'descricao.max' => 'A descrição não pode ter mais que 500 caracteres.'
        ]);

        $categoria->update($request->all());
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($categoria);
        }
        
        // Se for uma requisição web, redireciona
        return redirect()->route('categorias.show', $categoria)->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function destroy(Categoria $categoria)
    {
        // Verificar se a categoria tem produtos
        if ($categoria->produtos()->count() > 0) {
            if (request()->expectsJson() || request()->is('api/*')) {
                return response()->json([
                    'error' => 'Não é possível excluir uma categoria que possui produtos vinculados.'
                ], 422);
            }
            
            return redirect()->route('categorias.index')->with('error', 'Não é possível excluir uma categoria que possui produtos vinculados.');
        }
        
        $categoria->delete();
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Categoria deletada com sucesso']);
        }
        
        // Se for uma requisição web, redireciona
        return redirect()->route('categorias.index')->with('success', 'Categoria excluída com sucesso!');
    }
}
