/**
 * IA Customizada para Sistema de Delivery
 * Aprende com o contexto do sistema e interações do usuário
 */

class CustomAI {
    constructor() {
        this.context = {
            userName: null,
            userPreferences: [],
            lastOrders: [],
            currentCart: [],
            sessionHistory: []
        };
        
        this.knowledge = {
            products: [],
            categories: [],
            commonQuestions: this.getCommonQuestions(),
            intents: this.getIntents()
        };
        
        this.conversationContext = [];
        this.init();
    }
    
    async init() {
        console.log('🤖 IA Customizada inicializando...');
        await this.loadSystemContext();
        this.loadUserContext();
        console.log('✅ IA pronta!');
    }
    
    async loadSystemContext() {
        try {
            // Carregar produtos do sistema
            const response = await fetch('/api/products');
            if (response.ok) {
                const data = await response.json();
                this.knowledge.products = data.products || data;
                console.log(`📚 ${this.knowledge.products.length} produtos carregados`);
            }
            
            // Carregar categorias
            const catResponse = await fetch('/api/categories');
            if (catResponse.ok) {
                const catData = await catResponse.json();
                this.knowledge.categories = catData.categories || catData;
                console.log(`📂 ${this.knowledge.categories.length} categorias carregadas`);
            }
        } catch (error) {
            console.warn('⚠️ Erro ao carregar contexto:', error);
        }
    }
    
    loadUserContext() {
        // Carregar preferências do localStorage
        const savedContext = localStorage.getItem('ai_user_context');
        if (savedContext) {
            const data = JSON.parse(savedContext);
            this.context.userPreferences = data.preferences || [];
            this.context.lastOrders = data.lastOrders || [];
        }
        
        // Nome do usuário
        const userName = localStorage.getItem('user_name');
        if (userName) {
            this.context.userName = userName;
        }
        
        // Carrinho atual
        const cart = localStorage.getItem('cart');
        if (cart) {
            this.context.currentCart = JSON.parse(cart);
        }
    }
    
    saveUserContext() {
        localStorage.setItem('ai_user_context', JSON.stringify({
            preferences: this.context.userPreferences,
            lastOrders: this.context.lastOrders
        }));
    }
    
    getIntents() {
        return {
            greeting: {
                patterns: ['oi', 'olá', 'ola', 'hey', 'bom dia', 'boa tarde', 'boa noite', 'e aí', 'eai'],
                responses: [
                    'Oi {name}! Como posso ajudar você hoje?',
                    'Olá! Tudo bem? O que vai querer hoje?',
                    'E aí! Bora fazer um pedido?',
                    'Opa! Chegou com fome? Vamos lá!'
                ]
            },
            searchFood: {
                patterns: ['quero', 'busca', 'procura', 'tem', 'vende'],
                action: 'search'
            },
            showMenu: {
                patterns: ['cardápio', 'cardapio', 'menu', 'produtos', 'o que tem', 'opções', 'opcoes'],
                action: 'showMenu'
            },
            showCart: {
                patterns: ['carrinho', 'sacola', 'cesta', 'pedido atual', 'o que pedi'],
                action: 'showCart'
            },
            showOrders: {
                patterns: ['meus pedidos', 'pedidos', 'histórico', 'historico', 'já pedi', 'ja pedi'],
                action: 'showOrders'
            },
            recommendation: {
                patterns: ['recomenda', 'sugere', 'sugestão', 'sugestao', 'o que é bom', 'melhor'],
                action: 'recommend'
            },
            help: {
                patterns: ['ajuda', 'como funciona', 'não entendi', 'nao entendi', 'o que você faz', 'comandos'],
                action: 'help'
            }
        };
    }
    
    getCommonQuestions() {
        return {
            'qual o prazo de entrega': 'O prazo de entrega é de 30 a 45 minutos, dependendo da sua localização!',
            'quanto é o frete': 'O valor do frete varia de acordo com seu endereço. Você verá o valor antes de finalizar o pedido.',
            'formas de pagamento': 'Aceitamos cartão, dinheiro, PIX e vale refeição!',
            'tem desconto': 'Sempre temos promoções! Veja a seção de destaques no cardápio.',
            'como cancelo': 'Você pode cancelar pelo app em "Meus Pedidos", mas só antes do preparo começar.',
            'tempo de preparo': 'O preparo leva de 15 a 30 minutos, depois é só aguardar a entrega!',
            'taxa de entrega': 'A taxa de entrega é calculada automaticamente baseada na distância.',
            'pedido mínimo': 'O valor mínimo do pedido é R$ 15,00.'
        };
    }
    
