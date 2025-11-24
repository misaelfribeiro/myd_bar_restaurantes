# 🚀 Abrir Projeto no Android Studio

## ✅ Pré-requisitos

1. **Android Studio** instalado ([Download](https://developer.android.com/studio))
2. **Android SDK** configurado
3. **Java 11+** instalado
4. **Gradle** (já incluído no Android Studio)

## 📂 Localização do Projeto

```
c:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp
```

## 🔧 Passos para Abrir

### 1️⃣ Abrir Android Studio
- Inicie o Android Studio
- Clique em "Open" (ou File → Open)

### 2️⃣ Selecionar Pasta do Projeto
```
Navegue para: c:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp
Clique em "OK"
```

### 3️⃣ Aguardar Sincronização
- Android Studio vai sincronizar automaticamente
- Aguarde completar (pode levar 2-3 minutos na primeira vez)
- Se pedir para atualizar, clique em "Update"

### 4️⃣ Configurar Emulador ou Dispositivo

**Opção A: Usar Emulador (Recomendado para teste)**
```
Tools → Device Manager → Criar novo AVD
- Selecione: Pixel 4, API 34
- Dê um nome e crie
```

**Opção B: Dispositivo Físico**
```
1. Conecte via USB
2. Ative "Depuração USB" no celular
3. Android Studio detectará automaticamente
```

## 🏃 Compilar e Executar

### Via Android Studio (Visualmente)
1. Menu superior: `Run` (ou Shift+F10)
2. Selecione o emulador ou dispositivo
3. Clique em ✅ "OK"

### Via Terminal/PowerShell
```powershell
cd c:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp
./gradlew build        # Compilar
./gradlew installDebug # Instalar em dispositivo/emulador
```

## 📱 O que o App Faz

- ✅ Abre http://localhost/app-cliente/
- ✅ Exibe o app web em um WebView
- ✅ Acesso total a JavaScript
- ✅ Vibração do celular integrada
- ✅ Cache offline
- ✅ Funciona com localhost

## 🐛 Troubleshooting

### "Gradle sync failed"
```
1. File → Sync Now
2. Se persistir: File → Invalidate Caches → Restart
```

### "Cannot find SDK"
```
1. File → Project Structure
2. SDK Location → Edit e aponte para seu Android SDK
3. Geralmente em: C:\Users\[User]\AppData\Local\Android\sdk
```

### App conecta em localhost mas dá erro
```
1. Verifique se o servidor Laravel está rodando
2. No emulador, use: http://10.0.2.2:80 (em vez de localhost)
3. Atualize em MainActivity.kt:
   webView.loadUrl("http://10.0.2.2/app-cliente/")
```

### Permissões negadas
```
Android 6+ pede permissões em runtime
O app já está configurado no AndroidManifest.xml
Se pedir, clique "Permitir"
```

## 🎯 Estrutura do Projeto

```
MyDApp/
├── app/
│   ├── build.gradle              ← Dependências
│   ├── src/
│   │   ├── main/
│   │   │   ├── AndroidManifest.xml  ← Permissões
│   │   │   ├── java/
│   │   │   │   └── com/myd/restaurante/
│   │   │   │       └── MainActivity.kt  ← WebView
│   │   │   └── res/
│   │   │       ├── layout/
│   │   │       │   └── activity_main.xml
│   │   │       ├── values/
│   │   │       │   ├── colors.xml
│   │   │       │   ├── strings.xml
│   │   │       │   └── themes.xml
│   │   │       └── mipmap-*/    ← Ícones
│   │   └── test/
│   └── proguard-rules.pro
├── settings.gradle               ← Configurações globais
└── build.gradle                  ← Build do projeto
```

## 📦 Gerar APK para Distribuição

### Debug APK (Teste)
```powershell
./gradlew assembleDebug
# Saída: app/build/outputs/apk/debug/app-debug.apk
```

### Release APK (Produção)
```powershell
./gradlew assembleRelease
# Saída: app/build/outputs/apk/release/app-release.apk
# Nota: Precisa assinar com keystore
```

## 🔐 Assinar APK para Play Store

```bash
# 1. Gerar keystore (uma única vez)
keytool -genkey -v -keystore my-release-key.jks \
  -keyalg RSA -keysize 2048 -validity 10000 \
  -alias my-key-alias

# 2. Configurar assinatura em build.gradle
# 3. Gerar APK assinado
./gradlew assembleRelease
```

## 📞 Próximos Passos

1. ✅ Abra o projeto no Android Studio
2. ✅ Sincronize o Gradle
3. ✅ Crie/Conecte um emulador
4. ✅ Clique em "Run" (Shift+F10)
5. ✅ Teste o app
6. ✅ Exporte como APK

---

**Dúvidas?** Verifique os logs em: `Logcat` (View → Tool Windows → Logcat)

Aproveite! 🚀
