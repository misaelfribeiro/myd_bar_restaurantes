/**
 * Sistema de Mapas para Entregador com Leaflet.js
 * Exibe rotas para restaurante e cliente
 */

let currentMap = null;
let userMarker = null;
let destinoMarker = null;
let routeLine = null;

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

const restauranteIcon = L.divIcon({
    className: 'custom-marker-restaurante',
    html: `<div style="
        background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        border: 3px solid white;
        font-size: 24px;
    ">🍔</div>`,
    iconSize: [50, 50],
    iconAnchor: [25, 50]
});

const clienteIcon = L.divIcon({
    className: 'custom-marker-cliente',
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
 * Abrir mapa para ir ao restaurante
 */
async function abrirMapaRestaurante(entregaId, lat, lng) {
    console.log('🗺️ abrirMapaRestaurante chamado:', { entregaId, lat, lng });
    
    if (!lat || !lng || lat === 'null' || lng === 'null' || lat === 'undefined' || lng === 'undefined') {
        console.error('❌ Coordenadas inválidas:', { lat, lng });
        alert('Coordenadas do restaurante não disponíveis. Verifique o cadastro do restaurante.');
        return;
    }
    
    // Converter para número se vier como string
    lat = parseFloat(lat);
    lng = parseFloat(lng);
    
    if (isNaN(lat) || isNaN(lng)) {
        console.error('❌ Coordenadas não são números:', { lat, lng });
        alert('Coordenadas inválidas');
        return;
    }
    
    console.log('✅ Coordenadas válidas:', { lat, lng });
    
    // Buscar detalhes completos da entrega
    try {
        const data = await fetchAPI(`${API_URL}/entregadores/entregas/${entregaId}`);
        if (!data.success) {
            alert('Erro ao carregar dados da entrega');
            return;
        }
        
        const entrega = data.entrega;
        console.log('📦 Dados da entrega:', entrega);
        
        // Abrir modal com mapa
        const modalBody = document.getElementById('modalMapaBody');
        modalBody.innerHTML = `
            <div class="mapa-info">
                <div class="info-destino">
                    <h5><i class="fas fa-store"></i> Restaurante</h5>
                    <p><strong>Pedido:</strong> #${entrega.numero_pedido || entrega.id}</p>
                    <p><strong>Código Retirada:</strong> <span class="codigo-pequeno">${entrega.codigo_retirada ? entrega.codigo_retirada.toUpperCase() : 'N/A'}</span></p>
                    ${entrega.distancia_ate_restaurante_km ? `<p><strong>Distância:</strong> ${entrega.distancia_ate_restaurante_km} km</p>` : ''}
                    <p><small>Coords: ${lat.toFixed(6)}, ${lng.toFixed(6)}</small></p>
                </div>
            </div>
            <div id="mapContainer" style="height: 500px; width: 100%; border-radius: 8px; margin-top: 15px;"></div>
            <div class="mapa-acoes" style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                <button id="btnIniciarNavegacao" class="btn btn-success btn-lg" style="display: none; flex: 1; min-width: 200px;" onclick="iniciarNavegacao()">
                    <i class="fas fa-route"></i> Iniciar Navegação
                </button>
                <button class="btn btn-info" onclick="focarNoDestino()">
                    <i class="fas fa-store"></i> Ver Restaurante
                </button>
                <button class="btn btn-secondary" onclick="fecharModalMapa()">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        `;
        
        document.getElementById('modalMapa').classList.add('active');
        
        // Inicializar mapa após modal abrir
        setTimeout(() => {
            console.log('⏱️ Inicializando mapa...');
            initRouteMap('mapContainer', lat, lng, restauranteIcon, 'Restaurante');
        }, 300);
        
    } catch (error) {
        console.error('❌ Erro:', error);
        alert('Erro ao abrir mapa: ' + error.message);
    }
}

/**
 * Abrir mapa para ir ao cliente
 */
async function abrirMapaCliente(entregaId, lat, lng) {
    if (!lat || !lng) {
        alert('Coordenadas do cliente não disponíveis');
        return;
    }
    
    // Buscar detalhes completos da entrega
    try {
        const data = await fetchAPI(`${API_URL}/entregadores/entregas/${entregaId}`);
        if (!data.success) {
            alert('Erro ao carregar dados da entrega');
            return;
        }
        
        const entrega = data.entrega;
        
        // Abrir modal com mapa
        const modalBody = document.getElementById('modalMapaBody');
        modalBody.innerHTML = `
            <div class="mapa-info">
                <div class="info-destino">
                    <h5><i class="fas fa-user"></i> Cliente: ${entrega.cliente_nome}</h5>
                    <p><strong>Telefone:</strong> <a href="tel:${entrega.cliente_telefone}">${entrega.cliente_telefone}</a></p>
                    <p><strong>Endereço:</strong> ${entrega.endereco_completo}</p>
                    ${entrega.endereco_referencia ? `<p><strong>Referência:</strong> ${entrega.endereco_referencia}</p>` : ''}
                    ${entrega.distancia_ate_cliente_km ? `<p><strong>Distância:</strong> ${entrega.distancia_ate_cliente_km} km</p>` : ''}
                    ${entrega.observacoes ? `<p><strong>Obs:</strong> ${entrega.observacoes}</p>` : ''}
                </div>
            </div>
            <div id="mapContainer" style="height: 500px; width: 100%; border-radius: 8px; margin-top: 15px;"></div>
            <div class="mapa-acoes" style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                <button id="btnIniciarNavegacao" class="btn btn-success btn-lg" style="display: none; flex: 1; min-width: 200px;" onclick="iniciarNavegacao()">
                    <i class="fas fa-route"></i> Iniciar Navegação
                </button>
                <button class="btn btn-info" onclick="focarNoDestino()">
                    <i class="fas fa-user"></i> Ver Cliente
                </button>
                <a href="tel:${entrega.cliente_telefone}" class="btn btn-success">
                    <i class="fas fa-phone"></i> Ligar para Cliente
                </a>
                <button class="btn btn-secondary" onclick="fecharModalMapa()">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        `;
        
        document.getElementById('modalMapa').classList.add('active');
        
        // Inicializar mapa após modal abrir
        setTimeout(() => {
            initRouteMap('mapContainer', lat, lng, clienteIcon, entrega.cliente_nome);
        }, 100);
        
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao abrir mapa');
    }
}

/**
 * Inicializar mapa de rota
 */
function initRouteMap(containerId, destinoLat, destinoLng, icon, label) {
    console.log('🗺️ initRouteMap chamado:', { containerId, destinoLat, destinoLng, label });
    
    // Verificar se Leaflet está disponível
    if (typeof L === 'undefined') {
        console.error('❌ Leaflet não está carregado!');
        alert('Erro: Biblioteca de mapas não carregada. Recarregue a página.');
        return;
    }
    
    // Validar coordenadas
    destinoLat = parseFloat(destinoLat);
    destinoLng = parseFloat(destinoLng);
    
    if (isNaN(destinoLat) || isNaN(destinoLng)) {
        console.error('❌ Coordenadas inválidas:', { destinoLat, destinoLng });
        alert('Coordenadas inválidas');
        return;
    }
    
    // Destruir mapa anterior se existir
    if (currentMap) {
        console.log('🗑️ Removendo mapa anterior...');
        currentMap.remove();
        currentMap = null;
    }
    
    try {
        console.log('🎨 Criando mapa...');
        
        // Criar mapa
        currentMap = L.map(containerId, {
            zoomControl: true,
            scrollWheelZoom: true,
            dragging: true,
            touchZoom: true
        }).setView([destinoLat, destinoLng], 15);
        
        console.log('✅ Mapa criado');
        
        // Adicionar camada do OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(currentMap);
        
        console.log('✅ Camada de tiles adicionada');
        
        // Adicionar marcador do destino
        destinoMarker = L.marker([destinoLat, destinoLng], {
            icon: icon,
            title: label
        }).addTo(currentMap);
        
        destinoMarker.bindPopup(`
            <div style="text-align: center; padding: 10px;">
                <strong style="font-size: 16px;">${label}</strong><br>
                <small>${destinoLat.toFixed(6)}, ${destinoLng.toFixed(6)}</small>
            </div>
        `).openPopup();
        
        console.log('✅ Marcador de destino adicionado');
        
        // Tentar obter localização atual do usuário
        if (navigator.geolocation) {
            console.log('📍 Solicitando localização do usuário...');
            
            // Tentar getCurrentPosition primeiro (mais rápido)
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;
                    
                    console.log('✅ Localização obtida (getCurrentPosition):', { userLat, userLng, accuracy });
                    
                    // Adicionar marcador da posição real
                    userMarker = L.marker([userLat, userLng], {
                        icon: entregadorIcon,
                        title: 'Você está aqui'
                    }).addTo(currentMap);
                    
                    userMarker.bindPopup(`
                        <div style="text-align: center; padding: 10px;">
                            <strong style="font-size: 16px;">📍 Você está aqui</strong><br>
                            <small>Precisão: ${Math.round(accuracy)}m</small>
                        </div>
                    `);
                    
                    console.log('✅ Marcador do usuário adicionado');
                    
                    // Mostrar botão de iniciar navegação
                    const btnIniciarNavegacao = document.getElementById('btnIniciarNavegacao');
                    if (btnIniciarNavegacao) {
                        btnIniciarNavegacao.style.display = 'inline-block';
                    }
                    
                    // Buscar rota usando OSRM
                    buscarRotaOSRM(userLat, userLng, destinoLat, destinoLng);
                },
                (error) => {
                    console.warn('⚠️ getCurrentPosition falhou:', error.message, 'Código:', error.code);
                    
                    // Se getCurrentPosition falhar, não mostra erro - apenas ajusta o mapa
                    console.log('ℹ️ Mostrando apenas destino');
                    currentMap.setView([destinoLat, destinoLng], 15);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 10000
                }
            );
        } else {
            console.warn('⚠️ Geolocalização não disponível no navegador');
            alert('Seu navegador não suporta geolocalização');
        }
        
        // Forçar redimensionamento
        setTimeout(() => {
            console.log('🔄 Redimensionando mapa...');
            currentMap.invalidateSize();
        }, 100);
        
    } catch (error) {
        console.error('❌ Erro ao criar mapa:', error);
        alert('Erro ao criar mapa: ' + error.message);
    }
}

