# 🚀 Próximos Passos - Android Studio (Guia Prático)

## ✅ Se você já abriu o projeto

```
✓ Projeto aberto em Android Studio
✓ Gradle sincronizado
✓ Sem erros no Build
```

Se não, execute estes passos PRIMEIRO:
1. Abra Android Studio
2. File → Open → `android-studio-project\MyDApp`
3. Espere sincronizar (2-3 min)
4. Se pedir update, clique "Update"

---

## 🎯 PRÓXIMO PASSO 1: Criar/Conectar Emulador

### Opção A: Usar Emulador (Mais Fácil) ⭐

**No Android Studio:**

1. Menu superior → **Tools** → **Device Manager**
2. Clique em **"Create Device"**
3. Selecione **"Pixel 4"**
4. Clique em **"Next"**
5. Selecione **API 34** (Android 14)
6. Clique em **"Next"** → **"Finish"**
7. Aguarde download (pode levar 5-10 min na primeira vez)

**Verificar:**
- Na aba "Device Manager", você verá "Pixel 4 - API 34"
- Clique no botão ▶️ (Play) para iniciar

### Opção B: Dispositivo Físico

1. Conecte o celular via **USB**
2. Abra **Configurações** → **Sobre o telefone**
3. Toque 7x em **"Número da compilação"** para ativar "Modo desenvolvedor"
4. Volte e abra **"Opções de desenvolvedor"**
5. Ative **"Depuração USB"**
6. Clique em **"Permitir"** quando o Android Studio pedir
7. Android Studio detectará automaticamente

---

## 🎯 PRÓXIMO PASSO 2: Executar o App

**Opção 1: Via Menu (Visual)**
1. Menu superior → **Run** (ou Shift+F10)
2. Selecione o emulador/dispositivo
3. Clique em **OK**
4. Aguarde compilação (1-2 min)

**Opção 2: Via Terminal**
```powershell
cd c:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp
./gradlew installDebug
```

**O que vai acontecer:**
- ✅ App compila
- ✅ App instala no emulador/dispositivo
- ✅ App abre automaticamente
- ✅ Carrega http://localhost/app-cliente/

---

## 🎯 PRÓXIMO PASSO 3: Testar o App

### Verificar se está funcionando:

1. **App abriu com sucesso?**
   - Você verá a tela de login do MyD
   - Ou a tela de menu se já estiver logado

2. **Rastreamento em tempo real?**
   - Faça um pedido de teste
   - A tela atualiza a cada 5 segundos
   - Status muda: preparando → confirmado → entregue

3. **Vibração funciona?**
   - O celular deve vibrar ao receber notificação
   - Se não vibrar, verifique permissões

4. **Offline?**
   - Desative WiFi/dados
   - App continua funcionando com cache
   - Reative a conexão

### Ver Logs (Debugging):

```
View → Tool Windows → Logcat
```

Procure por mensagens que começam com `[SW]` ou `[Android]`

---

## 🎯 PRÓXIMO PASSO 4: Customizar o App

### Mudar URL (se não estiver em localhost)

**Arquivo:** `app/src/main/java/com/myd/restaurante/MainActivity.kt`

**Linha 74, mude:**
```kotlin
// De:
webView.loadUrl("http://localhost/app-cliente/")

// Para:
webView.loadUrl("http://seu-servidor.com/app-cliente/")
```

**Depois:**
1. Save (Ctrl+S)
2. Run (Shift+F10) para recompilar

### Mudar Nome do App

**Arquivo:** `app/src/main/res/values/strings.xml`

```xml
<!-- De: -->
<string name="app_name">MyD Bar & Restaurantes</string>

<!-- Para: -->
<string name="app_name">Seu Nome Aqui</string>
```

### Mudar Cores

**Arquivo:** `app/src/main/res/values/colors.xml`

```xml
<!-- Mudar cor primária -->
<color name="primary_red">#EA1D2C</color>

<!-- Exemplos de cores: -->
<!-- Azul: #2196F3 -->
<!-- Verde: #4CAF50 -->
<!-- Laranja: #FF9800 -->
```

### Mudar Ícone do App

Você pode trocar as imagens em:
```
app/src/main/res/mipmap-*/ic_launcher.png
```

Use um editor de imagens (Photoshop, GIMP, ou online)

---

## 🎯 PRÓXIMO PASSO 5: Compilar APK para Distribuição

### Debug APK (Para Teste)

```powershell
cd c:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp
./gradlew assembleDebug
```

**Output:** `app/build/outputs/apk/debug/app-debug.apk`

**Como usar:**
- Envie para amigos via WhatsApp
- Eles clicam no arquivo
- Android pede permissão para instalar
- Clicam em "Instalar"

### Release APK (Para Play Store)

```powershell
./gradlew assembleRelease
```

