<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pedidos = Pedido::with(['mesa', 'usuario', 'itens.produto'])->orderBy('created_at', 'desc')->get();
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($pedidos);
        }
        
        // Se for uma requisição web, retorna a view
        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Carregar mesas e usuários para o formulário
        $mesas = \App\Models\Mesa::orderBy('identificador')->get();
        $usuarios = \App\Models\Usuario::orderBy('nome')->get();
        
        return view('pedidos.create', compact('mesas', 'usuarios'));
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
            'mesa_id' => 'required|exists:mesas,id',
            'usuario_id' => 'required|exists:usuarios,id',
            'status' => 'required|string|in:pendente,em_preparo,pronto,entregue,cancelado'
        ], [
            'mesa_id.required' => 'A mesa é obrigatória.',
            'mesa_id.exists' => 'A mesa selecionada não existe.',
            'usuario_id.required' => 'O usuário (garçom) é obrigatório.',
            'usuario_id.exists' => 'O usuário selecionado não existe.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser: pendente, em_preparo, pronto, entregue ou cancelado.',
        ]);

        // O total será calculado automaticamente baseado nos itens
        $pedido = Pedido::create([
            'mesa_id' => $request->mesa_id,
            'usuario_id' => $request->usuario_id,
            'status' => $request->status,
            'total' => 0 // Será atualizado quando itens forem adicionados
        ]);
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($pedido->load(['mesa', 'usuario']), 201);
        }
        
        // Se for uma requisição web, redireciona
        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido criado com sucesso!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function show(Pedido $pedido)
    {
        $pedido->load(['mesa', 'usuario', 'itens.produto']);
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => true,
                'pedido' => $pedido
            ]);
        }
        
        // Se for uma requisição web, retorna a view
        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function edit(Pedido $pedido)
    {
        $mesas = \App\Models\Mesa::orderBy('identificador')->get();
        $usuarios = \App\Models\Usuario::orderBy('nome')->get();
        
        return view('pedidos.edit', compact('pedido', 'mesas', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'mesa_id' => 'sometimes|exists:mesas,id',
            'usuario_id' => 'sometimes|exists:usuarios,id',
            'total' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:pendente,em_preparo,pronto,entregue,cancelado'
        ], [
            'mesa_id.exists' => 'A mesa selecionada não existe.',
            'usuario_id.exists' => 'O usuário selecionado não existe.',
            'total.numeric' => 'O total deve ser um número.',
            'total.min' => 'O total não pode ser negativo.',
            'status.in' => 'O status deve ser: pendente, em_preparo, pronto, entregue ou cancelado.',
        ]);

        $pedido->update($request->all());
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($pedido->load(['mesa', 'usuario']));
        }
        
        // Se for uma requisição web, redireciona
        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        
        // Se for uma requisição API, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Pedido deletado com sucesso']);
        }
        
        // Se for uma requisição web, redireciona
        return redirect()->route('pedidos.index')->with('success', 'Pedido excluído com sucesso!');
    }
}
