// Configuração da API
const API_BASE_URL = '/api';
const APP_NAME = 'MyD Bar & Restaurantes';

// Estado global do app
const appState = {
    currentPage: 'home',
    user: null,
    token: null,
    cart: [],
    categories: [],
    products: [],
    orders: []
};

// Inicialização do app
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

async function initializeApp() {
    console.log('🚀 Inicializando app...');
    
    // Verificar token salvo
    const savedToken = localStorage.getItem('app_token');
    const savedUser = localStorage.getItem('app_user');
    
    if (savedToken && savedUser) {
        appState.token = savedToken;
        appState.user = JSON.parse(savedUser);
        console.log('✅ Usuário logado:', appState.user.nome);
    }
    
    // Carregar carrinho salvo
    const savedCart = localStorage.getItem('app_cart');
    if (savedCart) {
        appState.cart = JSON.parse(savedCart);
        updateCartBadge();
    }
    
    // Mostrar app instantaneamente (sem delay!)
    document.getElementById('loadingScreen').classList.add('d-none');
    document.getElementById('appContainer').classList.remove('d-none');
    
    // Mostrar página inicial
    if (appState.user && appState.token) {
        showHome();
    } else {
        showLogin();
    }
}

// Navegação
function showHome() {
    setActivePage('home');
    const content = `
        <div class="fade-in">
            <!-- Banner -->
            <div class="card mb-3" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border: none;">
                <div class="card-body py-4">
                    <h4 class="mb-2">
                        <i class="fas fa-fire me-2"></i>Bem-vindo${appState.user ? ', ' + appState.user.nome.split(' ')[0] : ''}!
                    </h4>
                    <p class="mb-0">Faça seu pedido e receba em casa</p>
                </div>
            </div>

            <!-- Restaurantes -->
            <h6 class="mb-3 fw-bold">
                <i class="fas fa-store me-2"></i>Restaurantes
            </h6>
            <div id="homeRestaurants" class="mb-4">
                <div class="col-12 text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>

            <!-- Categorias Populares -->
            <h6 class="mb-3 fw-bold">
                <i class="fas fa-utensils me-2"></i>Categorias
            </h6>
            <div id="homeCategories" class="row g-3 mb-4">
                <div class="col-12 text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>

            <!-- Combos -->
            <h6 class="mb-3 fw-bold">
                <i class="fas fa-boxes me-2"></i>Combos
            </h6>
            <div id="homeCombos" class="row g-3 mb-4">
                <div class="col-12 text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>

            <!-- Promoções -->
            <h6 class="mb-3 fw-bold">
                <i class="fas fa-tags me-2"></i>Promoções
            </h6>
            <div id="homePromotions" class="row g-3 mb-4">
                <div class="col-12 text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>

            <!-- Produtos em Destaque -->
            <h6 class="mb-3 fw-bold">
                <i class="fas fa-fire me-2"></i>Destaques
            </h6>
            <div id="featuredProducts" class="row g-3">
                <div class="col-12 text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
    loadHomeData();
}

async function loadHomeData() {
    try {
        // Carregar restaurantes e embaralhar
        const restaurantsResponse = await fetch(`${API_BASE_URL}/app/restaurantes`);
        const restaurantsData = await restaurantsResponse.json();
        
        console.log('Resposta API restaurantes home:', restaurantsData);
        
        let restaurants = restaurantsData.restaurantes || restaurantsData.data || restaurantsData;
        
        // Embaralhar restaurantes para cada usuário ver ordem diferente
        restaurants = shuffleArray(restaurants);
        
        // Mostrar restaurantes em grid de 4 colunas (máximo 8 aleatórios)
        if (restaurants && Array.isArray(restaurants) && restaurants.length > 0) {
            const restaurantsHtml = restaurants.slice(0, 8).map(rest => {
                const nome = rest.nome_fantasia || rest.nome || 'Restaurante';
                const logo = rest.logo || null;
                
                return `
                <div class="col-6 col-md-3">
                    <div class="home-restaurant-card" onclick="selectRestaurantForMenu('${rest.tenant_code}')">
                        <div class="home-restaurant-logo">
                            ${logo ? 
                                `<img src="${logo}" alt="${nome}">` : 
                                `<i class="fas fa-store-alt text-primary"></i>`
                            }
                        </div>
                        <div class="restaurant-name">${nome}</div>
                    </div>
                </div>
                `;
            }).join('');
            document.getElementById('homeRestaurants').innerHTML = `<div class="row g-3">${restaurantsHtml}</div>`;
        } else {
            document.getElementById('homeRestaurants').innerHTML = '<p class="text-center text-muted">Nenhum restaurante disponível</p>';
        }
        
        // Carregar categorias
        const categoriesResponse = await fetch(`${API_BASE_URL}/app/categorias`);
        const categoriesData = await categoriesResponse.json();
        appState.categories = categoriesData.data || categoriesData;
        
        // Mostrar categorias em círculos
        const categoriesHtml = appState.categories.slice(0, 8).map(cat => `
            <div class="col-4 col-md-3 col-lg-2">
                <div class="category-circle" onclick="showMenuByCategory(${cat.id})">
                    <div class="category-icon">
                        <i class="fas ${getCategoryIcon(cat.nome)}"></i>
                    </div>
                    <div class="category-name">${cat.nome}</div>
                </div>
            </div>
        `).join('');
        
        document.getElementById('homeCategories').innerHTML = categoriesHtml;
        
        // Carregar combos da API (já vem de todos os restaurantes embaralhados no backend)
        const combosResponse = await fetch(`${API_BASE_URL}/app/combos`);
        const combosData = await combosResponse.json();
        let combos = combosData.combos || combosData.data || combosData || [];
        
        // Embaralhar novamente no frontend para cada sessão ser única
        combos = shuffleArray(combos).slice(0, 6);
        
        console.log('Combos embaralhados:', combos);
        
        // Mostrar combos em grid 3 colunas
        if (combos.length > 0) {
            const combosHtml = combos.map(combo => `
                <div class="col-6 col-md-4">
                    ${createComboCard(combo)}
                </div>
            `).join('');
            document.getElementById('homeCombos').innerHTML = combosHtml;
        } else {
            document.getElementById('homeCombos').innerHTML = '<div class="col-12"><p class="text-center text-muted">Nenhum combo disponível</p></div>';
        }
        
        // Carregar TODOS os produtos de TODOS os restaurantes
        const productsResponse = await fetch(`${API_BASE_URL}/app/produtos?limit=100`);
        const productsData = await productsResponse.json();
        let allProducts = Array.isArray(productsData.data) ? productsData.data : 
                         Array.isArray(productsData) ? productsData : [];
        
        // Embaralhar produtos para cada usuário ver ordem diferente
        allProducts = shuffleArray(allProducts);
        
        // Filtrar produtos em destaque para promoções (de todos os restaurantes)
        let promotions = allProducts.filter(p => p.destaque === true || p.destaque === 1);
        promotions = shuffleArray(promotions).slice(0, 6);
        
        console.log('Promoções embaralhadas (produtos em destaque):', promotions);
        
        // Mostrar promoções em grid 3 colunas
        if (promotions.length > 0) {
            const promotionsHtml = promotions.map(product => `
                <div class="col-6 col-md-4">
                    ${createProductCard(product)}
                </div>
            `).join('');
            document.getElementById('homePromotions').innerHTML = promotionsHtml;
        } else {
            document.getElementById('homePromotions').innerHTML = '<div class="col-12"><p class="text-center text-muted">Nenhuma promoção disponível</p></div>';
        }
        
        // Produtos em destaque (mix aleatório de todos os restaurantes - 9 produtos)
        const featuredProducts = shuffleArray(allProducts).slice(0, 9);
        appState.products = featuredProducts;
        
        console.log('Destaques embaralhados:', featuredProducts);
        
        // Mostrar produtos em grid 3 colunas
        if (featuredProducts.length > 0) {
            const productsHtml = featuredProducts.map(product => `
                <div class="col-6 col-md-4">
                    ${createProductCard(product)}
                </div>
            `).join('');
            document.getElementById('featuredProducts').innerHTML = productsHtml;
        } else {
            document.getElementById('featuredProducts').innerHTML = '<div class="col-12"><p class="text-center text-muted">Nenhum produto disponível</p></div>';
        }
        
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
        showAlert('Erro ao carregar dados. Tente novamente.', 'danger');
    }
}

// Função auxiliar para embaralhar arrays (Fisher-Yates shuffle)
function shuffleArray(array) {
    const shuffled = [...array];
    for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    return shuffled;
}

function getCategoryIcon(categoryName) {
    const name = categoryName.toLowerCase();
    
    const iconMap = {
        'pizza': 'fa-pizza-slice',
        'pizzas': 'fa-pizza-slice',
        'hamburguer': 'fa-hamburger',
        'hambúrguer': 'fa-hamburger',
        'hamburger': 'fa-hamburger',
        'lanches': 'fa-hamburger',
        'lanche': 'fa-hamburger',
        'bebida': 'fa-wine-bottle',
        'bebidas': 'fa-wine-bottle',
        'refrigerante': 'fa-wine-bottle',
        'refrigerantes': 'fa-wine-bottle',
        'suco': 'fa-glass-whiskey',
        'sucos': 'fa-glass-whiskey',
        'sobremesa': 'fa-ice-cream',
        'sobremesas': 'fa-ice-cream',
        'doce': 'fa-cookie',
        'doces': 'fa-cookie',
        'sorvete': 'fa-ice-cream',
        'sorvetes': 'fa-ice-cream',
        'café': 'fa-coffee',
        'cafés': 'fa-coffee',
        'salgado': 'fa-drumstick-bite',
        'salgados': 'fa-drumstick-bite',
        'massa': 'fa-utensils',
        'massas': 'fa-utensils',
        'japonesa': 'fa-fish',
        'japonês': 'fa-fish',
        'sushi': 'fa-fish',
        'frutos do mar': 'fa-fish',
        'peixe': 'fa-fish',
        'carne': 'fa-drumstick-bite',
        'carnes': 'fa-drumstick-bite',
        'churrasco': 'fa-drumstick-bite',
        'salada': 'fa-leaf',
        'saladas': 'fa-leaf',
        'vegetariano': 'fa-leaf',
        'vegano': 'fa-seedling',
        'cerveja': 'fa-beer',
        'cervejas': 'fa-beer'
    };
    
    for (const [key, icon] of Object.entries(iconMap)) {
        if (name.includes(key)) {
            return icon;
        }
    }
    
    return 'fa-utensils';
}

function createComboCard(combo) {
    const desconto = combo.preco_original > combo.preco_combo 
        ? Math.round(((combo.preco_original - combo.preco_combo) / combo.preco_original) * 100)
        : 0;
    
    const imagemUrl = combo.imagem ? `/storage/${combo.imagem}` : '/img/placeholder-food.jpg';
    
    return `
        <div class="product-card combo-card" onclick="showComboDetail(${combo.id})">
            ${desconto > 0 ? `<div class="badge bg-danger position-absolute top-0 start-0 m-2" style="z-index: 10;">-${desconto}%</div>` : ''}
            <img src="${imagemUrl}" alt="${combo.nome}">
            <div class="product-card-body">
                <div class="product-card-title">
                    <i class="fas fa-boxes me-1"></i>${combo.nome}
                </div>
                <div class="product-card-description">${combo.descricao || 'Combo especial'}</div>
                <div class="product-card-actions">
                    <div>
                        ${combo.preco_original > combo.preco_combo ? `
                            <small class="text-muted text-decoration-line-through d-block">De: R$ ${parseFloat(combo.preco_original).toFixed(2)}</small>
                        ` : ''}
                        <div class="product-card-price">R$ ${parseFloat(combo.preco_combo).toFixed(2)}</div>
                    </div>
                    <button class="btn btn-sm btn-primary-custom" 
                            onclick="event.stopPropagation(); addComboToCart(${combo.id})">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

function createProductCard(product) {
    const isFavorite = favorites && favorites.includes(product.id);
    const imagemUrl = product.imagem ? `/storage/${product.imagem}` : '/img/placeholder-food.jpg';
    
    return `
        <div class="product-card" onclick="showProductDetail(${product.id})">
            <button class="btn btn-link position-absolute top-0 end-0 p-2" 
                    onclick="event.stopPropagation(); toggleFavorite(${product.id})" 
                    style="z-index: 10;">
                <i class="fa${isFavorite ? 's' : 'r'} fa-heart text-${isFavorite ? 'danger' : 'white'}" 
                   style="text-shadow: 0 0 3px rgba(0,0,0,0.5); font-size: 1.2rem;"></i>
            </button>
            <img src="${imagemUrl}" alt="${product.nome}">
            <div class="product-card-body">
                <div class="product-card-title">${product.nome}</div>
                <div class="product-card-description">${product.descricao || 'Produto delicioso'}</div>
                <div class="product-card-actions">
                    <div class="product-card-price">R$ ${parseFloat(product.preco).toFixed(2)}</div>
                    <button class="btn btn-sm btn-primary-custom" 
                            onclick="event.stopPropagation(); addToCartQuick(${product.id})">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

function showMenuByCategory(categoryId) {
    showMenu(categoryId);
}

// Funções para Combos
async function showComboDetail(comboId) {
    try {
        const response = await fetch(`${API_BASE_URL}/app/combos/${comboId}`);
        const data = await response.json();
        const combo = data.combo || data;
        
        if (!combo) {
            showAlert('Combo não encontrado', 'danger');
            return;
        }
        
        // Buscar produtos do combo
        const produtos = combo.produtos || [];
        const produtosHtml = produtos.map(p => `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                ${p.nome}
                <span class="badge bg-primary rounded-pill">${p.pivot?.quantidade || 1}x</span>
            </li>
        `).join('');
        
        const desconto = combo.preco_original > combo.preco_combo 
            ? Math.round(((combo.preco_original - combo.preco_combo) / combo.preco_original) * 100)
            : 0;
        
        const modal = `
            <div class="modal fade" id="comboModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-boxes me-2"></i>${combo.nome}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${combo.imagem ? `<div class="text-center mb-3"><img src="/storage/${combo.imagem}" class="img-fluid rounded" alt="${combo.nome}" style="max-height: 300px;"></div>` : ''}
                            ${desconto > 0 ? `<div class="alert alert-success"><i class="fas fa-tag me-2"></i>Economize ${desconto}%</div>` : ''}
                            <p class="text-muted">${combo.descricao || 'Combo especial'}</p>
                            <h6 class="mt-3">Itens inclusos:</h6>
                            <ul class="list-group mb-3">
                                ${produtosHtml || '<li class="list-group-item">Nenhum produto encontrado</li>'}
                            </ul>
                            <div class="d-flex justify-content-between align-items-center">
                                ${combo.preco_original > combo.preco_combo ? `
                                    <div>
                                        <small class="text-muted text-decoration-line-through">De: R$ ${parseFloat(combo.preco_original).toFixed(2)}</small>
                                        <h4 class="text-success mb-0">Por: R$ ${parseFloat(combo.preco_combo).toFixed(2)}</h4>
                                    </div>
                                ` : `
                                    <h4 class="text-success mb-0">R$ ${parseFloat(combo.preco_combo).toFixed(2)}</h4>
                                `}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary" onclick="addComboToCart(${combo.id}); bootstrap.Modal.getInstance(document.getElementById('comboModal')).hide();">
                                <i class="fas fa-shopping-cart me-2"></i>Adicionar ao Carrinho
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remover modal anterior se existir
        const oldModal = document.getElementById('comboModal');
        if (oldModal) oldModal.remove();
        
        document.body.insertAdjacentHTML('beforeend', modal);
        const modalEl = new bootstrap.Modal(document.getElementById('comboModal'));
        modalEl.show();
        
    } catch (error) {
        console.error('Erro ao carregar combo:', error);
        showAlert('Erro ao carregar detalhes do combo', 'danger');
    }
}

