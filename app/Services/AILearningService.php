<?php

namespace App\Services;

use App\Models\AINeuron;
use App\Models\AISynapse;
use App\Models\AIContext;
use App\Models\AITrainingData;
use App\Models\AIConversationSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AILearningService
{
    protected $inputSize = 100;  // Tamanho do vetor de entrada (embedding)
    protected $hiddenSize = 50;  // Neurônios na camada oculta
    protected $outputSize = 20;  // Possíveis intenções
    protected $learningRate = 0.01;

    /**
     * Inicializa a rede neural (primeira vez)
     */
    public function initializeNetwork()
    {
        if (AINeuron::count() > 0) {
            return ['status' => 'already_initialized'];
        }

        // Camada de entrada
        for ($i = 0; $i < $this->inputSize; $i++) {
            AINeuron::create([
                'layer' => 'input',
                'position' => $i,
                'type' => 'relu'
            ]);
        }

        // Camada oculta
        for ($i = 0; $i < $this->hiddenSize; $i++) {
            AINeuron::create([
                'layer' => 'hidden',
                'position' => $i,
                'type' => 'relu',
                'bias' => (rand(-100, 100) / 1000) // -0.1 a 0.1
            ]);
        }

        // Camada de saída
        for ($i = 0; $i < $this->outputSize; $i++) {
            AINeuron::create([
                'layer' => 'output',
                'position' => $i,
                'type' => 'sigmoid',
                'bias' => (rand(-100, 100) / 1000)
            ]);
        }

        // Criar sinapses input -> hidden
        $inputNeurons = AINeuron::where('layer', 'input')->get();
        $hiddenNeurons = AINeuron::where('layer', 'hidden')->get();

        foreach ($inputNeurons as $input) {
            foreach ($hiddenNeurons as $hidden) {
                AISynapse::create([
                    'from_neuron_id' => $input->id,
                    'to_neuron_id' => $hidden->id,
                    'weight' => (rand(-100, 100) / 1000) // -0.1 a 0.1
                ]);
            }
        }

        // Criar sinapses hidden -> output
        $outputNeurons = AINeuron::where('layer', 'output')->get();

        foreach ($hiddenNeurons as $hidden) {
            foreach ($outputNeurons as $output) {
                AISynapse::create([
                    'from_neuron_id' => $hidden->id,
                    'to_neuron_id' => $output->id,
                    'weight' => (rand(-100, 100) / 1000)
                ]);
            }
        }

        return [
            'status' => 'initialized',
            'neurons' => AINeuron::count(),
            'synapses' => AISynapse::count()
        ];
    }

    /**
     * Processa mensagem do usuário
     */
    public function processMessage($message, $sessionToken = null, $userId = null, $empresaId = null)
    {
        // Busca ou cria sessão
        $session = $this->getOrCreateSession($sessionToken, $userId);
        
        \Log::info('processMessage INÍCIO', [
            'message' => $message,
            'session_entities' => $session->entities
        ]);
        
        // Armazena empresa_id na sessão (mas não sobrescrever outros entities!)
        if ($empresaId) {
            $entities = $session->entities ?? [];
            $entities['empresa_id'] = $empresaId;
            $session->entities = $entities;
            // NÃO salvar aqui - será salvo no final do processMessage
        }

        // Converte mensagem em vetor numérico (embedding simples)
        $inputVector = $this->textToVector($message);

        // Propaga pela rede neural
        $outputs = $this->forwardPropagation($inputVector);

        // Busca contextos que combinam
        $matchedContexts = $this->findMatchingContexts($message, $outputs);

        // Seleciona melhor resposta
        $bestMatch = $this->selectBestResponse($matchedContexts, $session);

        // Se não encontrou match, usar resposta padrão
        if (!$bestMatch) {
            $bestMatch = [
                'response' => 'Desculpe, não entendi. Pode reformular? Você pode pedir: "quero pizza", "mostra o cardápio", "meus pedidos".',
                'intent' => 'unknown',
                'confidence' => 0,
                'action' => null,
                'parameters' => []
            ];
        }
        
        // Se a ação envolve produtos, busca produtos reais
        if (in_array($bestMatch['action'], ['searchProduct', 'filterByCategory', 'showMenu'])) {
            $bestMatch['session_token'] = $session->session_token;
            $bestMatch = $this->enrichWithProducts($bestMatch, $message, $empresaId, $session);
        }
        
        // Se quer adicionar ao carrinho do app
        if ($bestMatch['action'] === 'addToAppCart') {
            $bestMatch = $this->handleAddToAppCart($bestMatch, $session);
        }
        
        // Se quer ver o carrinho do app
        if ($bestMatch['action'] === 'viewAppCart') {
            $bestMatch = $this->handleViewAppCart($bestMatch, $session);
        }
        
        // Se quer finalizar pedido (checkout)
        if ($bestMatch['action'] === 'checkoutApp') {
            $bestMatch = $this->handleCheckoutApp($bestMatch, $session);
        }
        
        // Confirmar/alterar endereço
        if ($bestMatch['action'] === 'confirmAddress') {
            $bestMatch = $this->handleConfirmAddress($bestMatch, $session, $userId);
        }
        
        if ($bestMatch['action'] === 'changeAddress') {
            $bestMatch = $this->handleChangeAddress($bestMatch, $session);
        }
        
        // Formas de pagamento
        if ($bestMatch['action'] === 'showPaymentMethods') {
            $bestMatch = $this->handleShowPaymentMethods($bestMatch, $session);
        }
        
        if ($bestMatch['action'] === 'selectPayment') {
            $bestMatch = $this->handleSelectPayment($bestMatch, $session);
        }
        
        // Definir valor do troco
        if ($bestMatch['action'] === 'setChangeAmount') {
            $bestMatch['message'] = $message; // Passar mensagem para extrair valor
            $bestMatch = $this->handleSetChangeAmount($bestMatch, $session);
        }
        
        // Confirmar/finalizar pedido
        if ($bestMatch['action'] === 'confirmOrder') {
            $bestMatch = $this->handleConfirmOrder($bestMatch, $session);
        }
        
        // Se é confirmação após mostrar resumo
        if ($bestMatch['action'] === 'confirmAction') {
            $bestMatch = $this->handleConfirmAction($bestMatch, $session);
        }
        
        // Se a ação envolve carrinho backend (DESATIVADO - mantido para compatibilidade)
        if (in_array($bestMatch['action'], ['addToCartBackend', 'viewCartBackend', 'checkoutBackend', 'clearCartBackend'])) {
            $bestMatch = $this->processCartAction($bestMatch, $message, $session, $userId, $empresaId);
        }

        // Registra interação para treinamento futuro
        $this->recordInteraction($message, $bestMatch, $session, $userId);

        // Atualiza sessão (ÚNICO SAVE - mantém todos os entities)
        $session->touchActivity();
        if ($bestMatch) {
            $session->last_intent = $bestMatch['intent'];
            $session->pushContext($bestMatch['intent']);
        }
        
        $session->save(); // Salva uma única vez com todos os dados

        return [
            'response' => $bestMatch['response'] ?? 'Desculpe, não entendi. Pode reformular?',
            'intent' => $bestMatch['intent'] ?? 'unknown',
            'confidence' => $bestMatch['confidence'] ?? 0,
            'action' => $bestMatch['action'] ?? null,
            'parameters' => $bestMatch['parameters'] ?? [],
            'products' => $bestMatch['products'] ?? [],
            'cart' => $bestMatch['cart'] ?? [],
            'pedido_id' => $bestMatch['pedido_id'] ?? null,
            'add_to_cart_product' => $bestMatch['add_to_cart_product'] ?? null,
            'navigate_to' => $bestMatch['navigate_to'] ?? null,
            'address_data' => $bestMatch['address_data'] ?? null,
            'payment_methods' => $bestMatch['payment_methods'] ?? null,
            'payment_selected' => $bestMatch['payment_selected'] ?? null,
            'change_amount' => $bestMatch['change_amount'] ?? null,
            'show_summary' => $bestMatch['show_summary'] ?? false,
            'session_token' => $session->session_token
        ];
    }

    /**
     * Forward propagation pela rede neural
     */
    protected function forwardPropagation($inputVector)
    {
        // Camada de entrada
        $inputNeurons = AINeuron::where('layer', 'input')->orderBy('position')->get();
        foreach ($inputNeurons as $i => $neuron) {
            $neuron->activation = $inputVector[$i] ?? 0;
        }

        // Camada oculta
        $hiddenNeurons = AINeuron::where('layer', 'hidden')
            ->with('inputSynapses.fromNeuron')
            ->orderBy('position')
            ->get();

        foreach ($hiddenNeurons as $neuron) {
            $sum = $neuron->bias;
            foreach ($neuron->inputSynapses as $synapse) {
                $sum += $synapse->fromNeuron->activation * $synapse->weight;
            }
            $neuron->activation = $neuron->activate($sum);
        }

        // Camada de saída
        $outputNeurons = AINeuron::where('layer', 'output')
            ->with('inputSynapses.fromNeuron')
            ->orderBy('position')
            ->get();

        $outputs = [];
        foreach ($outputNeurons as $neuron) {
            $sum = $neuron->bias;
            foreach ($neuron->inputSynapses as $synapse) {
                $sum += $synapse->fromNeuron->activation * $synapse->weight;
            }
            $neuron->activation = $neuron->activate($sum);
            $outputs[$neuron->position] = $neuron->activation;
        }

        return $outputs;
    }

    /**
     * Converte texto em vetor numérico (embedding simples baseado em palavras-chave)
     */
    protected function textToVector($text)
    {
        $text = mb_strtolower($text);
        $vector = array_fill(0, $this->inputSize, 0);

        // Palavras-chave e suas posições no vetor
        $keywords = [
            'pizza' => 0, 'hamburguer' => 1, 'lanche' => 2, 'bebida' => 3,
            'cardapio' => 4, 'menu' => 5, 'carrinho' => 6, 'pedido' => 7,
            'entrega' => 8, 'status' => 9, 'pagar' => 10, 'pagamento' => 11,
            'ajuda' => 12, 'oi' => 13, 'olá' => 14, 'quero' => 15,
            'quanto' => 16, 'preco' => 17, 'valor' => 18, 'adicionar' => 19,
            'remover' => 20, 'cancelar' => 21, 'confirmar' => 22, 'finalizar' => 23,
            'suco' => 24, 'refrigerante' => 25, 'agua' => 26, 'cerveja' => 27,
            'sanduiche' => 28, 'combo' => 29, 'promocao' => 30, 'desconto' => 31
        ];

        // Marca presença de palavras-chave
        foreach ($keywords as $word => $position) {
            if (strpos($text, $word) !== false) {
                $vector[$position] = 1;
            }
        }

        // Adiciona informação de comprimento (normalizado)
        $words = explode(' ', $text);
        $vector[99] = min(count($words) / 10, 1); // normaliza entre 0 e 1

        return $vector;
    }

    /**
     * Encontra contextos que combinam com a mensagem
     */
    protected function findMatchingContexts($message, $neuralOutputs)
    {
        $contexts = AIContext::where('active', true)->get();
        $matches = [];

        foreach ($contexts as $context) {
            if ($context->matches($message)) {
                $confidence = $this->calculateConfidence($message, $context, $neuralOutputs);
                
                if ($confidence >= $context->confidence_threshold) {
                    $params = $context->extractParameters($message);
                    
                    // Mesclar com parâmetros salvos no contexto
                    if ($context->parameters) {
                        $contextParams = is_string($context->parameters) 
                            ? json_decode($context->parameters, true) 
                            : $context->parameters;
                        $params = array_merge($contextParams ?? [], $params);
                    }
                    
                    $matches[] = [
                        'context' => $context,
                        'intent' => $context->key,
                        'confidence' => $confidence,
                        'response' => $context->generateResponse($params),
                        'action' => $context->action,
                        'parameters' => $params
                    ];
                }
            }
        }

        // Ordena por confiança
        usort($matches, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });

        return $matches;
    }

    /**
     * Calcula confiança baseado em padrão + rede neural + histórico
     */
    protected function calculateConfidence($message, $context, $neuralOutputs)
    {
        $confidence = 0;

        // 50% baseado no match do padrão
        $confidence += 0.5;

        // 25% baseado na taxa de sucesso histórica (mínimo 20% se nunca usado)
        $successRate = $context->success_rate > 0 ? $context->success_rate : 0.8;
        $confidence += $successRate * 0.25;

        // 25% baseado na saída da rede neural
        $neuralConfidence = $neuralOutputs[$context->id % $this->outputSize] ?? 0.5;
        $confidence += $neuralConfidence * 0.25;
        
        // BOOST PRIORITÁRIO: Contextos de pagamento quando mensagem contém palavras-chave
        $messageLower = mb_strtolower($message);
        $paymentKeywords = ['pagar', 'pagamento', 'dinheiro', 'pix', 'cartão', 'cartao', 'crédito', 'credito', 'débito', 'debito', 'troco'];
        $isPaymentContext = strpos($context->key, 'payment') !== false || $context->category === 'payment' || $context->category === 'checkout';
        $isChangeContext = strpos($context->key, 'change_amount') !== false || strpos($context->action, 'setChangeAmount') !== false;
        
        $hasPaymentKeyword = false;
        $hasChangeKeyword = strpos($messageLower, 'troco') !== false;
        
        foreach($paymentKeywords as $keyword) {
            if (strpos($messageLower, $keyword) !== false) {
                $hasPaymentKeyword = true;
                break;
            }
        }
        
        if ($isPaymentContext && $hasPaymentKeyword) {
            $confidence += 0.35; // Boost grande para contextos de pagamento
        }
        
        if ($isChangeContext && $hasChangeKeyword) {
            $confidence += 0.40; // Boost ainda maior para troco
        }

        return min($confidence, 1);
    }

    /**
     * Seleciona melhor resposta considerando contexto da conversa
     */
    protected function selectBestResponse($matches, $session)
    {
        if (empty($matches)) {
            return null;
        }

        // Considera contexto da conversa
        $contextStack = $session->context_stack ?? [];
        $lastIntent = $session->last_intent ?? null;
        $entities = $session->entities ?? [];
        
        foreach ($matches as &$match) {
            $contextBonus = 0;
            
            // BOOST ALTO: Contextos que requerem conversa anterior
            if (isset($match['context']) && isset($match['context']->requires_context) && $match['context']->requires_context) {
                if (!empty($contextStack)) {
                    // Se há contexto anterior E o padrão requer contexto, aumenta muito
                    $contextBonus += 0.3;
                    
                    // BOOST EXTRA: Se é continuação direta do último intent
                    $lastContext = end($contextStack);
                    if ($this->isRelatedContext($match['intent'], $lastContext)) {
                        $contextBonus += 0.2;
                    }
                } else {
                    // Se NÃO há contexto mas o padrão requer, penaliza
                    $contextBonus -= 0.4;
                }
            }
            
            // BOOST MÉDIO: Relacionado ao contexto anterior mesmo sem flag
            if (!empty($contextStack)) {
                $lastContext = end($contextStack);
                
                // Busca/produto seguido de especificação
                if (strpos($lastContext, 'search_') !== false && 
                    (strpos($match['intent'], 'continuation') !== false || 
                     strpos($match['intent'], 'size_') !== false ||
                     strpos($match['intent'], 'preference_') !== false)) {
                    $contextBonus += 0.25;
                }
                
                // Carrinho seguido de quantidade/modificação
                if (strpos($lastContext, 'cart') !== false && 
                    (strpos($match['intent'], 'quantity') !== false ||
                     strpos($match['intent'], 'remove_') !== false ||
                     strpos($match['intent'], 'add_extra') !== false)) {
                    $contextBonus += 0.25;
                }
                
                // Pergunta seguida de resposta sim/não
                if (strpos($lastContext, 'ask_') !== false && 
                    (strpos($match['intent'], 'confirm_') !== false ||
                     strpos($match['intent'], 'cancel_') !== false)) {
                    $contextBonus += 0.25;
                }
            }
            
            // BOOST BAIXO: Entidades conhecidas na mensagem
            if (!empty($entities)) {
                $contextBonus += 0.1;
            }
            
            // Aplica o bonus
            $match['confidence'] = min($match['confidence'] + $contextBonus, 1.0);
            
            // Debug info
            $match['context_bonus'] = $contextBonus;
        }

        // Re-ordena por confiança
        usort($matches, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });

        return $matches[0];
    }
    
    /**
     * Verifica se dois contextos são relacionados
     */
    protected function isRelatedContext($current, $previous)
    {
        // Extrair categoria base
        $currentBase = explode('_', $current)[0];
        $previousBase = explode('_', $previous)[0];
        
        // Mesma categoria = relacionado
        if ($currentBase === $previousBase) {
            return true;
        }
        
        // Pares relacionados
        $related = [
            'search' => ['filter', 'show', 'continuation'],
            'cart' => ['add', 'remove', 'quantity', 'finish'],
            'ask' => ['confirm', 'cancel', 'response'],
            'greeting' => ['goodbye', 'help'],
        ];
        
        foreach ($related as $base => $relatedList) {
            if (strpos($previous, $base) !== false) {
                foreach ($relatedList as $rel) {
                    if (strpos($current, $rel) !== false) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Registra interação para aprendizado futuro
     */
    protected function recordInteraction($input, $result, $session, $userId)
    {
        AITrainingData::create([
            'user_id' => $userId,
            'input' => $input,
            'expected_output' => $result['response'] ?? null, // O que esperamos que responda
            'actual_output' => $result['response'] ?? null,
            'intent' => $result['intent'] ?? 'unknown',
            'context' => [
                'session_token' => $session->session_token,
                'previous_intent' => $session->last_intent,
                'message_count' => $session->message_count
            ],
            'confidence' => $result['confidence'] ?? 0,
            'correct' => true, // Será atualizado pelo feedback
        ]);

        // Registra uso do contexto
        if (isset($result['context'])) {
            $result['context']->recordUsage(true);
        }
    }

    /**
     * Aprende com feedback do usuário
     */
    public function learnFromFeedback($trainingDataId, $correct, $feedbackScore = null)
    {
        $data = AITrainingData::find($trainingDataId);
        if (!$data) {
            return ['status' => 'not_found'];
        }

        $data->correct = $correct;
        $data->feedback_score = $feedbackScore;
        $data->save();

        // Se incorreto, ajusta pesos da rede
        if (!$correct) {
            $this->backpropagate($data);
        }

        return ['status' => 'learned', 'training_data_id' => $trainingDataId];
    }

    /**
     * Backpropagation para ajustar pesos
     */
    protected function backpropagate($trainingData)
    {
        // Implementação simplificada de backpropagation
        // Em produção, isso seria mais complexo
        
        $inputVector = $this->textToVector($trainingData->input);
        $outputs = $this->forwardPropagation($inputVector);

        // Calcula erro (simplificado)
        $error = 1 - $trainingData->confidence;

        // Atualiza pesos das sinapses proporcionalmente ao erro
        $outputNeurons = AINeuron::where('layer', 'output')
            ->with('inputSynapses')
            ->get();

        foreach ($outputNeurons as $neuron) {
            foreach ($neuron->inputSynapses as $synapse) {
                $delta = $error * $this->learningRate * $synapse->fromNeuron->activation;
                $synapse->updateWeight($delta, $this->learningRate);
            }
        }

        $trainingData->markAsTrained();
    }
    
    /**
     * Enriquece resposta com produtos reais do banco
     */
    protected function enrichWithProducts($match, $message, $empresaId, $session = null)
    {
        if (!$empresaId) {
            return $match;
        }
        
        $query = \App\Models\Produto::where('tenant_code', $empresaId)
            ->where('ativo', true);
        
        // Extrai termos de busca da mensagem
        $searchTerms = $this->extractSearchTerms($message);
        $categoryFilter = null;
        
        // Se não há termos na mensagem atual, busca no contexto da sessão
        if (empty($searchTerms) && $session && $session->last_intent) {
            // Extrai termo do último intent (ex: "search_cerveja" -> "cerveja")
            if (preg_match('/search_(.+)/', $session->last_intent, $matches)) {
                $term = $matches[1];
                
                // Verifica se é uma categoria genérica (bebida, comida, lanche)
                $categories = ['bebida', 'comida', 'lanche', 'sobremesa', 'entrada'];
                if (in_array($term, $categories)) {
                    $categoryFilter = $term;
                } else {
                    $searchTerms = [$term];
                }
            }
        }
        
        // Filtra por categoria se for genérica
        if ($categoryFilter) {
            $categoria = \App\Models\Categoria::where('nome', 'LIKE', "%{$categoryFilter}%")
                ->where('tenant_code', $empresaId)
                ->first();
            
            if ($categoria) {
                $query->where('categoria_id', $categoria->id);
            }
        }
        
        // Busca por nome ou descrição
        if (!empty($searchTerms)) {
            $query->where(function($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    // Busca apenas no nome (não na descrição para ser mais restritivo)
                    $q->orWhere('nome', 'LIKE', $term)
                      ->orWhere('nome', 'LIKE', "{$term}%")
                      ->orWhere('nome', 'LIKE', "%{$term}");
                }
            });
            
            // Ordenar por relevância e limitar a 5 produtos
            $produtos = $query->get()->sortByDesc(function($produto) use ($searchTerms) {
                $score = 0;
                $nome = mb_strtolower($produto->nome);
                
                foreach ($searchTerms as $term) {
                    $termLower = mb_strtolower($term);
                    
                    // Nome exato = 100 pontos
                    if ($nome === $termLower) {
                        $score += 100;
                    }
                    // Nome começa com o termo = 50 pontos
                    elseif (strpos($nome, $termLower) === 0) {
                        $score += 50;
                    }
                    // Nome contém o termo = 25 pontos
                    elseif (strpos($nome, $termLower) !== false) {
                        $score += 25;
                    }
                }
                
                return $score;
            })->take(5); // Limitar a 5 produtos
        } else {
            // Busca produtos
            $produtos = $query->orderBy('preco', 'asc')->limit(5)->get();
        }
        
        if ($produtos->isEmpty()) {
            $match['response'] = "Não encontrei produtos com essas características. Quer ver o cardápio completo?";
            $match['products'] = [];
            return $match;
        }
        
        // Remover emojis dos produtos
        $produtos = $produtos->map(function($produto) {
            $produto->nome = $this->removeEmojis($produto->nome);
            $produto->descricao = $this->removeEmojis($produto->descricao);
            return $produto;
        });
        
        // Verifica se é busca por "mais barato" ou "menor preço"
        if (preg_match('/(mais barato|mais barata|menor pre[çc]o|menor valor)/i', $message)) {
            $produto = $produtos->first();
            $match['response'] = sprintf(
                "O(a) %s mais barato(a) é **%s** por **R$ %.2f**. %s. Quer adicionar ao carrinho?",
                $match['parameters']['product'] ?? $searchTerms[0] ?? 'produto',
                $produto->nome,
                $produto->preco,
                $produto->descricao ? substr($produto->descricao, 0, 60) . '...' : ''
            );
            $match['products'] = [$produto];
            $match['parameters']['selected_product'] = $produto->id;
        } else {
            // Lista produtos encontrados (apenas top 3)
            $count = $produtos->count();
            $topProducts = $produtos->take(3);
            
            $list = $topProducts->map(function($p) {
                return sprintf("• **%s** - R$ %.2f", $p->nome, $p->preco);
            })->implode("\n");
            
            $match['response'] = sprintf(
                "Encontrei %d %s:\n\n%s\n\nClique no botão \"Adicionar ao Carrinho\" do produto que você quiser! 🛒",
                $count,
                $count > 1 ? 'opções' : 'opção',
                $list
            );
            $match['products'] = $topProducts->toArray();
        }
        
        // Salvar produtos na sessão para usar no "quero esse"
        if ($session) {
            $entities = $session->entities ?? [];
            $entities['last_products'] = $match['products'];
            $session->entities = $entities;
            // NÃO salvar aqui - será salvo no final do processMessage
        }
        
        return $match;
    }
    
    /**
     * Extrai termos de busca da mensagem
     */
    protected function extractSearchTerms($message)
    {
        $text = mb_strtolower($message);
        
        // Remove palavras comuns e qualificadores
        $stopWords = ['o', 'a', 'de', 'da', 'do', 'para', 'com', 'sem', 'mais', 'menos', 'quero', 'busca', 'procura', 'mostra', 'me', 'um', 'uma', 'barato', 'barata', 'caro', 'cara', 'melhor', 'pior', 'grande', 'pequeno', 'médio'];
        
        $words = preg_split('/\s+/', $text);
        $terms = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        return array_values($terms);
    }
    
    /**
     * Remove emojis de uma string
     */
    protected function removeEmojis($text)
    {
        if (!$text) return $text;
        
        // Remove emojis usando regex
        $text = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $text); // Emoticons
        $text = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $text); // Símbolos & pictogramas
        $text = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $text); // Transporte & símbolos de mapa
        $text = preg_replace('/[\x{1F1E0}-\x{1F1FF}]/u', '', $text); // Bandeiras
        $text = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $text); // Símbolos diversos
        $text = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $text); // Dingbats
        $text = preg_replace('/[\x{FE00}-\x{FE0F}]/u', '', $text); // Seletores de variação
        $text = preg_replace('/[\x{1F900}-\x{1F9FF}]/u', '', $text); // Símbolos suplementares
        
        // Remove espaços extras
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }

    /**
     * Busca ou cria sessão de conversa
     */
    protected function getOrCreateSession($sessionToken, $userId)
    {
        if ($sessionToken) {
            $session = AIConversationSession::where('session_token', $sessionToken)->first();
            if ($session && !$session->isExpired()) {
                // Forçar reload do banco para pegar entities atualizados
                $session->refresh();
                return $session;
            }
        }

        return AIConversationSession::createSession($userId);
    }
    
    /**
     * Adiciona produto ao carrinho do app (cliente)
     */
    protected function handleAddToAppCart($match, $session)
    {
        // Pegar último produto mostrado
        $lastProducts = $session->entities['last_products'] ?? [];
        
        if (empty($lastProducts)) {
            $match['response'] = 'Você precisa escolher um produto primeiro. Que tal pedir algo? Ex: "quero cerveja"';
            return $match;
        }
        
        $product = $lastProducts[0]; // Pega o primeiro produto
        
        // Retornar dados do produto para o frontend adicionar ao carrinho
        $match['response'] = sprintf(
            "✅ %s - R$ %.2f será adicionado ao carrinho! Continue escolhendo ou vá ao carrinho para finalizar.",
            $product['nome'],
            $product['preco']
        );
        
        // Incluir o ID do produto para o frontend usar
        $match['add_to_cart_product'] = [
            'id' => $product['id'],
            'nome' => $product['nome'],
            'preco' => $product['preco']
        ];
        
        return $match;
    }
    
    /**
     * Navega para a tela de carrinho do app
     */
    protected function handleViewAppCart($match, $session)
    {
        $match['response'] = '🛒 Abrindo seu carrinho!';
        $match['navigate_to'] = 'cart';
        
        return $match;
    }
    
    /**
     * Navega para a tela de checkout do app
     */
    protected function handleCheckoutApp($match, $session)
    {
        // Validação: verificar se há items no carrinho antes de ir para checkout
        // Como o app usa appState.cart no frontend, não validamos aqui
        // O frontend deve validar antes de chamar proceedToCheckout
        
        $match['response'] = '✅ Vamos finalizar seu pedido!';
        $match['navigate_to'] = 'checkout';
        
        return $match;
    }
    
    /**
     * Confirma endereço de entrega
     */
    protected function handleConfirmAddress($match, $session, $userId)
    {
        if (!$userId) {
            $match['response'] = 'Você precisa estar logado para verificar seu endereço.';
            return $match;
        }
        
        // Buscar endereço da tabela clientes
        $cliente = DB::table('clientes')->where('id', $userId)->first();
        
        if (!$cliente || !$cliente->endereco_rua) {
            $match['response'] = 'Você ainda não cadastrou um endereço. Vou abrir a tela para você cadastrar!';
            $match['navigate_to'] = 'address_form';
            return $match;
        }
        
        $endereco = sprintf(
            "%s, %s - %s, %s - %s",
            $cliente->endereco_rua,
            $cliente->endereco_numero ?? 'S/N',
            $cliente->endereco_bairro ?? '',
            $cliente->endereco_cidade ?? '',
            $cliente->estado ?? ''
        );
        
        $match['response'] = sprintf(
            "Seu endereço de entrega é: %s. Está correto? Se quiser alterar, diga 'mudar endereço'.",
            $endereco
        );
        
        $match['address_data'] = [
            'endereco' => $cliente->endereco_rua,
            'numero' => $cliente->endereco_numero,
            'complemento' => $cliente->endereco_complemento,
            'bairro' => $cliente->endereco_bairro,
            'cidade' => $cliente->endereco_cidade,
            'estado' => $cliente->estado,
            'cep' => $cliente->endereco_cep
        ];
        
        return $match;
    }
    
    /**
     * Abre tela para alterar endereço
     */
    protected function handleChangeAddress($match, $session)
    {
        $match['response'] = 'Vou abrir a tela para você alterar o endereço!';
        $match['navigate_to'] = 'address_form';
        
        return $match;
    }
    
    /**
     * Mostra formas de pagamento disponíveis
     */
    protected function handleShowPaymentMethods($match, $session)
    {
        $paymentMethods = [
            'money' => 'Dinheiro',
            'card' => 'Cartão (Débito/Crédito)',
            'pix' => 'PIX'
        ];
        
        $methodsList = implode(', ', array_values($paymentMethods));
        
        $match['response'] = sprintf(
            "Formas de pagamento disponíveis: %s. Qual você prefere?",
            $methodsList
        );
        
        $match['payment_methods'] = $paymentMethods;
        
        return $match;
    }
    
    /**
     * Seleciona forma de pagamento
     */
    protected function handleSelectPayment($match, $session)
    {
        $parameters = $match['parameters'] ?? [];
        $paymentMethod = $parameters['payment_method'] ?? null;
        $needsChange = $parameters['needs_change'] ?? false;
        
        $paymentNames = [
            'dinheiro' => 'Dinheiro',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito' => 'Cartão de Débito',
            'pix' => 'PIX'
        ];
        
        $paymentName = $paymentNames[$paymentMethod] ?? 'forma de pagamento';
        
        // Resposta base
        $match['response'] = sprintf(
            "✅ Pagamento via %s selecionado!",
            $paymentName
        );
        
        // Se for dinheiro, perguntar sobre troco
        if ($paymentMethod === 'dinheiro' && $needsChange) {
            $match['response'] .= " Precisa de troco para quanto?";
        } else if ($paymentMethod === 'dinheiro') {
            $match['response'] .= " Agora é só confirmar o pedido.";
        } else {
            $match['response'] .= " Agora é só confirmar o pedido.";
        }
        
        $match['payment_selected'] = [
            'method' => $paymentMethod,
            'name' => $paymentName,
            'needs_change' => $needsChange
        ];
        
        // Salvar na sessão
        $entities = $session->entities ?? [];
        $entities['payment_method'] = $paymentMethod;
        $session->entities = $entities;
        
        return $match;
    }
    
    /**
     * Define valor do troco
     */
    protected function handleSetChangeAmount($match, $session)
    {
        $parameters = $match['parameters'] ?? [];
        $message = $match['message'] ?? '';
        
        // Extrair valor do troco da mensagem
        preg_match('/(\d+)/', $message, $matches);
        $amount = $matches[1] ?? null;
        
        if ($amount) {
            $match['response'] = "Troco para R$ {$amount},00 anotado! Agora é só confirmar o pedido.";
            $match['change_amount'] = floatval($amount);
            
            // Salvar na sessão
            $entities = $session->entities ?? [];
            $entities['change_for'] = floatval($amount);
            $session->entities = $entities;
        } else {
            $match['response'] = "Não entendi o valor. Pode repetir? Por exemplo: 'troco para 50'";
        }
        
        return $match;
    }
    
    /**
     * Handler para confirmar/finalizar pedido
     */
    protected function handleConfirmOrder($match, $session)
    {
        // SIMPLIFICADO: Apenas navega para checkout
        // O app já tem toda a lógica de validação e confirmação
        
        $match['response'] = "Certo! Vou abrir a tela de finalização para você conferir tudo e confirmar o pedido.";
        $match['navigate_to'] = 'checkout';
        
        return $match;
    }
    
    /**
     * Handler para confirmar pedido (clica no botão de confirmar)
     */
    protected function handleConfirmAction($match, $session)
    {
        // SIMPLIFICADO: Apenas aciona o botão de confirmar pedido que já existe no app
        $match['response'] = 'Perfeito! Finalizando seu pedido agora...';
        $match['navigate_to'] = 'confirm_order';
        
        return $match;
    }
    
    /**
     * Processa ações de carrinho via backend
     */
    protected function processCartAction($match, $message, $session, $userId, $empresaId)
    {
        $action = $match['action'];
        $cart = $session->entities['cart'] ?? [];
        
        switch ($action) {
            case 'addToCartBackend':
                // Pegar último produto mostrado
                $lastProducts = $session->entities['last_products'] ?? [];
                
                if (empty($lastProducts)) {
                    $match['response'] = 'Você precisa escolher um produto primeiro. Que tal pedir algo? Ex: "quero cerveja"';
                    $match['cart'] = $cart;
                    break;
                }
                
                $product = $lastProducts[0]; // Pega o primeiro produto
                
                // Adicionar ao carrinho
                $productId = $product['id'];
                $found = false;
                
                foreach ($cart as &$item) {
                    if ($item['product_id'] == $productId) {
                        $item['quantity']++;
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $cart[] = [
                        'product_id' => $productId,
                        'nome' => $product['nome'],
                        'preco' => $product['preco'],
                        'quantity' => 1
                    ];
                }
                
                // Salvar carrinho na sessão
                $entities = $session->entities ?? [];
                $entities['cart'] = $cart;
                $session->entities = $entities;
                $session->save();
                
                $total = array_sum(array_map(fn($item) => $item['preco'] * $item['quantity'], $cart));
                $match['response'] = sprintf(
                    "✅ %s adicionado(a) ao carrinho! Seu pedido agora tem %d item(ns) - Total: R$ %.2f. Quer adicionar mais algo ou finalizar?",
                    $product['nome'],
                    count($cart),
                    $total
                );
                $match['cart'] = $cart;
                break;
                
            case 'viewCartBackend':
                if (empty($cart)) {
                    $match['response'] = 'Seu carrinho está vazio. Que tal pedir algo? Ex: "quero pizza"';
                    $match['cart'] = [];
                    break;
                }
                
                $items = array_map(function($item) {
                    return sprintf("• %dx %s - R$ %.2f", $item['quantity'], $item['nome'], $item['preco'] * $item['quantity']);
                }, $cart);
                
                $total = array_sum(array_map(fn($item) => $item['preco'] * $item['quantity'], $cart));
                
                $match['response'] = sprintf(
                    "🛒 Seu carrinho:\n\n%s\n\n**Total: R$ %.2f**\n\nQuer finalizar o pedido?",
                    implode("\n", $items),
                    $total
                );
                $match['cart'] = $cart;
                break;
                
            case 'checkoutBackend':
                if (empty($cart)) {
                    $match['response'] = 'Seu carrinho está vazio. Adicione produtos antes de finalizar.';
                    $match['cart'] = [];
                    break;
                }
                
                // Criar pedido
                try {
                    $total = array_sum(array_map(fn($item) => $item['preco'] * $item['quantity'], $cart));
                    
                    $pedido = \App\Models\Pedido::create([
                        'usuario_id' => $userId,
                        'tenant_code' => $empresaId,
                        'total' => $total,
                        'status' => 'pendente',
                        'origem' => 'ia_assistant',
                    ]);
                    
                    // Adicionar itens do pedido
                    foreach ($cart as $item) {
                        $subtotal = $item['preco'] * $item['quantity'];
                        \App\Models\ItemPedido::create([
                            'pedido_id' => $pedido->id,
                            'produto_id' => $item['product_id'],
                            'quantidade' => $item['quantity'],
                            'preco_unitario' => $item['preco'],
                            'subtotal' => $subtotal,
                        ]);
                    }
                    
                    // Limpar carrinho
                    $entities = $session->entities ?? [];
                    $entities['cart'] = [];
                    $session->entities = $entities;
                    $session->save();
                    
                    $match['response'] = sprintf(
                        "🎉 Pedido #%d finalizado com sucesso!\n\nTotal: R$ %.2f\n\nEm breve você receberá a confirmação. Obrigado!",
                        $pedido->id,
                        $total
                    );
                    $match['cart'] = [];
                    $match['pedido_id'] = $pedido->id;
                    
                } catch (\Exception $e) {
                    $match['response'] = 'Ops! Tive um problema ao finalizar o pedido. Tente novamente.';
                    \Log::error('Erro ao finalizar pedido via IA: ' . $e->getMessage());
                }
                break;
                
            case 'clearCartBackend':
                $entities = $session->entities ?? [];
                $entities['cart'] = [];
                $session->entities = $entities;
                $session->save();
                
                $match['response'] = '🗑️ Carrinho limpo! Quer começar um novo pedido?';
                $match['cart'] = [];
                break;
        }
        
        return $match;
    }

    /**
     * Treina em lote com dados históricos
     */
    public function batchTrain($limit = 100)
    {
        $trainingData = AITrainingData::where('used_for_training', false)
            ->where('correct', true)
            ->limit($limit)
            ->get();

        $trained = 0;
        foreach ($trainingData as $data) {
            $this->backpropagate($data);
            $trained++;
        }

        return [
            'status' => 'completed',
            'trained_count' => $trained
        ];
    }
}