    async processMessage(message) {
        const msg = message.toLowerCase().trim();
        
        // Adicionar à história da conversa
        this.conversationContext.push({
            type: 'user',
            message: msg,
            timestamp: Date.now()
        });
        
        // Limitar histórico a 10 mensagens
        if (this.conversationContext.length > 10) {
            this.conversationContext.shift();
        }
        
        // Detectar intent
        const intent = this.detectIntent(msg);
        console.log('🎯 Intent detectado:', intent);
        
        // Processar baseado no intent
        let response;
        switch (intent.type) {
            case 'greeting':
                response = await this.handleGreeting(msg);
                break;
            case 'searchFood':
                response = await this.handleSearch(msg);
                break;
            case 'showMenu':
                response = this.handleShowMenu();
                break;
            case 'showCart':
                response = this.handleShowCart();
                break;
            case 'showOrders':
                response = this.handleShowOrders();
                break;
            case 'recommendation':
                response = await this.handleRecommendation(msg);
                break;
            case 'help':
                response = this.handleHelp();
                break;
            case 'question':
                response = this.handleQuestion(msg);
                break;
            default:
                response = await this.handleGeneral(msg);
        }
        
        // Adicionar resposta ao histórico
        this.conversationContext.push({
            type: 'assistant',
            message: response.text,
            timestamp: Date.now()
        });
        
        return response;
    }
    
    detectIntent(message) {
        const msg = message.toLowerCase();
        
        // Verificar perguntas comuns
        for (const [key, value] of Object.entries(this.knowledge.commonQuestions)) {
            if (msg.includes(key.toLowerCase())) {
                return { type: 'question', key };
            }
        }
        
        // Verificar intents
        for (const [intentName, intent] of Object.entries(this.knowledge.intents)) {
            for (const pattern of intent.patterns) {
                if (msg.includes(pattern.toLowerCase())) {
                    return { type: intentName, action: intent.action };
                }
            }
        }
        
        // Verificar se menciona produtos específicos
        const foundProducts = this.knowledge.products.filter(p => 
            msg.includes(p.nome.toLowerCase()) || 
            (p.descricao && msg.includes(p.descricao.toLowerCase()))
        );
        
        if (foundProducts.length > 0) {
            return { type: 'searchFood', products: foundProducts };
        }
        
        return { type: 'general' };
    }
    
    async handleGreeting(message) {
        const intent = this.knowledge.intents.greeting;
        let response = intent.responses[Math.floor(Math.random() * intent.responses.length)];
        
        // Personalizar com nome
        if (this.context.userName) {
            response = response.replace('{name}', this.context.userName);
        } else {
            response = response.replace('{name}', '');
        }
        
        // Se tiver pedidos recentes, sugerir
        if (this.context.lastOrders.length > 0) {
            const lastProduct = this.context.lastOrders[0];
            response += ` Da última vez você pediu ${lastProduct}. Quer repetir?`;
        }
        
        return { text: response, action: null };
    }
    
    async handleSearch(message) {
        const words = message.toLowerCase().split(' ');
        const searchTerms = words.filter(w => w.length > 3);
        
        const results = this.knowledge.products.filter(product => {
            const searchText = `${product.nome} ${product.descricao || ''}`.toLowerCase();
            return searchTerms.some(term => searchText.includes(term));
        });
        
        if (results.length === 0) {
            return {
                text: 'Não encontrei isso no cardápio. Quer ver todas as opções?',
                action: { type: 'showMenu' }
            };
        }
        
        if (results.length === 1) {
            const product = results[0];
            return {
                text: `Achei! ${product.nome} por R$ ${product.preco}. Quer adicionar ao carrinho?`,
                action: { type: 'showProduct', productId: product.id }
            };
        }
        
        return {
            text: `Encontrei ${results.length} opções! Vou mostrar pra você.`,
            action: { type: 'searchFood', query: searchTerms[0], results }
        };
    }
    
