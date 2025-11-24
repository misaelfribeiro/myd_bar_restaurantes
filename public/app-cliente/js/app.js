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

            <!-- Ações Rápidas -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="card h-100 text-center" onclick="showMenu()" style="cursor: pointer;">
                        <div class="card-body">
                            <i class="fas fa-utensils fa-2x text-primary mb-2"></i>
                            <h6 class="mb-0">Cardápio</h6>
                            <small class="text-muted">Ver produtos</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card h-100 text-center" onclick="typeof showOrders === 'function' ? showOrders() : console.warn('showOrders não carregado')" style="cursor: pointer;">
                        <div class="card-body">
                            <i class="fas fa-receipt fa-2x text-success mb-2"></i>
                            <h6 class="mb-0">Meus Pedidos</h6>
                            <small class="text-muted">Acompanhar</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categorias Populares -->
            <h6 class="mb-3 fw-bold">Categorias</h6>
            <div id="homeCategories" class="row g-2 mb-4">
                <div class="col-12 text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>

            <!-- Produtos em Destaque -->
            <h6 class="mb-3 fw-bold">Destaques</h6>
            <div id="featuredProducts">
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
        // Carregar categorias
        const categoriesResponse = await fetch(`${API_BASE_URL}/app/categorias`);
        const categoriesData = await categoriesResponse.json();
        appState.categories = categoriesData.data || categoriesData;
        
        // Mostrar categorias
        const categoriesHtml = appState.categories.slice(0, 4).map(cat => `
            <div class="col-6 col-md-3">
                <div class="card text-center h-100" onclick="showMenuByCategory(${cat.id})" style="cursor: pointer;">
                    <div class="card-body">
                        <i class="fas fa-tag fa-2x text-primary mb-2"></i>
                        <h6 class="mb-0 small">${cat.nome}</h6>
                    </div>
                </div>
            </div>
        `).join('');
        
        document.getElementById('homeCategories').innerHTML = categoriesHtml;
        
        // Carregar produtos
        const productsResponse = await fetch(`${API_BASE_URL}/app/produtos?limit=6`);
        const productsData = await productsResponse.json();
        appState.products = Array.isArray(productsData.data) ? productsData.data : 
                           Array.isArray(productsData) ? productsData : [];
        
        // Mostrar produtos
        if (appState.products.length > 0) {
            const productsHtml = appState.products.map(product => createProductCard(product)).join('');
            document.getElementById('featuredProducts').innerHTML = productsHtml;
        } else {
            document.getElementById('featuredProducts').innerHTML = '<p class="text-center text-muted">Nenhum produto disponível</p>';
        }
        
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
        showAlert('Erro ao carregar dados. Tente novamente.', 'danger');
    }
}

function createProductCard(product) {
    const isFavorite = favorites && favorites.includes(product.id);
    
    return `
        <div class="product-card" onclick="showProductDetail(${product.id})">
            <button class="btn btn-link position-absolute top-0 end-0 p-2" 
                    onclick="event.stopPropagation(); toggleFavorite(${product.id})" 
                    style="z-index: 10;">
                <i class="fa${isFavorite ? 's' : 'r'} fa-heart text-${isFavorite ? 'danger' : 'white'}" 
                   style="text-shadow: 0 0 3px rgba(0,0,0,0.5); font-size: 1.2rem;"></i>
            </button>
            <img src="${product.imagem || '/img/placeholder-food.jpg'}" alt="${product.nome}">
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
