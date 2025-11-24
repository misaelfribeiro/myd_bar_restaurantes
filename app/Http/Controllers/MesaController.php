<?php
namespace App\Http\Controllers;
use App\Models\Mesa;
use Illuminate\Http\Request;
class MesaController extends Controller
{
 public function index()
 {
 $mesas = Mesa::with('pedidos')->get();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($mesas);
 }
 return view('mesas.index', compact('mesas'));
 }

 public function create()
 {
 return view('mesas.create');
 }
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
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($mesa, 201);
 }
 return redirect()->route('mesas.index')->with('success', 'Mesa criada com sucesso!');
 }

 public function show(Mesa $mesa)
 {
 $mesa->load('pedidos');
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($mesa);
 }
 return view('mesas.show', compact('mesa'));
 }

 public function edit(Mesa $mesa)
 {
 return view('mesas.edit', compact('mesa'));
 }
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
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($mesa);
 }
 return redirect()->route('mesas.index')->with('success', 'Mesa atualizada com sucesso!');
 }

 public function destroy(Mesa $mesa)
 {
 $mesa->delete();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json(['message' => 'Mesa excluída com sucesso!']);
 }
 return redirect()->route('mesas.index')->with('success', 'Mesa excluída com sucesso!');
 }

 public function getCacheData()
 {
 $mesas = Mesa::all();
 return response()->json([
 'success' => true,
 'mesas' => $mesas
 ]);
 }
 public function stats()
 {
 $totalMesas = Mesa::count();
 $mesasOcupadas = Mesa::whereHas('pedidos', function($query) { 
 $query->whereIn('status', ['aberto', 'entregue']); 
 })->count();
 $mesasLivres = $totalMesas - $mesasOcupadas;
 $mesasComStatus = Mesa::with(['pedidos' => function($query) { 
 $query->whereIn('status', ['aberto', 'entregue'])->latest(); 
 }])->get()->map(function($mesa) {
 return [
 'id' => $mesa->id,
 'identificador' => $mesa->identificador,
 'lugares' => $mesa->lugares,
 'ocupada' => $mesa->pedidos->count() > 0,
 'pedido_id' => $mesa->pedidos->count() > 0 ? $mesa->pedidos->first()->id : null
 ];
 });
 return response()->json([
 'success' => true,
 'total_mesas' => $totalMesas,
 'mesas_livres' => $mesasLivres,
 'mesas_ocupadas' => $mesasOcupadas,
 'ocupacao_percentual' => $totalMesas > 0 ? round(($mesasOcupadas / $totalMesas) * 100, 1) : 0,
 'mesas' => $mesasComStatus
 ]);
 }
}