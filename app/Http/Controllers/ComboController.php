<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComboController extends Controller
{
    public function index()
    {
        $combos = Combo::with('produtos')->orderBy('nome')->get();
        
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($combos);
        }
        
        return view('combos.index', compact('combos'));
    }

    public function create()
    {
        $produtos = Produto::where('ativo', true)->orderBy('nome')->get();
        return view('combos.create', compact('produtos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco_combo' => 'required|numeric|min:0.01',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'produtos' => 'required|array|min:1',
            'produtos.*.id' => 'required|exists:produtos,id',
            'produtos.*.quantidade' => 'required|integer|min:1'
        ], [
            'nome.required' => 'O nome do combo é obrigatório',
            'preco_combo.required' => 'O preço do combo é obrigatório',
            'preco_combo.min' => 'O preço do combo deve ser maior que zero',
            'imagem.image' => 'O arquivo deve ser uma imagem',
            'imagem.mimes' => 'A imagem deve ser JPG, PNG ou GIF',
            'imagem.max' => 'A imagem não pode ser maior que 2MB',
            'produtos.required' => 'Adicione pelo menos um produto ao combo',
            'produtos.min' => 'Adicione pelo menos um produto ao combo',
        ]);

        // Calcula preço original
        $precoOriginal = 0;
        foreach ($validated['produtos'] as $prod) {
            $produto = Produto::find($prod['id']);
            $precoOriginal += $produto->preco * $prod['quantidade'];
        }

        // Valida se o preço do combo é menor que o original
        if ($validated['preco_combo'] >= $precoOriginal) {
            return back()->withErrors(['preco_combo' => 'O preço do combo deve ser menor que o valor original (R$ ' . number_format($precoOriginal, 2, ',', '.') . ')'])->withInput();
        }

        $combo = Combo::create([
            'nome' => $validated['nome'],
            'descricao' => $validated['descricao'],
            'preco_original' => $precoOriginal,
            'preco_combo' => $validated['preco_combo'],
            'ativo' => true,
            'destaque' => $request->has('destaque'),
            'tenant_code' => auth('admin')->check() ? auth('admin')->user()->tenant_code : auth()->user()->tenant_code
        ]);

        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('combos', 'public');
            $combo->update(['imagem' => $path]);
        }

        foreach ($validated['produtos'] as $prod) {
            $combo->produtos()->attach($prod['id'], ['quantidade' => $prod['quantidade']]);
        }

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($combo->load('produtos'), 201);
        }

        return redirect()->route('combos.index')->with('success', 'Combo criado com sucesso!');
    }

    public function show($id)
    {
        $combo = Combo::with('produtos')->findOrFail($id);
        
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => true,
                'combo' => $combo
            ]);
        }
        
        return view('combos.show', compact('combo'));
    }

    public function edit(Combo $combo)
    {
        $combo->load('produtos');
        $produtos = Produto::where('ativo', true)->orderBy('nome')->get();
        return view('combos.edit', compact('combo', 'produtos'));
    }

    public function update(Request $request, Combo $combo)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco_combo' => 'required|numeric|min:0.01',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'produtos' => 'required|array|min:1',
            'produtos.*.id' => 'required|exists:produtos,id',
            'produtos.*.quantidade' => 'required|integer|min:1',
            'ativo' => 'nullable|boolean'
        ], [
            'nome.required' => 'O nome do combo é obrigatório',
            'preco_combo.required' => 'O preço do combo é obrigatório',
            'preco_combo.min' => 'O preço do combo deve ser maior que zero',
            'imagem.image' => 'O arquivo deve ser uma imagem',
            'imagem.mimes' => 'A imagem deve ser JPG, PNG ou GIF',
            'imagem.max' => 'A imagem não pode ser maior que 2MB',
            'produtos.required' => 'Adicione pelo menos um produto ao combo',
            'produtos.min' => 'Adicione pelo menos um produto ao combo',
        ]);

        // Calcula preço original
        $precoOriginal = 0;
        foreach ($validated['produtos'] as $prod) {
            $produto = Produto::find($prod['id']);
            $precoOriginal += $produto->preco * $prod['quantidade'];
        }

        // Valida se o preço do combo é menor que o original
        if ($validated['preco_combo'] >= $precoOriginal) {
            return back()->withErrors(['preco_combo' => 'O preço do combo deve ser menor que o valor original (R$ ' . number_format($precoOriginal, 2, ',', '.') . ')'])->withInput();
        }

        $combo->update([
            'nome' => $validated['nome'],
            'descricao' => $validated['descricao'],
            'preco_original' => $precoOriginal,
            'preco_combo' => $validated['preco_combo'],
            'ativo' => $validated['ativo'] ?? $combo->ativo,
            'destaque' => $request->has('destaque')
        ]);

        if ($request->hasFile('imagem')) {
            if ($combo->imagem) {
                Storage::disk('public')->delete($combo->imagem);
            }
            $path = $request->file('imagem')->store('combos', 'public');
            $combo->update(['imagem' => $path]);
        }

        $combo->produtos()->detach();
        foreach ($validated['produtos'] as $prod) {
            $combo->produtos()->attach($prod['id'], ['quantidade' => $prod['quantidade']]);
        }

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($combo->load('produtos'));
        }

        return redirect()->route('combos.index')->with('success', 'Combo atualizado com sucesso!');
    }

    public function destroy(Combo $combo)
    {
        if ($combo->imagem) {
            Storage::disk('public')->delete($combo->imagem);
        }
        
        $combo->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['success' => true, 'message' => 'Combo excluído']);
        }

        return redirect()->route('combos.index')->with('success', 'Combo excluído com sucesso!');
    }

    public function toggleStatus(Combo $combo)
    {
        $combo->update(['ativo' => !$combo->ativo]);
        
        return response()->json([
            'success' => true,
            'ativo' => $combo->ativo
        ]);
    }
 
 /**
  * API para App Cliente - Lista combos ativos
  * Retorna 6 combos aleatórios de todos os restaurantes
  * Combos em destaque aparecem 3x mais (maior chance de serem sorteados)
  */
 public function appCombos(Request $request)
 {
     // Busca combos em destaque (aparecem 3x mais)
     $combosDestaque = Combo::with(['produtos', 'tenant'])
         ->where('ativo', true)
         ->where('destaque', true)
         ->get();
     
     // Busca combos normais
     $combosNormais = Combo::with(['produtos', 'tenant'])
         ->where('ativo', true)
         ->where('destaque', false)
         ->get();
     
     // Monta pool: cada combo em destaque aparece 3x
     $pool = collect();
     foreach ($combosDestaque as $combo) {
         $pool->push($combo, $combo, $combo); // Adiciona 3x
     }
     $pool = $pool->merge($combosNormais);
     
     // Embaralha e pega 6 únicos
     $combosSelecionados = $pool->shuffle()->take(20)->unique('id')->take(6)->values();
     
     return response()->json([
         'success' => true,
         'data' => $combosSelecionados
     ]);
 }
}
