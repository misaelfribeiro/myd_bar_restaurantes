<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AILearningService;
use App\Models\AIContext;
use App\Models\AITrainingData;
use App\Models\AIConversationSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AILearningService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Inicializa a rede neural (apenas primeira vez)
     * POST /api/ai/initialize
     */
    public function initialize()
    {
        $result = $this->aiService->initializeNetwork();
        
        return response()->json([
            'success' => true,
            'message' => 'Rede neural inicializada',
            'data' => $result
        ]);
    }

    /**
     * Processa mensagem do usuário
     * POST /api/ai/process
     * 
     * Body: {
     *   "message": "quero uma pizza",
     *   "session_token": "abc123...",
     *   "user_id": 1
     * }
     */
    public function process(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:500',
            'session_token' => 'nullable|string',
            'user_id' => 'nullable|integer|exists:users,id',
            'empresa_id' => 'nullable|string' // Pode ser tenant_code (string) ou id (integer)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Adiciona empresa_id ao contexto (pode ser tenant_code)
            $empresaId = $request->empresa_id ?? $request->header('X-Tenant-Code');
            
            $result = $this->aiService->processMessage(
                $request->message,
                $request->session_token,
                $request->user_id ?? auth()->id(),
                $empresaId
            );

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar mensagem',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registra feedback do usuário sobre uma resposta
     * POST /api/ai/feedback
     * 
     * Body: {
     *   "training_data_id": 123,
     *   "correct": true,
     *   "feedback_score": 5
     * }
     */
    public function feedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'training_data_id' => 'required|integer|exists:ai_training_data,id',
            'correct' => 'required|boolean',
            'feedback_score' => 'nullable|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $result = $this->aiService->learnFromFeedback(
                $request->training_data_id,
                $request->correct,
                $request->feedback_score
            );

            return response()->json([
                'success' => true,
                'message' => 'Feedback registrado e aprendido',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Treina a IA em lote com dados históricos
     * POST /api/ai/train
     * 
     * Body: {
     *   "limit": 100
     * }
     */
    public function train(Request $request)
    {
        $limit = $request->input('limit', 100);

        try {
            $result = $this->aiService->batchTrain($limit);

            return response()->json([
                'success' => true,
                'message' => 'Treinamento concluído',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao treinar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista contextos aprendidos
     * GET /api/ai/contexts
     */
    public function contexts(Request $request)
    {
        $query = AIContext::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $contexts = $query->orderBy('usage_count', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $contexts
        ]);
    }

    /**
     * Adiciona novo contexto de aprendizado
     * POST /api/ai/contexts
     * 
     * Body: {
     *   "category": "menu",
     *   "key": "search_pizza",
     *   "pattern": "quero * pizza *",
     *   "response_template": "Encontrei pizzas para você!",
     *   "action": "searchProduct",
     *   "parameters": {"product_type": "pizza"}
     * }
     */
    public function createContext(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|max:50',
            'key' => 'required|string|max:100|unique:ai_contexts,key',
            'pattern' => 'required|string',
            'response_template' => 'required|string',
            'action' => 'nullable|string|max:50',
            'parameters' => 'nullable|array',
            'confidence_threshold' => 'nullable|numeric|min:0|max:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $context = AIContext::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Contexto criado com sucesso',
                'data' => $context
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar contexto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estatísticas da IA
     * GET /api/ai/stats
     */
    public function stats()
    {
        // Recent interactions
        $recentInteractions = AITrainingData::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $stats = [
            'total_neurons' => \App\Models\AINeuron::count(),
            'total_synapses' => \App\Models\AISynapse::count(),
            'total_contexts' => AIContext::count(),
            'total_interactions' => AITrainingData::count(),
            
            'neurons_by_layer' => [
                'input' => \App\Models\AINeuron::where('layer', 'input')->count(),
                'hidden' => \App\Models\AINeuron::where('layer', 'hidden')->count(),
                'output' => \App\Models\AINeuron::where('layer', 'output')->count(),
            ],
            
            'avg_synapse_weight' => \App\Models\AISynapse::avg('weight') ?? 0,
            'total_synapse_updates' => \App\Models\AISynapse::sum('updates') ?? 0,
            
            'trained_count' => AITrainingData::where('used_for_training', true)->count(),
            'pending_training' => AITrainingData::where('used_for_training', false)->count(),
            
            'correct_rate' => (AITrainingData::where('correct', true)->count() / max(AITrainingData::count(), 1)) * 100,
            'avg_confidence' => AITrainingData::avg('confidence') ?? 0,
            'context_success_rate' => (AIContext::avg('success_rate') ?? 0) * 100,
            
            'contexts_by_category' => AIContext::selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category'),
            
            'active_sessions' => AIConversationSession::where('expires_at', '>', now())->count(),
            
            'recent_interactions' => $recentInteractions
        ];

        return response()->json($stats);
    }

    /**
     * Limpa sessões expiradas
     * POST /api/ai/cleanup
     */
    public function cleanup()
    {
        $deleted = AIConversationSession::cleanExpiredSessions();

        return response()->json([
            'success' => true,
            'message' => 'Limpeza concluída',
            'deleted_sessions' => $deleted
        ]);
    }

    /**
     * Atualiza um contexto existente
     * PUT /api/ai/contexts/{id}
     */
    public function updateContext($id, Request $request)
    {
        $context = AIContext::find($id);
        
        if (!$context) {
            return response()->json([
                'success' => false,
                'message' => 'Contexto não encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'category' => 'sometimes|string|max:50',
            'pattern' => 'sometimes|string',
            'response_template' => 'sometimes|string',
            'action' => 'nullable|string|max:100',
            'confidence_threshold' => 'sometimes|numeric|min:0|max:1',
            'active' => 'sometimes|boolean'
        ]);

        $context->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contexto atualizado com sucesso',
            'data' => $context
        ]);
    }

    /**
     * Deleta um contexto
     * DELETE /api/ai/contexts/{id}
     */
    public function deleteContext($id)
    {
        $context = AIContext::find($id);
        
        if (!$context) {
            return response()->json([
                'success' => false,
                'message' => 'Contexto não encontrado'
            ], 404);
        }

        $key = $context->key;
        $context->delete();

        return response()->json([
            'success' => true,
            'message' => "Contexto '{$key}' deletado com sucesso"
        ]);
    }

    /**
     * Estatísticas de Feedback
     * GET /api/ai/feedback/stats
     */
    public function feedbackStats()
    {
        try {
            // Contadores
            $positive = AITrainingData::where('feedback_score', '>=', 4)->count();
            $neutral = AITrainingData::whereBetween('feedback_score', [2, 3])->count();
            $negative = AITrainingData::where('feedback_score', '<=', 1)->count();
            $total = $positive + $neutral + $negative;
            
            // Taxa de satisfação
            $satisfactionRate = $total > 0 ? ($positive / $total * 100) : 0;
            
            // Top contextos mais avaliados
            $topRatedContexts = AITrainingData::whereNotNull('feedback_score')
                ->selectRaw('context, AVG(feedback_score) as avg_score, COUNT(*) as count')
                ->groupBy('context')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'context' => is_array($item->context) ? ($item->context['matched_key'] ?? 'unknown') : $item->context,
                        'avg_score' => (float) $item->avg_score,
                        'count' => $item->count
                    ];
                });
            
            // Feedbacks recentes
            $recentFeedbacks = AITrainingData::whereNotNull('feedback_score')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['input', 'actual_output', 'feedback_score', 'correct', 'created_at']);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'positive_count' => $positive,
                    'neutral_count' => $neutral,
                    'negative_count' => $negative,
                    'total_feedbacks' => $total,
                    'satisfaction_rate' => $satisfactionRate,
                    'top_rated_contexts' => $topRatedContexts,
                    'recent_feedbacks' => $recentFeedbacks
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas de feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
