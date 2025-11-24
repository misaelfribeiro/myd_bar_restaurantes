# 🎤 Hotword "Carla" - Ativação por Voz

## ✨ Implementado

Sistema de ativação por voz que permite ao usuário chamar a assistente dizendo "Carla".

## 🚀 Funcionalidades

### 1. **Escuta Contínua em Background**
- Reconhecimento de fala separado rodando continuamente
- Detecta a palavra "Carla" ou variações ("Karla")
- Baixo consumo de recursos

### 2. **Ativação Automática**
- Quando detecta "Carla", automaticamente:
  1. Para o modo hotword
  2. Responde: "Olá! O que posso fazer por você hoje?"
  3. Inicia gravação do comando do usuário (2 segundos após falar)

### 3. **Reinício Automático**
- Após processar o comando, volta a escutar por "Carla"
- Ciclo contínuo de: hotword → comando → processamento → hotword

### 4. **Toggle On/Off**
- Switch no modal da Carla: "Modo Ei Carla"
- Estado salvo no localStorage
- Ativa/desativa a escuta contínua

### 5. **Feedback Visual**
- Botão muda de cor quando em modo hotword:
  - **Normal**: Gradiente roxo (#667eea → #764ba2)
  - **Hotword**: Gradiente verde (#11998e → #38ef7d)
  - **Escutando**: Gradiente rosa com pulse
- Emoji 👂 aparece no botão quando em modo hotword
- Animações suaves de pulse e wave

## 📁 Arquivos Modificados

### `public/app-cliente/js/voice-assistant.js`

**Novas Propriedades:**
```javascript
this.hotwordRecognition = null;
this.isHotwordListening = false;
this.hotwordDetected = false;
```

**Novos Métodos:**
- `initHotwordRecognition()` - Inicializa reconhecimento separado para hotword
- `startHotwordListening()` - Inicia escuta contínua por "Carla"
- `stopHotwordListening()` - Para escuta de hotword
- `onHotwordDetected()` - Callback quando "Carla" é detectada

**Modificações:**
- `constructor()` - Adiciona propriedades de hotword
- `initRecognition()` - Chama `initHotwordRecognition()`
- `createUI()` - Adiciona toggle no modal
- `initModalListeners()` - Listener para o toggle
- `processCommand()` - Reinicia hotword após processar
- `addStyles()` - Estilos para modo hotword

### `public/app-cliente/teste-hotword.html`
Página de teste com instruções e monitoramento visual.

## 🎨 CSS Adicionado

```css
/* Botão em modo hotword */
.voice-assistant-btn.hotword-mode {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    animation: pulse-slow 3s infinite;
}

/* Emoji indicador */
.voice-assistant-btn.hotword-mode::after {
    content: '👂';
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 16px;
    animation: wave 2s infinite;
}

/* Animações */
@keyframes pulse-slow {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.03); }
}

@keyframes wave {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(15deg); }
    75% { transform: rotate(-15deg); }
}
```

## 🧪 Como Testar

1. Acesse: `http://localhost/app-cliente/teste-hotword.html`
2. Clique no botão flutuante roxo (canto inferior direito)
3. Ative o switch "Modo Ei Carla"
4. Observe o botão mudar para verde com 👂
5. Diga "Carla" em voz alta
6. Aguarde resposta automática
7. Diga seu comando (ex: "mostra os restaurantes")
8. Sistema volta a escutar "Carla" automaticamente

## 🔧 Configurações

### Sensibilidade
- Usa `interimResults: true` para captura mais responsiva
- Threshold padrão do Web Speech API

### Reinício Automático
- Delay de 2 segundos após processar comando
- Verifica se toggle ainda está ativado

### Persistência
- Estado salvo em `localStorage.hotwordMode`
- Restaurado automaticamente ao carregar página

## ⚙️ Requisitos Técnicos

- **Navegador**: Chrome/Edge (Web Speech API)
- **Permissão**: Acesso ao microfone
- **Conexão**: Internet (para recognition)

## 🐛 Tratamento de Erros

- `no-speech`: Reinicia automaticamente (silencioso)
- `aborted`: Não reinicia (parada intencional)
- Outros erros: Tenta reiniciar após 1 segundo

## 📱 Compatibilidade

✅ **Desktop**: Chrome, Edge
✅ **Android**: Chrome
❌ **iOS/Safari**: Não suportado (Web Speech API limitada)

## 🎯 Fluxo Completo

```
1. Usuário ativa toggle "Modo Ei Carla"
   ↓
2. Sistema inicia hotwordRecognition contínuo
   ↓
3. Botão fica verde com 👂 (modo hotword)
   ↓
4. Usuário diz "Carla"
   ↓
5. Sistema detecta hotword
   ↓
6. Para hotwordRecognition
   ↓
7. Fala: "Olá! O que posso fazer por você hoje?"
   ↓
8. Após 2s, inicia recognition normal
   ↓
9. Usuário diz comando
   ↓
10. Processa comando com IA
    ↓
11. Executa ações necessárias
    ↓
12. Volta para passo 2 (reinicia hotword)
```

## 🚨 Notas Importantes

1. **Performance**: Hotword listening usa recursos do navegador continuamente
2. **Privacidade**: Áudio não é gravado, apenas processado localmente
3. **Bateria**: Pode impactar bateria em dispositivos móveis
4. **Falsos Positivos**: Palavras similares podem ativar ("Clara", "Carla")

## ✅ Status

✨ **IMPLEMENTADO E FUNCIONANDO**

- [x] Reconhecimento de hotword "Carla"
- [x] Ativação automática com saudação
- [x] Gravação automática do comando
- [x] Reinício automático do hotword
- [x] Toggle on/off com persistência
- [x] Feedback visual (cores e animações)
- [x] Página de teste funcional
