// Gerenciamento de Localização

let watchId = null;
let currentPosition = null;

// Iniciar rastreamento de localização
function iniciarRastreamento() {
    if ('geolocation' in navigator) {
        watchId = navigator.geolocation.watchPosition(
            atualizarPosicao,
            erroLocalizacao,
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    } else {
        console.error('Geolocalização não suportada');
    }
}

// Parar rastreamento
function pararRastreamento() {
    if (watchId) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }
}

// Atualizar posição
function atualizarPosicao(position) {
    currentPosition = {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
        accuracy: position.coords.accuracy
    };
    
    // Enviar para o servidor
    enviarLocalizacao(currentPosition);
}

// Enviar localização para o servidor
async function enviarLocalizacao(position) {
    try {
        await fetchAPI(ENDPOINTS.ATUALIZAR_LOCALIZACAO, {
            method: 'POST',
            body: JSON.stringify(position)
        });
    } catch (error) {
        console.error('Erro ao enviar localização:', error);
    }
}

// Erro de localização
function erroLocalizacao(error) {
    console.error('Erro ao obter localização:', error);
    
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert('Por favor, permita o acesso à sua localização para usar o app.');
            break;
        case error.POSITION_UNAVAILABLE:
            console.error('Localização indisponível');
            break;
        case error.TIMEOUT:
            console.error('Timeout ao obter localização');
            break;
    }
}

// Solicitar permissão de localização
async function solicitarPermissaoLocalizacao() {
    if ('geolocation' in navigator) {
        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject);
            });
            return true;
        } catch (error) {
            console.error('Permissão de localização negada:', error);
            return false;
        }
    }
    return false;
}

// Calcular distância entre dois pontos (Haversine)
function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371; // Raio da Terra em km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;
    return distance.toFixed(2);
}
