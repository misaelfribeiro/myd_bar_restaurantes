# Guia Completo - App Entregador Android

## 📱 Sobre o App

O **App Entregador** é um aplicativo Android nativo que funciona como uma WebView otimizada do sistema PWA de entregas. Ele oferece:

- ✅ Login seguro de entregadores
- ✅ Visualização de entregas disponíveis
- ✅ Aceitar e gerenciar entregas em tempo real
- ✅ Rastreamento GPS em background
- ✅ Histórico completo de entregas
- ✅ Controle de ganhos (diário, semanal, mensal)
- ✅ Notificações push
- ✅ Modo offline
- ✅ Interface nativa Android

## 🚀 Como Compilar

### Opção 1: Script Automático (Recomendado)

Execute o script PowerShell na raiz do projeto:

```powershell
.\compilar-app-entregador.ps1
```

O script irá:
1. Limpar builds anteriores
2. Permitir escolher entre Debug ou Release
3. Compilar o APK
4. Mostrar localização do arquivo
5. Oferecer instalar diretamente no dispositivo

### Opção 2: Android Studio

1. **Abrir o Projeto**
   - Abra o Android Studio
   - File → Open
   - Selecione a pasta `app-entregador-android`

2. **Configurar URL**
   - Abra `MainActivity.java`
   - Altere `BASE_URL`:
     ```java
     // Emulador
     private static final String BASE_URL = "http://10.0.2.2/app-entregador/";
     
     // Dispositivo real (use seu IP)
     private static final String BASE_URL = "http://192.168.1.XXX/app-entregador/";
     ```

3. **Sincronizar**
   - File → Sync Project with Gradle Files
   - Aguarde o download das dependências

4. **Compilar**
   - Build → Build Bundle(s) / APK(s) → Build APK(s)
   - Ou clique no botão Run (▶️) para instalar direto

### Opção 3: Linha de Comando

```powershell
cd app-entregador-android

# Debug
.\gradlew.bat assembleDebug

# Release
.\gradlew.bat assembleRelease
```

## 📦 Localização dos APKs

Após a compilação:

**Debug:**
```
app-entregador-android\app\build\outputs\apk\debug\app-debug.apk
```

**Release:**
```
app-entregador-android\app\build\outputs\apk\release\app-release.apk
```

## 📲 Como Instalar

### No Emulador Android Studio

1. Inicie o emulador no Android Studio
2. Arraste o APK para a janela do emulador
3. Ou use: `adb install app-debug.apk`

### Em Dispositivo Real (USB)

1. **Habilitar Depuração USB:**
   - Configurações → Sobre o telefone
   - Toque 7x em "Número da versão"
   - Volte → Opções do desenvolvedor
   - Ative "Depuração USB"

2. **Conectar e Instalar:**
   ```powershell
   adb devices  # Verificar conexão
   adb install -r app\build\outputs\apk\debug\app-debug.apk
   ```

### Instalação Manual

1. Copie o APK para o dispositivo (via cabo, Bluetooth, email, etc)
2. Abra o arquivo no dispositivo
3. Autorize "Fontes desconhecidas" se solicitado
4. Toque em "Instalar"

## 🔧 Configurações Importantes

### 1. URL do Servidor

**Arquivo:** `MainActivity.java` (linha ~15)

```java
// ALTERE PARA SEU AMBIENTE:

// Desenvolvimento - Emulador
private static final String BASE_URL = "http://10.0.2.2/app-entregador/";

// Desenvolvimento - Dispositivo Real (mesma rede WiFi)
private static final String BASE_URL = "http://192.168.1.100/app-entregador/";

// Produção
private static final String BASE_URL = "https://seudominio.com.br/app-entregador/";
```

### 2. Package Name (ID do App)

**Arquivo:** `app/build.gradle` (linha ~8)

```gradle
defaultConfig {
    applicationId "com.mydbar.entregador"  // ALTERE AQUI
    // ...
}
```

**Importante:** Se alterar o package, também renomeie a pasta Java:
```
app/src/main/java/com/mydbar/entregador/
```

### 3. Nome do App

**Arquivo:** `app/src/main/res/values/strings.xml`

```xml
<string name="app_name">Entregador</string>  <!-- ALTERE AQUI -->
```

### 4. Ícone do App

Substitua os ícones nas pastas:
- `res/mipmap-mdpi/` - 48x48px
- `res/mipmap-hdpi/` - 72x72px
- `res/mipmap-xhdpi/` - 96x96px
- `res/mipmap-xxhdpi/` - 144x144px
- `res/mipmap-xxxhdpi/` - 192x192px

Nomeie os arquivos como `ic_launcher.png` e `ic_launcher_round.png`

### 5. Cores do App

**Arquivo:** `app/src/main/res/values/colors.xml`

```xml
<resources>
    <color name="primary">#FF6200EE</color>      <!-- Cor primária -->
    <color name="primary_dark">#FF3700B3</color> <!-- Cor da barra de status -->
    <color name="accent">#FF03DAC5</color>       <!-- Cor de destaque -->
</resources>
```

## 🔐 Build de Produção (Release)

Para publicar na Play Store, você precisa assinar o APK:

### 1. Criar Keystore

```powershell
keytool -genkey -v -keystore entregador.keystore -alias entregador -keyalg RSA -keysize 2048 -validity 10000
```

Preencha:
- Senha do keystore
- Nome, organização, etc.

**⚠️ IMPORTANTE:** Guarde o keystore e as senhas em local seguro!

### 2. Configurar no Gradle

