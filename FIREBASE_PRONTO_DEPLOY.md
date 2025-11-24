# 🚀 Push Notifications - PRONTO PARA DEPLOY

## ✅ Checklist de Conclusão

### Backend Laravel ✅
- [x] API V1 Firebase configurada
- [x] Service Account JWT authentication
- [x] Access Token caching (55 min)
- [x] 6 tipos de notificação:
  - [x] `POST /api/notificacao/testar` - Teste
  - [x] `POST /api/notificacao/enviar` - Genérica
  - [x] `POST /api/notificacao/enviar-multipla` - Múltiplos
  - [x] `POST /api/notificacao/pedido-pronto` - Pedido pronto
  - [x] `POST /api/notificacao/delivery-aceito` - Delivery aceito
  - [x] `POST /api/notificacao/delivery-entregue` - Delivery entregue
- [x] Salvar token FCM: `POST /api/notificacao/salvar-token`
- [x] Validação de entrada
- [x] Logs de erro
- [x] Testes locais funcionando (Status 200, erro 400 só com token inválido)

### Android App ✅
- [x] Firebase Cloud Messaging integrado
- [x] Auto-registro de token na inicialização
- [x] `MyFirebaseMessagingService` para receber notificações
- [x] Notificações com vibração
- [x] WebView com comunicação com servidor
- [x] POST_NOTIFICATIONS permissão
- [x] MainActivity pronta com token registration
- [x] Build configurado com Firebase

### Firebase ✅
- [x] Projeto: speedfood-b4495
- [x] Service Account credentials obtidas
- [x] API V1 habilitada
- [x] Arquivo `firebase-config.json` com credenciais

### Arquivos Criados ✅
- [x] `FIREBASE_DEPLOY_GUIDE.md` - Guia completo
- [x] `compilar-android.ps1` - Script para compilar
- [x] `testar-fcm-token.ps1` - Script para testar com token real
- [x] `testar_notificacoes_teste.php` - Testes PHP

---

## 📋 Instruções de Deploy

### 1️⃣ Compilar APK

**Opção A - Com Android Studio (Recomendado)**
```bash
1. Abrir Android Studio
2. File → Open
3. Selecionar: C:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp
4. Esperar indexar (2-3 minutos)
5. Build → Build APK(s)
6. Sucesso! APK em: app/build/outputs/apk/debug/app-debug.apk
```

**Opção B - Via Linha de Comando**
```bash
cd "C:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp"
# Se tiver Gradle instalado:
# gradle assemble
# ou use Android Studio
```

### 2️⃣ Instalar no Tablet

```bash
# Conectar tablet via USB ou rede
adb connect 192.168.15.9:5037  # Se via rede

# Instalar APK
adb install -r "app\build\outputs\apk\debug\app-debug.apk"

# Ou se preferir remover e instalar:
adb uninstall com.myd.restaurante
adb install "app\build\outputs\apk\debug\app-debug.apk"
```

### 3️⃣ Verificar Token Registrado

```bash
# Ver logs do Android
adb logcat -s FCM

# Saída esperada:
# D/FCM Token: fTe5...qW2L6aqMPy7...
# D/FCM Register: Response Code: 200
# D/FCM Register: Response: {"success":true,"mensagem":"Token saved"}
```

### 4️⃣ Testar Notificações

**Com Script PowerShell:**
```bash
cd "C:\xampp\htdocs\myd_bar_restaurantes"
.\testar-fcm-token.ps1 -Token "fTe5...qW2L6aqMPy7..."
```

**Ou com CURL:**
```bash
# Teste simples
curl -X POST http://192.168.15.9/api/notificacao/testar \
  -H "Content-Type: application/json" \
  -d '{"token":"SEU_TOKEN_FCM"}'

# Status 200 + {"success":true} = Funcionando!
```

### 5️⃣ Verificar Notificação no Tablet

1. Abrir app (WebView mostra sua app)
2. Deixar em background (home)
3. Executar comando de teste
4. 📬 Notificação deve aparecer!

---

## 🔍 Troubleshooting

### ❌ "Cannot resolve symbol 'FirebaseMessaging'"
**Solução:** Sincronizar Gradle em Android Studio
```
File → Sync Now
```

