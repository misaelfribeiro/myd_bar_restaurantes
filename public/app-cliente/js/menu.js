// Menu e Cardápio

let selectedCategory = null;
let showFavoritesOnly = false;
let searchTimeout = null;
let favorites = JSON.parse(localStorage.getItem('product_favorites') || '[]');

function showMenu(categoryId = null) {
    // Verificar se tem restaurante selecionado
    if (!hasSelectedRestaurant()) {
        console.log('⚠️ Nenhum restaurante selecionado, redirecionando...');
        showRestaurants();
        return;
    }
    
    setActivePage('menu');
    selectedCategory = categoryId;
    
    // Parar tracking de pedidos se estiver ativo
    if (typeof stopOrderTracking === 'function') {
        stopOrderTracking();
    }
    
    // Obter nome do restaurante selecionado
    const tenantCode = getSelectedRestaurant();
    const restaurant = appState.restaurants?.find(r => r.tenant_code === tenantCode);
    const restaurantName = restaurant?.nome_fantasia || 'Cardápio';
    
    const content = `
        <div class="fade-in">
            <!-- Info do Restaurante Selecionado -->
            <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
                <div>
                    <i class="fas fa-store me-2"></i>
                    <strong>${restaurantName}</strong>
                </div>
                <button class="btn btn-sm btn-outline-primary" onclick="showRestaurants()">
                    <i class="fas fa-exchange-alt me-1"></i>Trocar
                </button>
            </div>
            
            <!-- Search Bar Aprimorada -->
            <div class="search-bar position-relative mb-3">
                <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" class="form-control ps-5 pe-5" placeholder="Buscar produtos..." 
                       id="searchInput" oninput="debouncedSearch()">
                <button class="btn btn-link position-absolute d-none" id="clearSearch" 
                        style="right: 5px; top: 50%; transform: translateY(-50%);" onclick="clearSearch()">
                    <i class="fas fa-times text-muted"></i>
                </button>
            </div>
            
            <!-- Filtros e Contadores -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <small class="text-muted" id="resultsCounter"></small>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleFavoritesOnly()" id="favBtn">
                        <i class="far fa-heart"></i> Favoritos
                    </button>
                </div>
            </div>

            <!-- Categories -->
            <div class="category-pills mb-3" id="categoryPills">
                <div class="text-center w-100 py-2">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="productsGrid">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Carregando produtos...</p>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
    loadMenuData();
}

async function loadMenuData() {
    try {
        // Carregar categorias
        if (appState.categories.length === 0) {
            const categoriesResponse = await fetch(`${API_BASE_URL}/app/categorias`);
            const categoriesData = await categoriesResponse.json();
            
            console.log('Categorias recebidas:', categoriesData);
            
            // Categorias podem vir como array direto ou dentro de um objeto
            if (Array.isArray(categoriesData)) {
                appState.categories = categoriesData;
            } else {
                appState.categories = categoriesData.categorias || categoriesData.data || [];
            }
        }
        
        // Renderizar categorias
        renderCategories();
        
        // Carregar produtos
        await loadProducts();
        
    } catch (error) {
        console.error('Erro ao carregar menu:', error);
        document.getElementById('productsGrid').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                <p class="text-muted">Erro ao carregar produtos</p>
                <button class="btn btn-primary" onclick="loadMenuData()">Tentar novamente</button>
            </div>
        `;
    }
}

function renderCategories() {
    const categoriesHtml = `
        <div class="category-pill ${!selectedCategory ? 'active' : ''}" onclick="filterByCategory(null)">
            <i class="fas fa-th me-1"></i> Todos
        </div>
        ${appState.categories.map(cat => `
            <div class="category-pill ${selectedCategory === cat.id ? 'active' : ''}" 
                 onclick="filterByCategory(${cat.id})">
                ${cat.nome}
            </div>
        `).join('')}
    `;
    
    document.getElementById('categoryPills').innerHTML = categoriesHtml;
}

