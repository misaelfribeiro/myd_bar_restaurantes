/**
 * Carla - Assistente Virtual da EatsFood
 * IA Própria com Aprendizado Neural
 * Usa Web Speech API para reconhecimento e síntese de voz
 */

class VoiceAssistant {
    constructor() {
        this.recognition = null;
        this.hotwordRecognition = null; // Reconhecimento separado para hotword
        this.synthesis = window.speechSynthesis;
        this.isListening = false;
        this.isEnabled = false;
        this.isHotwordListening = false; // Flag para hotword
        this.hotwordDetected = false;
        this.hotwordCheckInterval = null; // Interval para verificar se hotword está ativo
        this.manualStop = false; // Flag para parada manual
        
        // Configurações de voz
        this.voiceConfig = {
            lang: 'pt-BR',
            rate: 1.1,  // Mais rápido
            pitch: 1.1,  // Mais agudo/feminino
            volume: 1.0
        };
        
        // Sessão da IA
        this.sessionToken = localStorage.getItem('ai_session_token') || null;
        this.apiBaseUrl = '/api/ai';
        this.lastProducts = []; // Armazena últimos produtos mostrados
        
        // Carregar vozes (necessário no Chrome/Android)
        this.loadVoices();
        
        this.init();
    }
    
    loadVoices() {
        // Chrome carrega vozes de forma assíncrona
        if (this.synthesis) {
            let voices = this.synthesis.getVoices();
            
            // Se não houver vozes, aguardar evento
            if (voices.length === 0) {
                this.synthesis.addEventListener('voiceschanged', () => {
                    voices = this.synthesis.getVoices();
                });
            }
        }
    }
    
    init() {
        // Verificar suporte do navegador
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            console.warn('Reconhecimento de voz não suportado');
            this.showUnsupportedMessage();
            return;
        }
        