/**
 * Buscar rota usando OSRM (Open Source Routing Machine)
 */
async function buscarRotaOSRM(origemLat, origemLng, destinoLat, destinoLng) {
    try {
        console.log('🔍 Buscando rota...', { origemLat, origemLng, destinoLat, destinoLng });
        
        // URL da API OSRM (servidor público)
        const url = `https://router.project-osrm.org/route/v1/driving/${origemLng},${origemLat};${destinoLng},${destinoLat}?overview=full&geometries=geojson&steps=true`;
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
            console.warn('⚠️ Rota não encontrada, usando linha reta');
            desenharLinhaReta(origemLat, origemLng, destinoLat, destinoLng);
            return;
        }
        
        const route = data.routes[0];
        const coordinates = route.geometry.coordinates;
        
        // Converter coordenadas [lng, lat] para [lat, lng] (formato Leaflet)
        const routeCoords = coordinates.map(coord => [coord[1], coord[0]]);
        
        // Remover linha antiga se existir
        if (routeLine) {
            currentMap.removeLayer(routeLine);
        }
        
        // Desenhar rota
        routeLine = L.polyline(routeCoords, {
            color: '#667eea',
            weight: 5,
            opacity: 0.8,
            lineJoin: 'round',
            lineCap: 'round'
        }).addTo(currentMap);
        
        // Calcular distância e tempo
        const distanciaKm = (route.distance / 1000).toFixed(2);
        const tempoMin = Math.ceil(route.duration / 60);
        
        // Adicionar popup com informações da rota
        const centro = routeCoords[Math.floor(routeCoords.length / 2)];
        L.popup()
            .setLatLng(centro)
            .setContent(`
                <div style="text-align: center; padding: 8px;">
                    <strong>📏 ${distanciaKm} km</strong><br>
                    <strong>⏱️ ~${tempoMin} min</strong>
                </div>
            `)
            .addTo(currentMap);
        
        // Ajustar zoom para mostrar toda a rota
        const bounds = routeLine.getBounds();
        currentMap.fitBounds(bounds, { padding: [50, 50] });
        
        console.log('✅ Rota desenhada:', { distanciaKm, tempoMin });
        
    } catch (error) {
        console.error('❌ Erro ao buscar rota:', error);
        // Fallback para linha reta
        desenharLinhaReta(origemLat, origemLng, destinoLat, destinoLng);
    }
}