**Arquivo:** `app/build.gradle`

```gradle
android {
    signingConfigs {
        release {
            storeFile file("../entregador.keystore")
            storePassword "SUA_SENHA_AQUI"
            keyAlias "entregador"
            keyPassword "SUA_SENHA_AQUI"
        }
    }
    
    buildTypes {
        release {
            signingConfig signingConfigs.release
            minifyEnabled true
            shrinkResources true
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }
}
```

### 3. Compilar Release

```powershell
cd app-entregador-android
.\gradlew.bat assembleRelease
```

O APK assinado estará em:
```
app\build\outputs\apk\release\app-release.apk
```

## 🧪 Testar o App

### Verificar Conexão

1. **Servidor rodando:**
   ```powershell
   php artisan serve
   ```

2. **Testar no navegador do dispositivo:**
   - Emulador: `http://10.0.2.2:8000/app-entregador`
   - Real: `http://SEU_IP:8000/app-entregador`

3. **Se funcionar no navegador mas não no app:**
   - Verifique a URL em `MainActivity.java`
   - Limpe cache: Settings → Apps → Entregador → Clear Data

### Verificar Permissões

O app solicita:
- ✅ Localização precisa
- ✅ Localização em background
- ✅ Internet
- ✅ Notificações (Android 13+)

Confirme que todas foram concedidas:
- Settings → Apps → Entregador → Permissions

### Debug via ADB

Ver logs em tempo real:

```powershell
adb logcat | Select-String "Entregador|chromium"
```

## 🐛 Problemas Comuns

### 1. "Gradle sync failed"

**Solução:**
```powershell
cd app-entregador-android
.\gradlew.bat clean
.\gradlew.bat build
```

### 2. "WebView not loading"

**Causas comuns:**
- Servidor não está rodando
- URL incorreta em MainActivity.java
- Firewall bloqueando conexão
- Dispositivo em rede diferente

**Teste:**
```powershell
# No navegador do dispositivo, acesse:
http://10.0.2.2:8000/app-entregador  # Emulador
http://192.168.1.XXX:8000/app-entregador  # Real
```

### 3. "Location not working"

**Soluções:**
- Conceda permissões de localização
- Ative GPS no dispositivo
- No emulador: Extended controls (…) → Location

### 4. "App keeps closing"

**Debug:**
```powershell
adb logcat > log.txt
# Abra o app, aguarde o crash
# Verifique log.txt para o erro
```

### 5. "Could not find adb"

**Solução:**
```powershell
# Adicione ao PATH:
$env:Path += ";C:\Users\SEU_USUARIO\AppData\Local\Android\Sdk\platform-tools"
```

## 📊 Monitoramento

### Rastreamento GPS

O app usa um Foreground Service que:
- Atualiza localização a cada 30 segundos
- Envia para API automaticamente
- Funciona mesmo com app em background
- Mostra notificação persistente

### Verificar Status do Serviço

```powershell
adb shell dumpsys activity services | Select-String "LocationService"
```

## 🚀 Publicar na Play Store

### Requisitos

1. ✅ APK/AAB assinado
2. ✅ Conta Google Play Console ($25 única vez)
3. ✅ Ícone 512x512px
4. ✅ Screenshots (mínimo 2)
5. ✅ Descrição do app
6. ✅ Política de privacidade

### Gerar AAB (recomendado)

```powershell
.\gradlew.bat bundleRelease
```

Arquivo gerado:
```
app\build\outputs\bundle\release\app-release.aab
```

### Upload

1. Acesse [Google Play Console](https://play.google.com/console)
2. Criar novo aplicativo
3. Upload do AAB
4. Preencher informações da loja
5. Submeter para revisão

## 📱 Requisitos do Dispositivo

- Android 7.0 (API 24) ou superior
- GPS/Localização
- Conexão com internet
- 50 MB espaço livre
- 1 GB RAM mínimo

## 🔒 Segurança para Produção

### AndroidManifest.xml

```xml
<!-- DESABILITAR para produção: -->
android:usesCleartextTraffic="false"
android:debuggable="false"
```

### Usar HTTPS

```java
// MainActivity.java - PRODUÇÃO:
private static final String BASE_URL = "https://seudominio.com.br/app-entregador/";
```

### ProGuard

Já configurado em `proguard-rules.pro` para:
- Ofuscar código
- Remover logs
- Reduzir tamanho

## 📞 Suporte

### Logs

```powershell
# Ver todos os logs
adb logcat

# Filtrar apenas o app
adb logcat | Select-String "Entregador"

# Salvar em arquivo
adb logcat > logs.txt
```

### Informações do Dispositivo

```powershell
adb shell getprop ro.build.version.release  # Versão Android
adb shell getprop ro.product.model          # Modelo
adb shell dumpsys battery                   # Bateria
```

## 🎓 Recursos Adicionais

- [Android Developer Guide](https://developer.android.com/)
- [WebView Guide](https://developer.android.com/guide/webapps/webview)
- [Location Services](https://developer.android.com/training/location)

## ✅ Checklist de Deploy

- [ ] URL de produção configurada
- [ ] Keystore criado e guardado
- [ ] APK assinado gerado
- [ ] Testado em dispositivo real
- [ ] Ícones personalizados
- [ ] Nome do app configurado
- [ ] Permissões necessárias funcionando
- [ ] Rastreamento GPS testado
- [ ] Build release minificado
- [ ] HTTPS configurado
- [ ] Política de privacidade criada

---

**Pronto!** Seu app está pronto para ser compilado e instalado no Android! 🎉
