/**
 * Sistema de Rastreamento em Tempo Real com Leaflet.js
 * Exibe mapa com localização do entregador e destino
 */

// Usar var ao invés de let para evitar erro de redeclaração
if (typeof trackingMap === 'undefined') var trackingMap = null;
if (typeof entregadorMarker === 'undefined') var entregadorMarker = null;
if (typeof destinoMarker === 'undefined') var destinoMarker = null;
if (typeof routeLine === 'undefined') var routeLine = null;
if (typeof trackingInterval === 'undefined') var trackingInterval = null;

// Ícones personalizados
const entregadorIcon = L.divIcon({
    className: 'custom-marker-entregador',
    html: `<div style="
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        border: 3px solid white;
        font-size: 24px;
    ">🏍️</div>`,
    iconSize: [50, 50],
    iconAnchor: [25, 25]
});

const destinoIcon = L.divIcon({
    className: 'custom-marker-destino',
    html: `<div style="
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        border: 3px solid white;
        font-size: 22px;
    ">🏠</div>`,
    iconSize: [45, 45],
    iconAnchor: [22, 45]
});

/**
 * Inicializar mapa de rastreamento
 */
function initTrackingMap(containerId, deliveryData) {
    console.log('🗺️ Inicializando mapa de rastreamento...');
    console.log('   Container ID:', containerId);
    console.log('   Delivery Data:', deliveryData);
    
    // Verificar se Leaflet está disponível
    if (typeof L === 'undefined') {
        console.error('❌ Leaflet não está carregado!');
        document.getElementById(containerId).innerHTML = `
            <div class="alert alert-danger m-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Erro: Biblioteca de mapas não carregada. Recarregue a página.
            </div>
        `;
        return null;
    }
    
    // Verificar se o container existe
    const container = document.getElementById(containerId);
    if (!container) {
        console.error('❌ Container do mapa não encontrado:', containerId);
        return null;
    }
    
    console.log('✅ Container encontrado, Leaflet disponível');
    
    // Verificar se tem coordenadas
    if (!deliveryData.destino_latitude || !deliveryData.destino_longitude) {
        console.warn('⚠️ Coordenadas do destino não disponíveis');
        container.innerHTML = `
            <div class="alert alert-warning m-3 text-center">
                <i class="fas fa-map-marked-alt fa-2x mb-2"></i>
                <p class="mb-0">Aguardando localização do destino...</p>
                <small class="text-muted">O mapa aparecerá assim que o endereço for confirmado</small>
            </div>
        `;
        return null;
    }
    
    console.log('📍 Coordenadas do destino:', deliveryData.destino_latitude, deliveryData.destino_longitude);
    
    // Destruir mapa anterior se existir
    if (trackingMap) {
        console.log('🗑️ Removendo mapa anterior...');
        trackingMap.remove();
        trackingMap = null;
    }
    
    try {
        console.log('🎨 Criando mapa Leaflet...');
        
        // Criar mapa centrado no Brasil
        trackingMap = L.map(containerId, {
            zoomControl: true,
            scrollWheelZoom: false,
            dragging: true,
            touchZoom: true
        }).setView([-23.550520, -46.633308], 13); // São Paulo como padrão
        
        console.log('✅ Mapa criado');
        
        // Adicionar camada do OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(trackingMap);
        
        console.log('✅ Camada de tiles adicionada');
        
    } catch (error) {
        console.error('❌ Erro ao criar mapa:', error);
        container.innerHTML = `
            <div class="alert alert-danger m-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Erro ao carregar mapa: ${error.message}
            </div>
        `;
        return null;
    }
    
    // Posição do destino (endereço do cliente)
    const destinoLat = deliveryData.destino_latitude || -23.550520;
    const destinoLng = deliveryData.destino_longitude || -46.633308;
    
    // Adicionar marcador do destino
    destinoMarker = L.marker([destinoLat, destinoLng], {
        icon: destinoIcon,
        title: 'Seu endereço'
    }).addTo(trackingMap);
    
    destinoMarker.bindPopup(`
        <div style="text-align: center; padding: 10px;">
            <strong style="font-size: 16px;">🏠 Seu Endereço</strong><br>
            <small style="color: #666;">${deliveryData.endereco_rua}, ${deliveryData.endereco_numero}</small><br>
            <small style="color: #666;">${deliveryData.endereco_bairro}</small>
        </div>
    `);
    
    // Se tiver localização do entregador
    if (deliveryData.entregador_latitude && deliveryData.entregador_longitude) {
        updateEntregadorPosition(
            deliveryData.entregador_latitude,
            deliveryData.entregador_longitude,
            deliveryData.entregador_nome || 'Entregador'
        );
        
        // Ajustar zoom para mostrar ambos os marcadores
        const bounds = L.latLngBounds([
            [deliveryData.entregador_latitude, deliveryData.entregador_longitude],
            [destinoLat, destinoLng]
        ]);
        trackingMap.fitBounds(bounds, { padding: [50, 50] });
    } else {
        // Centralizar no destino
        trackingMap.setView([destinoLat, destinoLng], 15);
    }
    
    // Forçar redimensionamento após renderização
    setTimeout(() => {
        trackingMap.invalidateSize();
    }, 100);
    
    return trackingMap;
}

/**
 * Atualizar posição do entregador no mapa
 */