        // Em mobile, não solicitar permissão automaticamente
        if (/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
            this.initRecognition();
        } else {
            this.requestMicrophonePermission();
        }
    }
    
    async requestMicrophonePermission() {
        // Não pedir permissão automaticamente - apenas inicializar
        // A permissão será pedida quando o usuário clicar no botão
        this.initRecognition();
    }
    
    showUnsupportedMessage() {
        const voiceBtn = document.createElement('button');
        voiceBtn.className = 'voice-assistant-btn disabled';
        voiceBtn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
        voiceBtn.title = 'Assistente de voz não disponível neste dispositivo';
        voiceBtn.style.opacity = '0.5';
        voiceBtn.style.cursor = 'not-allowed';
        document.body.appendChild(voiceBtn);
    }
    
    showPermissionError() {
        // Apenas log no console - não mostrar toast automático
        console.warn('Permissão de microfone necessária');
    }
    
    showMobilePermissionDialog() {
        // Apenas log no console - não mostrar toast automático
        console.warn('Configure permissão de microfone nas configurações do navegador');
    }
    
    showToast(message, type = 'info', duration = 3000) {
        // Criar toast Bootstrap
        const toastContainer = document.getElementById('toastContainer') || this.createToastContainer();
        
        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'warning' ? 'bg-warning' : type === 'info' ? 'bg-info' : 'bg-primary';
        
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: duration });
        toast.show();
        
        // Remover após esconder
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }
    
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
    
    initRecognition() {
        // Inicializar reconhecimento de voz
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.recognition = new SpeechRecognition();
        
        this.recognition.lang = 'pt-BR';
        this.recognition.continuous = true; // SEMPRE CONTÍNUO
        this.recognition.interimResults = true; // Ativar resultados intermediários
        this.recognition.maxAlternatives = 1;
        
        // Variável para controle de estado
        this.isRecordingCommand = false; // false = ouvindo "Carla", true = gravando comando
        this.commandTimeout = null;
        this.commandStartTime = null;
        
        // Eventos do reconhecimento
        this.recognition.onstart = () => {
            this.isListening = true;
            
            // Só fazer beep quando estiver gravando comando
            if (this.isRecordingCommand) {
                console.log('🔴 GRAVANDO COMANDO AGORA!');
                try {
                    const beep = new SpeechSynthesisUtterance('🎤');
                    beep.volume = 0.1;
                    beep.rate = 3;
                    if (this.synthesis) this.synthesis.speak(beep);
                } catch (e) {}
            } else {
                console.log('👂 Ouvindo em background por "Carla"...');
            }
        };
        
        this.recognition.onend = () => {
            console.log('🟡 Recognition ended');
            this.isListening = false;
            
            // Só reiniciar se ainda estiver ativo e não foi parado manualmente
            if (this.isActive && !this.manualStop) {
                setTimeout(() => {
                    try {
                        this.recognition.start();
                        console.log('🔄 Recognition reiniciado automaticamente');
                    } catch (e) {
                        console.log('⚠️ Erro ao reiniciar:', e.message);
                    }
                }, 100);
            } else {
                console.log('⏸️ Recognition não será reiniciado (ativo:', this.isActive, 'manualStop:', this.manualStop, ')');
            }
        };
        
        this.recognition.onresult = (event) => {
            const last = event.results.length - 1;
            const result = event.results[last];
            const transcript = result[0].transcript.toLowerCase();
            const isFinal = result.isFinal;
            
            // Log apenas quando relevante
            if (isFinal || transcript.includes('carla') || transcript.includes('karla')) {
                console.log('🎙️ Captou:', transcript, '| Final:', isFinal, '| Modo:', this.isRecordingCommand ? '🔴 COMANDO' : '👂 HOTWORD');
            }
            
            // MODO 1: Procurando "Carla"
            if (!this.isRecordingCommand) {
                if (transcript.includes('carla') || transcript.includes('karla')) {
                    console.log('🎤 ========== "CARLA" DETECTADA! ==========');
                    this.isRecordingCommand = true;
                    this.commandStartTime = Date.now();
                    
                    // Acender botão
                    const btn = document.getElementById('voiceAssistantBtn');
                    if (btn) {
                        btn.classList.add('listening');
                        btn.style.background = 'linear-gradient(135deg, #f5576c 0%, #ff6b6b 100%) !important';
                    }
                    
                    // Timeout: 5s para falar comando
                    if (this.commandTimeout) clearTimeout(this.commandTimeout);
                    this.commandTimeout = setTimeout(() => {
                        console.log('⏰ Timeout - voltando ao modo hotword');
                        this.resetToHotwordMode();
                    }, 5000);
                }
                return;
            }
            
            // MODO 2: Gravando comando
            if (this.isRecordingCommand && isFinal) {
                if (this.commandTimeout) {
                    clearTimeout(this.commandTimeout);
                    this.commandTimeout = null;
                }
                
                let command = transcript.replace(/^\s*(carla|karla)\s*/gi, '').trim();
                
                console.log('✅ Comando finalizado:', command);
                this.isRecordingCommand = false;
                
                // Apagar botão
                this.resetToHotwordMode();
                
                if (command.length > 0) {
                    this.processCommand(command);
                } else {
                    console.log('⚠️ Sem comando, enviando saudação');
                    this.processCommand('olá');
                }
            }
        };
        
        this.recognition.onerror = (event) => {
            console.error('❌ Erro no reconhecimento:', event.error);
            
            // Ignorar erros comuns
            if (event.error === 'no-speech') {
                console.log('🤫 Sem fala - continuando...');
                return;
            }
            
            if (event.error === 'aborted') {
                console.log('⚠️ Recognition aborted - continuando...');
                return;
            }
            
            let errorMessage = 'Desculpe, não consegui entender. Pode repetir?';
            
            // Mensagens específicas por tipo de erro
            switch(event.error) {
                case 'not-allowed':
                case 'permission-denied':
                    errorMessage = 'Você precisa permitir o acesso ao microfone para usar a Carla, sua assistente virtual.';
                    this.showPermissionError();
                    break;
                case 'no-speech':
                    errorMessage = 'Não consegui ouvir nada. Tente falar mais alto.';
                    break;
                case 'network':
                    errorMessage = 'Erro de conexão. Verifique sua internet.';
                    break;
            }
            
            // Mostrar erro apenas visualmente (sem falar)
            console.log('❌ Erro:', errorMessage);
            this.updateUI('error');
        };
        
        this.recognition.onend = () => {
            console.log('🟡 Recognition ended');
            this.isListening = false;
            
            // Só reiniciar se NÃO foi abortado manualmente
            if (!this.manualStop) {
                console.log('🔄 Reiniciando recognition em 500ms...');
                setTimeout(() => {
                    try {
                        this.recognition.start();
                        console.log('✅ Recognition reiniciado');
                    } catch (e) {
                        console.log('⚠️ Erro ao reiniciar:', e.message);
                    }
                }, 500); // Aumentar delay para 500ms
            } else {
                console.log('⏸️ Parado manualmente, não reiniciando');
                this.manualStop = false;
            }
        };
        
        // NÃO inicializar hotword recognition separado
        // Usar apenas um recognition contínuo
        
        this.isEnabled = true;
        this.createUI();
        
        // Iniciar recognition automaticamente após 1 segundo
        setTimeout(() => {
            try {
                this.recognition.start();
                console.log('👂 Carla iniciada - sempre ouvindo!');
            } catch (e) {
                console.error('❌ Erro ao iniciar:', e);
            }
        }, 1000);
    }
    
    resetToHotwordMode() {
        const btn = document.getElementById('voiceAssistantBtn');
        if (btn) {
            btn.classList.remove('listening');
            btn.style.background = 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';
        }
        this.isRecordingCommand = false;
        if (this.commandTimeout) {
            clearTimeout(this.commandTimeout);
            this.commandTimeout = null;
        }
    }
    
    createUI() {
        // Verificar se já pediu permissão antes
        const hasAskedPermission = localStorage.getItem('voiceAssistantPermission');
        
        // Criar botão flutuante da assistente
        const voiceBtn = document.createElement('button');
        voiceBtn.id = 'voiceAssistantBtn';
        voiceBtn.className = 'voice-assistant-btn hotword-mode'; // Sempre em modo hotword
        
        // Estilo inline para garantir visibilidade
        voiceBtn.style.cssText = `
            position: fixed !important;
            bottom: 80px !important;
            right: 20px !important;
            width: 60px !important;
            height: 60px !important;
            border-radius: 50% !important;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
            border: none !important;
            color: white !important;
            font-size: 24px !important;
            cursor: pointer !important;
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.4) !important;
            z-index: 999999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            opacity: 1 !important;
            visibility: visible !important;
        `;
        
        voiceBtn.innerHTML = `
            <i class="fas fa-microphone"></i>
            <span class="pulse-ring"></span>
        `;
        voiceBtn.title = 'Carla - Sempre ouvindo! Diga "Carla" para ativar';
        
        // Adicionar badge "SEMPRE OUVINDO"
        const badge = document.createElement('span');
        badge.style.cssText = `
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            font-size: 16px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(17, 153, 142, 0.4);
        `;
        badge.innerHTML = '👂';
        voiceBtn.appendChild(badge);
        
        voiceBtn.addEventListener('click', async () => {
            // Abrir modal para visualizar status
            const modal = document.getElementById('voiceAssistantModal');
            if (modal) {
                modal.classList.add('show');
                this.initModalListeners();
            }
            
            console.log('👂 Modal aberto - Carla está sempre ouvindo por "Carla"');
        });
        
        document.body.appendChild(voiceBtn);
        
        // Criar modal de feedback
        const modal = document.createElement('div');
        modal.id = 'voiceAssistantModal';
        modal.className = 'voice-assistant-modal';
        modal.innerHTML = `
            <div class="voice-assistant-content">
                <button class="btn-close-modal" onclick="document.getElementById('voiceAssistantModal').classList.remove('show')" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">×</button>
                
                <h4 style="margin-bottom: 20px; color: #667eea;">
                    🤖 Carla - Assistente Virtual
                </h4>
                
                <div class="alert alert-success mb-3" style="border-radius: 12px; border-left: 4px solid #11998e;">
                    <i class="fas fa-microphone-alt me-2"></i>
                    <strong>👂 Estou sempre ouvindo!</strong>
                    <p class="mb-0 mt-2 small">Diga "Carla" a qualquer momento para me ativar</p>
                </div>
                
                <p class="voice-status">Aguardando você dizer "Carla"...</p>
                <p class="voice-transcript"></p>
                
                <div class="voice-suggestions">
                    <p class="text-muted small">Exemplos de comandos:</p>
                    <div class="d-flex gap-2 flex-wrap justify-content-center mt-2">
                        <span class="badge bg-secondary" style="cursor: default;">🏪 Restaurantes</span>
                        <span class="badge bg-secondary" style="cursor: default;">🍺 Bebidas</span>
                        <span class="badge bg-secondary" style="cursor: default;">🛒 Carrinho</span>
                        <span class="badge bg-secondary" style="cursor: default;">💳 Finalizar</span>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Adicionar estilos
        this.addStyles();
    }
    
    initModalListeners() {
        console.log('🎯 Modal inicializado - Modo sempre ouvindo ativo');
        // Sem listeners necessários - hotword sempre ativo
    }
    
    addStyles() {
        const voiceAssistantStyle = document.createElement('style');
        voiceAssistantStyle.textContent = `
            .voice-assistant-btn {
                position: fixed;
                bottom: 80px;
                right: 20px;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                color: white;
                font-size: 24px;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
                transition: all 0.3s ease;
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 1;
            }
            
            .voice-assistant-btn:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            }
            
            .voice-assistant-btn.listening {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                animation: pulse 1.5s infinite;
            }
            
            .voice-assistant-btn.hotword-mode {
                background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                animation: pulse-slow 3s infinite;
            }
            
            .voice-assistant-btn.hotword-mode::after {
                content: '👂';
                position: absolute;
                top: -5px;
                right: -5px;
                font-size: 16px;
                animation: wave 2s infinite;
            }
            
            .pulse-ring {
                position: absolute;
                width: 100%;
                height: 100%;
                border: 3px solid rgba(102, 126, 234, 0.5);
                border-radius: 50%;
                animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
                opacity: 0;
            }
            
            .voice-assistant-btn.listening .pulse-ring {
                animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
            
            @keyframes pulse-slow {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.03); }
            }
            
            @keyframes wave {
                0%, 100% { transform: rotate(0deg); }
                25% { transform: rotate(15deg); }
                75% { transform: rotate(-15deg); }
            }
            
            @keyframes pulse-ring {
                0% {
                    transform: scale(0.8);
                    opacity: 1;
                }
                100% {
                    transform: scale(1.4);
                    opacity: 0;
                }
            }
            
            .voice-assistant-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                backdrop-filter: blur(5px);
            }
            
            .voice-assistant-modal.show {
                display: flex;
            }
            
            .voice-assistant-content {
                background: white;
                border-radius: 20px;
                padding: 40px;
                max-width: 500px;
                width: 90%;
                text-align: center;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            }
            
            .voice-animation {
                display: flex;
                gap: 8px;
                justify-content: center;
                align-items: flex-end;
                height: 60px;
                margin-bottom: 20px;
            }
            
            .voice-wave {
                width: 8px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 4px;
                animation: wave 1.2s ease-in-out infinite;
            }
            
            .voice-wave:nth-child(2) {
                animation-delay: 0.2s;
            }
            
            .voice-wave:nth-child(3) {
                animation-delay: 0.4s;
            }
            
            @keyframes wave {
                0%, 100% { height: 20px; }
                50% { height: 50px; }
            }
            
            .voice-status {
                font-size: 18px;
                font-weight: 600;
                color: #333;
                margin-bottom: 10px;
            }
            
            .voice-transcript {
                font-size: 16px;
                color: #666;
                min-height: 30px;
                font-style: italic;
            }
            
            .voice-suggestions {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #eee;
            }
            
            .voice-badge-new {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #f5576c;
                color: white;
                font-size: 10px;
                font-weight: bold;
                padding: 2px 6px;
                border-radius: 10px;
                animation: badge-pulse 2s infinite;
            }
            
            @keyframes badge-pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            
            /* Estilos específicos para mobile */
            @media (max-width: 768px) {
                .voice-assistant-btn {
                    width: 56px;
                    height: 56px;
                    font-size: 20px;
                    bottom: 90px;
                    right: 16px;
                    /* Aumentar área de toque */
                    -webkit-tap-highlight-color: transparent;
                }
                
                .voice-assistant-btn:active {
                    transform: scale(0.95);
                }
                
                .voice-assistant-content {
                    padding: 24px;
                    border-radius: 16px;
                    margin: 16px;
                }
                
                .voice-animation {
                    height: 50px;
                }
                
                .voice-status {
                    font-size: 16px;
                }
            }
        `;
        
        document.head.appendChild(voiceAssistantStyle);
    }
    
    updateUI(state) {
        const btn = document.getElementById('voiceAssistantBtn');
        const modal = document.getElementById('voiceAssistantModal');
        const status = modal?.querySelector('.voice-status');
        
        if (!status) return; // Se modal não existir, sair
        
        // Só atualizar UI se modal estiver visível ou for estado de botão
        const isModalVisible = modal.classList.contains('show');
        
        switch(state) {
            case 'listening':
                btn?.classList.add('listening');
                if (isModalVisible) {
                    status.textContent = '🎤 Estou ouvindo... pode falar!';
                }
                break;
                
            case 'processing':
                if (isModalVisible) {
                    status.textContent = '🤔 Processando seu pedido...';
                }
                break;
                
            case 'idle':
                btn?.classList.remove('listening');
                if (isModalVisible) {
                    status.textContent = 'Aguardando você dizer "Carla"...';
                }
                break;
                
            case 'error':
                // Só mostrar erro se modal estiver visível
                if (isModalVisible) {
                    status.textContent = '❌ Ops! Não entendi. Tente novamente';
                }
                break;
        }
    }
    
    start() {
        if (!this.isEnabled || !this.recognition) {
            console.error('❌ Recognition não disponível');
            return;
        }
        
        // Se já estiver ouvindo, não iniciar de novo
        if (this.isListening) {
            console.log('⚠️ Já está ouvindo, ignorando start()');
            return;
        }
        
        console.log('🎬 Iniciando command recognition...');
        
        try {
            setTimeout(() => {
                try {
                    this.recognition.start();
                    console.log('✅ Recognition.start() chamado');
                } catch (startError) {
                    if (startError.message && startError.message.includes('already started')) {
                        console.log('⚠️ Recognition já estava iniciado');
                    } else {
                        console.error('❌ Erro ao iniciar:', startError);
                    }
                }
            }, 100);
        } catch (error) {
            console.error('❌ Erro no start():', error);
        }
    }
    
    stop() {
        console.log('🛑 Parando recognition manualmente');
        this.manualStop = true;
        if (this.recognition && this.isListening) {
            this.recognition.stop();
        }
    }
    
    speak(text) {
        return new Promise((resolve) => {
            // Verificar se está no app Android nativo
            const isNativeAndroidApp = navigator.userAgent.includes('MyDApp');
            
            // Se estiver no app Android nativo, usar TTS nativo
            if (isNativeAndroidApp && window.AndroidBridge && typeof window.AndroidBridge.speak === 'function') {
                try {
                    window.AndroidBridge.speak(text);
                    // Estimar tempo de fala (Android)
                    const estimatedTime = text.length * 50; // ~50ms por caractere
                    setTimeout(() => resolve(), estimatedTime);
                    return;
                } catch (error) {
                    console.error('Erro TTS nativo:', error);
                }
            }
            
            // Fallback para Web Speech API
            if (!this.synthesis) {
                resolve();
                return;
            }
            
            this.synthesis.cancel();
            
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = this.voiceConfig.lang;
            utterance.rate = this.voiceConfig.rate;
            utterance.pitch = this.voiceConfig.pitch;
            utterance.volume = this.voiceConfig.volume;
            
            const voices = this.synthesis.getVoices();
            const ptBrVoice = voices.find(voice => voice.lang === 'pt-BR' || voice.lang.startsWith('pt'));
            if (ptBrVoice) {
                utterance.voice = ptBrVoice;
            }
            
            // Resolver promise quando a fala terminar
            utterance.onend = () => {
                console.log('🔊 Fala concluída!');
                resolve();
            };
            
            utterance.onerror = (error) => {
                console.error('Erro na síntese de voz:', error);
                resolve(); // Resolver mesmo com erro
            };
            
            // Timeout de segurança (caso onend não seja chamado)
            const estimatedTime = text.length * 60; // ~60ms por caractere
            setTimeout(() => {
                console.log('⏰ Timeout de segurança da fala atingido');
                resolve();
            }, estimatedTime + 1000);
            
            console.log('🔊 Iniciando síntese:', text);
            this.synthesis.speak(utterance);
        });
    }
    
    async processCommand(transcript) {
        console.log('🔄 processCommand chamado com:', transcript);
        
        // Validar se tem conteúdo
        if (!transcript || transcript.trim().length === 0) {
            console.log('⚠️ Transcript vazio, voltando ao hotword');
            
            // Voltar ao hotword silenciosamente
            if (this.hotwordDetected) {
                this.hotwordDetected = false;
                setTimeout(() => {
                    this.isHotwordListening = false;
                    this.startHotwordListening();
                }, 1000);
            }
            return;
        }
        
        // Salvar original
        const originalTranscript = transcript;
        
        // Filtrar "Carla" do início (caso tenha sido capturado junto com comando)
        transcript = transcript.replace(/^\s*(carla|karla)\s*/gi, '').trim();
        
        console.log('📝 Original:', originalTranscript);
        console.log('🧹 Limpo:', transcript);
        
        // Se ficou vazio após limpar, significa que só disse "Carla"
        // Enviar "olá" para a API (que TEM contexto de saudação!)
        if (transcript.length === 0) {
            console.log('⚠️ Apenas "Carla" detectado, enviando saudação para API');
            transcript = 'olá';
        }
        
        console.log('📦 Processando comando válido:', transcript);
        
        const modal = document.getElementById('voiceAssistantModal');
        const transcriptEl = modal?.querySelector('.voice-transcript');
        if (transcriptEl) {
            transcriptEl.textContent = `"${transcript}"`;
        }
        
        try {
            // Processa com a API de IA
            console.log('📡 Enviando para API:', transcript);
            const result = await this.processWithAI(transcript);
            
            console.log('📦 Resultado completo:', result);
            console.log('🛍️ Produtos:', result.products);
            console.log('🧭 Navigate_to recebido:', result.navigate_to);
            console.log('📋 Keys do result:', Object.keys(result));
            
            // Armazenar a mensagem original para extrair parâmetros
            result.originalMessage = transcript;
            
            // Variável para controlar navegação após fala
            let shouldNavigate = null;
            if (result.navigate_to) {
                shouldNavigate = result.navigate_to;
                console.log('✅ Navegação agendada para:', shouldNavigate);
            } else {
                console.log('⚠️ Nenhuma navegação detectada (navigate_to não existe ou é null/undefined)');
            }
            
            this.speak(result.response);
            
            // Se houver navegação, executar após a fala
            if (shouldNavigate) {
                setTimeout(() => {
                    this.navigateToScreen(shouldNavigate);
                }, 2000); // Dar tempo para a fala terminar
            }
            
            // Mostrar produtos se houver
            console.log('📦 Verificando produtos:', result.products);
            console.log('📦 Tipo:', typeof result.products);
            console.log('📦 É array?', Array.isArray(result.products));
            console.log('📦 Quantidade:', result.products ? result.products.length : 0);
            
            if (result.products && result.products.length > 0) {
                console.log('🎨 Exibindo produtos no modal...');
                this.displayProducts(result.products, modal);
            } else {
                console.log('⚠️ Nenhum produto para exibir');
                // Limpar container de produtos se existir
                const productsContainer = modal?.querySelector('.voice-products');
                if (productsContainer) {
                    productsContainer.innerHTML = '';
                }
            }
            
            // Mostrar carrinho se houver itens
            if (result.cart && result.cart.length > 0) {
                console.log('🛒 Carrinho da IA:', result.cart);
                // Apenas exibir no modal - não sincronizar
                this.displayCart(result.cart, modal);
            }
            
            // Mostrar mensagem de pedido criado
            if (result.pedido_id) {
                console.log('✅ Pedido criado:', result.pedido_id);
                this.showOrderConfirmation(result.pedido_id, modal);
            }
            
            // Adicionar produto automaticamente ao carrinho do app
            if (result.add_to_cart_product) {
                console.log('🛒 Adicionando produto automaticamente:', result.add_to_cart_product);
                this.addToAppCart(result.add_to_cart_product.id);
            }
            
            // Mostrar endereço se houver
            if (result.address_data) {
                console.log('📍 Dados de endereço:', result.address_data);
                this.displayAddress(result.address_data, modal);
            }
            
            // Mostrar formas de pagamento se houver
            if (result.payment_methods) {
                console.log('💳 Formas de pagamento:', result.payment_methods);
                this.displayPaymentMethods(result.payment_methods, modal);
            }
            
            // Confirmar seleção de pagamento
            if (result.payment_selected) {
                console.log('✅ Pagamento selecionado:', result.payment_selected);
                // Salvar no localStorage para uso no checkout
                localStorage.setItem('selected_payment_method', result.payment_selected.method);
                
                // Se precisar de troco, salvar flag
                if (result.payment_selected.needs_change) {
                    localStorage.setItem('needs_change', 'true');
                } else {
                    localStorage.removeItem('needs_change');
                }
            }
            
            // Confirmar valor do troco
            if (result.change_amount) {
                console.log('✅ Valor do troco:', result.change_amount);
                localStorage.setItem('change_for', result.change_amount);
            }
            
            // Navegação já foi agendada acima (após a fala)
            
            if (result.action) {
                setTimeout(() => {
                    this.executeAction(result);
                }, 1000);
            }
            
        } catch (error) {
            console.error('❌ Erro ao processar:', error);
            this.speak('Desculpe, tive um problema. Pode tentar novamente?');
        }
        
        // Sempre reiniciar hotword após processar (modo sempre ativo)
        if (this.hotwordDetected) {
            this.hotwordDetected = false;
            setTimeout(() => {
                console.log('🔄 Reiniciando modo hotword após comando...');
                this.isHotwordListening = false; // Resetar flag
                this.startHotwordListening();
            }, 2000); // Aguardar 2 segundos após terminar de processar
        }
    }
    
    displayProducts(products, modal) {
        if (!modal) return;
        
        // Guardar produtos para referência futura
        this.lastProducts = products;
        
        let productsContainer = modal.querySelector('.voice-products');
        if (!productsContainer) {
            productsContainer = document.createElement('div');
            productsContainer.className = 'voice-products';
            productsContainer.style.cssText = 'max-height: 300px; overflow-y: auto; margin: 15px 0;';
            const statusEl = modal.querySelector('.voice-status');
            if (statusEl && statusEl.parentNode) {
                statusEl.parentNode.insertBefore(productsContainer, statusEl.nextSibling);
            }
        }
        
        productsContainer.innerHTML = products.slice(0, 3).map(p => `
            <div class="product-card-mini" style="background: white; padding: 15px; margin: 10px 0; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                    ${p.imagem ? `<img src="${p.imagem}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" alt="${p.nome}">` : '<div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-utensils" style="color: #999;"></i></div>'}
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 15px; color: #333;">${p.nome}</div>
                        <div style="color: #667eea; font-weight: 700; font-size: 18px;">R$ ${parseFloat(p.preco).toFixed(2)}</div>
                    </div>
                </div>
                ${p.descricao ? `<div style="font-size: 13px; color: #666; margin-bottom: 10px;">${p.descricao.substring(0, 80)}${p.descricao.length > 80 ? '...' : ''}</div>` : ''}
                <div style="display: flex; gap: 8px;">
                    <button onclick="window.voiceAssistantInstance.addToAppCart(${p.id})" class="btn btn-primary btn-sm" style="flex: 1; border-radius: 8px; font-size: 13px;">
                        <i class="fas fa-cart-plus"></i> Adicionar ao Carrinho
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    /**
     * Adiciona produto ao carrinho do app
     */
    addToAppCart(productId) {
        console.log('🛒 Adicionando produto ao carrinho do app:', productId);
        
        // Buscar produto nos lastProducts
        const product = this.lastProducts.find(p => p.id === productId);
        
        if (!product) {
            console.error('Produto não encontrado');
            this.speak('Desculpe, não encontrei esse produto.');
            return;
        }
        
        // Adicionar ao carrinho do app usando função global
        if (typeof addToCart === 'function') {
            addToCart(productId);
            this.speak(`${product.nome} adicionado ao carrinho!`);
            
            // Fechar modal após adicionar
            setTimeout(() => {
                const modal = document.getElementById('voiceAssistantModal');
                if (modal) {
                    const bootstrapModal = bootstrap.Modal.getInstance(modal);
                    if (bootstrapModal) {
                        bootstrapModal.hide();
                    }
                }
            }, 1000);
        } else {
            console.error('Função addToCart não encontrada');
            this.speak('Desculpe, não consegui adicionar ao carrinho.');
        }
    }
    
    addProductToCart(productId) {
        // Usar método unificado
        this.addToAppCart(productId);
    }
    
    navigateToScreen(screen) {
        console.log('🧭 Navegando para tela:', screen);
        
        // Fechar modal imediatamente
        const modal = document.getElementById('voiceAssistantModal');
        if (modal) {
            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            if (bootstrapModal) {
                bootstrapModal.hide();
            }
            // Remover backdrop manualmente
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
        
        // Navegar para a tela apropriada com delay maior
        setTimeout(() => {
            console.log('🚀 Executando navegação para:', screen);
            
            if (screen === 'cart') {
                // Abrir carrinho
                if (typeof showCart === 'function') {
                    console.log('✅ Chamando showCart()');
                    showCart();
                } else {
                    console.error('❌ Função showCart não encontrada');
                    alert('Erro: função showCart não encontrada');
                }
            } else if (screen === 'checkout') {
                // Validar se há items no carrinho
                if (typeof appState !== 'undefined' && (!appState.cart || appState.cart.length === 0)) {
                    console.warn('⚠️ Carrinho vazio - não pode ir para checkout');
                    this.speak('Seu carrinho está vazio! Adicione produtos antes de finalizar o pedido.');
                    return;
                }
                
                // Abrir checkout
                if (typeof proceedToCheckout === 'function') {
                    console.log('✅ Chamando proceedToCheckout()');
                    proceedToCheckout();
                    // Aplicar forma de pagamento selecionada após delay
                    this.applySelectedPaymentMethod();
                } else if (typeof showCart === 'function') {
                    console.log('⚠️ proceedToCheckout não encontrado, abrindo carrinho');
                    // Fallback: abrir carrinho
                    showCart();
                } else {
                    console.error('❌ Funções de navegação não encontradas');
                    alert('Erro: funções de navegação não encontradas');
                }
            } else if (screen === 'address_form') {
                // Abrir perfil para editar endereço
                if (typeof showProfile === 'function') {
                    console.log('✅ Chamando showProfile() para editar endereço');
                    showProfile();
                } else {
                    console.error('❌ Função showProfile não encontrada');
                    alert('Erro: função showProfile não encontrada');
                }
            } else if (screen === 'confirm_order') {
                // Confirmar pedido
                if (typeof confirmOrder === 'function') {
                    console.log('✅ Chamando confirmOrder() para finalizar pedido');
                    confirmOrder();
                } else {
                    console.error('❌ Função confirmOrder não encontrada');
                    alert('Erro: função confirmOrder não encontrada');
                }
            }
        }, 800);
    }
    
    applySelectedPaymentMethod() {
        // Aplicar forma de pagamento selecionada no dropdown do checkout
        const selectedMethod = localStorage.getItem('selected_payment_method');
        const needsChange = localStorage.getItem('needs_change');
        const changeFor = localStorage.getItem('change_for');
        
        console.log('💳 Aplicando pagamento:', { selectedMethod, needsChange, changeFor });
        
        if (selectedMethod) {
            // Tentar múltiplas vezes com delays crescentes
            const attempts = [1500, 2500, 3500];
            
            attempts.forEach(delay => {
                setTimeout(() => {
                    const paymentSelect = document.getElementById('paymentMethod');
                    if (paymentSelect && paymentSelect.value !== selectedMethod) {
                        paymentSelect.value = selectedMethod;
                        console.log('✅ Forma de pagamento aplicada:', selectedMethod, 'após', delay, 'ms');
                        
                        // Disparar evento change para atualizar UI
                        const event = new Event('change', { bubbles: true });
                        paymentSelect.dispatchEvent(event);
                        
                        // Se for dinheiro e precisa de troco, preencher campos
                        if (selectedMethod === 'dinheiro' && changeFor) {
                            setTimeout(() => {
                                const needChangeCheckbox = document.getElementById('needChange');
                                const changeForInput = document.getElementById('changeFor');
                                
                                if (needChangeCheckbox) {
                                    needChangeCheckbox.checked = true;
                                    console.log('✅ Checkbox de troco marcado');
                                    
                                    // Disparar evento para mostrar campo de troco
                                    const evt = new Event('change', { bubbles: true });
                                    needChangeCheckbox.dispatchEvent(evt);
                                }
                                
                                if (changeForInput) {
                                    changeForInput.value = changeFor;
                                    console.log('✅ Valor do troco preenchido:', changeFor);
                                }
                            }, 300);
                        }
                    } else if (!paymentSelect) {
                        console.warn('⚠️ Campo paymentMethod não encontrado após', delay, 'ms');
                    }
                }, delay);
            });
        }
    }
    
    clearSelection() {
        console.log('🗑️ Limpando seleção');
        
        const modal = document.getElementById('voiceAssistantModal');
        const productsContainer = modal?.querySelector('.voice-products');
        
        if (productsContainer) {
            productsContainer.innerHTML = '';
        }
        
        this.speak('Seleção cancelada. O que mais posso fazer por você?');
    }
    
    /**
     * Exibe o carrinho no modal
     */
    displayCart(cart, modal) {
        if (!modal || !cart || cart.length === 0) return;
        
        let cartContainer = modal.querySelector('.voice-cart');
        if (!cartContainer) {
            cartContainer = document.createElement('div');
            cartContainer.className = 'voice-cart';
            cartContainer.style.cssText = 'max-height: 300px; overflow-y: auto; margin: 15px 0;';
            const statusEl = modal.querySelector('.voice-status');
            if (statusEl && statusEl.parentNode) {
                statusEl.parentNode.insertBefore(cartContainer, statusEl.nextSibling);
            }
        }
        
        const total = cart.reduce((sum, item) => sum + (item.preco * item.quantity), 0);
        
        cartContainer.innerHTML = `
            <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-weight: 600; font-size: 16px; color: #333; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-shopping-cart" style="color: #667eea;"></i>
                    Seu Carrinho
                </div>
                ${cart.map(item => `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                        <div>
                            <div style="font-weight: 500; font-size: 14px;">${item.quantity}x ${item.nome}</div>
                            <div style="font-size: 12px; color: #666;">R$ ${parseFloat(item.preco).toFixed(2)} cada</div>
                        </div>
                        <div style="font-weight: 600; color: #667eea;">R$ ${(item.preco * item.quantity).toFixed(2)}</div>
                    </div>
                `).join('')}
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; margin-top: 10px; border-top: 2px solid #667eea;">
                    <div style="font-weight: 700; font-size: 16px;">Total:</div>
                    <div style="font-weight: 700; font-size: 18px; color: #667eea;">R$ ${total.toFixed(2)}</div>
                </div>
            </div>
        `;
    }
    
    /**
     * Exibe confirmação de pedido criado
     */
    showOrderConfirmation(pedidoId, modal) {
        if (!modal) return;
        
        let confirmContainer = modal.querySelector('.voice-order-confirm');
        if (!confirmContainer) {
            confirmContainer = document.createElement('div');
            confirmContainer.className = 'voice-order-confirm';
            confirmContainer.style.cssText = 'margin: 15px 0;';
            const statusEl = modal.querySelector('.voice-status');
            if (statusEl && statusEl.parentNode) {
                statusEl.parentNode.insertBefore(confirmContainer, statusEl.nextSibling);
            }
        }
        
        confirmContainer.innerHTML = `
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 12px; color: white; text-align: center;">
                <div style="font-size: 48px; margin-bottom: 10px;">🎉</div>
                <div style="font-weight: 700; font-size: 18px; margin-bottom: 10px;">Pedido Finalizado!</div>
                <div style="font-size: 14px; opacity: 0.9;">Número do Pedido: #${pedidoId}</div>
            </div>
        `;
    }
    
    /**
     * Exibe endereço do usuário
     */
    displayAddress(addressData, modal) {
        if (!modal || !addressData) return;
        
        let addressContainer = modal.querySelector('.voice-address');
        if (!addressContainer) {
            addressContainer = document.createElement('div');
            addressContainer.className = 'voice-address';
            addressContainer.style.cssText = 'margin: 15px 0;';
            const statusEl = modal.querySelector('.voice-status');
            if (statusEl && statusEl.parentNode) {
                statusEl.parentNode.insertBefore(addressContainer, statusEl.nextSibling);
            }
        }
        
        const enderecoCompleto = `${addressData.endereco}, ${addressData.numero || 'S/N'}${addressData.complemento ? ' - ' + addressData.complemento : ''} - ${addressData.bairro}, ${addressData.cidade}/${addressData.estado}${addressData.cep ? ' - CEP: ' + addressData.cep : ''}`;
        
        addressContainer.innerHTML = `
            <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-weight: 600; font-size: 16px; color: #333; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: #667eea;"></i>
                    Endereço de Entrega
                </div>
                <div style="font-size: 14px; color: #666; line-height: 1.6;">
                    ${enderecoCompleto}
                </div>
            </div>
        `;
    }
    
    /**
     * Exibe formas de pagamento
     */
    displayPaymentMethods(paymentMethods, modal) {
        if (!modal || !paymentMethods) return;
        
        let paymentContainer = modal.querySelector('.voice-payment-methods');
        if (!paymentContainer) {
            paymentContainer = document.createElement('div');
            paymentContainer.className = 'voice-payment-methods';
            paymentContainer.style.cssText = 'margin: 15px 0;';
            const statusEl = modal.querySelector('.voice-status');
            if (statusEl && statusEl.parentNode) {
                statusEl.parentNode.insertBefore(paymentContainer, statusEl.nextSibling);
            }
        }
        
        const icons = {
            'money': '💵',
            'card': '💳',
            'pix': '📱'
        };
        
        const methodsHTML = Object.entries(paymentMethods).map(([key, name]) => `
            <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 24px;">${icons[key] || '💰'}</span>
                <span style="font-weight: 500; font-size: 14px;">${name}</span>
            </div>
        `).join('');
        
        paymentContainer.innerHTML = `
            <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-weight: 600; font-size: 16px; color: #333; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-credit-card" style="color: #667eea;"></i>
                    Formas de Pagamento
                </div>
                ${methodsHTML}
            </div>
        `;
    }
    
    /**
     * Exibe confirmação de pedido criado (MANTIDO PARA COMPATIBILIDADE)
     */
    showOrderConfirmationOld(pedidoId, modal) {
        if (!modal) return;
        
        let confirmContainer = modal.querySelector('.voice-order-confirm');
        if (!confirmContainer) {
            confirmContainer = document.createElement('div');
            confirmContainer.className = 'voice-order-confirm';
            confirmContainer.style.cssText = 'margin: 15px 0;';
            const statusEl = modal.querySelector('.voice-status');
            if (statusEl && statusEl.parentNode) {
                statusEl.parentNode.insertBefore(confirmContainer, statusEl.nextSibling);
            }
        }
        
        confirmContainer.innerHTML = `
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 12px; color: white; text-align: center;">
                <div style="font-size: 48px; margin-bottom: 10px;">🎉</div>
                <div style="font-weight: 700; font-size: 18px; margin-bottom: 10px;">Pedido Finalizado!</div>
                <div style="font-size: 14px; opacity: 0.9;">Número do Pedido: #${pedidoId}</div>
                <div style="font-size: 13px; opacity: 0.8; margin-top: 10px;">Você receberá a confirmação em breve</div>
            </div>
        `;
        
        // Limpar carrinho do display
        const cartContainer = modal.querySelector('.voice-cart');
        if (cartContainer) {
            cartContainer.innerHTML = '';
        }
    }
    
    /**
     * Processa mensagem com a API de IA
     */
    async processWithAI(message) {
        console.log('🚀 processWithAI CHAMADO!');
        console.log('📨 Mensagem:', message);
        
        try {
            const userId = localStorage.getItem('user_id');
            const tenantCode = localStorage.getItem('tenant_code') || 'RESTAURANTE0001';
            
            console.log('👤 User ID:', userId);
            console.log('🏢 Tenant:', tenantCode);
            console.log('🔑 Session Token:', this.sessionToken);
            
            const payload = {
                message: message,
                session_token: this.sessionToken,
                user_id: userId,
                empresa_id: tenantCode
            };
            
            console.log('📦 Payload completo:', JSON.stringify(payload, null, 2));
            console.log('🌐 URL:', `${this.apiBaseUrl}/process`);
            
            const response = await fetch(`${this.apiBaseUrl}/process`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Tenant-Code': tenantCode
                },
                body: JSON.stringify(payload)
            });
            
            console.log('📡 Response status:', response.status);
            console.log('📡 Response OK?:', response.ok);
            
            const data = await response.json();
            console.log('📦 Response data:', JSON.stringify(data, null, 2));
            
            if (!response.ok) {
                throw new Error(data.message || 'Erro na API');
            }
            
            // Salva session token
            if (data.data.session_token) {
                this.sessionToken = data.data.session_token;
                localStorage.setItem('ai_session_token', this.sessionToken);
                console.log('💾 Session token salvo:', this.sessionToken);
            }
            
            console.log('✅ IA respondeu:', data.data.response);
            
            return {
                response: data.data.response,
                intent: data.data.intent,
                confidence: data.data.confidence,
                action: data.data.action,
                parameters: data.data.parameters,
                products: data.data.products || [],
                cart: data.data.cart || [],
                pedido_id: data.data.pedido_id,
                add_to_cart_product: data.data.add_to_cart_product,
                navigate_to: data.data.navigate_to,
                address_data: data.data.address_data,
                payment_methods: data.data.payment_methods,
                payment_selected: data.data.payment_selected
            };
            
        } catch (error) {
            console.error('❌ ERRO na API de IA:', error);
            console.error('❌ Stack:', error.stack);
            // Fallback para modo básico
            return this.detectBasicAction(message);
        }
    }
    
    detectBasicAction(transcript) {
        const msg = transcript.toLowerCase();
        
        // Saudações
        if (/^(oi|olá|ola|hey|ei|bom dia|boa tarde|boa noite|tudo bem|como vai)/.test(msg)) {
            const respostas = [
                'Olá! Tudo ótimo! O que você gostaria de pedir hoje?',
                'Oi! Como posso ajudar? Quer ver o cardápio?',
                'E aí! Tô aqui pra te ajudar. O que vai querer?'
            ];
            return { 
                response: respostas[Math.floor(Math.random() * respostas.length)],
                execute: false
            };
        }
        
        // Buscar comida
        const foods = ['pizza', 'hamburguer', 'hambúrguer', 'lanche', 'sanduíche', 'bebida', 'suco', 'refrigerante', 'água', 'cerveja'];
        for (const food of foods) {
            if (msg.includes(food)) {
                return {
                    response: `Perfeito! Vou procurar ${food} para você!`,
                    type: 'searchFood',
                    query: food,
                    execute: true
                };
            }
        }
        
        // Ver cardápio
        if (msg.includes('cardápio') || msg.includes('cardapio') || msg.includes('menu') || msg.includes('produtos')) {
            return {
                response: 'Abrindo o cardápio completo pra você!',
                type: 'showMenu',
                execute: true
            };
        }
        
        // Ver carrinho
        if (msg.includes('carrinho') || msg.includes('sacola')) {
            return {
                response: 'Mostrando seu carrinho!',
                type: 'showCart',
                execute: true
            };
        }
        
        // Ver pedidos
        if (msg.includes('pedidos') || msg.includes('status') || msg.includes('entrega')) {
            return {
                response: 'Consultando seus pedidos!',
                type: 'showOrders',
                execute: true
            };
        }
        
        // Não entendeu
        return {
            response: 'Não entendi muito bem. Você pode dizer: quero pizza, mostra o cardápio, ou meus pedidos.',
            execute: false
        };
    }
    
    detectIntent(command) {
        // Remover pontuação e normalizar
        const cleanCommand = command.replace(/[?!.,;]/g, '').toLowerCase().trim();
        
        // Saudações - responder com conversa
        const greetings = /^(oi|olá|ola|hey|ei|bom dia|boa tarde|boa noite|tudo bem|como vai|e aí)/;
        if (greetings.test(cleanCommand) && cleanCommand.split(' ').length <= 4) {
            return { type: 'greeting', query: cleanCommand };
        }
        
        // Palavras-chave específicas para cada intenção
        const intents = {
            // Busca de comida - precisa ter verbo de ação + comida
            searchFood: {
                verbs: ['quero', 'queria', 'gostaria', 'preciso', 'vou querer', 'me traz', 'pede'],
                items: ['pizza', 'hamburguer', 'lanche', 'sanduíche', 'suco', 'refrigerante', 'bebida', 'água', 'cerveja', 'prato', 'comida', 'almoço', 'jantar', 'café']
            },
            
            // Busca de restaurante
            searchRestaurant: {
                keywords: ['restaurante', 'loja', 'estabelecimento', 'lugar para comer', 'onde comer']
            },
            
            // Ver cardápio
            showMenu: {
                keywords: ['cardápio', 'cardapio', 'menu', 'ver produtos', 'mostrar produtos', 'o que tem', 'tem o que']
            },
            
            // Ver carrinho
            showCart: {
                keywords: ['carrinho', 'minha sacola', 'meu pedido atual', 'o que pedi']
            },
            
            // Ver pedidos anteriores
            showOrders: {
                keywords: ['meus pedidos', 'pedidos anteriores', 'histórico', 'últimos pedidos']
            },
            
            // Ajuda
            help: {
                keywords: ['ajuda', 'ajudar', 'como funciona', 'o que você faz', 'comandos', 'o que posso fazer']
            }
        };
        
        // Detectar saudação seguida de pedido
        if (cleanCommand.match(/^(oi|olá|ola).*(quero|queria|me traz)/)) {
            const foodPart = cleanCommand.replace(/^(oi|olá|ola)[,\s]*/i, '');
            return { type: 'searchFood', query: foodPart };
        }
        
        // Verificar busca de comida (precisa ter verbo + item)
        const hasVerb = intents.searchFood.verbs.some(verb => cleanCommand.includes(verb));
        const hasItem = intents.searchFood.items.some(item => cleanCommand.includes(item));
        if (hasVerb && hasItem) {
            return { type: 'searchFood', query: cleanCommand };
        }
        
        // Verificar outras intenções
        for (const [intentType, config] of Object.entries(intents)) {
            if (intentType === 'searchFood') continue; // Já verificamos acima
            
            if (config.keywords && config.keywords.some(keyword => cleanCommand.includes(keyword))) {
                return { type: intentType, query: cleanCommand };
            }
        }
        
        // Se não detectou nada específico e tem poucas palavras, provavelmente é conversa
        if (cleanCommand.split(' ').length <= 5) {
            return { type: 'smallTalk', query: cleanCommand };
        }
        
        // Padrão: busca geral só se tiver palavras de comida
        const hasFoodWord = intents.searchFood.items.some(item => cleanCommand.includes(item));
        if (hasFoodWord) {
            return { type: 'searchFood', query: cleanCommand };
        }
        
        // Se nada fez sentido, é conversa genérica
        return { type: 'smallTalk', query: cleanCommand };
    }
    
    executeAction(action) {
        if (!action || !action.action) return;
        
        const actionType = action.action;
        const params = action.parameters || {};
        
        console.log('🎬 Executando ação:', actionType, params);
        console.log('📦 Produtos disponíveis:', action.products);
        
        switch(actionType) {
            case 'selectRestaurant':
                console.log('🏪 Selecionando restaurante');
                this.selectRestaurantAction(action);
                break;
                
            case 'showRestaurants':
                console.log('📋 Mostrando lista de restaurantes');
                this.showRestaurantsAction();
                break;
                
            case 'searchProduct':
                // Se há produtos na resposta da API, mostrar diretamente
                if (action.products && action.products.length > 0) {
                    console.log('✨ Mostrando ' + action.products.length + ' produtos da API');
                    this.showProductResults(action.products);
                } else {
                    // Fallback: busca tradicional
                    const query = params.query || params.product || action.intent?.split('_')[1] || '';
                    this.searchFood(query);
                }
                break;
                
            case 'showMenu':
                this.showMenuAction();
                break;
                
            case 'showCategories':
                this.showMenuAction();
                break;
                
            case 'showCart':
                this.showCartAction();
                break;
                
            case 'clearCart':
                this.clearCartAction();
                break;
                
            case 'showOrders':
                this.showOrdersAction();
                break;
                
            case 'showOrderStatus':
                this.showOrdersAction();
                break;
                
            case 'showDeliveryStatus':
                this.showOrdersAction();
                break;
                
            case 'addToCart':
                this.addToCartAction();
                break;
                
            case 'checkout':
                this.checkoutAction();
                break;
                
            case 'showPromotions':
                this.showMenuAction(); // Pode filtrar promoções depois
                break;
                
            case 'showCombos':
                this.showMenuAction(); // Pode filtrar combos depois
                break;
                
            case 'showHighlights':
                this.showMenuAction(); // Pode filtrar destaques depois
                break;
                
            default:
                console.log('⚠️ Ação não mapeada:', actionType);
        }
    }
    
    async searchFood(query) {
        if (typeof showMenu === 'function') {
            showMenu();
            
            setTimeout(() => {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = query;
                    searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }, 500);
        }
    }
    
    showMenuAction() {
        if (typeof showMenu === 'function') {
            showMenu();
        }
    }
    
    showCartAction() {
        if (typeof showCart === 'function') {
            showCart();
        }
    }
    
    showOrdersAction() {
        if (typeof showOrders === 'function') {
            showOrders();
        }
    }
    
    clearCartAction() {
        // Limpar carrinho do appState
        if (typeof appState !== 'undefined') {
            appState.cart = [];
            appState.selectedRestaurant = null;
            localStorage.removeItem('app_selected_restaurant');
        }
        
        // Limpar do localStorage
        localStorage.removeItem('cart');
        
        // Atualizar badge
        if (typeof updateCartBadge === 'function') {
            updateCartBadge();
        }
        
        console.log('✅ Carrinho limpo com sucesso');
        this.speak('Carrinho limpo! Quer pedir algo novo?');
        
        // Atualizar tela se estiver mostrando carrinho
        if (typeof showCart === 'function') {
            setTimeout(() => showCart(), 500);
        }
    }
    
    addToCartAction() {
        console.log('🛒 Action addToCart acionada');
        console.log('Últimos produtos:', this.lastProducts);
        
        // Fechar modal
        const modal = document.getElementById('voiceAssistantModal');
        if (modal) {
            modal.classList.remove('show');
        }
        
        // Se tem produtos, adicionar o primeiro
        if (this.lastProducts && this.lastProducts.length > 0) {
            const product = this.lastProducts[0];
            this.addProductToCart(product.id);
        } else {
            this.speak('Desculpe, você precisa escolher um produto primeiro. Pode dizer: quero bebida');
        }
    }
    
    checkoutAction() {
        console.log('💳 Action checkout acionada');
        
        // Fechar modal
        const modal = document.getElementById('voiceAssistantModal');
        if (modal) {
            modal.classList.remove('show');
        }
        
        if (typeof showCart === 'function') {
            showCart();
            this.speak('Abrindo carrinho para finalizar o pedido!');
            setTimeout(() => {
                const checkoutBtn = document.querySelector('.btn-finalizar-pedido');
                if (checkoutBtn) {
                    checkoutBtn.scrollIntoView({ behavior: 'smooth' });
                }
            }, 500);
        }
    }
    
    searchFood(query) {
        console.log('🔍 Buscando por:', query);
        
        // Navegar para a tela de cardápio com filtro
        if (typeof showMenu === 'function') {
            showMenu();
            
            setTimeout(() => {
                // Aplicar filtro de busca
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = query;
                    searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }, 500);
        }
    }
    
    selectRestaurantAction(action) {
        console.log('🏪 Ação de selecionar restaurante:', action);
        
        // Fechar modal
        const modal = document.getElementById('voiceAssistantModal');
        if (modal) {
            modal.classList.remove('show');
        }
        
        // Extrair nome do restaurante da mensagem original
        const message = (action.originalMessage || '').toLowerCase();
        console.log('📝 Mensagem original:', message);
        
        // Lista de palavras-chave para remover
        const removeWords = ['carla', 'seleciona', 'escolhe', 'abre', 'entra', 'vai', 'para', 'mostra', 'quero', 'ver', 'o', 'a', 'restaurante', 'loja', 'estabelecimento'];
        
        // Extrair nome do restaurante
        let restaurantName = message;
        removeWords.forEach(word => {
            restaurantName = restaurantName.replace(new RegExp('\\b' + word + '\\b', 'g'), '');
        });
        
        // Limpar espaços extras
        restaurantName = restaurantName.trim().replace(/\s+/g, ' ');
        
        console.log('🔍 Nome extraído:', restaurantName);
        
        // Navegar para lista de restaurantes
        if (typeof showMenu === 'function') {
            showMenu(); // Vai mostrar a lista de restaurantes
            
            // Se conseguiu extrair nome, tentar selecionar
            if (restaurantName && restaurantName.length > 2) {
                setTimeout(() => {
                    this.findAndSelectRestaurant(restaurantName);
                }, 800);
            }
        }
    }
    
    showRestaurantsAction() {
        console.log('📋 Mostrando lista de restaurantes');
        
        // Fechar modal
        const modal = document.getElementById('voiceAssistantModal');
        if (modal) {
            modal.classList.remove('show');
        }
        
        // Navegar para lista de restaurantes
        if (typeof showMenu === 'function') {
            showMenu(); // Mostra lista de restaurantes
        }
    }
    
    async findAndSelectRestaurant(restaurantName) {
        console.log('🔍 Procurando restaurante:', restaurantName);
        
        // Carregar restaurantes se não estiver carregado
        if (!window.appState?.restaurants || window.appState.restaurants.length === 0) {
            console.log('📥 Carregando lista de restaurantes...');
            
            try {
                const response = await fetch(`${API_BASE_URL}/app/restaurantes`);
                const data = await response.json();
                window.appState.restaurants = data.restaurantes || [];
            } catch (error) {
                console.error('Erro ao carregar restaurantes:', error);
                return;
            }
        }
        
        // Buscar restaurante por nome (case insensitive)
        const searchTerm = restaurantName.toLowerCase();
        const restaurant = window.appState.restaurants.find(r => 
            r.nome_fantasia?.toLowerCase().includes(searchTerm) ||
            r.razao_social?.toLowerCase().includes(searchTerm)
        );
        
        if (restaurant && typeof selectRestaurantForMenu === 'function') {
            console.log('✅ Restaurante encontrado:', restaurant.nome_fantasia);
            await selectRestaurantForMenu(restaurant.tenant_code);
            this.speak(`Abrindo ${restaurant.nome_fantasia}!`);
        } else {
            console.log('❌ Restaurante não encontrado:', restaurantName);
            this.speak('Desculpe, não encontrei esse restaurante. Quer ver a lista?');
        }
    }
    
    showProductResults(products) {
        console.log('📋 Mostrando resultados:', products);
        
        // Se há produtos, usar o tenant_code do primeiro para selecionar restaurante
        if (products.length > 0 && products[0].tenant_code) {
            const tenantCode = products[0].tenant_code;
            console.log('🏪 Selecionando restaurante:', tenantCode);
            
            // Selecionar restaurante e depois mostrar menu filtrado
            if (typeof selectRestaurantForMenu === 'function') {
                selectRestaurantForMenu(tenantCode).then(() => {
                    console.log('✅ Restaurante selecionado, mostrando produtos');
                    
                    // Aguardar menu carregar
                    setTimeout(() => {
                        // Filtrar por nome do primeiro produto
                        const searchInput = document.getElementById('searchInput');
                        if (searchInput && products[0]) {
                            searchInput.value = products[0].nome;
                            searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }, 500);
                });
            } else {
                // Fallback: navegar para menu e filtrar
                if (typeof showMenu === 'function') {
                    showMenu();
                    
                    setTimeout(() => {
                        const searchInput = document.getElementById('searchInput');
                        if (searchInput && products[0]) {
                            searchInput.value = products[0].nome;
                            searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }, 500);
                }
            }
        } else {
            // Fallback: busca genérica
            if (typeof showMenu === 'function') {
                showMenu();
            }
        }
    }
    
    showCart() {
        this.speak('Abrindo seu carrinho');
        if (typeof showCart === 'function') {
            showCart();
        }
        this.updateUI('idle');
    }
    
    showOrders() {
        this.speak('Mostrando seus pedidos');
        if (typeof showOrders === 'function') {
            showOrders();
        }
        this.updateUI('idle');
    }
    
    showHelp() {
        const helpText = 'Eu posso te ajudar a procurar comida, mostrar restaurantes, ver o cardápio e muito mais. Basta falar o que você quer!';
        this.speak(helpText);
        this.updateUI('idle');
    }
    
    extractKeywords(text) {
        // Lista de palavras comuns para remover
        const stopWords = ['quero', 'queria', 'gostaria', 'de', 'um', 'uma', 'o', 'a', 'comer', 'ver', 'mostrar', 'buscar', 'procurar'];
        
        // Extrair palavras relevantes
        const words = text.toLowerCase()
            .split(' ')
            .filter(word => word.length > 2 && !stopWords.includes(word));
        
        return words.length > 0 ? words : ['comida'];
    }
}

// Inicializar e expor globalmente
if (typeof window !== 'undefined') {
    // Aguardar DOM carregar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.voiceAssistantInstance = new VoiceAssistant();
        });
    } else {
        window.voiceAssistantInstance = new VoiceAssistant();
    }
}