async function loadProducts(categoryId = null) {
    try {
        let url = `${API_BASE_URL}/app/produtos`;
        const params = new URLSearchParams();
        
        // Adicionar tenant_code do restaurante selecionado
        const tenantCode = getSelectedRestaurant();
        if (tenantCode) {
            params.append('tenant_code', tenantCode);
        }
        
        if (categoryId) {
            params.append('categoria_id', categoryId);
        }
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        console.log('Produtos recebidos:', data);
        
        // A API retorna { success: true, produtos: [...] }
        if (Array.isArray(data)) {
            appState.products = data;
        } else {
            appState.products = data.produtos || data.data || [];
        }
        
        // Filtrar produtos ativos (não "disponivel", é "ativo")
        appState.products = appState.products.filter(p => p.ativo === 1 || p.ativo === true);
        
        console.log('Produtos filtrados:', appState.products.length);
        
        renderProducts(appState.products);
        
    } catch (error) {
        console.error('Erro ao carregar produtos:', error);
        document.getElementById('productsGrid').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                <p class="text-muted">Erro ao carregar produtos</p>
                <button class="btn btn-primary" onclick="loadProducts()">Tentar novamente</button>
            </div>
        `;
    }
}

function renderProducts(products) {
    // Filtrar favoritos se necessário
    let filteredProducts = products;
    if (showFavoritesOnly) {
        filteredProducts = products.filter(p => favorites.includes(p.id));
    }
    
    // Atualizar contador
    const counter = document.getElementById('resultsCounter');
    if (counter) {
        counter.textContent = `${filteredProducts.length} produto${filteredProducts.length !== 1 ? 's' : ''}`;
    }
    
    if (filteredProducts.length === 0) {
        const message = showFavoritesOnly ? 
            'Nenhum produto favorito. Toque no ❤️ para adicionar!' : 
            'Nenhum produto encontrado';
        
        document.getElementById('productsGrid').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-shopping-basket fa-3x text-muted mb-3"></i>
                <p class="text-muted">${message}</p>
                ${showFavoritesOnly ? '<button class="btn btn-primary" onclick="toggleFavoritesOnly()">Ver Todos</button>' : ''}
            </div>
        `;
        return;
    }
    
    const productsHtml = filteredProducts.map(product => createProductCard(product)).join('');
    document.getElementById('productsGrid').innerHTML = productsHtml;
}

function filterByCategory(categoryId) {
    selectedCategory = categoryId;
    renderCategories();
    loadProducts(categoryId);
}

function searchProducts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    if (searchTerm.length === 0) {
        renderProducts(appState.products);
        return;
    }
    
    const filtered = appState.products.filter(product => 
        product.nome.toLowerCase().includes(searchTerm) ||
        (product.descricao && product.descricao.toLowerCase().includes(searchTerm))
    );
    
    renderProducts(filtered);
}

function showProductDetail(productId) {
    const product = appState.products.find(p => p.id === productId);
    
    if (!product) {
        showAlert('Produto não encontrado', 'danger');
        return;
    }
    
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'productModal';
    modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img src="${product.imagem || '/img/placeholder-food.jpg'}" 
                         class="img-fluid rounded mb-3" alt="${product.nome}">
                    <h5 class="fw-bold mb-2">${product.nome}</h5>
                    <p class="text-muted">${product.descricao || 'Produto delicioso do nosso cardápio'}</p>
                    
                    ${product.categoria ? `
                        <span class="badge bg-light text-dark mb-3">
                            <i class="fas fa-tag me-1"></i>${product.categoria.nome}
                        </span>
                    ` : ''}
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="product-card-price">R$ ${parseFloat(product.preco).toFixed(2)}</div>
                        ${!product.ativo ? '<span class="badge bg-danger">Indisponível</span>' : ''}
                    </div>
                    
                    <!-- Quantidade -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Quantidade</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="productQty" value="1" min="1" max="99">
                            <button class="btn btn-outline-secondary" type="button" onclick="increaseQty()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Observações -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observações (opcional)</label>
                        <textarea class="form-control" id="productObs" rows="2" 
                                  placeholder="Ex: Sem cebola, mal passado..."></textarea>
                    </div>
                    
                    <button class="btn btn-primary-custom w-100" onclick="addToCartFromModal(${product.id})" 
                            ${!product.ativo ? 'disabled' : ''}>
                        <i class="fas fa-shopping-cart me-2"></i>Adicionar ao Carrinho
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    modal.addEventListener('hidden.bs.modal', () => {
        modal.remove();
    });
}

function increaseQty() {
    const input = document.getElementById('productQty');
    input.value = Math.min(parseInt(input.value) + 1, 99);
}

