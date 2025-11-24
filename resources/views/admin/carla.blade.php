@extends('layouts.app')

@section('title', 'Painel IA - Carla')

@section('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s;
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 10px;
    }
    
    .neuron-bar {
        height: 10px;
        border-radius: 5px;
        background: linear-gradient(90deg, var(--info), var(--primary));
        margin: 5px 0;
    }
    
    .interaction-item {
        border-left: 3px solid var(--primary);
        padding: 10px 15px;
        margin: 10px 0;
        background: #f8f9fa;
        border-radius: 5px;
    }
    
    .progress-label {
        font-size: 12px;
        margin-bottom: 5px;
    }
    
    .loading {
        text-align: center;
        padding: 40px;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="bi bi-robot"></i> Painel IA - Carla
    </h1>
    <p class="text-muted">Gerenciamento Completo da Inteligência Artificial</p>
</div>

<!-- Stats Cards -->
<div class="row" id="statsCards">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary text-white">
                <i class="bi bi-cpu"></i>
            </div>
            <h6 class="text-muted">Neurônios</h6>
            <h3 id="totalNeurons">-</h3>
            <small class="text-muted">Rede Neural</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-success text-white">
                <i class="bi bi-diagram-3"></i>
            </div>
            <h6 class="text-muted">Sinapses</h6>
            <h3 id="totalSynapses">-</h3>
            <small class="text-muted">Conexões</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-info text-white">
                <i class="bi bi-book"></i>
            </div>
            <h6 class="text-muted">Contextos</h6>
            <h3 id="totalContexts">-</h3>
            <small class="text-muted">Conhecimento</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning text-white">
                <i class="bi bi-chat-dots"></i>
            </div>
            <h6 class="text-muted">Interações</h6>
            <h3 id="totalInteractions">-</h3>
            <small class="text-muted">Conversas</small>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-graph-up"></i> Métricas de Performance</h5>
                <hr>
                <div class="mb-3">
                    <div class="progress-label d-flex justify-content-between">
                        <span>Taxa de Acerto</span>
                        <strong id="correctRate">-</strong>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div id="correctRateBar" class="progress-bar bg-success" role="progressbar"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="progress-label d-flex justify-content-between">
                        <span>Confiança Média</span>
                        <strong id="avgConfidence">-</strong>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div id="avgConfidenceBar" class="progress-bar bg-info" role="progressbar"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="progress-label d-flex justify-content-between">
                        <span>Taxa de Sucesso (Contextos)</span>
                        <strong id="contextSuccess">-</strong>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div id="contextSuccessBar" class="progress-bar bg-primary" role="progressbar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-mortarboard"></i> Status do Treinamento</h5>
                <hr>
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h2 id="trainedCount" class="text-success">-</h2>
                        <small>Dados Treinados</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h2 id="pendingCount" class="text-warning">-</h2>
                        <small>Pendentes</small>
                    </div>
                    <div class="col-6">
                        <h2 id="totalUpdates" class="text-primary">-</h2>
                        <small>Atualizações</small>
                    </div>
                    <div class="col-6">
                        <h2 id="avgWeight" class="text-info">-</h2>
                        <small>Peso Médio</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><i class="bi bi-gear"></i> Ações de Treinamento</h5>
        <hr>
        <div class="row">
            <div class="col-md-4 mb-3">
                <button class="btn btn-primary w-100" onclick="trainWithHistory()">
                    <i class="bi bi-mortarboard"></i> Treinar com Histórico
                </button>
                <small class="text-muted d-block mt-2">Aplica backpropagation nos dados acumulados</small>
            </div>
            <div class="col-md-4 mb-3">
                <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addContextModal">
                    <i class="bi bi-plus-circle"></i> Adicionar Contexto
                </button>
                <small class="text-muted d-block mt-2">Ensinar novo conhecimento à Carla</small>
            </div>
            <div class="col-md-4 mb-3">
                <button class="btn btn-info w-100" onclick="loadStats()">
                    <i class="bi bi-arrow-clockwise"></i> Atualizar Estatísticas
                </button>
                <small class="text-muted d-block mt-2">Recarregar dados em tempo real</small>
            </div>
        </div>
    </div>
</div>

<!-- Contexts List -->
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><i class="bi bi-book-half"></i> Contextos Ativos (<span id="contextsCount">0</span>)</h5>
        <hr>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Categoria</th>
                        <th>Key</th>
                        <th>Padrão</th>
                        <th>Uso</th>
                        <th>Taxa Sucesso</th>
                        <th>Confiança</th>
                        <th>Status</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody id="contextsTable">
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2">Carregando contextos...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Interactions -->
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><i class="bi bi-chat-left-text"></i> Últimas Interações (10)</h5>
        <hr>
        <div id="recentInteractions">
            <div class="loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2">Carregando interações...</p>
            </div>
        </div>
    </div>
</div>

<!-- Análise de Feedback dos Usuários -->
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><i class="bi bi-hand-thumbs-up"></i> Análise de Feedback dos Usuários</h5>
        <hr>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                    <h2 class="text-success mb-0" id="feedbackPositive">-</h2>
                    <small class="text-muted">👍 Positivos</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                    <h2 class="text-danger mb-0" id="feedbackNegative">-</h2>
                    <small class="text-muted">👎 Negativos</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                    <h2 class="text-warning mb-0" id="feedbackNeutral">-</h2>
                    <small class="text-muted">😐 Neutros</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                    <h2 class="text-info mb-0" id="feedbackSatisfaction">-</h2>
                    <small class="text-muted">📊 Satisfação</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h6>Distribuição de Avaliações</h6>
                <div class="progress mb-2" style="height: 30px;">
                    <div id="feedbackPositiveBar" class="progress-bar bg-success" style="width: 0%">0%</div>
                    <div id="feedbackNeutralBar" class="progress-bar bg-warning" style="width: 0%">0%</div>
                    <div id="feedbackNegativeBar" class="progress-bar bg-danger" style="width: 0%">0%</div>
                </div>
                <div class="d-flex justify-content-between small text-muted">
                    <span>👍 Positivo</span>
                    <span>😐 Neutro</span>
                    <span>👎 Negativo</span>
                </div>
            </div>
            <div class="col-md-6">
                <h6>Top 5 Contextos Mais Avaliados</h6>
                <div id="topRatedContexts">
                    <div class="text-center text-muted">
                        <small>Carregando...</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h6>Avaliações Recentes</h6>
            <div id="recentFeedbacks" style="max-height: 300px; overflow-y: auto;">
                <div class="text-center text-muted">
                    <small>Carregando feedbacks...</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Neural Network Details -->
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><i class="bi bi-diagram-3-fill"></i> Estrutura da Rede Neural</h5>
        <hr>
        <div class="row">
            <div class="col-md-4">
                <h6>Camada de Entrada</h6>
                <div id="inputLayer"></div>
            </div>
            <div class="col-md-4">
                <h6>Camada Oculta</h6>
                <div id="hiddenLayer"></div>
            </div>
            <div class="col-md-4">
                <h6>Camada de Saída</h6>
                <div id="outputLayer"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Context -->
<div class="modal fade" id="addContextModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Adicionar Novo Contexto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addContextForm">
                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select class="form-select" id="contextCategory" required>
                            <option value="greeting">Saudação</option>
                            <option value="search">Busca</option>
                            <option value="menu">Cardápio</option>
                            <option value="cart">Carrinho</option>
                            <option value="orders">Pedidos</option>
                            <option value="delivery">Entrega</option>
                            <option value="info">Informação</option>
                            <option value="help">Ajuda</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Key (Identificador único)</label>
                        <input type="text" class="form-control" id="contextKey" required placeholder="ex: search_pizza">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Padrão (Regex)</label>
                        <input type="text" class="form-control" id="contextPattern" required placeholder="ex: *(pizza)*">
                        <small class="text-muted">Use * como wildcard, (x|y) para opções</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Resposta</label>
                        <textarea class="form-control" id="contextResponse" rows="3" required placeholder="Resposta da Carla"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ação</label>
                        <select class="form-select" id="contextAction">
                            <option value="">Nenhuma</option>
                            <optgroup label="🔍 Busca e Filtros">
                                <option value="searchProduct">Buscar Produto</option>
                                <option value="filterByCategory">Filtrar por Categoria</option>
                                <option value="showMenu">Mostrar Cardápio</option>
                            </optgroup>
                            <optgroup label="🛒 Carrinho e Compras">
                                <option value="showCart">Mostrar Carrinho</option>
                                <option value="addToCart">Adicionar ao Carrinho</option>
                                <option value="removeFromCart">Remover do Carrinho</option>
                                <option value="checkout">Finalizar Pedido</option>
                            </optgroup>
                            <optgroup label="📦 Pedidos">
                                <option value="showOrders">Mostrar Pedidos</option>
                                <option value="repeatOrder">Repetir Pedido</option>
                                <option value="cancelOrder">Cancelar Pedido</option>
                                <option value="scheduleOrder">Agendar Pedido</option>
                            </optgroup>
                            <optgroup label="🚚 Entrega">
                                <option value="trackDelivery">Rastrear Entrega</option>
                                <option value="changeAddress">Mudar Endereço</option>
                            </optgroup>
                            <optgroup label="💰 Pagamento e Promoções">
                                <option value="applyDiscount">Aplicar Cupom</option>
                                <option value="showPromotions">Mostrar Promoções</option>
                                <option value="changePayment">Mudar Forma de Pagamento</option>
                            </optgroup>
                            <optgroup label="👤 Conta e Perfil">
                                <option value="showProfile">Mostrar Perfil</option>
                                <option value="showFavorites">Mostrar Favoritos</option>
                                <option value="showReviews">Mostrar Avaliações</option>
                            </optgroup>
                            <optgroup label="❓ Ajuda">
                                <option value="contactSupport">Contatar Suporte</option>
                                <option value="showFAQ">Mostrar FAQ</option>
                            </optgroup>
                        </select>
                        <small class="text-muted">Ação que será executada no app quando este contexto for ativado</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Limiar de Confiança (0.0 - 1.0)</label>
                        <input type="number" class="form-control" id="contextThreshold" step="0.1" min="0" max="1" value="0.7">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveContext()">Salvar Contexto</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Context -->
<div class="modal fade" id="editContextModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Contexto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editContextForm">
                    <input type="hidden" id="editContextId">
                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select class="form-select" id="editContextCategory" required>
                            <option value="greeting">Saudação</option>
                            <option value="search">Busca</option>
                            <option value="menu">Cardápio</option>
                            <option value="cart">Carrinho</option>
                            <option value="orders">Pedidos</option>
                            <option value="delivery">Entrega</option>
                            <option value="info">Informação</option>
                            <option value="help">Ajuda</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Key (Identificador único)</label>
                        <input type="text" class="form-control" id="editContextKey" required readonly>
                        <small class="text-muted">A key não pode ser alterada</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Padrão (Regex)</label>
                        <input type="text" class="form-control" id="editContextPattern" required>
                        <small class="text-muted">Use * como wildcard, (x|y) para opções</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Resposta</label>
                        <textarea class="form-control" id="editContextResponse" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ação</label>
                        <select class="form-select" id="editContextAction">
                            <option value="">Nenhuma</option>
                            <optgroup label="🔍 Busca e Filtros">
                                <option value="searchProduct">Buscar Produto</option>
                                <option value="filterByCategory">Filtrar por Categoria</option>
                                <option value="showMenu">Mostrar Cardápio</option>
                            </optgroup>
                            <optgroup label="🛒 Carrinho e Compras">
                                <option value="showCart">Mostrar Carrinho</option>
                                <option value="addToCart">Adicionar ao Carrinho</option>
                                <option value="removeFromCart">Remover do Carrinho</option>
                                <option value="checkout">Finalizar Pedido</option>
                            </optgroup>
                            <optgroup label="📦 Pedidos">
                                <option value="showOrders">Mostrar Pedidos</option>
                                <option value="repeatOrder">Repetir Pedido</option>
                                <option value="cancelOrder">Cancelar Pedido</option>
                                <option value="scheduleOrder">Agendar Pedido</option>
                            </optgroup>
                            <optgroup label="🚚 Entrega">
                                <option value="trackDelivery">Rastrear Entrega</option>
                                <option value="changeAddress">Mudar Endereço</option>
                            </optgroup>
                            <optgroup label="💰 Pagamento e Promoções">
                                <option value="applyDiscount">Aplicar Cupom</option>
                                <option value="showPromotions">Mostrar Promoções</option>
                                <option value="changePayment">Mudar Forma de Pagamento</option>
                            </optgroup>
                            <optgroup label="👤 Conta e Perfil">
                                <option value="showProfile">Mostrar Perfil</option>
                                <option value="showFavorites">Mostrar Favoritos</option>
                                <option value="showReviews">Mostrar Avaliações</option>
                            </optgroup>
                            <optgroup label="❓ Ajuda">
                                <option value="contactSupport">Contatar Suporte</option>
                                <option value="showFAQ">Mostrar FAQ</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Limiar de Confiança (0.0 - 1.0)</label>
                        <input type="number" class="form-control" id="editContextThreshold" step="0.1" min="0" max="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="editContextActive">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="updateContext()">Atualizar Contexto</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const API_BASE = '{{ url("/api/ai") }}';
let statsData = null;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadStats();
    loadContexts();
    loadRecentInteractions();
    loadFeedbackAnalysis();
    
    // Auto-refresh every 30 seconds
    setInterval(loadStats, 30000);
    setInterval(loadFeedbackAnalysis, 60000); // Feedback a cada 60s
});

