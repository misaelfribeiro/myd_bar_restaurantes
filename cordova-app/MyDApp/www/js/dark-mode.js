// Dark Mode Toggle

let darkMode = localStorage.getItem('darkMode') === 'true';

// Aplicar modo escuro no carregamento
document.addEventListener('DOMContentLoaded', function() {
    if (darkMode) {
        enableDarkMode();
    }
});

function toggleDarkMode() {
    darkMode = !darkMode;
    localStorage.setItem('darkMode', darkMode);
    
    if (darkMode) {
        enableDarkMode();
    } else {
        disableDarkMode();
    }
}

function enableDarkMode() {
    document.body.classList.add('dark-mode');
    updateDarkModeIcon();
}

function disableDarkMode() {
    document.body.classList.remove('dark-mode');
    updateDarkModeIcon();
}

function updateDarkModeIcon() {
    const icon = document.getElementById('darkModeIcon');
    if (icon) {
        icon.className = darkMode ? 'fas fa-sun' : 'fas fa-moon';
    }
}

// Adicionar estilos do Dark Mode
const darkModeStyles = document.createElement('style');
darkModeStyles.id = 'darkModeStyles';
darkModeStyles.textContent = `
    /* Dark Mode Variables */
    body.dark-mode {
        --bg-primary: #1a1a1a;
        --bg-secondary: #2d2d2d;
        --bg-card: #363636;
        --text-primary: #ffffff;
        --text-secondary: #b0b0b0;
        --text-muted: #808080;
        --border-color: #404040;
        --shadow: rgba(0, 0, 0, 0.5);
    }
    
    /* Background Colors */
    body.dark-mode {
        background-color: var(--bg-primary) !important;
        color: var(--text-primary) !important;
    }
    
    body.dark-mode .app-content {
        background-color: var(--bg-primary);
    }
    
    /* Cards */
    body.dark-mode .card {
        background-color: var(--bg-card);
        border-color: var(--border-color);
        color: var(--text-primary);
    }
    
    body.dark-mode .product-card {
        background-color: var(--bg-card);
        border-color: var(--border-color);
    }
    
    body.dark-mode .product-card-body {
        background-color: var(--bg-card);
    }
    
    body.dark-mode .product-card-title {
        color: var(--text-primary);
    }
    
    body.dark-mode .product-card-description {
        color: var(--text-secondary);
    }
    
    /* Form Controls */
    body.dark-mode .form-control,
    body.dark-mode .form-select {
        background-color: var(--bg-secondary);
        border-color: var(--border-color);
        color: var(--text-primary);
    }
    
    body.dark-mode .form-control:focus,
    body.dark-mode .form-select:focus {
        background-color: var(--bg-secondary);
        border-color: #6366f1;
        color: var(--text-primary);
    }
    
    body.dark-mode .form-control::placeholder {
        color: var(--text-muted);
    }
    
    /* Buttons */
    body.dark-mode .btn-outline-primary {
        color: #8b5cf6;
        border-color: #8b5cf6;
    }
    
    body.dark-mode .btn-outline-primary:hover,
    body.dark-mode .btn-outline-primary.active {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
        color: white;
    }
    
    body.dark-mode .btn-outline-secondary {
        color: var(--text-secondary);
        border-color: var(--border-color);
    }
    
    body.dark-mode .btn-link {
        color: var(--text-primary);
    }
    
    /* Category Pills */
    body.dark-mode .category-pill {
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }
    
    body.dark-mode .category-pill.active {
        background-color: #6366f1;
        color: white;
        border-color: #6366f1;
    }
    
    /* Text Colors */
    body.dark-mode .text-muted {
        color: var(--text-muted) !important;
    }
    
    body.dark-mode .text-dark {
        color: var(--text-primary) !important;
    }
    
    body.dark-mode h1, 
    body.dark-mode h2, 
    body.dark-mode h3, 
    body.dark-mode h4, 
    body.dark-mode h5, 
    body.dark-mode h6 {
        color: var(--text-primary);
    }
    
    /* Modal */
    body.dark-mode .modal-content {
        background-color: var(--bg-card);
        color: var(--text-primary);
    }
    
    body.dark-mode .modal-header {
        border-bottom-color: var(--border-color);
    }
    
    body.dark-mode .modal-footer {
        border-top-color: var(--border-color);
    }
    
    body.dark-mode .btn-close {
        filter: invert(1);
    }
    
    /* Badges */
    body.dark-mode .badge.bg-light {
        background-color: var(--bg-secondary) !important;
        color: var(--text-primary) !important;
    }
    
    /* Cart */
    body.dark-mode .cart-item {
        background-color: var(--bg-card);
        border-color: var(--border-color);
    }
    
    /* Bottom Navigation */
    body.dark-mode .bottom-nav {
        background-color: var(--bg-card);
        border-top-color: var(--border-color);
    }
    
    body.dark-mode .nav-item {
        color: var(--text-muted);
    }
    
    body.dark-mode .nav-item.active {
        color: #8b5cf6;
    }
    
    /* Shadows */
    body.dark-mode .shadow,
    body.dark-mode .shadow-sm,
    body.dark-mode .shadow-lg {
        box-shadow: 0 4px 6px var(--shadow) !important;
    }
    
    /* Search Bar */
    body.dark-mode .search-bar input {
        background-color: var(--bg-secondary);
        border-color: var(--border-color);
        color: var(--text-primary);
    }
    
    body.dark-mode .search-bar i {
        color: var(--text-muted);
    }
    
    /* Alerts */
    body.dark-mode .alert {
        background-color: var(--bg-card);
        border-color: var(--border-color);
        color: var(--text-primary);
    }
    
    /* Dividers */
    body.dark-mode hr {
        border-color: var(--border-color);
        opacity: 1;
    }
    
    /* Spinners */
    body.dark-mode .spinner-border {
        border-color: var(--text-muted);
        border-right-color: transparent;
    }
    
    /* Lists */
    body.dark-mode .list-group-item {
        background-color: var(--bg-card);
        border-color: var(--border-color);
        color: var(--text-primary);
    }
    
    /* Tables */
    body.dark-mode .table {
        color: var(--text-primary);
    }
    
    body.dark-mode .table-bordered {
        border-color: var(--border-color);
    }
    
    body.dark-mode .table-bordered td,
    body.dark-mode .table-bordered th {
        border-color: var(--border-color);
    }
    
    /* Transition suave */
    body,
    .card,
    .product-card,
    .form-control,
    .btn,
    .modal-content {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }
`;

document.head.appendChild(darkModeStyles);

// Exportar funções
window.toggleDarkMode = toggleDarkMode;