⚠️ **Nota:** Precisa assinar com keystore
(Explicado abaixo)

---

## 🎯 PRÓXIMO PASSO 6: Assinar APK para Play Store

### Criar Keystore (Uma única vez)

```powershell
# Execute no PowerShell
keytool -genkey -v -keystore my-release-key.jks `
  -keyalg RSA -keysize 2048 -validity 10000 `
  -alias my-key-alias
```

**Responda as perguntas:**
- Password: `sua_senha_forte`
- Nome: Seu Nome
- Organização: Sua Empresa
- Cidade: Sua Cidade
- Estado: UF
- País: BR

### Configurar Assinatura no build.gradle

**Arquivo:** `app/build.gradle`

Adicione antes de `buildTypes`:

```gradle
signingConfigs {
    release {
        storeFile file("../my-release-key.jks")
        storePassword "sua_senha_forte"
        keyAlias "my-key-alias"
        keyPassword "sua_senha_forte"
    }
}

buildTypes {
    release {
        signingConfig signingConfigs.release
        minifyEnabled false
        proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
    }
}
```

### Compilar Release APK Assinado

```powershell
./gradlew assembleRelease
```

**Output:** `app/build/outputs/apk/release/app-release.apk`

---

## 🎯 PRÓXIMO PASSO 7: Publicar na Play Store

### Requisitos:
- ✅ Conta Google Play Developer ($25 única vez)
- ✅ APK assinado (v1.0.0+)
- ✅ Screenshots (4-6)
- ✅ Descrição do app
- ✅ Política de privacidade

### Processo:

1. Acesse [Google Play Console](https://play.google.com/console)
2. Clique em **"Create App"**
3. Preencha:
   - Nome: MyD Bar & Restaurantes
   - Categoria: Comida e Bebida
   - Descrição: "App de delivery com rastreamento em tempo real"
4. Suba o APK assinado
5. Preencha screenshots e informações
6. Clique em **"Submit for Review"**
7. Aguarde 1-3 horas para revisão
8. Se aprovado, fica disponível na Play Store! 🎉

---

## 🐛 Troubleshooting Comum

### "App conecta em localhost mas dá erro"

**Se estiver usando emulador:**

1. Abra `MainActivity.kt`
2. Mude a linha 74 para:
```kotlin
webView.loadUrl("http://10.0.2.2/app-cliente/")
// 10.0.2.2 é como o emulador acessa localhost do PC
```

3. Compile novamente (Run)

### "Gradle build failed"

```powershell
# Execute:
./gradlew clean
./gradlew build

# Se ainda falhar:
File → Invalidate Caches → Restart
```

### "Cannot find device"

1. Tools → Device Manager
2. Clique no botão ▶️ para iniciar emulador
3. Aguarde completar o boot
4. Run novamente

### "Permissões negadas"

Android 6+ pede permissões em runtime:
- Clique em **"Permitir"** quando o app pedir
- Permissões já estão no AndroidManifest.xml

---

## 📊 Progresso Esperado

```
□ 1. Abrir em Android Studio         ← Você está aqui
□ 2. Criar emulador/conectar device  ← PRÓXIMO
□ 3. Executar app (Run)              ← PRÓXIMO
□ 4. Testar funcionalidades          ← PRÓXIMO
□ 5. Customizar (cores, nome, etc)   ← DEPOIS
□ 6. Compilar debug APK              ← DEPOIS
□ 7. Compilar release APK            ← DEPOIS
□ 8. Publicar na Play Store          ← FINAL
```

---

## ⚡ Comandos Rápidos

```powershell
# Abrir projeto
cd c:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp

# Compilar apenas (sem instalar)
./gradlew build

# Compilar e instalar debug
./gradlew installDebug

# Compilar debug APK
./gradlew assembleDebug

# Compilar release APK
./gradlew assembleRelease

# Limpar build (se tiver erro)
./gradlew clean

# Ver versão do Gradle
./gradlew --version
```

---

## 📱 Resultado Final

**Após completar estes passos, você terá:**

✅ App funcionando em Android  
✅ APK pronto para distribuição  
✅ App instalável em Play Store  
✅ Usuários podem fazer pedidos  
✅ Rastreamento em tempo real  
✅ Vibração ao receber atualizações  

---

## 🎯 Recomendação: Comece por aqui

**1. AGORA MESMO:**
- Crie um emulador (Pixel 4, API 34)
- Execute o app (Shift+F10)
- Teste as funcionalidades

**2. DEPOIS:**
- Customize cores/nome do app
- Compile debug APK
- Envie para amigos testarem

**3. FINAL:**
- Compile release APK
- Assine com keystore
- Publique na Play Store

---

**Próximo passo?** Crie o emulador agora! 🚀

Tools → Device Manager → Create Device

Aproveite! 🎉