function decreaseQty() {
    const input = document.getElementById('productQty');
    input.value = Math.max(parseInt(input.value) - 1, 1);
}

function addToCartFromModal(productId) {
    const qty = parseInt(document.getElementById('productQty').value);
    const obs = document.getElementById('productObs').value;
    
    addToCart(productId, qty, obs);
    
    // Fechar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
    modal.hide();
}

async function addToCart(productId, quantidade = 1, observacoes = null) {
    const product = appState.products.find(p => p.id === productId);
    
    if (!product) {
        showAlert('Produto não encontrado', 'danger');
        return;
    }
    
    if (!product.ativo) {
        showAlert('Produto indisponível no momento', 'warning');
        return;
    }
    
    console.log('➕ Adicionando produto:', {
        id: product.id,
        nome: product.nome,
        tenant_code: product.tenant_code,
        carrinho_atual: appState.cart.length
    });
    
    // Verificar se há produtos de outro restaurante no carrinho
    if (appState.cart.length > 0) {
        const firstItemTenantCode = appState.cart[0].tenant_code;
        
        console.log('🔍 Validando restaurante:', {
            carrinho: firstItemTenantCode,
            novo_produto: product.tenant_code
        });
        
        if (firstItemTenantCode && firstItemTenantCode !== product.tenant_code) {
            // Buscar nome dos restaurantes
            const currentRestaurant = appState.restaurants?.find(r => r.tenant_code === firstItemTenantCode);
            const newRestaurant = appState.restaurants?.find(r => r.tenant_code === product.tenant_code);
            
            const currentName = currentRestaurant?.nome_fantasia || 'outro restaurante';
            const newName = newRestaurant?.nome_fantasia || 'este restaurante';
            
            console.log('⚠️ Restaurantes diferentes detectados!', {
                atual: currentName,
                novo: newName
            });
            
            const confirmed = await showRestaurantChangeModal(currentName, newName);
            
            if (confirmed) {
                console.log('✅ Usuário aceitou limpar o carrinho');
                appState.cart = [];
                localStorage.setItem('app_cart', JSON.stringify(appState.cart));
                updateCartBadge();
            } else {
                console.log('❌ Usuário cancelou a adição');
                return;
            }
        }
    }
    
    // Verificar se produto já está no carrinho
    const existingItem = appState.cart.find(item => 
        item.produto_id === productId && item.observacoes === observacoes
    );
    
    if (existingItem) {
        existingItem.quantidade += quantidade;
    } else {
        appState.cart.push({
            produto_id: productId,
            nome: product.nome,
            preco: parseFloat(product.preco),
            quantidade: quantidade,
            observacoes: observacoes,
            imagem: product.imagem,
            tenant_code: product.tenant_code
        });
    }
    
    // Salvar no localStorage
    localStorage.setItem('app_cart', JSON.stringify(appState.cart));
    
    updateCartBadge();
    showAlert(`${product.nome} adicionado ao carrinho!`, 'success');
    
    // Debug
    console.log('🛒 Carrinho atualizado:', appState.cart.map(i => ({
        nome: i.nome,
        tenant_code: i.tenant_code
    })));
}

// ============================================
// FUN\u00c7\u00d5ES DE BUSCA E FILTROS APRIMORADOS
// ============================================

function debouncedSearch() {
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearSearch');
    
    // Mostrar/ocultar bot\u00e3o de limpar
    if (searchInput.value.length > 0) {
        clearBtn.classList.remove('d-none');
    } else {
        clearBtn.classList.add('d-none');
    }
    
    // Debounce: aguardar 300ms ap\u00f3s parar de digitar
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchProducts();
    }, 300);
}

function searchProducts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    if (searchTerm.length === 0) {
        renderProducts(appState.products);
        return;
    }
    
    const filtered = appState.products.filter(product => 
        product.nome.toLowerCase().includes(searchTerm) ||
        (product.descricao && product.descricao.toLowerCase().includes(searchTerm)) ||
        (product.categoria && product.categoria.nome.toLowerCase().includes(searchTerm))
    );
    
    renderProducts(filtered);
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearch').classList.add('d-none');
    renderProducts(appState.products);
}

// ============================================
// SISTEMA DE FAVORITOS
// ============================================