// Load Statistics
async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/stats`);
        const data = await response.json();
        statsData = data;
        
        updateStatsCards(data);
        updatePerformanceMetrics(data);
        updateTrainingStatus(data);
        updateNeuralNetwork(data);
    } catch (error) {
        console.error('Erro ao carregar estatísticas:', error);
    }
}

// Update Stats Cards
function updateStatsCards(data) {
    document.getElementById('totalNeurons').textContent = data.total_neurons || 0;
    document.getElementById('totalSynapses').textContent = data.total_synapses || 0;
    document.getElementById('totalContexts').textContent = data.total_contexts || 0;
    document.getElementById('totalInteractions').textContent = data.total_interactions || 0;
}

// Update Performance Metrics
function updatePerformanceMetrics(data) {
    const correctRate = data.correct_rate || 0;
    const avgConfidence = data.avg_confidence || 0;
    const contextSuccess = data.context_success_rate || 0;
    
    document.getElementById('correctRate').textContent = `${correctRate.toFixed(1)}%`;
    document.getElementById('correctRateBar').style.width = `${correctRate}%`;
    updateProgressColor('correctRateBar', correctRate);
    
    document.getElementById('avgConfidence').textContent = `${(avgConfidence * 100).toFixed(1)}%`;
    document.getElementById('avgConfidenceBar').style.width = `${avgConfidence * 100}%`;
    updateProgressColor('avgConfidenceBar', avgConfidence * 100);
    
    document.getElementById('contextSuccess').textContent = `${contextSuccess.toFixed(1)}%`;
    document.getElementById('contextSuccessBar').style.width = `${contextSuccess}%`;
    updateProgressColor('contextSuccessBar', contextSuccess);
}

// Update Progress Bar Color
function updateProgressColor(elementId, value) {
    const bar = document.getElementById(elementId);
    bar.className = 'progress-bar';
    
    if (value >= 90) {
        bar.classList.add('bg-success');
    } else if (value >= 70) {
        bar.classList.add('bg-info');
    } else if (value >= 50) {
        bar.classList.add('bg-warning');
    } else {
        bar.classList.add('bg-danger');
    }
}

// Update Training Status
function updateTrainingStatus(data) {
    document.getElementById('trainedCount').textContent = data.trained_count || 0;
    document.getElementById('pendingCount').textContent = data.pending_training || 0;
    document.getElementById('totalUpdates').textContent = formatNumber(data.total_synapse_updates || 0);
    document.getElementById('avgWeight').textContent = (data.avg_synapse_weight || 0).toFixed(4);
}

// Update Neural Network Display
function updateNeuralNetwork(data) {
    const layers = data.neurons_by_layer || { input: 0, hidden: 0, output: 0 };
    
    updateLayerDisplay('inputLayer', layers.input || 0, 'info');
    updateLayerDisplay('hiddenLayer', layers.hidden || 0, 'primary');
    updateLayerDisplay('outputLayer', layers.output || 0, 'success');
}

// Update Layer Display
function updateLayerDisplay(elementId, count, color) {
    const container = document.getElementById(elementId);
    container.innerHTML = `
        <p class="text-center"><strong>${count}</strong> neurônios</p>
        <div class="neuron-bar bg-${color}" style="width: ${Math.min(count / 100 * 100, 100)}%"></div>
    `;
}

// Load Contexts
async function loadContexts() {
    try {
        const response = await fetch(`${API_BASE}/contexts`);
        const result = await response.json();
        
        // A API retorna {success: true, data: {data: [...], ...paginação}}
        const contexts = result.data?.data || result.data || [];
        
        document.getElementById('contextsCount').textContent = contexts.length;
        
        const tbody = document.getElementById('contextsTable');
        if (contexts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">Nenhum contexto encontrado</td></tr>';
            return;
        }
        
        tbody.innerHTML = contexts.map(ctx => `
            <tr>
                <td><span class="badge bg-primary">${ctx.category}</span></td>
                <td><code>${ctx.key}</code></td>
                <td><small>${truncate(ctx.pattern, 50)}</small></td>
                <td><span class="badge bg-info">${ctx.usage_count || 0}</span></td>
                <td>
                    <div class="progress" style="height: 20px; min-width: 80px;">
                        <div class="progress-bar ${getSuccessColor(ctx.success_rate)}" 
                             style="width: ${(ctx.success_rate * 100).toFixed(0)}%">
                            ${(ctx.success_rate * 100).toFixed(0)}%
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-secondary">${ctx.confidence_threshold || 0.7}</span></td>
                <td>
                    ${ctx.active ? '<i class="bi bi-check-circle text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>'}
                </td>
                <td>
                    <button class="btn btn-sm btn-warning me-1" onclick='editContext(${JSON.stringify(ctx)})' title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteContext(${ctx.id}, '${ctx.key}')" title="Deletar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Erro ao carregar contextos:', error);
        document.getElementById('contextsTable').innerHTML = '<tr><td colspan="8" class="text-center text-danger">Erro ao carregar contextos</td></tr>';
    }
}

