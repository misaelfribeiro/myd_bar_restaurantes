// App principal

// Menu lateral
function toggleMenu() {
    const menu = document.getElementById('sideMenu');
    const overlay = document.getElementById('menuOverlay');
    
    menu.classList.toggle('active');
    overlay.classList.toggle('active');
}

// Trocar tabs
function showTab(tabName) {
    // Esconder todas as tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Mostrar tab selecionada
    document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.add('active');
    
    // Fechar menu
    toggleMenu();
    
    // Carregar dados da tab
    switch(tabName) {
        case 'entregas':
            carregarEntregasDisponiveis();
            carregarEntregasAceitas();
            break;
        case 'historico':
            carregarHistorico();
            break;
        case 'ganhos':
            carregarGanhos();
            break;
    }
}

// Carregar dados iniciais
function carregarDadosIniciais() {
    carregarEntregasDisponiveis();
    carregarEntregasAceitas();
    
    // Solicitar permissão de localização
    solicitarPermissaoLocalizacao().then(permitido => {
        if (permitido) {
            iniciarRastreamento();
        }
    });
    
    // Atualizar a cada 30 segundos
    setInterval(() => {
        const disponivel = localStorage.getItem(STORAGE_KEYS.DISPONIVEL) === 'true';
        if (disponivel) {
            carregarEntregasDisponiveis();
            carregarEntregasAceitas();
        }
    }, 30000);
}

// Editar perfil
function editarPerfil() {
    alert('Funcionalidade em desenvolvimento');
}

// Notificações
function solicitarPermissaoNotificacoes() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                console.log('Permissão de notificação concedida');
            }
        });
    }
}

// Mostrar notificação
function mostrarNotificacao(titulo, corpo) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(titulo, {
            body: corpo,
            icon: 'img/icon-192.png',
            badge: 'img/icon-192.png'
        });
    }
}

// Inicializar app
document.addEventListener('DOMContentLoaded', function() {
    // Solicitar permissão de notificações
    solicitarPermissaoNotificacoes();
});

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalEntrega');
    if (e.target === modal) {
        fecharModal();
    }
});
