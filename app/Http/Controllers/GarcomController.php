<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\Usuario;
use App\Models\ItemPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GarcomController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id() ?? 1;
        
        $meusPedidosHoje = Pedido::where('usuario_id', $userId)->whereDate('created_at', today())->count();
        $minhaVendaHoje = Pedido::where('usuario_id', $userId)->whereDate('created_at', today())->sum('total');
        $mesasDisponiveis = Mesa::count();
        $mesasOcupadas = Mesa::whereHas('pedidos', function($query) { $query->where('status', 'aberto'); })->count();
        $pedidosAbertos = Pedido::where('status', 'aberto')->count();
          $ultimosPedidos = Pedido::with(['mesa', 'itens.produto'])->where('usuario_id', $userId)->latest()->limit(5)->get();
        $mesasOcupadasInfo = Mesa::with(['pedidos' => function($query) { $query->where('status', 'aberto')->latest(); }])->whereHas('pedidos', function($query) { $query->where('status', 'aberto'); })->get();
        $mesasDisponiveisInfo = Mesa::whereDoesntHave('pedidos', function($query) { $query->where('status', 'aberto'); })->limit(6)->get();
        $user = (object) ['nome' => 'Garçom Demo'];
        
        return view('garcom.dashboard', compact('meusPedidosHoje', 'minhaVendaHoje', 'mesasDisponiveis', 'mesasOcupadas', 'pedidosAbertos', 'ultimosPedidos', 'mesasOcupadasInfo', 'mesasDisponiveisInfo', 'user'));
    }

    public function cardapio(Request $request)
    {
        $categorias = Categoria::with(['produtos' => function($query) { $query->where('ativo', true)->orderBy('nome'); }])->get();
        
        $produtosBusca = [];
        if ($request->has('busca') && !empty($request->busca)) {
            $produtosBusca = Produto::where('nome', 'like', '%' . $request->busca . '%')->where('ativo', true)->with('categoria')->get();
        }
        
        return view('garcom.cardapio', compact('categorias', 'produtosBusca'));
    }

    public function mesas()
    {
        $mesas = Mesa::with(['pedidos' => function($query) { $query->where('status', 'aberto')->with('usuario'); }])->orderBy('identificador')->get();
        return view('garcom.mesas', compact('mesas'));
    }

    public function buscarProdutos(Request $request)
    {
        $term = $request->get('q', '');
        
        if (empty($term)) {
            return response()->json([]);
        }
        
        $produtos = Produto::where('ativo', true)
            ->where(function($query) use ($term) {
                $query->where('nome', 'like', '%' . $term . '%')
                      ->orWhere('codigo', 'like', '%' . $term . '%');
            })
            ->with('categoria')
            ->limit(20)
            ->get()
            ->map(function($produto) {
                return [
                    'id' => $produto->id,
                    'nome' => $produto->nome,
                    'codigo' => $produto->codigo,
                    'preco' => $produto->preco,
                    'categoria' => $produto->categoria->nome,
                    'tipo_preparo' => $produto->tipo_preparo,
                    'descricao' => $produto->descricao
                ];
            });
            
        return response()->json($produtos);
    }

    public function criarPedidoRapido()
    {
        // Buscar mesas com informações sobre pedidos abertos
        $mesas = Mesa::with(['pedidos' => function($query) { 
            $query->where('status', 'aberto')->with('usuario'); 
        }])->orderBy('identificador')->get();
        
        // Marcar cada mesa como ocupada ou livre
        $mesas->each(function($mesa) {
            $mesa->ocupada = $mesa->pedidos->where('status', 'aberto')->count() > 0;
            $mesa->pedido_atual = $mesa->pedidos->where('status', 'aberto')->first();
        });
        
        $categorias = Categoria::with(['produtos' => function($query) { $query->where('ativo', true)->orderBy('nome'); }])->get();
        return view('garcom.pedido-rapido', compact('mesas', 'categorias'));
    }

    public function meusPedidos(Request $request)
    {
        $userId = Auth::id() ?? 1;
        $query = Pedido::with(['mesa', 'itens.produto'])->where('usuario_id', $userId);
        
        if ($request->has('data') && !empty($request->data)) {
            $query->whereDate('created_at', $request->data);
        } else {
            $query->whereDate('created_at', today());
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        $pedidos = $query->latest()->paginate(10);
        
        $estatisticas = [
            'total_pedidos' => Pedido::where('usuario_id', $userId)->whereDate('created_at', today())->count(),
            'valor_total' => Pedido::where('usuario_id', $userId)->whereDate('created_at', today())->sum('total'),
            'pedidos_abertos' => Pedido::where('usuario_id', $userId)->where('status', 'aberto')->count(),
            'pedidos_finalizados' => Pedido::where('usuario_id', $userId)->whereDate('created_at', today())->where('status', 'finalizado')->count()
        ];
        
        return view('garcom.meus-pedidos', compact('pedidos', 'estatisticas'));
    }

    public function storePedidoRapido(Request $request)
    {
        // Log para debug
        Log::info('=== INÍCIO CRIAÇÃO PEDIDO ===');
        Log::info('Request data:', $request->all());
        Log::info('Content-Type:', [$request->header('Content-Type')]);
        Log::info('User ID:', [Auth::id()]);
        
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();
            $userId = Auth::id() ?? 1;
            
            // Verificar se a mesa já tem pedidos abertos
            $pedidosAbertos = Pedido::where('mesa_id', $request->mesa_id)
                                   ->where('status', 'aberto')
                                   ->count();
            
            if ($pedidosAbertos > 0) {
                Log::warning('Tentativa de criar pedido em mesa ocupada', [
                    'mesa_id' => $request->mesa_id,
                    'pedidos_abertos' => $pedidosAbertos
                ]);
                
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Esta mesa já possui um pedido em andamento. Finalize o pedido atual ou adicione itens ao pedido existente.'
                    ], 422);
                }
                
                return back()->with('error', 'Esta mesa já possui um pedido em andamento.');
            }
            
            $total = 0;
            foreach ($request->itens as $item) {
                $produto = Produto::find($item['produto_id']);
                $total += $produto->preco * $item['quantidade'];
            }
            
            $pedido = Pedido::create([
                'usuario_id' => $userId,
                'mesa_id' => $request->mesa_id,
                'total' => $total,
                'status' => 'aberto',
                'observacoes' => $request->observacoes ?? ''
            ]);
              foreach ($request->itens as $item) {
                $produto = Produto::find($item['produto_id']);
                ItemPedido::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $produto->preco,
                    'subtotal' => $produto->preco * $item['quantidade'],
                    'observacoes' => $item['observacoes'] ?? ''
                ]);
            }
            
            DB::commit();
            
            Log::info('Pedido criado com sucesso!', ['pedido_id' => $pedido->id]);
            
            // Se for requisição AJAX, retornar JSON
            if ($request->expectsJson() || $request->ajax()) {
                Log::info('Retornando JSON response');
                return response()->json([
                    'success' => true,
                    'message' => 'Pedido criado com sucesso!',
                    'pedido_id' => $pedido->id
                ]);
            }
            
            return redirect()->route('garcom.dashboard')->with('success', 'Pedido criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Erro ao criar pedido:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Se for requisição AJAX, retornar JSON de erro
            if ($request->expectsJson() || $request->ajax()) {
                Log::info('Retornando JSON error response');
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar pedido: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->with('error', 'Erro ao criar pedido: ' . $e->getMessage());
        }
    }

    public function finalizarMesa(Request $request, $mesaId)
    {
        try {
            DB::beginTransaction();
            $pedidos = Pedido::where('mesa_id', $mesaId)->where('status', 'aberto')->get();
            foreach ($pedidos as $pedido) {
                $pedido->update(['status' => 'finalizado']);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Mesa finalizada com sucesso!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }    }

    public function verPedido(Pedido $pedido)
    {
        $pedido->load(['mesa', 'usuario', 'itens.produto']);
        
        return view('garcom.pedido', compact('pedido'));
    }

    public function atualizarStatusPedido(Request $request, Pedido $pedido)
    {
        try {
            $status = $request->input('status');
            
            if (!in_array($status, ['aberto', 'finalizado', 'cancelado'])) {
                return response()->json(['success' => false, 'message' => 'Status inválido']);
            }
            
            $pedido->update(['status' => $status]);
            
            return response()->json([
                'success' => true, 
                'message' => "Pedido {$status} com sucesso!"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ]);
        }
    }

    public function adicionarItensPedido(Request $request)
    {
        $mesaId = $request->get('mesa');
        $pedidoId = $request->get('pedido');
        
        // Verificar se a mesa e pedido existem
        $mesa = Mesa::findOrFail($mesaId);
        $pedido = Pedido::findOrFail($pedidoId);
        
        // Verificar se o pedido está aberto
        if ($pedido->status !== 'aberto') {
            return redirect()->route('garcom.mesas')->with('error', 'Este pedido já foi finalizado.');
        }
        
        // Carregar categorias e produtos
        $categorias = Categoria::with(['produtos' => function($query) { 
            $query->where('ativo', true)->orderBy('nome');
        }])->get();
        
        // Carregar todas as mesas para o seletor (mas a mesa atual estará pré-selecionada)
        $mesas = Mesa::orderBy('identificador')->get();
        
        return view('garcom.adicionar-itens', compact('categorias', 'mesas', 'mesa', 'pedido'));
    }

    public function storeItensPedido(Request $request)
    {
        try {
            Log::info('🛒 Recebendo adição de itens ao pedido:', $request->all());
            
            $request->validate([
                'pedido_id' => 'required|exists:pedidos,id',
                'itens' => 'required|array',
                'itens.*.produto_id' => 'required|exists:produtos,id',
                'itens.*.quantidade' => 'required|integer|min:1|max:10'
            ]);
            
            $pedido = Pedido::findOrFail($request->pedido_id);
            
            // Verificar se o pedido ainda está aberto
            if ($pedido->status !== 'aberto') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido já foi finalizado e não pode ser modificado.'
                ], 422);
            }
            
            DB::beginTransaction();
            
            $totalAdicional = 0;
            
            foreach ($request->itens as $itemData) {
                $produto = Produto::findOrFail($itemData['produto_id']);
                
                // Verificar se o item já existe no pedido
                $itemExistente = ItemPedido::where('pedido_id', $pedido->id)
                                          ->where('produto_id', $produto->id)
                                          ->first();                if ($itemExistente) {
                    // Atualizar quantidade do item existente
                    $itemExistente->quantidade += $itemData['quantidade'];
                    $itemExistente->subtotal = $itemExistente->preco_unitario * $itemExistente->quantidade;
                    
                    // Adicionar observações se fornecidas
                    if (!empty($itemData['observacoes'])) {
                        $observacoesExistentes = $itemExistente->observacoes ?: '';
                        $novasObservacoes = $itemData['observacoes'];
                        $itemExistente->observacoes = $observacoesExistentes 
                            ? $observacoesExistentes . ' | ' . $novasObservacoes 
                            : $novasObservacoes;
                    }
                    
                    $itemExistente->save();
                    Log::info("📝 Item existente atualizado:", [
                        'produto' => $produto->nome,
                        'quantidade_anterior' => $itemExistente->quantidade - $itemData['quantidade'],
                        'quantidade_adicionada' => $itemData['quantidade'],
                        'quantidade_nova' => $itemExistente->quantidade,
                        'subtotal_novo' => $itemExistente->subtotal,
                        'observacoes' => $itemExistente->observacoes
                    ]);
                } else {
                    // Criar novo item no pedido
                    $subtotal = $produto->preco * $itemData['quantidade'];
                    ItemPedido::create([
                        'pedido_id' => $pedido->id,
                        'produto_id' => $produto->id,
                        'quantidade' => $itemData['quantidade'],
                        'preco_unitario' => $produto->preco,
                        'subtotal' => $subtotal,
                        'observacoes' => $itemData['observacoes'] ?? ''
                    ]);
                    Log::info("🆕 Novo item adicionado:", [
                        'produto' => $produto->nome,
                        'quantidade' => $itemData['quantidade'],
                        'preco' => $produto->preco,
                        'subtotal' => $subtotal,
                        'observacoes' => $itemData['observacoes'] ?? ''
                    ]);
                }
                
                $totalAdicional += $produto->preco * $itemData['quantidade'];
            }
            
            // Atualizar total do pedido
            $pedido->total += $totalAdicional;
            $pedido->save();
            
            Log::info("💰 Total do pedido atualizado:", [
                'total_anterior' => $pedido->total - $totalAdicional,
                'total_adicional' => $totalAdicional,
                'total_novo' => $pedido->total
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Itens adicionados com sucesso ao pedido!',
                'pedido_id' => $pedido->id,
                'total_novo' => $pedido->total,
                'total_adicional' => $totalAdicional
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('❌ Erro ao adicionar itens ao pedido:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar itens: ' . $e->getMessage()
            ], 500);
        }
    }    public function dashboardData()
    {
        $userId = Auth::id() ?? 1;
        
        // Buscar dados atualizados
        $meusPedidosHoje = Pedido::where('usuario_id', $userId)->whereDate('created_at', today())->count();
        $minhaVendaHoje = Pedido::where('usuario_id', $userId)->whereDate('created_at', today())->sum('total');
        $mesasDisponiveis = Mesa::count();
        $mesasOcupadas = Mesa::whereHas('pedidos', function($query) { 
            $query->where('status', 'aberto'); 
        })->count();
          // Buscar informações das mesas ocupadas
        $mesasOcupadasInfo = Mesa::with(['pedidos' => function($query) { 
            $query->where('status', 'aberto')->latest(); 
        }])->whereHas('pedidos', function($query) { 
            $query->where('status', 'aberto');        })->get()->map(function($mesa) {
            $pedidoAtual = $mesa->pedidos->first();
            return [
                'id' => $mesa->id,
                'numero' => $mesa->numero,
                'identificador' => $mesa->identificador,
                'pedido_id' => $pedidoAtual ? $pedidoAtual->id : null,
                'valor_total' => $pedidoAtual ? number_format($pedidoAtual->total, 2, ',', '.') : 'R$ 0,00'
            ];
        });
        
        // Buscar mesas disponíveis (sem pedidos abertos)
        $mesasDisponiveisInfo = Mesa::whereDoesntHave('pedidos', function($query) { 
            $query->where('status', 'aberto');
        })->limit(6)->get()->map(function($mesa) {
            return [
                'id' => $mesa->id,
                'numero' => $mesa->numero,
                'identificador' => $mesa->identificador
            ];
        });
        
        // Buscar últimos pedidos
        $ultimosPedidos = Pedido::with(['mesa', 'itens.produto'])
            ->where('usuario_id', $userId)
            ->whereDate('created_at', today())
            ->latest()
            ->limit(5)
            ->get()            ->map(function($pedido) {
                return [
                    'id' => $pedido->id,
                    'mesa_identificador' => $pedido->mesa->identificador ?? 'Mesa ' . $pedido->mesa->numero,
                    'itens_count' => $pedido->itens->count(),
                    'primeiro_item' => $pedido->itens->first() ? $pedido->itens->first()->produto->nome : '',
                    'valor_total' => number_format($pedido->total, 2, ',', '.'),
                    'status' => $pedido->status,
                    'horario' => $pedido->created_at->format('H:i')
                ];
            });
          $data = [
            'meusPedidosHoje' => $meusPedidosHoje,
            'minhaVendaHoje' => number_format($minhaVendaHoje, 2, ',', '.'),
            'mesasDisponiveis' => $mesasDisponiveis,
            'mesasOcupadas' => $mesasOcupadas,
            'mesasOcupadasInfo' => $mesasOcupadasInfo,
            'mesasDisponiveisInfo' => $mesasDisponiveisInfo,
            'ultimosPedidos' => $ultimosPedidos,
            'timestamp' => now()->format('H:i:s')
        ];
        
        return response()->json($data);
    }

    public function trocarMesa(Request $request)
    {
        try {
            $request->validate([
                'pedido_id' => 'required|exists:pedidos,id',
                'nova_mesa_id' => 'required|exists:mesas,id',
                'motivo' => 'nullable|string|max:500'
            ]);
            
            $pedido = Pedido::findOrFail($request->pedido_id);
            $novaMesa = Mesa::findOrFail($request->nova_mesa_id);
            $mesaAnterior = $pedido->mesa;
            
            // Verificar se a nova mesa está disponível
            $mesaOcupada = Mesa::whereHas('pedidos', function($query) {
                $query->where('status', 'aberto');
            })->where('id', $request->nova_mesa_id)->exists();
            
            if ($mesaOcupada) {
                return response()->json([
                    'success' => false,
                    'message' => 'A mesa selecionada já está ocupada.'
                ]);
            }
            
            // Verificar se é o garçom responsável pelo pedido
            $userId = Auth::id() ?? 1;
            if ($pedido->usuario_id != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você só pode alterar seus próprios pedidos.'
                ]);
            }
            
            // Verificar se o pedido está aberto
            if ($pedido->status != 'aberto') {
                return response()->json([
                    'success' => false,
                    'message' => 'Só é possível trocar mesa de pedidos em andamento.'
                ]);
            }
            
            // Atualizar o pedido
            $motivoTexto = $request->motivo ? " - Motivo: " . $request->motivo : "";
            $observacaoTroca = "Mesa alterada de {$mesaAnterior->identificador ?? 'Mesa ' . $mesaAnterior->numero} para {$novaMesa->identificador ?? 'Mesa ' . $novaMesa->numero}{$motivoTexto}";
            
            $observacaoAtual = $pedido->observacoes ? $pedido->observacoes . "\n" : "";
            
            $pedido->update([
                'mesa_id' => $request->nova_mesa_id,
                'observacoes' => $observacaoAtual . $observacaoTroca
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Mesa alterada com sucesso para {$novaMesa->identificador ?? 'Mesa ' . $novaMesa->numero}",
                'nova_mesa' => [
                    'id' => $novaMesa->id,
                    'nome' => $novaMesa->identificador ?? 'Mesa ' . $novaMesa->numero
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao trocar mesa: ' . $e->getMessage()
            ]);
        }
    }
}