function toggleFavorite(productId) {
    if (favorites.includes(productId)) {
        favorites = favorites.filter(id => id !== productId);
    } else {
        favorites.push(productId);
    }
    
    localStorage.setItem('product_favorites', JSON.stringify(favorites));
    
    // Re-renderizar produtos para atualizar \u00edcones
    const currentSearch = document.getElementById('searchInput')?.value;
    if (currentSearch) {
        searchProducts();
    } else {
        renderProducts(appState.products);
    }
}

function toggleFavoritesOnly() {
    showFavoritesOnly = !showFavoritesOnly;
    
    const favBtn = document.getElementById('favBtn');
    if (showFavoritesOnly) {
        favBtn.innerHTML = '<i class=\"fas fa-heart text-danger\"></i> Favoritos';
        favBtn.classList.add('active');
    } else {
        favBtn.innerHTML = '<i class=\"far fa-heart\"></i> Favoritos';
        favBtn.classList.remove('active');
    }
    
    renderProducts(appState.products);
}

// ============================================
// ADICIONAR AO CARRINHO COM FEEDBACK VISUAL
// ============================================

async function addToCartQuick(productId) {
    const product = appState.products.find(p => p.id === productId);
    
    if (!product || !product.ativo) {
        showAlert('Produto indisponível', 'warning');
        return;
    }
    
    console.log('➕ [QUICK] Adicionando produto:', {
        id: product.id,
        nome: product.nome,
        tenant_code: product.tenant_code
    });
    
    // Verificar se há produtos de outro restaurante no carrinho
    if (appState.cart.length > 0) {
        const firstItemTenantCode = appState.cart[0].tenant_code;
        
        console.log('🔍 [QUICK] Validando restaurante:', {
            carrinho: firstItemTenantCode,
            novo_produto: product.tenant_code
        });
        
        if (firstItemTenantCode && firstItemTenantCode !== product.tenant_code) {
            const currentRestaurant = appState.restaurants?.find(r => r.tenant_code === firstItemTenantCode);
            const newRestaurant = appState.restaurants?.find(r => r.tenant_code === product.tenant_code);
            
            const currentName = currentRestaurant?.nome_fantasia || 'outro restaurante';
            const newName = newRestaurant?.nome_fantasia || 'este restaurante';
            
            console.log('⚠️ [QUICK] Restaurantes diferentes!');
            
            const confirmed = await showRestaurantChangeModal(currentName, newName);
            
            if (confirmed) {
                console.log('✅ [QUICK] Carrinho limpo');
                appState.cart = [];
                localStorage.setItem('app_cart', JSON.stringify(appState.cart));
                updateCartBadge();
            } else {
                console.log('❌ [QUICK] Cancelado');
                return;
            }
        }
    }
    
    // Verificar se produto já está no carrinho
    const existingItem = appState.cart.find(item => 
        item.produto_id === productId && !item.observacoes
    );
    
    if (existingItem) {
        existingItem.quantidade += 1;
    } else {
        appState.cart.push({
            produto_id: productId,
            nome: product.nome,
            preco: parseFloat(product.preco),
            quantidade: 1,
            observacoes: null,
            imagem: product.imagem,
            tenant_code: product.tenant_code
        });
    }
    
    localStorage.setItem('app_cart', JSON.stringify(appState.cart));
    updateCartBadge();
    
    // Feedback visual melhorado
    showMiniCartNotification(product);
    
    console.log('🛒 [QUICK] Carrinho:', appState.cart.map(i => i.nome));
}

function showMiniCartNotification(product) {
    const notification = document.createElement('div');
    notification.className = 'position-fixed bg-success text-white p-3 rounded shadow-lg';
    notification.style.cssText = 'bottom: 80px; right: 20px; z-index: 9999; animation: slideInRight 0.3s ease-out;';
    notification.innerHTML = `
        <div class=\"d-flex align-items-center\">
            <i class=\"fas fa-check-circle fa-2x me-3\"></i>
            <div>
                <strong>${product.nome}</strong><br>
                <small>Adicionado ao carrinho</small>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remover ap\u00f3s 2 segundos
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

// Adicionar anima\u00e7\u00f5es CSS
if (!document.getElementById('menuAnimations')) {
    const style = document.createElement('style');
    style.id = 'menuAnimations';
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .product-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
    `;
    document.head.appendChild(style);
}
