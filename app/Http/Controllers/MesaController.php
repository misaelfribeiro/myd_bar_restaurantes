<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mesas = Mesa::with('pedidos')->get();
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($mesas);
        }
        
        // Se for uma requisição web, retorna a view
        return view('mesas.index', compact('mesas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('mesas.create');
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
            'identificador' => 'required|string|max:255|unique:mesas',
            'lugares' => 'required|integer|min:1'
        ], [
            'identificador.required' => 'O identificador da mesa é obrigatório.',
            'identificador.unique' => 'Já existe uma mesa com este identificador.',
            'lugares.required' => 'O número de lugares é obrigatório.',
            'lugares.min' => 'A mesa deve ter pelo menos 1 lugar.',
        ]);

        $mesa = Mesa::create($request->all());
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($mesa, 201);
        }
        
        // Se for uma requisição web, redireciona
        return redirect()->route('mesas.index')->with('success', 'Mesa criada com sucesso!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function show(Mesa $mesa)
    {
        $mesa->load('pedidos');
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($mesa);
        }
        
        // Se for uma requisição web, retorna a view
        return view('mesas.show', compact('mesa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function edit(Mesa $mesa)
    {
        return view('mesas.edit', compact('mesa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mesa $mesa)
    {
        $request->validate([
            'identificador' => 'sometimes|string|max:255|unique:mesas,identificador,' . $mesa->id,
            'lugares' => 'sometimes|integer|min:1'
        ], [
            'identificador.unique' => 'Já existe uma mesa com este identificador.',
            'lugares.min' => 'A mesa deve ter pelo menos 1 lugar.',
        ]);

        $mesa->update($request->all());
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($mesa);
        }
        
        // Se for uma requisição web, redireciona
        return redirect()->route('mesas.index')->with('success', 'Mesa atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mesa  $mesa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mesa $mesa)
    {
        $mesa->delete();
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Mesa deletada com sucesso']);
        }
        
        // Se for uma requisição web, redireciona
        return redirect()->route('mesas.index')->with('success', 'Mesa excluída com sucesso!');
    }
}
