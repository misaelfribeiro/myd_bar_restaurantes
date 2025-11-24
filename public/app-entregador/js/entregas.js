// Gerenciamento de Entregas

// Carregar entregas disponíveis
async function carregarEntregasDisponiveis() {
    try {
        const data = await fetchAPI(ENDPOINTS.ENTREGAS_DISPONIVEIS);
        
        if (data.success) {
            renderizarEntregasDisponiveis(data.entregas);
        }
    } catch (error) {
        console.error('Erro ao carregar entregas disponíveis:', error);
    }
}

// Carregar entregas ativas
async function carregarEntregasAceitas() {
    try {
        const data = await fetchAPI(ENDPOINTS.ENTREGAS_ATIVAS);
        
        if (data.success) {
            renderizarEntregasAceitas(data.entregas);
            atualizarEstatisticas(data.stats);
        }
    } catch (error) {
        console.error('Erro ao carregar entregas ativas:', error);
    }
}

// Renderizar entregas disponíveis
function renderizarEntregasDisponiveis(entregas) {
    const container = document.getElementById('entregasDisponiveis');
    
    if (!entregas || entregas.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>Nenhuma entrega disponível no momento</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = entregas.map(entrega => `
        <div class="entrega-card">
            <div class="entrega-header">
                <span class="entrega-numero">
                    <i class="fas fa-box"></i> #${entrega.id}
                </span>
                <span class="entrega-valor">R$ ${formatarDinheiro(entrega.taxa_entrega)}</span>
            </div>
            
            <div class="entrega-info">
                <div class="info-item">
                    <i class="fas fa-user"></i>
                    <span>${entrega.cliente_nome}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${entrega.endereco_completo}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-route"></i>
                    <span>${entrega.distancia ? entrega.distancia + ' km' : 'Calcular rota'}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span>Tempo estimado: ${entrega.tempo_estimado} min</span>
                </div>
            </div>
            
            <div class="entrega-actions">
                <button class="btn btn-success" onclick="aceitarEntrega(${entrega.id})">
                    <i class="fas fa-check"></i> Aceitar
                </button>
                <button class="btn btn-secondary" onclick="verDetalhesEntrega(${entrega.id})">
                    <i class="fas fa-eye"></i> Detalhes
                </button>
            </div>
        </div>
    `).join('');
}

// Renderizar entregas aceitas
function renderizarEntregasAceitas(entregas) {
    const container = document.getElementById('entregasAceitas');
    
    if (!entregas || entregas.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>Você não tem entregas ativas</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = entregas.map(entrega => `
        <div class="entrega-card">
            <div class="entrega-header">
                <span class="entrega-numero">
                    <i class="fas fa-box"></i> #${entrega.id}
                </span>
                <span class="badge badge-${getStatusColor(entrega.status)}">${getStatusLabel(entrega.status)}</span>
            </div>
            
            <div class="entrega-info">
                <div class="info-item">
                    <i class="fas fa-user"></i>
                    <span>${entrega.cliente_nome}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <span>${entrega.cliente_telefone}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${entrega.endereco_completo}</span>
                </div>
                ${entrega.observacoes ? `
                <div class="info-item">
                    <i class="fas fa-comment"></i>
                    <span>${entrega.observacoes}</span>
                </div>
                ` : ''}
            </div>
            
            <div class="entrega-actions">
                ${getAcoesEntrega(entrega)}
            </div>
        </div>
    `).join('');
}

// Aceitar entrega
async function aceitarEntrega(id) {
    if (!confirm('Deseja aceitar esta entrega?')) return;
    
    try {
        const data = await fetchAPI(`${ENDPOINTS.ACEITAR_ENTREGA}/${id}`, {
            method: 'POST'
        });
        
        if (data.success) {
            alert('Entrega aceita com sucesso!');
            carregarEntregasDisponiveis();
            carregarEntregasAceitas();
        } else {
            alert(data.message || 'Erro ao aceitar entrega');
        }
    } catch (error) {
        console.error('Erro ao aceitar entrega:', error);
        alert('Erro ao aceitar entrega');
    }
}

// Atualizar status da entrega
async function atualizarStatusEntrega(id, status) {
    try {
        const data = await fetchAPI(`${ENDPOINTS.ATUALIZAR_STATUS}/${id}`, {
            method: 'POST',
            body: JSON.stringify({ status })
        });
        
        if (data.success) {
            carregarEntregasAceitas();
            
            if (status === 'entregue') {
                alert('Entrega finalizada com sucesso!');
            }
        } else {
            alert(data.message || 'Erro ao atualizar status');
        }
    } catch (error) {
        console.error('Erro ao atualizar status:', error);
        alert('Erro ao atualizar status');
    }
}

// Ver detalhes da entrega
async function verDetalhesEntrega(id) {
    try {
        const data = await fetchAPI(`${API_URL}/entregadores/entregas/${id}`);
        
        if (data.success) {
            const entrega = data.entrega;
            
            const modalBody = document.getElementById('modalEntregaBody');
            modalBody.innerHTML = `
                <div class="entrega-detalhes">
                    <h4><i class="fas fa-box"></i> Pedido #${entrega.id}</h4>
                    
                    <div class="detalhes-section">
                        <h5><i class="fas fa-user"></i> Cliente</h5>
                        <p><strong>Nome:</strong> ${entrega.cliente_nome}</p>
                        <p><strong>Telefone:</strong> ${entrega.cliente_telefone}</p>
                        <p><strong>Endereço:</strong> ${entrega.endereco_completo}</p>
                    </div>
                    
                    <div class="detalhes-section">
                        <h5><i class="fas fa-dollar-sign"></i> Pagamento</h5>
                        <p><strong>Taxa de Entrega:</strong> R$ ${formatarDinheiro(entrega.taxa_entrega)}</p>
                        ${entrega.pedido ? `<p><strong>Total do Pedido:</strong> R$ ${formatarDinheiro(entrega.pedido.total)}</p>` : ''}
                    </div>
                    
                    ${entrega.observacoes ? `
                    <div class="detalhes-section">
                        <h5><i class="fas fa-comment"></i> Observações</h5>
                        <p>${entrega.observacoes}</p>
                    </div>
                    ` : ''}
                    
                    <div id="detalhesMap" style="height: 200px; border-radius: 8px; margin-top: 15px;"></div>
                </div>
            `;
            
            document.getElementById('modalEntrega').classList.add('active');
            
            // Inicializar mapa
            setTimeout(() => {
                if (entrega.latitude && entrega.longitude) {
                    const map = L.map('detalhesMap').setView([entrega.latitude, entrega.longitude], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                    L.marker([entrega.latitude, entrega.longitude]).addTo(map);
                }
            }, 100);
        }
    } catch (error) {
        console.error('Erro ao carregar detalhes:', error);
    }
}

// Fechar modal
function fecharModal() {
    document.getElementById('modalEntrega').classList.remove('active');
}

// Get ações da entrega baseado no status
function getAcoesEntrega(entrega) {
    switch(entrega.status) {
        case 'pendente':
            return `
                <button class="btn btn-success" onclick="atualizarStatusEntrega(${entrega.id}, 'coletado')">
                    <i class="fas fa-box"></i> Coletar Pedido
                </button>
                <button class="btn btn-secondary" onclick="verDetalhesEntrega(${entrega.id})">
                    <i class="fas fa-eye"></i> Detalhes
                </button>
            `;
        case 'coletado':
            return `
                <button class="btn btn-success" onclick="atualizarStatusEntrega(${entrega.id}, 'em_rota')">
                    <i class="fas fa-route"></i> Iniciar Rota
                </button>
                <button class="btn btn-secondary" onclick="verDetalhesEntrega(${entrega.id})">
                    <i class="fas fa-map"></i> Ver Mapa
                </button>
            `;
        case 'em_rota':
            return `
                <button class="btn btn-success" onclick="atualizarStatusEntrega(${entrega.id}, 'entregue')">
                    <i class="fas fa-check"></i> Confirmar Entrega
                </button>
                <button class="btn btn-secondary" onclick="verDetalhesEntrega(${entrega.id})">
                    <i class="fas fa-map"></i> Ver Mapa
                </button>
            `;
        default:
            return `
                <button class="btn btn-secondary" onclick="verDetalhesEntrega(${entrega.id})">
                    <i class="fas fa-eye"></i> Ver Detalhes
                </button>
            `;
    }
}

// Get status color
function getStatusColor(status) {
    const colors = {
        'pendente': 'warning',
        'coletado': 'info',
        'em_rota': 'primary',
        'entregue': 'success',
        'cancelado': 'danger'
    };
    return colors[status] || 'secondary';
}

// Get status label
function getStatusLabel(status) {
    const labels = {
        'pendente': 'Pendente',
        'coletado': 'Coletado',
        'em_rota': 'Em Rota',
        'entregue': 'Entregue',
        'cancelado': 'Cancelado'
    };
    return labels[status] || status;
}

// Atualizar estatísticas
function atualizarEstatisticas(stats) {
    if (stats) {
        document.getElementById('totalEntregas').textContent = stats.total || 0;
        document.getElementById('ganhosHoje').textContent = 'R$ ' + formatarDinheiro(stats.ganhos || 0);
    }
}

// Formatar dinheiro
function formatarDinheiro(valor) {
    return parseFloat(valor || 0).toFixed(2).replace('.', ',');
}

// Carregar histórico
async function carregarHistorico(filtro = 'hoje') {
    try {
        const data = await fetchAPI(`${ENDPOINTS.HISTORICO}?filtro=${filtro}`);
        
        if (data.success) {
            renderizarHistorico(data.entregas);
        }
    } catch (error) {
        console.error('Erro ao carregar histórico:', error);
    }
}

// Renderizar histórico
function renderizarHistorico(entregas) {
    const container = document.getElementById('historicoLista');
    
    if (!entregas || entregas.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <p>Nenhuma entrega no histórico</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = entregas.map(entrega => `
        <div class="entrega-card">
            <div class="entrega-header">
                <span class="entrega-numero">#${entrega.id}</span>
                <span class="entrega-valor">R$ ${formatarDinheiro(entrega.taxa_entrega)}</span>
            </div>
            <div class="entrega-info">
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <span>${formatarData(entrega.data_entrega)}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${entrega.endereco_bairro}, ${entrega.endereco_cidade}</span>
                </div>
            </div>
        </div>
    `).join('');
}

// Filtrar histórico
function filtrarHistorico() {
    const filtro = document.getElementById('filtroHistorico').value;
    carregarHistorico(filtro);
}

// Carregar ganhos
async function carregarGanhos() {
    try {
        const data = await fetchAPI(ENDPOINTS.GANHOS);
        
        if (data.success) {
            document.getElementById('ganhoHoje').textContent = 'R$ ' + formatarDinheiro(data.hoje);
            document.getElementById('ganhoSemana').textContent = 'R$ ' + formatarDinheiro(data.semana);
            document.getElementById('ganhoMes').textContent = 'R$ ' + formatarDinheiro(data.mes);
        }
    } catch (error) {
        console.error('Erro ao carregar ganhos:', error);
    }
}

// Formatar data
function formatarData(data) {
    const date = new Date(data);
    return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}
