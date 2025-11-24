# 📱 MyD Android Studio Project

Projeto Android Studio nativo com WebView para o app MyD Bar & Restaurantes.

## 🚀 Quick Start

```bash
# 1. Abra em Android Studio
File → Open → android-studio-project\MyDApp

# 2. Sincronize o Gradle
Gradle sync automático (ou Ctrl+Shift+O)

# 3. Execute no emulador/dispositivo
Run (Shift+F10)
```

## 📋 Características

- ✅ WebView carregando app web em localhost
- ✅ Vibração integrada (via AndroidBridge)
- ✅ Cache offline automático
- ✅ Suporte a localhost, 127.0.0.1 e myd.local
- ✅ Material Design 3
- ✅ API 24+ (Android 7.0+)

## 🔧 Configuração Rápida

### 1. Adicione o caminho do Android SDK

Se não conseguir sincronizar:

```
File → Project Structure → SDK Location
```

Aponte para: `C:\Users\[SEU_USUARIO]\AppData\Local\Android\sdk`

### 2. Para testar em emulador

Se precisar trocar localhost por IP do emulador:

**MainActivity.kt** (linha ~74)
```kotlin
// De:
webView.loadUrl("http://localhost/app-cliente/")

// Para (se usar emulador):
webView.loadUrl("http://10.0.2.2/app-cliente/")
```

### 3. Instale um emulador

```
Tools → Device Manager → Create Device
- Pixel 4, API 34, 1GB RAM
```

## 📦 Estrutura

```
app/src/main/
├── AndroidManifest.xml
├── java/com/myd/restaurante/
│   └── MainActivity.kt         ← Main Activity com WebView
└── res/
    ├── layout/
    │   └── activity_main.xml   ← UI
    ├── values/
    │   ├── colors.xml
    │   ├── strings.xml
    │   └── themes.xml
    └── mipmap-*/               ← Ícones (6 densidades)
```

## 🎨 Customização

### Mudar URL da app

**MainActivity.kt** (linha 74)
```kotlin
webView.loadUrl("http://localhost/app-cliente/")  // ← Mude aqui
```

### Mudar cores

**res/values/colors.xml**
```xml
<color name="primary_red">#EA1D2C</color>  ← Cor primária
```

### Mudar nome do app

**res/values/strings.xml**
```xml
<string name="app_name">Seu Nome Aqui</string>
```

## 🏃 Compilar APK

### Debug (Para teste)
```bash
./gradlew assembleDebug
# Output: app/build/outputs/apk/debug/app-debug.apk
```

### Release (Para Play Store)
```bash
./gradlew assembleRelease
# Output: app/build/outputs/apk/release/app-release.apk
```

## 📲 Instalar APK

### No emulador
```bash
./gradlew installDebug
```

### No dispositivo físico
```bash
adb install app/build/outputs/apk/debug/app-debug.apk
```

## 🐛 Debug

### Ver logs
```
View → Tool Windows → Logcat
```

### Inspecionar elementos
```
Chrome DevTools (F12) → Connect device
chrome://inspect
```

### Debugar JavaScript
```javascript
// Em MainActivity.kt, a ponte JavaScript está ativa
window.vibrate([200,100,200]);  // Vibrar do JS
window.AndroidBridge.showMessage("Olá!");  // Toast
```

## 📝 Dependências

- AndroidX AppCompat 1.6.1
- Material Components 1.11.0
- WebKit 1.7.0
- Lifecycle Runtime 2.6.2

## 🔐 Permissões

Já configuradas no `AndroidManifest.xml`:
- INTERNET
- ACCESS_NETWORK_STATE
- VIBRATE
- ACCESS_COARSE_LOCATION
- ACCESS_FINE_LOCATION
- CAMERA

## ✅ Checklist Final

- [x] Projeto criado
- [x] MainActivity com WebView
- [x] Layouts criados
- [x] Ícones gerados (6 densidades)
- [x] Cores e temas configurados
- [x] Permissões adicionadas
- [x] Bridge JavaScript ativo
- [x] Gradle configurado
- [x] Pronto para Build

## 🚀 Próximos Passos

1. Abra em Android Studio
2. Sincronize Gradle
3. Crie um emulador (ou conecte dispositivo)
4. Execute (Shift+F10)
5. Teste o app
6. Exporte como APK

---

**Caminho do projeto:**
```
c:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp
```

**Versão:** 1.0.0  
**Android Mínimo:** API 24 (Android 7.0)  
**Android Alvo:** API 35 (Android 15)  

Aproveite! 🎉
