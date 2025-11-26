// Gerenciamento de Restaurantes

// Mostrar lista de restaurantes
async function showRestaurants() {
    setActivePage('restaurants');
    
    const content = `
        <div class="fade-in">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-store me-2"></i>Escolha o Restaurante
                </h5>
            </div>
            
            <!-- Lista de Restaurantes -->
            <div id="restaurantsList">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Carregando restaurantes...</p>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
    await loadRestaurants();
}

// Carregar lista de restaurantes
async function loadRestaurants() {
    try {
        const response = await fetch(`${API_BASE_URL}/app/restaurantes`);
        const data = await response.json();
        
        console.log('Resposta da API restaurantes:', data);
        
        const restaurants = data.restaurantes || data.data || data;
        
        if (restaurants && Array.isArray(restaurants) && restaurants.length > 0) {
            appState.restaurants = restaurants;
            renderRestaurants(restaurants);
        } else {
            throw new Error('Nenhum restaurante encontrado');
        }
    } catch (error) {
        console.error('Erro ao carregar restaurantes:', error);
        document.getElementById('restaurantsList').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                <p class="text-muted">Erro ao carregar restaurantes</p>
                <button class="btn btn-primary" onclick="loadRestaurants()">Tentar novamente</button>
            </div>
        `;
    }
}

// Renderizar lista de restaurantes
function renderRestaurants(restaurants) {
    if (!restaurants || restaurants.length === 0) {
        document.getElementById('restaurantsList').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-store-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum restaurante disponível</p>
            </div>
        `;
        return;
    }
    
    const restaurantsHtml = restaurants.map(restaurant => {
        const isOpen = checkIfOpen(restaurant.horario_abertura, restaurant.horario_fechamento);
        const statusBadge = isOpen 
            ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Aberto</span>'
            : '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Fechado</span>';
        
        const logo = restaurant.logo 
            ? `<img src="/storage/${restaurant.logo}" class="restaurant-logo" alt="${restaurant.nome_fantasia}">`
            : `<div class="restaurant-logo-placeholder"><i class="fas fa-store fa-2x text-primary"></i></div>`;
        
        const deliveryInfo = restaurant.aceita_delivery 
            ? `
                <div class="mt-2 pt-2 border-top">
                    <small class="text-muted d-block">
                        <i class="fas fa-motorcycle me-1"></i>
                        Entrega: R$ ${parseFloat(restaurant.taxa_entrega_padrao || 0).toFixed(2)} 
                        • ${restaurant.tempo_entrega_minutos || 45} min
                    </small>
                    <small class="text-muted">
                        <i class="fas fa-shopping-bag me-1"></i>
                        Pedido mínimo: R$ ${parseFloat(restaurant.pedido_minimo || 0).toFixed(2)}
                    </small>
                </div>
            `
            : '';
        
        return `
            <div class="card restaurant-card mb-3 shadow-sm" onclick="selectRestaurantForMenu('${restaurant.tenant_code}')" style="cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            ${logo}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">${restaurant.nome_fantasia}</h6>
                                    <small class="text-muted">${restaurant.razao_social || ''}</small>
                                </div>
                                ${statusBadge}
                            </div>
                            
                            ${restaurant.descricao ? `<p class="text-muted small mb-2">${restaurant.descricao}</p>` : ''}
                            
                            <div class="restaurant-info">
                                <small class="text-muted d-block">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    ${restaurant.endereco_rua}, ${restaurant.endereco_numero} - ${restaurant.endereco_bairro}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="fas fa-clock me-1"></i>
                                    ${restaurant.horario_abertura || '00:00'} às ${restaurant.horario_fechamento || '23:59'}
                                </small>
                                ${restaurant.telefone || restaurant.celular ? `
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone me-1"></i>
                                        ${restaurant.celular || restaurant.telefone}
                                    </small>
                                ` : ''}
                            </div>
                            
                            ${deliveryInfo}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    document.getElementById('restaurantsList').innerHTML = restaurantsHtml;
}

// Verificar se restaurante está aberto
function checkIfOpen(opening, closing) {
    if (!opening || !closing) return true;
    
    try {
        const now = new Date();
        const currentTime = now.getHours() * 60 + now.getMinutes();
        
        const [openHour, openMin] = opening.split(':').map(Number);
        const [closeHour, closeMin] = closing.split(':').map(Number);
        
        const openTime = openHour * 60 + openMin;
        const closeTime = closeHour * 60 + closeMin;
        
        if (closeTime < openTime) {
            // Fecha depois da meia-noite
            return currentTime >= openTime || currentTime <= closeTime;
        }
        
        return currentTime >= openTime && currentTime <= closeTime;
    } catch (error) {
        return true;
    }
}

// Selecionar restaurante e ir para o cardápio
async function selectRestaurantForMenu(tenantCode) {
    try {
        console.log('🏪 Restaurante selecionado:', tenantCode);
        
        // Salvar tenant_code no localStorage
        localStorage.setItem('selected_tenant_code', tenantCode);
        localStorage.setItem('tenant_code', tenantCode); // Para compatibilidade
        
        // Atualizar appState
        appState.selectedRestaurant = tenantCode;
        
        // Buscar dados do restaurante
        const restaurant = appState.restaurants?.find(r => r.tenant_code === tenantCode);
        if (restaurant) {
            console.log('✅ Restaurante:', restaurant.nome_fantasia);
        }
        
        // Mostrar feedback visual
        showAlert(`${restaurant?.nome_fantasia || 'Restaurante'} selecionado!`, 'success');
        
        // Aguardar um momento e ir para o cardápio
        setTimeout(() => {
            showMenu();
        }, 300);
        
    } catch (error) {
        console.error('Erro ao selecionar restaurante:', error);
        showAlert('Erro ao selecionar restaurante', 'danger');
    }
}

// Verificar se tem restaurante selecionado
function hasSelectedRestaurant() {
    return !!localStorage.getItem('selected_tenant_code');
}

// Obter restaurante selecionado
function getSelectedRestaurant() {
    return localStorage.getItem('selected_tenant_code');
}

// Limpar seleção de restaurante
function clearSelectedRestaurant() {
    localStorage.removeItem('selected_tenant_code');
    localStorage.removeItem('tenant_code');
    appState.selectedRestaurant = null;
}
