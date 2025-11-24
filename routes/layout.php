<?php
use Illuminate\Support\Facades\Route;
use App\Models\Categoria;
use App\Models\Produto;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\User;
Route::prefix('modern')->name('modern.')->group(function () {
 Route::get('/dashboard', function () {
 return view('dashboard-main');
 })->name('dashboard');
 Route::get('/categorias', function () {
 $categorias = Categoria::with('produtos')->get();
 return view('categorias.index-layout', compact('categorias'));
 })->name('categorias');
 Route::get('/produtos', function () {
 $produtos = Produto::with('categoria')->get();
 $categorias = Categoria::all();
 return view('produtos.index-layout', compact('produtos', 'categorias'));
 })->name('produtos');
 Route::get('/mesas', function () {
 $mesas = Mesa::with(['pedidos' => function($query) {
 $query->whereIn('status', ['pendente', 'em_preparo', 'pronto']);
 }])->get();
 return view('mesas.index-layout', compact('mesas'));
 })->name('mesas');
 Route::get('/pedidos', function () {
 $pedidos = Pedido::with(['mesa', 'itens.produto', 'usuario'])
 ->orderBy('created_at', 'desc')
 ->get();
 $mesas = Mesa::all();
 return view('pedidos.index-layout', compact('pedidos', 'mesas'));
 })->name('pedidos');
 Route::get('/usuarios', function () {
 $usuarios = User::orderBy('created_at', 'desc')->get();
 return view('users.index-layout', compact('usuarios'));
 })->name('usuarios');
 Route::get('/caixa', function () {
 $pedidosPendentes = Pedido::with(['mesa', 'itens.produto'])
 ->where('status', 'entregue')
 ->orWhere('status', 'pronto')
 ->get();
 $resumo = [
 'receita_total' => $pedidosPendentes->sum('valor_total'),
 'total_pedidos' => $pedidosPendentes->count(),
 'crescimento' => 15.5,
 'formas_pagamento' => [
 'dinheiro' => ['quantidade' => 12, 'valor' => 450.00, 'percentual' => 35],
 'cartao_debito' => ['quantidade' => 8, 'valor' => 320.00, 'percentual' => 25],
 'cartao_credito' => ['quantidade' => 10, 'valor' => 380.00, 'percentual' => 30],
 'pix' => ['quantidade' => 4, 'valor' => 130.00, 'percentual' => 10]
 ],
 'forma_mais_usada' => 'Dinheiro'
 ];
 $ultimosPagamentos = collect([]);
 return view('caixa.dashboard-layout', compact(
 'pedidosPendentes',
 'resumo', 
 'ultimosPagamentos'
 ));
 })->name('caixa');
});