    handleShowMenu() {
        const totalProducts = this.knowledge.products.length;
        const categories = this.knowledge.categories.length;
        
        return {
            text: `Temos ${totalProducts} produtos em ${categories} categorias! Dá uma olhada!`,
            action: { type: 'showMenu' }
        };
    }
    
    handleShowCart() {
        const cartItems = this.context.currentCart.length;
        
        if (cartItems === 0) {
            return {
                text: 'Seu carrinho está vazio. Quer ver o cardápio?',
                action: null
            };
        }
        
        return {
            text: `Você tem ${cartItems} ${cartItems === 1 ? 'item' : 'itens'} no carrinho!`,
            action: { type: 'showCart' }
        };
    }
    
    handleShowOrders() {
        return {
            text: 'Vou mostrar seus pedidos!',
            action: { type: 'showOrders' }
        };
    }
    
    async handleRecommendation(message) {
        // Produtos em destaque ou mais vendidos
        const featured = this.knowledge.products.filter(p => p.destaque || p.popular);
        
        if (featured.length > 0) {
            const random = featured[Math.floor(Math.random() * featured.length)];
            return {
                text: `Recomendo muito o ${random.nome}! ${random.descricao || 'É um dos favoritos aqui!'}`,
                action: { type: 'showProduct', productId: random.id }
            };
        }
        
        // Recomendar baseado em pedidos anteriores
        if (this.context.lastOrders.length > 0) {
            return {
                text: `Baseado no que você já pediu, acho que vai gostar das nossas novidades! Quer ver?`,
                action: { type: 'showMenu' }
            };
        }
        
        return {
            text: 'Deixa eu mostrar os produtos mais pedidos por aqui!',
            action: { type: 'showMenu', filter: 'popular' }
        };
    }
    
    handleHelp() {
        return {
            text: `Posso te ajudar com:\n
• "Quero pizza" - buscar produtos\n
• "Mostra o cardápio" - ver todos os produtos\n
• "Meu carrinho" - ver o que já adicionou\n
• "Meus pedidos" - ver histórico\n
• "Recomenda algo" - sugestões\n
• Perguntas sobre entrega, pagamento, etc.`,
            action: null
        };
    }
    
    handleQuestion(message) {
        for (const [key, answer] of Object.entries(this.knowledge.commonQuestions)) {
            if (message.includes(key.toLowerCase())) {
                return { text: answer, action: null };
            }
        }
        
        return {
            text: 'Não tenho certeza sobre isso. Quer falar com o atendimento?',
            action: null
        };
    }
    
    async handleGeneral(message) {
        // Tentar encontrar algo relevante
        const words = message.split(' ').filter(w => w.length > 3);
        
        for (const word of words) {
            const products = this.knowledge.products.filter(p => 
                p.nome.toLowerCase().includes(word.toLowerCase())
            );
            
            if (products.length > 0) {
                return {
                    text: `Falando em ${word}, temos essas opções:`,
                    action: { type: 'searchFood', query: word, results: products }
                };
            }
        }
        
        // Resposta genérica inteligente
        const responses = [
            'Interessante! Mas não entendi direito. Pode explicar de outro jeito?',
            'Hmm, não captei. Tenta perguntar sobre produtos, cardápio ou pedidos?',
            'Não tenho certeza do que você quer. Diz "ajuda" pra ver o que posso fazer!',
            'Desculpa, não entendi. Quer ver o cardápio?'
        ];
        
        return {
            text: responses[Math.floor(Math.random() * responses.length)],
            action: null
        };
    }
    
    // Aprendizado: salvar interação bem-sucedida
    learnFromSuccess(userMessage, action) {
        const preference = {
            message: userMessage,
            action: action,
            timestamp: Date.now()
        };
        
        this.context.userPreferences.push(preference);
        
        // Manter só as últimas 50
        if (this.context.userPreferences.length > 50) {
            this.context.userPreferences.shift();
        }
        
        this.saveUserContext();
    }
    
    // Registrar pedido realizado
    registerOrder(orderDetails) {
        this.context.lastOrders.unshift(orderDetails);
        
        // Manter só os últimos 10
        if (this.context.lastOrders.length > 10) {
            this.context.lastOrders.pop();
        }
        
        this.saveUserContext();
    }
}

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.CustomAI = CustomAI;
}