async function addComboToCart(comboId) {
    try {
        // Buscar detalhes completos do combo
        const response = await fetch(`${API_BASE_URL}/app/combos/${comboId}`);
        const data = await response.json();
        const combo = data.combo || data;
        
        if (!combo) {
            showAlert('Combo não encontrado', 'danger');
            return;
        }
        
        console.log('➕ [COMBO] Adicionando combo:', combo);
        
        // Verificar se há produtos de outro restaurante no carrinho
        if (appState.cart.length > 0) {
            const firstItemTenantCode = appState.cart[0].tenant_code;
            
            if (firstItemTenantCode && firstItemTenantCode !== combo.tenant_code) {
                const currentRestaurant = appState.restaurants?.find(r => r.tenant_code === firstItemTenantCode);
                const newRestaurant = appState.restaurants?.find(r => r.tenant_code === combo.tenant_code);
                
                const currentName = currentRestaurant?.nome_fantasia || 'outro restaurante';
                const newName = newRestaurant?.nome_fantasia || 'este restaurante';
                
                const confirmed = await showRestaurantChangeModal(currentName, newName);
                
                if (confirmed) {
                    appState.cart = [];
                    localStorage.setItem('app_cart', JSON.stringify(appState.cart));
                    updateCartBadge();
                } else {
                    return;
                }
            }
        }
        
        // Verificar se combo já está no carrinho
        const existingItem = appState.cart.find(item => 
            item.tipo === 'combo' && item.combo_id === comboId
        );
        
        if (existingItem) {
            existingItem.quantidade += 1;
        } else {
            // Montar descrição dos produtos do combo
            const produtos = combo.produtos || [];
            const descricaoProdutos = produtos.map(p => 
                `${p.pivot?.quantidade || 1}x ${p.nome}`
            ).join(', ');
            
            appState.cart.push({
                tipo: 'combo',
                combo_id: comboId,
                nome: combo.nome,
                descricao: descricaoProdutos,
                preco: parseFloat(combo.preco_combo),
                quantidade: 1,
                imagem: combo.imagem,
                tenant_code: combo.tenant_code,
                produtos: produtos
            });
        }
        
        localStorage.setItem('app_cart', JSON.stringify(appState.cart));
        updateCartBadge();
        
        // Feedback visual
        showAlert(`${combo.nome} adicionado ao carrinho!`, 'success');
        
        console.log('🛒 [COMBO] Carrinho:', appState.cart);
        
    } catch (error) {
        console.error('Erro ao adicionar combo:', error);
        showAlert('Erro ao adicionar combo ao carrinho', 'danger');
    }
}

function setActivePage(page) {
    appState.currentPage = page;
    
    // Atualizar navegação
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    const navMapping = {
        'home': 0,
        'menu': 1,
        'cart': 2,
        'orders': 3
    };
    
    const index = navMapping[page];
    if (index !== undefined) {
        document.querySelectorAll('.nav-item')[index].classList.add('active');
    }
}

function updateCartBadge() {
    const totalItems = appState.cart.reduce((sum, item) => sum + item.quantidade, 0);
    document.getElementById('cartBadge').textContent = totalItems;
    
    // Salvar no localStorage
    localStorage.setItem('app_cart', JSON.stringify(appState.cart));
}

function showAlert(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 3000);
}
