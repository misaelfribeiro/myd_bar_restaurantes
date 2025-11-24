# 📱 GUIA DE INSTALAÇÃO - MyD Bar & Restaurantes

## ✅ OPÇÃO 1: Instalar como PWA (Mais Fácil e Rápido) ⭐ RECOMENDADO

### No Google Chrome/Android:
1. Abra: **http://localhost/app-cliente/**
2. Aguarde 5-10 segundos navegando no app
3. Você verá um ícone de **instalação (⬇️ + ⊕)** na barra de endereço
4. Clique nele
5. Selecione "Instalar"
6. Pronto! O app está instalado 🎉

### No iPhone/Safari:
1. Abra: **http://localhost/app-cliente/**
2. Clique no ícone de **compartilhamento** (↗️)
3. Selecione "Adicionar à Tela de Início"
4. Escolha um nome e confirme

---

## 🤖 OPÇÃO 2: Instalar APK nativa (Requer Java 11+)

### Passos:
```bash
cd C:\xampp\htdocs\myd_bar_restaurantes\cordova-app\MyDApp

# Configure Java 11+ e execute:
cordova build android
```

Após compilar, encontre a APK em:
```
C:\xampp\htdocs\myd_bar_restaurantes\cordova-app\MyDApp\platforms\android\app\build\outputs\apk\debug\app-debug.apk
```

Instale no Android:
- Copie o arquivo para o celular
- Abra o arquivo e confirme a instalação

---

## 🌐 OPÇÃO 3: Usar myd.local com HTTPS

Se quiser usar `myd.local` em vez de `localhost`:

1. Gere certificado SSL para XAMPP
2. Configure HTTPS no Apache
3. Service Worker funcionará em `myd.local`

---

## ✨ Características da PWA

- ✅ Funciona offline (com cache)
- ✅ Ícone na home screen
- ✅ Notificações em tempo real
- ✅ Vibração ao receber atualizações
- ✅ Acompanhamento de pedidos em tempo real
- ✅ Sem necessidade de download na Play Store

---

## 📲 URL para Acesso

### Desenvolvimento (Localhost):
- http://localhost/app-cliente/

### Com domínio local:
- http://myd.local/app-cliente/ (requer HTTPS)

---

## 🔧 Solução de Problemas

### Service Worker não registra?
- Limpe cache: Ctrl + F5
- Abra DevTools (F12) → Application → Clear Storage
- Recarregue

### Não consegue ver prompt de instalação?
- Aguarde 30 segundos navegando no app
- A PWA precisa de pelo menos 2 visitas
- Chrome deve ser versão 44+

### Ícone não aparece?
- Verifique em `http://localhost/app-cliente/icons/`
- Limpe cache do navegador

---

**Dúvidas?** Verifique a aba Application/Manifest no DevTools (F12)