function updateEntregadorPosition(lat, lng, nome) {
    console.log(`📍 Atualizando posição do entregador: ${lat}, ${lng}`);
    
    if (!trackingMap) {
        console.warn('⚠️ Mapa não inicializado');
        return;
    }
    
    const newPosition = [lat, lng];
    
    if (entregadorMarker) {
        // Animar movimento do marcador
        const oldPosition = entregadorMarker.getLatLng();
        animateMarker(entregadorMarker, oldPosition, newPosition, 1000);
    } else {
        // Criar novo marcador
        entregadorMarker = L.marker(newPosition, {
            icon: entregadorIcon,
            title: nome
        }).addTo(trackingMap);
        
        entregadorMarker.bindPopup(`
            <div style="text-align: center; padding: 10px;">
                <strong style="font-size: 16px;">🏍️ ${nome}</strong><br>
                <small style="color: #666;">A caminho do seu endereço</small><br>
                <div style="margin-top: 8px;">
                    <span class="badge bg-success">Em Rota</span>
                </div>
            </div>
        `);
    }
    
    // Atualizar linha da rota
    updateRouteLine();
}

/**
 * Animar movimento do marcador
 */
function animateMarker(marker, from, to, duration) {
    const start = Date.now();
    const fromLat = from.lat;
    const fromLng = from.lng;
    const toLat = to[0];
    const toLng = to[1];
    
    function animate() {
        const now = Date.now();
        const progress = Math.min((now - start) / duration, 1);
        
        // Interpolação linear
        const currentLat = fromLat + (toLat - fromLat) * progress;
        const currentLng = fromLng + (toLng - fromLng) * progress;
        
        marker.setLatLng([currentLat, currentLng]);
        
        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    }
    
    animate();
}

/**
 * Atualizar linha da rota entre entregador e destino
 */
function updateRouteLine() {
    if (!entregadorMarker || !destinoMarker) return;
    
    // Remover linha anterior
    if (routeLine) {
        trackingMap.removeLayer(routeLine);
    }
    
    // Criar nova linha
    const points = [
        entregadorMarker.getLatLng(),
        destinoMarker.getLatLng()
    ];
    
    routeLine = L.polyline(points, {
        color: '#667eea',
        weight: 4,
        opacity: 0.7,
        dashArray: '10, 10',
        lineJoin: 'round'
    }).addTo(trackingMap);
    
    // Calcular distância
    const distance = calculateDistance(
        entregadorMarker.getLatLng().lat,
        entregadorMarker.getLatLng().lng,
        destinoMarker.getLatLng().lat,
        destinoMarker.getLatLng().lng
    );
    
    // Atualizar UI com distância
    const distanceElement = document.getElementById('distanciaEntregador');
    if (distanceElement) {
        distanceElement.textContent = `${distance.toFixed(1)} km`;
    }
}

/**
 * Calcular distância entre dois pontos (fórmula de Haversine)
 */
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Raio da Terra em km
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c;
    
    return distance;
}

function toRad(degrees) {
    return degrees * Math.PI / 180;
}

/**
 * Iniciar rastreamento automático
 */
function startDeliveryTracking(deliveryId) {
    console.log('🚀 Iniciando rastreamento automático do delivery #' + deliveryId);
    
    // Limpar intervalo anterior
    if (trackingInterval) {
        clearInterval(trackingInterval);
    }
    
    // Atualizar a cada 5 segundos
    trackingInterval = setInterval(async () => {
        try {
            const response = await authFetch(`${API_BASE_URL}/app/deliveries/${deliveryId}/tracking`);
            const data = await response.json();
            
            if (data.success && data.delivery) {
                const delivery = data.delivery;
                
                // Atualizar posição do entregador se mudou
                if (delivery.entregador_latitude && delivery.entregador_longitude) {
                    updateEntregadorPosition(
                        delivery.entregador_latitude,
                        delivery.entregador_longitude,
                        delivery.entregador_nome || 'Entregador'
                    );
                }
                
                // Se entrega foi concluída, parar rastreamento
                if (delivery.status === 'entregue' || delivery.status === 'cancelado') {
                    stopDeliveryTracking();
                }
            }
        } catch (error) {
            console.error('❌ Erro ao atualizar rastreamento:', error);
        }
    }, 5000); // 5 segundos
}

/**
 * Parar rastreamento
 */
function stopDeliveryTracking() {
    console.log('🛑 Parando rastreamento automático');
    
    if (trackingInterval) {
        clearInterval(trackingInterval);
        trackingInterval = null;
    }
}

/**
 * Centralizar mapa no entregador
 */
function centerOnEntregador() {
    if (trackingMap && entregadorMarker) {
        trackingMap.setView(entregadorMarker.getLatLng(), 16, {
            animate: true,
            duration: 1
        });
        entregadorMarker.openPopup();
    }
}

/**
 * Centralizar mapa no destino
 */
function centerOnDestino() {
    if (trackingMap && destinoMarker) {
        trackingMap.setView(destinoMarker.getLatLng(), 16, {
            animate: true,
            duration: 1
        });
        destinoMarker.openPopup();
    }
}

/**
 * Mostrar ambos os marcadores
 */
function showBothMarkers() {
    if (trackingMap && entregadorMarker && destinoMarker) {
        const bounds = L.latLngBounds([
            entregadorMarker.getLatLng(),
            destinoMarker.getLatLng()
        ]);
        trackingMap.fitBounds(bounds, { padding: [50, 50] });
    }
}
