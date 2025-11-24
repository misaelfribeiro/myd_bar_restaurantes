// Configurações da API
const API_BASE_URL = window.location.origin;
const API_URL = `${API_BASE_URL}/api`;

// Endpoints
const ENDPOINTS = {
    LOGIN: `${API_URL}/entregadores/auth/login`,
    ME: `${API_URL}/entregadores/auth/me`,
    LOGOUT: `${API_URL}/entregadores/auth/logout`,
    ENTREGAS_DISPONIVEIS: `${API_URL}/entregadores/entregas/disponiveis`,
    ENTREGAS_ATIVAS: `${API_URL}/entregadores/entregas/ativas`,
    ACEITAR_ENTREGA: `${API_URL}/entregadores/entregas/aceitar`,
    RECUSAR_ENTREGA: `${API_URL}/entregadores/entregas/recusar`,
    ATUALIZAR_STATUS: `${API_URL}/entregadores/entregas/status`,
    ATUALIZAR_LOCALIZACAO: `${API_URL}/entregadores/localizacao`,
    HISTORICO: `${API_URL}/entregadores/entregas/historico`,
    GANHOS: `${API_URL}/entregadores/ganhos`,
    PERFIL: `${API_URL}/entregadores/perfil`,
    TOGGLE_DISPONIBILIDADE: `${API_URL}/entregadores/disponibilidade/toggle`
};

// Storage keys
const STORAGE_KEYS = {
    TOKEN: 'entregador_token',
    USER: 'entregador_user',
    DISPONIVEL: 'entregador_disponivel'
};
