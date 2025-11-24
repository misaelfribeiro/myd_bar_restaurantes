# 🔔 Firebase Push Notifications - Guia de Deploy

## ✅ Status Atual

**Backend (Laravel):** ✅ Completo
- API V1 Firebase configurada
- Service Account JWT authentication
- 6 tipos de notificações implementadas
- Todas as rotas testadas

**Android App:** 🚀 Pronto para compilar
- Firebase Cloud Messaging integrado
- Token FCM auto-registrado ao iniciar
- Notificações com vibração ativa
- WebView com comunicação com servidor

## 📋 Próximos Passos

### 1️⃣ Compilar APK com Android Studio

```bash
# Abrir projeto no Android Studio
"C:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp"

# Depois: Build → Build APK(s)
# Output: app/build/outputs/apk/debug/app-debug.apk
```

### 2️⃣ Instalar no Tablet (via ADB)

```bash
cd "C:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp"

# Instalar APK
adb install -r "app\build\outputs\apk\debug\app-debug.apk"

# Verificar logs
adb logcat | findstr FCM
```

### 3️⃣ Testar Notificações

Após instalar o app no tablet:

```bash
# 1. Abrir app para que registre o token
# 2. Verificar token nos logs do Android Studio
# 3. Copiar token FCM

# 4. Testar API com o token real
curl -X POST http://192.168.15.9/api/notificacao/testar \
  -H "Content-Type: application/json" \
  -d '{"token":"SEU_TOKEN_FCM_AQUI"}'

# 5. Verificar se notificação chegou no tablet
```

### 4️⃣ Testar Todos os Tipos

```bash
# Pedido Pronto
curl -X POST http://192.168.15.9/api/notificacao/pedido-pronto \
  -H "Content-Type: application/json" \
  -d '{"token":"SEU_TOKEN_FCM","pedido_id":"123"}'

# Delivery Aceito
curl -X POST http://192.168.15.9/api/notificacao/delivery-aceito \
  -H "Content-Type: application/json" \
  -d '{"token":"SEU_TOKEN_FCM","pedido_id":"123"}'

# Delivery Entregue
curl -X POST http://192.168.15.9/api/notificacao/delivery-entregue \
  -H "Content-Type: application/json" \
  -d '{"token":"SEU_TOKEN_FCM","pedido_id":"123"}'
```

## 🔍 Debugging

### Ver Logs do Android
```bash
# Todos os logs FCM
adb logcat -s FCM

# Logs detalhados
adb logcat -s MyDApp
```

### Ver Token Registrado no Laravel
```bash
# Verificar cache
php artisan tinker
>>> Cache::get('fcm_token_SEU_TOKEN_AQUI')

# Ver logs
tail -f storage/logs/laravel.log
```

### Problemas Comuns

| Problema | Solução |
|----------|---------|
| "Firebase error" Status 400 | Token FCM inválido ou não registrado |
| Notificação não chega | Verificar se app tem POST_NOTIFICATIONS permission |
| Token não registra | Verificar connection com servidor (IP correto?) |
| "Connection refused" | Servidor Laravel não está rodando |

## 📁 Arquivos Modificados

**Backend:**
- `app/Http/Controllers/Api/NotificacaoController.php` - Controller com API V1 JWT auth
- `routes/api.php` - 7 rotas de notificação

**Android:**
- `MainActivity.kt` - Auto-registro de token FCM
- `MyFirebaseMessagingService.kt` - Recebimento de notificações
- `AndroidManifest.xml` - Permissões e serviços
- `app/build.gradle` - Dependências Firebase

**Firebase:**
- `firebase-config.json` - Service Account credentials (NÃO COMMITAR)
- `speedfood-b4495-firebase-adminsdk-fbsvc-48e9c4e399.json` - Credenciais

## 🔐 Segurança

⚠️ **IMPORTANTE:**
- `firebase-config.json` contém a chave privada!
- Adicionar ao `.gitignore` se usar Git
- Nunca compartilhar ou commitar

## 📱 Fluxo Completo

```
1. User abre app Android
   ↓
2. MainActivity registra token FCM
   ↓
3. Token enviado para Laravel via HTTP POST
   ↓
4. Laravel salva em cache
   ↓
5. Admin envia notificação via API Laravel
   ↓
6. Firebase recebe via API V1 JWT Auth
   ↓
7. Notificação chega no Android
   ↓
8. MyFirebaseMessagingService mostra notificação
   ↓
9. User clica → abre app (WebView) com pedido_id
```

## ✨ Próximas Melhorias

- [ ] Salvar tokens em banco de dados
- [ ] Dashboard para enviar notificações via UI
- [ ] Histórico de notificações
- [ ] Analytics de entrega
- [ ] Notificações em background service

## 📞 Suporte

Consulte logs:
```bash
# Laravel
tail -f storage/logs/laravel.log

# Android
adb logcat -s "FCM|MyDApp"

# Firebase
google cloud console → Firebase → Cloud Messaging
```
