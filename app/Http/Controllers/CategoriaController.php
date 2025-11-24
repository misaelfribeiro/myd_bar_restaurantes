<?php
namespace App\Http\Controllers;
use App\Models\Categoria;
use Illuminate\Http\Request;
class CategoriaController extends Controller
{
 public function index()
 {
 $categorias = Categoria::with('produtos')->get();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($categorias);
 }
 return view('categorias.index', compact('categorias'));
 }

 public function create()
 {
 return view('categorias.create');
 }
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
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($categoria, 201);
 }
 return redirect()->route('categorias.index')->with('success', 'Categoria criada com sucesso!');
 }

 public function show(Categoria $categoria)
 {
 $categoria->load('produtos');
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($categoria);
 }
 return view('categorias.show', compact('categoria'));
 }

 public function edit(Categoria $categoria)
 {
 return view('categorias.edit', compact('categoria'));
 }
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
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json($categoria);
 }
 return redirect()->route('categorias.index')->with('success', 'Categoria atualizada com sucesso!');
 }

 public function destroy(Categoria $categoria)
 {
 if ($categoria->produtos()->count() > 0) {
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json(['error' => 'Não é possível excluir categoria com produtos vinculados.'], 400);
 }
 return redirect()->route('categorias.index')->with('error', 'Não é possível excluir categoria com produtos vinculados.');
 }
 $categoria->delete();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json(['message' => 'Categoria excluída com sucesso!']);
 }
 return redirect()->route('categorias.index')->with('success', 'Categoria excluída com sucesso!');
 }

 public function getCacheData()
 {
 $categorias = Categoria::all();
 return response()->json([
 'success' => true,
 'categorias' => $categorias
 ]);
 }
}