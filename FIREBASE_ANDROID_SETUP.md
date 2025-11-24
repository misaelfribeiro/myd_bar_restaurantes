# 🔥 Configurar Firebase no App Entregador Android

## ✅ Arquivos já configurados

Todos os arquivos necessários já foram criados e configurados:

- ✅ `app-entregador-android/build.gradle` - Google Services plugin
- ✅ `app-entregador-android/app/build.gradle` - Firebase dependencies
- ✅ `MyFirebaseMessagingService.java` - Serviço FCM
- ✅ `WebAppInterface.java` - Bridge JavaScript ↔ Android
- ✅ `MainActivity.java` - Obtém token FCM
- ✅ `AndroidManifest.xml` - Permissões e serviço registrado
- ✅ `notifications.js` - Detecta Android e usa token nativo

## 📋 Único passo necessário: google-services.json

### Opção 1: Baixar do Firebase Console (RECOMENDADO)

1. Acesse: https://console.firebase.google.com/project/speedfood-b4495/settings/general/
2. Na seção "Seus apps", procure por **App Entregador** ou clique em "Adicionar app" → Android
3. **Nome do pacote**: `com.mydbar.entregador`
4. **Apelido do app**: App Entregador
5. Baixe o arquivo `google-services.json`
6. **Copie para**: `app-entregador-android/app/google-services.json`

### Opção 2: Usar o mesmo do app-cliente

Se você já tem o `google-services.json` do projeto:

```powershell
Copy-Item "android-app/app/google-services.json" -Destination "app-entregador-android/app/google-services.json"
```

## 🚀 Compilar e testar

### 1. Abrir no Android Studio

```powershell
cd app-entregador-android
```

Abra o projeto no Android Studio.

### 2. Sync Gradle

No Android Studio: **File** → **Sync Project with Gradle Files**

### 3. Compilar APK

**Build** → **Build Bundle(s) / APK(s)** → **Build APK(s)**

OU via terminal:

```powershell
cd app-entregador-android
.\gradlew assembleDebug
```

### 4. Instalar no dispositivo

```powershell
adb install app\build\outputs\apk\debug\app-debug.apk
```

## ✅ Como funciona

1. **App Android inicia** → `MainActivity` solicita token FCM
2. **Token obtido** → Salvo em SharedPreferences
3. **WebView carrega** → `notifications.js` detecta `Android.getFCMToken()`
4. **Token enviado** → POST `/api/entregadores/device-token`
5. **Notificação enviada** → `MyFirebaseMessagingService` recebe
6. **Notificação exibida** → Sistema Android mostra ao entregador

## 🔍 Debug

### Ver logs no Android Studio

```
adb logcat | grep -E "FCM|Firebase|Notification"
```

### Ver token FCM

No logcat, procure por:
```
📱 FCM Token: [token aqui]
```

### Testar notificação

1. Faça login no app como entregador
2. Aguarde token ser registrado (veja logs)
3. No backend, marque entrega como disponível para plataforma
4. Entregador próximo receberá notificação!

## 🎯 Diferenças Web vs Android

| Recurso | Web/PWA | Android Nativo |
|---------|---------|----------------|
| Service Worker | ✅ Sim | ❌ Não usado |
| Firebase SDK JS | ✅ Carregado | ❌ Opcional |
| Firebase SDK Android | ❌ N/A | ✅ Nativo |
| Token via `getToken()` | ✅ JS | ❌ N/A |
| Token via `Android.getFCMToken()` | ❌ N/A | ✅ Java |
| Notificação em background | Service Worker | FCM Service |
| Notificação em foreground | `onMessage()` | Intent direto |

## 📱 Testado em

- ✅ Android 10+
- ✅ WebView com JavaScript habilitado
- ✅ Firebase Cloud Messaging v32.7.0
- ✅ Notificações com som e vibração
