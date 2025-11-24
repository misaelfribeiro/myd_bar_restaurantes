<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entregador;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EntregadorApiController extends Controller
{
    /**
     * Login do entregador
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'senha' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        $entregador = Entregador::where('email', $request->email)->first();

        if (!$entregador || !Hash::check($request->senha, $entregador->senha)) {
            return response()->json([
                'success' => false,
                'message' => 'E-mail ou senha inválidos'
            ], 401);
        }

        if ($entregador->status !== 'ativo') {
            return response()->json([
                'success' => false,
                'message' => 'Entregador não está ativo. Entre em contato com o administrador.'
            ], 403);
        }

        // Criar token
        $token = $entregador->createToken('entregador-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'entregador' => [
                'id' => $entregador->id,
                'nome' => $entregador->nome,
                'email' => $entregador->email,
                'telefone' => $entregador->telefone,
                'cpf' => $entregador->cpf,
                'veiculo' => $entregador->veiculo_tipo,
                'foto' => $entregador->foto ? asset('storage/' . $entregador->foto) : null
            ]
        ]);
    }

    /**
     * Logout do entregador
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Dados do entregador autenticado
     */
    public function me(Request $request)
    {
        $entregador = $request->user();

        return response()->json([
            'success' => true,
            'entregador' => [
                'id' => $entregador->id,
                'nome' => $entregador->nome,
                'email' => $entregador->email,
                'telefone' => $entregador->telefone,
                'cpf' => $entregador->cpf,
                'veiculo' => $entregador->veiculo_tipo,
                'foto' => $entregador->foto ? asset('storage/' . $entregador->foto) : null,
                'status' => $entregador->status,
                'disponivel' => $entregador->disponivel
            ]
        ]);
    }

    /**
     * Listar entregas disponíveis
     */
    public function entregasDisponiveis(Request $request)
    {
        $entregas = Delivery::where('status', 'pendente')
            ->whereNull('entregador_id')
            ->with(['pedido'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($delivery) {
                return [
                    'id' => $delivery->id,
                    'cliente_nome' => $delivery->cliente_nome,
                    'cliente_telefone' => $delivery->cliente_telefone,
                    'endereco_completo' => $delivery->endereco_completo,
                    'endereco_bairro' => $delivery->endereco_bairro,
                    'endereco_cidade' => $delivery->endereco_cidade,
                    'taxa_entrega' => $delivery->taxa_entrega,
                    'tempo_estimado' => $delivery->tempo_estimado,
                    'distancia' => $delivery->distancia_km,
                    'latitude' => $delivery->latitude,
                    'longitude' => $delivery->longitude,
                    'observacoes' => $delivery->observacoes,
                    'created_at' => $delivery->created_at->format('d/m/Y H:i'),
                    'pedido' => $delivery->pedido ? [
                        'id' => $delivery->pedido->id,
                        'total' => $delivery->pedido->total
                    ] : null
                ];
            });

        return response()->json([
            'success' => true,
            'entregas' => $entregas
        ]);
    }

    /**
     * Listar entregas ativas do entregador
     */
    public function entregasAtivas(Request $request)
    {
        $entregador = $request->user();

        $entregas = Delivery::where('entregador_id', $entregador->id)
            ->whereIn('status', ['coletado', 'em_rota'])
            ->with(['pedido'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($delivery) {
                return [
                    'id' => $delivery->id,
                    'cliente_nome' => $delivery->cliente_nome,
                    'cliente_telefone' => $delivery->cliente_telefone,
                    'endereco_completo' => $delivery->endereco_completo,
                    'endereco_bairro' => $delivery->endereco_bairro,
                    'endereco_cidade' => $delivery->endereco_cidade,
                    'taxa_entrega' => $delivery->taxa_entrega,
                    'tempo_estimado' => $delivery->tempo_estimado,
                    'distancia' => $delivery->distancia_km,
                    'latitude' => $delivery->latitude,
                    'longitude' => $delivery->longitude,
                    'observacoes' => $delivery->observacoes,
                    'status' => $delivery->status,
                    'created_at' => $delivery->created_at->format('d/m/Y H:i'),
                    'pedido' => $delivery->pedido ? [
                        'id' => $delivery->pedido->id,
                        'total' => $delivery->pedido->total
                    ] : null
                ];
            });

        // Estatísticas do dia
        $hoje = now()->startOfDay();
        $stats = [
            'total' => Delivery::where('entregador_id', $entregador->id)
                ->where('created_at', '>=', $hoje)
                ->count(),
            'ganhos' => Delivery::where('entregador_id', $entregador->id)
                ->where('created_at', '>=', $hoje)
                ->where('status', 'entregue')
                ->sum('taxa_entrega')
        ];

        return response()->json([
            'success' => true,
            'entregas' => $entregas,
            'stats' => $stats
        ]);
    }

    /**
     * Aceitar entrega
     */
    public function aceitarEntrega(Request $request, $id)
    {
        $entregador = $request->user();

        $delivery = Delivery::find($id);

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Entrega não encontrada'
            ], 404);
        }

        if ($delivery->entregador_id) {
            return response()->json([
                'success' => false,
                'message' => 'Esta entrega já foi aceita por outro entregador'
            ], 400);
        }

        $delivery->entregador_id = $entregador->id;
        $delivery->entregador_nome = $entregador->nome;
        $delivery->entregador_telefone = $entregador->telefone;
        $delivery->status = 'confirmado';
        $delivery->save();

        return response()->json([
            'success' => true,
            'message' => 'Entrega aceita com sucesso',
            'delivery' => $delivery
        ]);
    }

    /**
     * Atualizar status da entrega
     */
    public function atualizarStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:coletado,em_rota,entregue'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $entregador = $request->user();
        $delivery = Delivery::where('id', $id)
            ->where('entregador_id', $entregador->id)
            ->first();

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Entrega não encontrada ou não pertence a você'
            ], 404);
        }

        $delivery->status = $request->status;

        // Atualizar datas
        switch($request->status) {
            case 'coletado':
                $delivery->data_confirmacao = now();
                break;
            case 'em_rota':
                $delivery->data_saida = now();
                break;
            case 'entregue':
                $delivery->data_entrega = now();
                // Atualizar pedido vinculado
                if ($delivery->pedido_id) {
                    $delivery->pedido->status = 'finalizado';
                    $delivery->pedido->save();
                }
                break;
        }

        $delivery->save();

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso',
            'delivery' => $delivery
        ]);
    }

    /**
     * Detalhes de uma entrega
     */
    public function detalhesEntrega(Request $request, $id)
    {
        $delivery = Delivery::with(['pedido.itens.produto'])->find($id);

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Entrega não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'entrega' => [
                'id' => $delivery->id,
                'cliente_nome' => $delivery->cliente_nome,
                'cliente_telefone' => $delivery->cliente_telefone,
                'endereco_completo' => $delivery->endereco_completo,
                'taxa_entrega' => $delivery->taxa_entrega,
                'tempo_estimado' => $delivery->tempo_estimado,
                'latitude' => $delivery->latitude,
                'longitude' => $delivery->longitude,
                'observacoes' => $delivery->observacoes,
                'status' => $delivery->status,
                'pedido' => $delivery->pedido ? [
                    'id' => $delivery->pedido->id,
                    'total' => $delivery->pedido->total,
                    'itens' => $delivery->pedido->itens->map(function($item) {
                        return [
                            'produto' => $item->produto->nome,
                            'quantidade' => $item->quantidade,
                            'preco' => $item->preco
                        ];
                    })
                ] : null
            ]
        ]);
    }

    /**
     * Histórico de entregas
     */
    public function historico(Request $request)
    {
        $entregador = $request->user();
        $filtro = $request->get('filtro', 'hoje');

        $query = Delivery::where('entregador_id', $entregador->id)
            ->where('status', 'entregue');

        switch($filtro) {
            case 'hoje':
                $query->whereDate('data_entrega', today());
                break;
            case 'semana':
                $query->whereBetween('data_entrega', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'mes':
                $query->whereMonth('data_entrega', now()->month);
                break;
        }

        $entregas = $query->orderBy('data_entrega', 'desc')
            ->get()
            ->map(function($delivery) {
                return [
                    'id' => $delivery->id,
                    'cliente_nome' => $delivery->cliente_nome,
                    'endereco_bairro' => $delivery->endereco_bairro,
                    'endereco_cidade' => $delivery->endereco_cidade,
                    'taxa_entrega' => $delivery->taxa_entrega,
                    'data_entrega' => $delivery->data_entrega
                ];
            });

        return response()->json([
            'success' => true,
            'entregas' => $entregas
        ]);
    }

    /**
     * Ganhos do entregador
     */
    public function ganhos(Request $request)
    {
        $entregador = $request->user();

        $hoje = Delivery::where('entregador_id', $entregador->id)
            ->where('status', 'entregue')
            ->whereDate('data_entrega', today())
            ->sum('taxa_entrega');

        $semana = Delivery::where('entregador_id', $entregador->id)
            ->where('status', 'entregue')
            ->whereBetween('data_entrega', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('taxa_entrega');

        $mes = Delivery::where('entregador_id', $entregador->id)
            ->where('status', 'entregue')
            ->whereMonth('data_entrega', now()->month)
            ->sum('taxa_entrega');

        return response()->json([
            'success' => true,
            'hoje' => $hoje,
            'semana' => $semana,
            'mes' => $mes
        ]);
    }

    /**
     * Atualizar localização do entregador
     */
    public function atualizarLocalizacao(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $entregador = $request->user();
        $entregador->latitude = $request->latitude;
        $entregador->longitude = $request->longitude;
        $entregador->ultima_localizacao = now();
        $entregador->save();

        return response()->json([
            'success' => true,
            'message' => 'Localização atualizada'
        ]);
    }

    /**
     * Toggle disponibilidade
     */
    public function toggleDisponibilidade(Request $request)
    {
        $entregador = $request->user();
        $disponivel = $request->get('disponivel', !$entregador->disponivel);
        
        $entregador->disponivel = $disponivel;
        $entregador->save();

        return response()->json([
            'success' => true,
            'disponivel' => $entregador->disponivel,
            'message' => $disponivel ? 'Você está disponível para entregas' : 'Você está indisponível'
        ]);
    }

    /**
     * Atualizar perfil
     */
    public function atualizarPerfil(Request $request)
    {
        $entregador = $request->user();

        $validator = Validator::make($request->all(), [
            'nome' => 'sometimes|string|max:255',
            'telefone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|unique:entregadores,email,' . $entregador->id,
            'senha' => 'sometimes|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        if ($request->has('nome')) $entregador->nome = $request->nome;
        if ($request->has('telefone')) $entregador->telefone = $request->telefone;
        if ($request->has('email')) $entregador->email = $request->email;
        if ($request->has('senha')) $entregador->senha = Hash::make($request->senha);

        $entregador->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'entregador' => $entregador
        ]);
    }
}