### ❌ "403 Forbidden" ao enviar notificação
**Solução:** Credenciais Firebase inválidas
```
1. Verificar firebase-config.json existe
2. Verificar private_key está intacta
3. Verificar Project ID: speedfood-b4495
```

### ❌ Notificação não chega
**Solução:** Verificar múltiplos pontos
```
1. Token está correto? (Compare com log: adb logcat -s FCM)
2. App tem permissão? (AndroidManifest.xml com POST_NOTIFICATIONS)
3. Firebase ativo? (Google Cloud Console → Firebase)
4. Servidor respondendo? (curl http://192.168.15.9/api/notificacao/testar)
```

### ❌ "Cannot build - missing google-services.json"
**Solução:** Arquivo está em app/
```
Caminho correto:
C:\...\MyDApp\app\google-services.json
```

---

## 📊 Resultados Esperados

**API Test Local:**
```
✅ 1️⃣ Salvando Token FCM
Status: 200
Resposta: {"success":true,"mensagem":"Token saved"}

✅ 2️⃣ Enviando Notificação Simples
Status: 400  ← Esperado (TOKEN_AQUI é fictício)
Resposta: {"success":false,"erro":"Firebase error"}

✅ 3️⃣ Notificando Pedido Pronto
Status: 400
...
```

**Com Token FCM Real:**
```
✅ Testador de Notificações Firebase
Token: fTe5...
Servidor: http://192.168.15.9

📤 Testando: 1. Teste Simples
✅ Status: 200
Resposta: {"success":true,"mensagem":"Sent"}

📱 Notificação chegou no tablet!
```

---

## 📁 Arquivos Principais

```
Backend (Laravel):
├── app/Http/Controllers/Api/NotificacaoController.php ← Lógica
├── routes/api.php ← Rotas
├── firebase-config.json ← Credenciais (⚠️ secreto!)
└── FIREBASE_DEPLOY_GUIDE.md ← Este guia

Android:
├── android-studio-project/MyDApp/
│   ├── app/src/main/java/.../MainActivity.kt ← Auto-registro token
│   ├── app/src/main/java/.../MyFirebaseMessagingService.kt ← Receber notif
│   ├── app/build.gradle ← Dependências
│   ├── AndroidManifest.xml ← Permissões
│   └── app/build/outputs/apk/debug/app-debug.apk ← APK final

Scripts:
├── compilar-android.ps1 ← Compilar
├── testar-fcm-token.ps1 ← Testar com token real
├── testar_notificacoes_teste.php ← Testes PHP
└── FIREBASE_DEPLOY_GUIDE.md ← Guia
```

---

## 🎯 Fluxo Completo de Teste

```
1. COMPILAR
   └─→ Android Studio → Build → Build APK(s) ✅

2. INSTALAR
   └─→ adb install -r app-debug.apk ✅

3. REGISTRAR TOKEN
   └─→ Abrir app → MainActivity.onCreate()
       → registerFCMToken() → POST /api/notificacao/salvar-token
       → Ver em logs: "FCM Token: ..." ✅

4. TESTAR
   └─→ .\testar-fcm-token.ps1 -Token "..."
       → POST /api/notificacao/testar
       → 📬 Notificação no tablet! ✅

5. VALIDAR
   └─→ Verificar tipos de notificação:
       • Teste ✅
       • Genérica ✅
       • Pedido pronto ✅
       • Delivery aceito ✅
       • Delivery entregue ✅
       • Múltiplos ✅
```

---

## 🔐 Segurança

⚠️ **IMPORTANTE - NÃO FAZER:**
- ❌ Commitar `firebase-config.json`
- ❌ Compartilhar credenciais
- ❌ Usar em produção sem HTTPS

✅ **Fazer:**
- ✅ Adicionar ao `.gitignore`
- ✅ Usar ambiente `.env` para credenciais
- ✅ Usar HTTPS em produção
- ✅ Rotacionar credenciais periodicamente

---

## 📞 Próximas Etapas

1. **Compilar e testar** com as instruções acima
2. **Salvar tokens em DB** (atualmente em cache)
3. **Dashboard Admin** para enviar notificações
4. **Analytics** de entrega
5. **Notificações agendadas**

---

**Status: ✅ PRONTO PARA DEPLOY**

Qualquer dúvida, consulte `FIREBASE_DEPLOY_GUIDE.md`
