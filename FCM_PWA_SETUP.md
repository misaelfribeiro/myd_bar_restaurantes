# 🔔 Firebase Cloud Messaging (FCM) - App Cliente PWA

## ✅ Status da Implementação

### Backend (Laravel)
- ✅ Migration `fcm_tokens` table criada
- ✅ Model `FcmToken` configurado
- ✅ Controller `NotificacaoController` com API V1 Firebase
- ✅ Rota `/api/app/auth/fcm-token` para salvar tokens
- ✅ Método `saveFcmToken()` no `ClienteAuthController`

### Frontend (PWA)
- ✅ Firebase SDK 10.7.1 integrado
- ✅ `firebase-messaging-sw.js` criado (Service Worker para notificações)
- ✅ `notifications.js` - Classe NotificationManager
- ✅ Inicialização automática após login
- ✅ Permissão de notificação solicitada
- ✅ Token FCM salvo no backend

## 🔧 Configuração Necessária

### 1. Gerar VAPID Key no Firebase Console

1. Acesse: https://console.firebase.google.com/
2. Selecione o projeto: **speedfood-b4495**
3. Vá em: **Configurações do Projeto** (ícone de engrenagem)
4. Aba: **Cloud Messaging**
5. Role até: **Configuração da Web**
6. Clique em: **Gerar par de chaves**
7. Copie a **Chave pública da Web push (VAPID)**

### 2. Atualizar o Código

Edite o arquivo: `public/app-cliente/js/notifications.js`

Linha 105, substitua a VAPID key:

```javascript
const currentToken = await this.messaging.getToken({
    vapidKey: 'SUA_VAPID_KEY_AQUI' // <-- Cole a chave aqui
});
```

### 3. Testar no Navegador

1. Abra o app: http://localhost/app-cliente
2. Faça login
3. Permita notificações quando solicitado
4. Verifique no console do navegador:
   - "✅ Firebase Cloud Messaging initialized"
   - "Notification permission granted"
   - "FCM Token: ..." (um token longo)
   - "✅ FCM token saved to backend"

## 📱 Como Funciona

### Fluxo de Registro:

1. **Usuário faz login** → App inicializa FCM
2. **Solicita permissão** → Notification.requestPermission()
3. **Obtém token FCM** → messaging.getToken()
4. **Salva no backend** → POST /api/app/auth/fcm-token
5. **Token armazenado** → Tabela `fcm_tokens` com user_id

### Fluxo de Notificação:

**Quando pedido muda de status:**

1. Backend chama `NotificacaoController`
2. Busca token FCM do cliente na tabela
3. Envia notificação via Firebase API V1
4. Firebase entrega ao navegador/app
5. Service Worker exibe notificação

**Foreground (app aberto):**
- Notificação aparece como toast no canto superior direito
- Som de notificação toca
- Clique navega para tela de pedidos

**Background (app fechado):**
- Service Worker captura mensagem
- Exibe notificação do sistema
- Clique abre o app na tela de pedidos

## 🧪 Testando Notificações

### Via cURL (manual):

```bash
# Obter token FCM do cliente (checar tabela fcm_tokens)
# Substitua SEU_TOKEN_FCM pelo token real

curl -X POST http://localhost/api/notificacao/testar \
  -H "Content-Type: application/json" \
  -d '{"token":"SEU_TOKEN_FCM"}'
```

### Automaticamente:

As notificações são enviadas automaticamente quando:
- Pedido é confirmado
- Pedido está sendo preparado
- Pedido está pronto
- Entregador aceita delivery
- Pedido está em rota de entrega
- Pedido foi entregue

## 🔍 Debugging

### Ver tokens no banco:

```sql
SELECT * FROM fcm_tokens WHERE user_id = 1;
```

### Ver logs do Laravel:

```bash
tail -f storage/logs/laravel.log
```

### Console do navegador:

Pressione F12 e veja a aba Console para mensagens como:
- FCM Token: ey...
- Background message received
- Notification shown

## 📋 Checklist de Implementação

- [x] Backend: Migration fcm_tokens
- [x] Backend: Método saveFcmToken
- [x] Backend: Rota /api/app/auth/fcm-token
- [x] Frontend: Firebase SDK integrado
- [x] Frontend: Service Worker criado
- [x] Frontend: Classe NotificationManager
- [x] Frontend: Inicialização no app.js
- [ ] **PENDENTE:** Adicionar VAPID key real
- [ ] **PENDENTE:** Testar notificações reais

## 🚀 Próximos Passos

1. Obter VAPID key do Firebase Console
2. Atualizar notifications.js com a chave
3. Testar permissão de notificação
4. Verificar token salvo no banco
5. Enviar notificação de teste via cURL
6. Confirmar recebimento no navegador

## 📱 Suporte de Navegadores

- ✅ Chrome/Edge (Desktop e Mobile)
- ✅ Firefox (Desktop e Mobile)
- ⚠️ Safari (iOS 16.4+, com limitações)
- ❌ Internet Explorer (não suportado)

## 🔒 Segurança

- Tokens são únicos por usuário + dispositivo
- Armazenados com device_id para identificação
- Podem ser revogados individualmente
- Expiram automaticamente se não forem usados

## 📖 Documentação Firebase

- Firebase Console: https://console.firebase.google.com/
- FCM Documentation: https://firebase.google.com/docs/cloud-messaging/js/client
- VAPID Key Setup: https://firebase.google.com/docs/cloud-messaging/js/client#configure_web_credentials_with_fcm
