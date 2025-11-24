# App Entregador - Android

Aplicativo Android para entregadores do sistema MYD Bar & Restaurantes.

## 📱 Funcionalidades

- Login de entregadores
- Visualização de entregas disponíveis
- Aceitar e gerenciar entregas
- Rastreamento de localização em tempo real
- Histórico de entregas
- Controle de ganhos
- Notificações push
- Modo offline

## 🛠️ Tecnologias

- Android SDK (API 24+)
- WebView com JavaScript Interface
- Google Play Services Location
- Foreground Service para rastreamento
- Material Design Components

## 📋 Pré-requisitos

- Android Studio Arctic Fox ou superior
- JDK 8 ou superior
- Android SDK Build Tools 34
- Gradle 8.0

## 🚀 Como Compilar no Android Studio

### 1. Abrir o Projeto

1. Abra o Android Studio
2. Clique em **File** → **Open**
3. Navegue até a pasta `app-entregador-android`
4. Clique em **OK**

### 2. Configurar URL da API

Edite o arquivo `MainActivity.java` e altere a URL base:

```java
// Para emulador Android
private static final String BASE_URL = "http://10.0.2.2/app-entregador/";

// Para dispositivo real, use seu IP local:
private static final String BASE_URL = "http://192.168.1.XXX/app-entregador/";
```

### 3. Sincronizar Gradle

1. Aguarde o Android Studio sincronizar automaticamente
2. Ou clique em **File** → **Sync Project with Gradle Files**

### 4. Compilar APK

#### APK Debug (para testes):
```bash
# No terminal do Android Studio ou PowerShell:
cd app-entregador-android
gradlew assembleDebug
```

O APK será gerado em: `app/build/outputs/apk/debug/app-debug.apk`

#### APK Release (para produção):
```bash
gradlew assembleRelease
```

O APK será gerado em: `app/build/outputs/apk/release/app-release.apk`

### 5. Instalar no Dispositivo

#### Via Android Studio:
1. Conecte seu dispositivo via USB (com depuração USB ativada)
2. Clique no botão **Run** (▶️) na toolbar
3. Selecione seu dispositivo

#### Via ADB:
```bash
adb install app/build/outputs/apk/debug/app-debug.apk
```

#### Manual:
1. Copie o APK para o dispositivo
2. Abra o arquivo no dispositivo
3. Autorize instalação de fontes desconhecidas se necessário

## 🔧 Configurações Importantes

### Permissões

O app solicita as seguintes permissões:
- `ACCESS_FINE_LOCATION` - Localização precisa
- `ACCESS_COARSE_LOCATION` - Localização aproximada
- `INTERNET` - Acesso à internet
- `FOREGROUND_SERVICE` - Serviço em primeiro plano
- `POST_NOTIFICATIONS` - Notificações (Android 13+)
- `VIBRATE` - Vibração

### Network Security

O app está configurado para aceitar tráfego HTTP (cleartextTraffic) para desenvolvimento.

Para produção, altere em `AndroidManifest.xml`:
```xml
android:usesCleartextTraffic="false"
```

E use apenas HTTPS.

## 📦 Estrutura do Projeto

```
app-entregador-android/
├── app/
│   ├── src/main/
│   │   ├── java/com/mydbar/entregador/
│   │   │   ├── MainActivity.java          # Activity principal
│   │   │   ├── WebAppInterface.java       # Interface JavaScript
│   │   │   └── LocationService.java       # Serviço de localização
│   │   ├── res/
│   │   │   ├── layout/
│   │   │   │   └── activity_main.xml      # Layout principal
│   │   │   ├── values/
│   │   │   │   ├── strings.xml            # Strings do app
│   │   │   │   ├── colors.xml             # Cores
│   │   │   │   └── themes.xml             # Temas
│   │   │   └── xml/
│   │   │       ├── backup_rules.xml
│   │   │       └── network_security_config.xml
│   │   └── AndroidManifest.xml            # Manifest do app
│   ├── build.gradle                       # Build do módulo
│   └── proguard-rules.pro                 # Regras ProGuard
├── gradle/
│   └── wrapper/
│       └── gradle-wrapper.properties      # Configuração Gradle
├── build.gradle                           # Build root
├── settings.gradle                        # Configurações projeto
└── gradle.properties                      # Propriedades Gradle
```

## 🎨 Personalização

### Alterar Ícone do App

Substitua os ícones nas pastas:
- `res/mipmap-mdpi/ic_launcher.png` (48x48)
- `res/mipmap-hdpi/ic_launcher.png` (72x72)
- `res/mipmap-xhdpi/ic_launcher.png` (96x96)
- `res/mipmap-xxhdpi/ic_launcher.png` (144x144)
- `res/mipmap-xxxhdpi/ic_launcher.png` (192x192)

### Alterar Nome do App

Edite `res/values/strings.xml`:
```xml
<string name="app_name">Seu Nome</string>
```

### Alterar Package Name

1. Edite `build.gradle`:
```gradle
namespace 'com.seudominio.entregador'
applicationId "com.seudominio.entregador"
```

2. Renomeie os pacotes Java

## 🐛 Resolução de Problemas

### Erro de Sincronização Gradle
```bash
# Limpar e reconstruir
gradlew clean
gradlew build
```

### Erro de Permissão de Localização
- Verifique se as permissões foram concedidas nas configurações do app
- No emulador, use localizações simuladas

### WebView não carrega
- Verifique se o servidor Laravel está rodando
- Teste a URL no navegador do dispositivo
- Verifique o IP/URL em MainActivity.java

### App fecha ao iniciar
- Verifique os logs: `adb logcat`
- Verifique se todas as dependências foram baixadas

## 📝 Notas de Desenvolvimento

### Testar no Emulador

O emulador Android usa o IP `10.0.2.2` para acessar `localhost` do host.

### Testar em Dispositivo Real

1. Certifique-se de que o dispositivo está na mesma rede Wi-Fi
2. Use o IP local do seu computador (ex: 192.168.1.XXX)
3. Desabilite firewall se necessário

### Build de Produção

Para gerar um APK assinado:

1. Crie um keystore:
```bash
keytool -genkey -v -keystore entregador.keystore -alias entregador -keyalg RSA -keysize 2048 -validity 10000
```

2. Configure em `app/build.gradle`:
```gradle
android {
    signingConfigs {
        release {
            storeFile file("entregador.keystore")
            storePassword "sua_senha"
            keyAlias "entregador"
            keyPassword "sua_senha"
        }
    }
    buildTypes {
        release {
            signingConfig signingConfigs.release
        }
    }
}
```

3. Build:
```bash
gradlew assembleRelease
```

## 📱 Requisitos do Dispositivo

- Android 7.0 (API 24) ou superior
- GPS/Localização
- Conexão com internet
- Mínimo 100 MB de espaço livre

## 🔐 Segurança

Para produção:
- Use HTTPS para todas as conexões
- Implemente certificate pinning
- Ofusque o código com ProGuard
- Não exponha credenciais no código

## 📞 Suporte

Para problemas ou dúvidas sobre o app Android, consulte:
- Logs: `adb logcat | grep Entregador`
- Android Developer Documentation
- Stack Overflow

## 📄 Licença

Este projeto faz parte do sistema MYD Bar & Restaurantes.