/**
 * Desenhar linha reta (fallback)
 */
function desenharLinhaReta(origemLat, origemLng, destinoLat, destinoLng) {
    // Remover linha antiga se existir
    if (routeLine) {
        currentMap.removeLayer(routeLine);
    }
    
    // Desenhar linha da rota
    routeLine = L.polyline([
        [origemLat, origemLng],
        [destinoLat, destinoLng]
    ], {
        color: '#667eea',
        weight: 4,
        opacity: 0.7,
        dashArray: '10, 10'
    }).addTo(currentMap);
    
    // Ajustar zoom para mostrar ambos os marcadores
    const bounds = L.latLngBounds([
        [origemLat, origemLng],
        [destinoLat, destinoLng]
    ]);
    currentMap.fitBounds(bounds, { padding: [50, 50] });
}

/**
 * Abrir no Google Maps
 */
function abrirGoogleMaps(lat, lng) {
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank');
}

/**
 * Abrir no Waze
 */
function abrirWaze(lat, lng) {
    window.open(`https://waze.com/ul?ll=${lat},${lng}&navigate=yes`, '_blank');
}

/**
 * Fechar modal do mapa
 */
function fecharModalMapa() {
    document.getElementById('modalMapa').classList.remove('active');
    if (currentMap) {
        currentMap.remove();
        currentMap = null;
    }
    userMarker = null;
    destinoMarker = null;
    routeLine = null;
}

