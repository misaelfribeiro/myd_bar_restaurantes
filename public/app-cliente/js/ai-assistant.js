/**
 * Assistente de IA - Carla 🤖
 * Usa a IA própria do sistema com memória conversacional
 */

class AIAssistant {
    constructor() {
        // Usar API própria da Carla
        this.apiUrl = '/api/ai/process';
        this.sessionToken = localStorage.getItem('carla_session_token');
        
        this.conversationHistory = [];
        this.context = {
            userName: null,
            currentPage: 'home',
            cartItems: [],
            lastOrder: null,
            availableProducts: []
        };
    }
    
    async processMessage(userMessage) {
        console.log('🤖 Carla processando:', userMessage);
        
        // Atualizar contexto
        this.updateContext();
        
        // Obter tenant_code do localStorage ou config
        const tenantCode = localStorage.getItem('tenant_code') || 
                         (window.appState?.tenantCode) || 
                         'RESTAURANTE0001'; // fallback para tenant padrão
        
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Tenant-Code': tenantCode
                },
                body: JSON.stringify({
                    message: userMessage,
                    session_token: this.sessionToken,
                    user_id: this.context.userId || null,
                    empresa_id: tenantCode
                })
            });
            
            if (!response.ok) {
                throw new Error('Erro na API da Carla');
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Erro ao processar');
            }
            
            const result = data.data;
            
            // Salvar session token para manter contexto
            if (result.session_token) {
                this.sessionToken = result.session_token;
                localStorage.setItem('carla_session_token', result.session_token);
            }
            
            console.log('🤖 Carla respondeu:', result.response);
            console.log('🎯 Intenção:', result.intent);
            console.log('🎬 Ação:', result.action);
            console.log('📊 Confiança:', (result.confidence * 100).toFixed(1) + '%');
            
            // Se há produtos, mostrar
            if (result.products && result.products.length > 0) {
                console.log('🍕 Produtos encontrados:', result.products.length);
            }
            
            // Adicionar ao histórico
            this.conversationHistory.push({
                user: userMessage,
                assistant: result.response,
                intent: result.intent,
                confidence: result.confidence,
                products: result.products || [],
                timestamp: Date.now()
            });
            
            // Executar ação se houver
            const action = this.mapAction(result);
            
            return {
                text: result.response,
                action: action,
                confidence: result.confidence,
                intent: result.intent,
                products: result.products || []
            };
            
        } catch (error) {
            console.error('❌ Erro ao processar com Carla:', error);
            return {
                text: 'Desculpe, tive um problema ao processar sua mensagem. Pode tentar novamente? 😔',
                action: null
            };
        }
    }
    
    mapAction(result) {
        if (!result.action) {
            return null;
        }
        
        const actionMap = {
            // Busca e Menu
            'searchProduct': { type: 'searchFood', query: result.parameters?.product || '' },
            'filterByCategory': { type: 'showMenu', category: result.parameters?.category || '' },
            'showMenu': { type: 'showMenu' },
            
            // Carrinho
            'showCart': { type: 'showCart' },
            'addToCart': { type: 'addToCart', product: result.parameters?.product },
            'removeFromCart': { type: 'removeFromCart', product: result.parameters?.product },
            'checkout': { type: 'checkout' },
            'clearCart': { type: 'clearCart' },
            
            // Pedidos
            'showOrders': { type: 'showOrders' },
            'repeatOrder': { type: 'repeatOrder' },
            'trackDelivery': { type: 'trackDelivery' },
            'cancelOrder': { type: 'cancelOrder' },
            
            // Delivery
            'changeAddress': { type: 'changeAddress' },
            'deliveryTime': { type: 'deliveryTime' },
            
            // Pagamento
            'paymentMethods': { type: 'paymentMethods' },
            'applyDiscount': { type: 'applyDiscount', coupon: result.parameters?.coupon },
            'showPromotions': { type: 'showPromotions' },
            
            // Conta
            'showProfile': { type: 'showProfile' },
            'showFavorites': { type: 'showFavorites' },
            
            // Suporte
            'contactSupport': { type: 'contactSupport' },
            'reportProblem': { type: 'reportProblem' }
        };
        
        return actionMap[result.action] || { type: result.action, ...result.parameters };
    }
    
    updateContext() {
        // Atualizar contexto com dados do app
        if (window.appState) {
            this.context.userId = window.appState.user?.id;
            this.context.userName = window.appState.user?.nome;
            this.context.currentPage = window.appState.currentPage;
            this.context.cartItems = window.appState.cart || [];
            this.context.availableProducts = window.appState.products || [];
        }
    }
    
    clearHistory() {
        this.conversationHistory = [];
        this.sessionToken = null;
        localStorage.removeItem('carla_session_token');
    }
    
    async sendFeedback(trainingDataId, isCorrect, score) {
        try {
            const response = await fetch('/api/ai/feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    training_data_id: trainingDataId,
                    correct: isCorrect,
                    feedback_score: score
                })
            });
            
            const data = await response.json();
            console.log('📝 Feedback enviado:', data);
            return data.success;
        } catch (error) {
            console.error('❌ Erro ao enviar feedback:', error);
            return false;
        }
    }
}

// Exportar para uso global
window.AIAssistant = AIAssistant;
