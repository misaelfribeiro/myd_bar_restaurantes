// Admin Carla - JavaScript
const API_BASE = window.location.origin + '/api/ai';
let statsData = null;
let addContextModal = null;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    addContextModal = new bootstrap.Modal(document.getElementById('addContextModal'));
    loadStats();
    loadContexts();
    loadRecentInteractions();
    
    // Auto-refresh every 30 seconds
    setInterval(loadStats, 30000);
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
        showError('Erro ao carregar estatísticas');
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
    
    // Correct Rate
    document.getElementById('correctRate').textContent = `${correctRate.toFixed(1)}%`;
    document.getElementById('correctRateBar').style.width = `${correctRate}%`;
    updateProgressColor('correctRateBar', correctRate);
    
    // Average Confidence
    document.getElementById('avgConfidence').textContent = `${(avgConfidence * 100).toFixed(1)}%`;
    document.getElementById('avgConfidenceBar').style.width = `${avgConfidence * 100}%`;
    updateProgressColor('avgConfidenceBar', avgConfidence * 100);
    
    // Context Success
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
        const contexts = await response.json();
        
        document.getElementById('contextsCount').textContent = contexts.length;
        
        const tbody = document.getElementById('contextsTable');
        if (contexts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">Nenhum contexto encontrado</td></tr>';
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
                    ${ctx.is_active ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'}
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Erro ao carregar contextos:', error);
        document.getElementById('contextsTable').innerHTML = 
            '<tr><td colspan="7" class="text-center text-danger">Erro ao carregar contextos</td></tr>';
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
                        <strong><i class="fas fa-user"></i> Entrada:</strong> ${item.input}
                        <br>
                        <strong><i class="fas fa-robot"></i> Saída:</strong> ${item.actual_output}
                        <br>
                        <small class="text-muted">
                            Intent: <code>${item.intent || 'N/A'}</code> | 
                            Context: <code>${item.context || 'N/A'}</code> | 
                            Confiança: <strong>${(item.confidence * 100).toFixed(1)}%</strong>
                        </small>
                    </div>
                    <div class="text-end">
                        ${item.correct ? 
                            '<span class="badge bg-success"><i class="fas fa-check"></i> Correto</span>' : 
                            '<span class="badge bg-warning"><i class="fas fa-question"></i> Não validado</span>'
                        }
                        <br>
                        <small class="text-muted">${formatDate(item.created_at)}</small>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Erro ao carregar interações:', error);
        document.getElementById('recentInteractions').innerHTML = 
            '<p class="text-center text-danger">Erro ao carregar interações</p>';
    }
}

// Train with History
async function trainWithHistory() {
    if (!confirm('Deseja treinar a Carla com todo o histórico de interações? Isso pode levar alguns minutos.')) {
        return;
    }
    
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Treinando...';
    
    try {
        const response = await fetch(`${API_BASE}/train`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ batch: true })
        });
        
        const result = await response.json();
        
        showSuccess(`Treinamento concluído! ${result.trained_count} interações processadas.`);
        loadStats();
    } catch (error) {
        console.error('Erro ao treinar:', error);
        showError('Erro ao executar treinamento');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-graduation-cap"></i> Treinar com Histórico';
    }
}

// Show Add Context Modal
function showAddContextModal() {
    document.getElementById('addContextForm').reset();
    addContextModal.show();
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
        is_active: true
    };
    
    try {
        const response = await fetch(`${API_BASE}/contexts`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(contextData)
        });
        
        if (response.ok) {
            showSuccess('Contexto adicionado com sucesso!');
            addContextModal.hide();
            loadContexts();
            loadStats();
        } else {
            const error = await response.json();
            showError(error.message || 'Erro ao salvar contexto');
        }
    } catch (error) {
        console.error('Erro ao salvar contexto:', error);
        showError('Erro ao salvar contexto');
    }
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

function showSuccess(message) {
    alert('✅ ' + message);
}

function showError(message) {
    alert('❌ ' + message);
}

// Export Stats (Bonus)
function exportStats() {
    if (!statsData) return;
    
    const dataStr = JSON.stringify(statsData, null, 2);
    const blob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `carla-stats-${new Date().toISOString()}.json`;
    a.click();
    URL.revokeObjectURL(url);
}