/**
 * Iniciar navegação - abre app externo (Google Maps, Waze, etc)
 */
function iniciarNavegacao() {
    if (!userMarker || !destinoMarker) {
        alert('Localização não disponível para navegação');
        return;
    }
    
    const origem = userMarker.getLatLng();
    const destino = destinoMarker.getLatLng();
    
    // URL universal que funciona em todos os dispositivos
    const url = `https://www.google.com/maps/dir/?api=1&origin=${origem.lat},${origem.lng}&destination=${destino.lat},${destino.lng}&travelmode=driving`;
    
    // Abre em nova aba
    window.open(url, '_blank');
}

/**
 * Focar na posição de origem (entregador)
 */
function focarNaOrigem() {
    if (!currentMap || !userMarker) {
        alert('Localização não disponível');
        return;
    }
    
    const pos = userMarker.getLatLng();
    currentMap.setView([pos.lat, pos.lng], 17);
    userMarker.openPopup();
}

/**
 * Focar no destino (restaurante ou cliente)
 */
function focarNoDestino() {
    if (!currentMap || !destinoMarker) {
        alert('Destino não disponível');
        return;
    }
    
    const pos = destinoMarker.getLatLng();
    currentMap.setView([pos.lat, pos.lng], 17);
    destinoMarker.openPopup();
}

/**
 * Confirmar código de retirada e pegar pedido
 */
async function confirmarCodigoRetirada(id) {
    const codigo = prompt('Digite o código de retirada que o atendente informou:');
    
    if (!codigo) return;
    
    // Por enquanto aceita qualquer código, depois pode validar no backend
    await atualizarStatusEntrega(id, 'saiu_entrega');
}