// Load Recent Interactions
async function loadRecentInteractions() {
    try {
        const response = await fetch(`${API_BASE}/stats`);
        const data = await response.json();
        const interactions = data.recent_interactions || [];
        
        const container = document.getElementById('recentInteractions');
        
        if (interactions.length === 0) {
            container.innerHTML = '<p class="text-center text-muted">Nenhuma interação recente</p>';
            return;
        }
        
        container.innerHTML = interactions.map(item => `
            <div class="interaction-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <strong><i class="bi bi-person"></i> Entrada:</strong> ${item.input}
                        <br>
                        <strong><i class="bi bi-robot"></i> Saída:</strong> ${item.actual_output}
                        <br>
                        <small class="text-muted">
                            Intent: <code>${item.intent || 'N/A'}</code> | 
                            Context: <code>${item.context || 'N/A'}</code> | 
                            Confiança: <strong>${(item.confidence * 100).toFixed(1)}%</strong>
                        </small>
                    </div>
                    <div class="text-end">
                        ${item.correct ? 
                            '<span class="badge bg-success"><i class="bi bi-check"></i> Correto</span>' : 
                            '<span class="badge bg-warning"><i class="bi bi-question"></i> Não validado</span>'
                        }
                        <br>
                        <small class="text-muted">${formatDate(item.created_at)}</small>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Erro ao carregar interações:', error);
    }
}

// Train with History
async function trainWithHistory() {
    if (!confirm('Deseja treinar a Carla com todo o histórico de interações? Isso pode levar alguns minutos.')) {
        return;
    }
    
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Treinando...';
    
    try {
        const response = await fetch(`${API_BASE}/train`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ batch: true })
        });
        
        const result = await response.json();
        
        alert(`✅ Treinamento concluído! ${result.data?.trained_count || 0} interações processadas.`);
        loadStats();
    } catch (error) {
        console.error('Erro ao treinar:', error);
        alert('❌ Erro ao executar treinamento');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-mortarboard"></i> Treinar com Histórico';
    }
}

// Save Context
async function saveContext() {
    const form = document.getElementById('addContextForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const contextData = {
        category: document.getElementById('contextCategory').value,
        key: document.getElementById('contextKey').value,
        pattern: document.getElementById('contextPattern').value,
        response_template: document.getElementById('contextResponse').value,
        action: document.getElementById('contextAction').value || null,
        confidence_threshold: parseFloat(document.getElementById('contextThreshold').value),
        active: true
    };
    
    try {
        const response = await fetch(`${API_BASE}/contexts`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(contextData)
        });
        
        if (response.ok) {
            alert('✅ Contexto adicionado com sucesso!');
            bootstrap.Modal.getInstance(document.getElementById('addContextModal')).hide();
            form.reset();
            loadContexts();
            loadStats();
        } else {
            const error = await response.json();
            alert('❌ ' + (error.message || 'Erro ao salvar contexto'));
        }
    } catch (error) {
        console.error('Erro ao salvar contexto:', error);
        alert('❌ Erro ao salvar contexto');
    }
}

// Edit Context
function editContext(context) {
    // Preencher o formulário
    document.getElementById('editContextId').value = context.id;
    document.getElementById('editContextCategory').value = context.category;
    document.getElementById('editContextKey').value = context.key;
    document.getElementById('editContextPattern').value = context.pattern;
    document.getElementById('editContextResponse').value = context.response_template;
    document.getElementById('editContextAction').value = context.action || '';
    document.getElementById('editContextThreshold').value = context.confidence_threshold;
    document.getElementById('editContextActive').value = context.active ? '1' : '0';
    
    // Abrir modal
    new bootstrap.Modal(document.getElementById('editContextModal')).show();
}

// Update Context
async function updateContext() {
    const id = document.getElementById('editContextId').value;
    const contextData = {
        category: document.getElementById('editContextCategory').value,
        pattern: document.getElementById('editContextPattern').value,
        response_template: document.getElementById('editContextResponse').value,
        action: document.getElementById('editContextAction').value,
        confidence_threshold: parseFloat(document.getElementById('editContextThreshold').value),
        active: document.getElementById('editContextActive').value === '1'
    };
    
    try {
        const response = await fetch(`${API_BASE}/contexts/${id}`, {
            method: 'PUT',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(contextData)
        });
        
        if (response.ok) {
            alert('✅ Contexto atualizado com sucesso!');
            bootstrap.Modal.getInstance(document.getElementById('editContextModal')).hide();
            loadContexts();
            loadStats();
        } else {
            const error = await response.json();
            alert('❌ ' + (error.message || 'Erro ao atualizar contexto'));
        }
    } catch (error) {
        console.error('Erro ao atualizar contexto:', error);
        alert('❌ Erro ao atualizar contexto');
    }
}

// Delete Context
async function deleteContext(id, key) {
    if (!confirm(`Tem certeza que deseja deletar o contexto "${key}"?\n\nEsta ação não pode ser desfeita!`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/contexts/${id}`, {
            method: 'DELETE',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        if (response.ok) {
            alert('✅ Contexto deletado com sucesso!');
            loadContexts();
            loadStats();
        } else {
            const error = await response.json();
            alert('❌ ' + (error.message || 'Erro ao deletar contexto'));
        }
    } catch (error) {
        console.error('Erro ao deletar contexto:', error);
        alert('❌ Erro ao deletar contexto');
    }
}

// Load Feedback Analysis
async function loadFeedbackAnalysis() {
    try {
        const response = await fetch(`${API_BASE}/feedback/stats`);
        const data = await response.json();
        
        if (!data.success) {
            console.error('Erro ao carregar feedback stats');
            return;
        }
        
        const stats = data.data;
        
        // Cards de totais
        document.getElementById('feedbackPositive').textContent = stats.positive_count || 0;
        document.getElementById('feedbackNegative').textContent = stats.negative_count || 0;
        document.getElementById('feedbackNeutral').textContent = stats.neutral_count || 0;
        document.getElementById('feedbackSatisfaction').textContent = 
            (stats.satisfaction_rate || 0).toFixed(1) + '%';
        
        // Barra de progresso
        const total = (stats.positive_count || 0) + (stats.negative_count || 0) + (stats.neutral_count || 0);
        if (total > 0) {
            const posPercent = ((stats.positive_count || 0) / total * 100).toFixed(1);
            const neuPercent = ((stats.neutral_count || 0) / total * 100).toFixed(1);
            const negPercent = ((stats.negative_count || 0) / total * 100).toFixed(1);
            
            document.getElementById('feedbackPositiveBar').style.width = posPercent + '%';
            document.getElementById('feedbackPositiveBar').textContent = posPercent + '%';
            document.getElementById('feedbackNeutralBar').style.width = neuPercent + '%';
            document.getElementById('feedbackNeutralBar').textContent = neuPercent + '%';
            document.getElementById('feedbackNegativeBar').style.width = negPercent + '%';
            document.getElementById('feedbackNegativeBar').textContent = negPercent + '%';
        }
        
        // Top contextos avaliados
        if (stats.top_rated_contexts && stats.top_rated_contexts.length > 0) {
            document.getElementById('topRatedContexts').innerHTML = stats.top_rated_contexts.map(ctx => `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <div>
                        <small><code>${ctx.context}</code></small>
                        <br>
                        <span class="badge bg-${ctx.avg_score >= 4 ? 'success' : ctx.avg_score >= 3 ? 'warning' : 'danger'}">
                            ${ctx.avg_score.toFixed(1)} ⭐
                        </span>
                    </div>
                    <span class="text-muted">${ctx.count} avaliações</span>
                </div>
            `).join('');
        } else {
            document.getElementById('topRatedContexts').innerHTML = 
                '<div class="text-center text-muted"><small>Nenhuma avaliação ainda</small></div>';
        }
        
        // Feedbacks recentes
        if (stats.recent_feedbacks && stats.recent_feedbacks.length > 0) {
            document.getElementById('recentFeedbacks').innerHTML = stats.recent_feedbacks.map(fb => `
                <div class="d-flex justify-content-between align-items-start mb-2 p-2 border-bottom">
                    <div class="flex-grow-1">
                        <small class="text-muted">${formatDate(fb.created_at)}</small>
                        <br>
                        <strong>Entrada:</strong> <small>${truncate(fb.input, 50)}</small>
                        <br>
                        <strong>Resposta:</strong> <small>${truncate(fb.actual_output, 50)}</small>
                    </div>
                    <div class="text-end ms-2">
                        ${getFeedbackIcon(fb.feedback_score)}
                        <br>
                        <span class="badge bg-${fb.correct ? 'success' : 'danger'}">
                            ${fb.correct ? '✓' : '✗'}
                        </span>
                    </div>
                </div>
            `).join('');
        } else {
            document.getElementById('recentFeedbacks').innerHTML = 
                '<div class="text-center text-muted"><small>Nenhum feedback recente</small></div>';
        }
        
    } catch (error) {
        console.error('Erro ao carregar análise de feedback:', error);
    }
}

function getFeedbackIcon(score) {
    if (score >= 4) return '<span class="text-success fs-4">👍</span>';
    if (score >= 3) return '<span class="text-warning fs-4">😐</span>';
    return '<span class="text-danger fs-4">👎</span>';
}

// Utility Functions
function formatNumber(num) {
    return new Intl.NumberFormat('pt-BR').format(num);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR', { 
        day: '2-digit', 
        month: '2-digit', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

function truncate(str, maxLength) {
    return str.length > maxLength ? str.substring(0, maxLength) + '...' : str;
}

function getSuccessColor(rate) {
    if (rate >= 0.9) return 'bg-success';
    if (rate >= 0.7) return 'bg-info';
    if (rate >= 0.5) return 'bg-warning';
    return 'bg-danger';
}
</script>
@endpush
