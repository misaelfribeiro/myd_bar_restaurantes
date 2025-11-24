# 🍕 MyD Bar & Restaurantes - Delivery App

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](IMPLEMENTATION_SUMMARY.md)
[![Status](https://img.shields.io/badge/status-Production%20Ready-brightgreen.svg)](#)
[![PWA](https://img.shields.io/badge/PWA-Enabled-blueviolet.svg)](#pwa-progressive-web-app)

Um aplicativo moderno de delivery com design iFood-style, rastreamento em tempo real e instalação nativa em Android/iOS.

## 🚀 Quick Start (30 segundos)

```bash
# 1. Abra em qualquer navegador Chrome
http://localhost/app-cliente/

# 2. Aguarde 10 segundos

# 3. Clique no ícone de instalação ⬇️

# 4. Pronto! 🎉
```

**Ou visite**: http://localhost/quick-start.html

## ✨ Características

- 📱 **PWA Instalável** - Funciona como app nativo
- 🔄 **Rastreamento Real-time** - Atualizações a cada 5 segundos
- 📵 **Funciona Offline** - Cache automático com Service Worker
- 📳 **Vibração Móvel** - Feedback háptico em 7 padrões
- 🎨 **Design Moderno** - Inspirado em iFood, minimalista
- 🔐 **Autenticação Segura** - Login com Sanctum
- ⚡ **Performance** - Carregamento ultra-rápido
- 🌐 **Responsivo** - Adaptado para todos os tamanhos

## 📂 Estrutura do Projeto

```
myd_bar_restaurantes/
├── public/
│   ├── app-cliente/           # 👈 APP PRINCIPAL
│   │   ├── index.html
│   │   ├── manifest.json      # PWA Config
│   │   ├── service-worker.js  # Cache/Offline
│   │   ├── css/
│   │   │   └── app.css        # Design moderno
│   │   ├── js/
│   │   │   ├── orders.js      # Rastreamento ✨
│   │   │   ├── menu.js
│   │   │   ├── cart.js
│   │   │   ├── auth.js
│   │   │   └── ...
│   │   ├── icons/             # Ícones 192-512px
│   │   └── teste-pwa.html     # Diagnóstico
│   ├── quick-start.html       # Landing page
│   └── app-wrapper.html       # Alternativo
├── cordova-app/
│   └── MyDApp/                # Projeto Cordova (APK)
├── app/
│   ├── Models/
│   │   ├── Pedido.php
│   │   ├── Delivery.php
│   │   └── ...
│   ├── Http/Controllers/
│   │   └── Api/AppController.php
│   └── ...
├── routes/
│   ├── web.php
│   ├── api.php
│   └── ...
├── IMPLEMENTATION_SUMMARY.md  # Resumo técnico
├── APP_READY_TO_DEPLOY.md
└── README.md                  # Este arquivo
```

## 🏗️ Tecnologias Utilizadas

### Backend
- **Laravel 11** - Framework PHP
- **Eloquent ORM** - Database abstraction
- **Sanctum** - API authentication
- **MySQL** - Database

### Frontend
- **Vanilla JavaScript** - Sem frameworks
- **Bootstrap 5.3** - Responsive design
- **Font Awesome 6.4** - Icons
- **Service Worker API** - PWA offline
- **Vibration API** - Mobile haptics
- **Fetch API** - HTTP requests

### Mobile
- **Apache Cordova** - Cross-platform
- **Android SDK** - Native compilation
- **Gradle** - Build system

## 📲 Como Instalar

### 1️⃣ Como PWA (Recomendado)

**Chrome/Edge Android:**
1. Abra http://localhost/app-cliente/
2. Menu (⋮) → Instalar aplicativo
3. Confirme

**iPhone/Safari:**
1. Abra http://localhost/app-cliente/
2. Compartilhar (↗️) → Adicionar à Tela de Início
3. Confirme

### 2️⃣ Como APK Nativa

```bash
cd cordova-app/MyDApp
cordova build android
```

APK estará em: `platforms/android/app/build/outputs/apk/debug/app-debug.apk`

### 3️⃣ Via wrapper HTML

```
http://localhost/app-wrapper.html
```

## 🧪 Testes

### Verificar PWA
```
http://localhost/app-cliente/teste-pwa.html
```

### Testes Rápidos
```bash
node teste-rapido.js
```

### DevTools (F12)
- **Console** - Logs e erros
- **Application** - Service Worker, Cache, Storage
- **Network** - Requisições API

## 🔧 Configuração

### Variáveis de Ambiente

```bash
# .env
APP_URL=http://localhost
API_URL=http://localhost/api
```

### URLs Importantes

| Recurso | URL |
|---------|-----|
| App | http://localhost/app-cliente/ |
| PWA Test | http://localhost/app-cliente/teste-pwa.html |
| Quick Start | http://localhost/quick-start.html |
| API | http://localhost/api/ |
| Laravel Admin | http://localhost/admin |

## 📊 Status de Verificação

- [x] Service Worker registrado
- [x] Manifest válido
- [x] Ícones carregados
- [x] PWA instalável
- [x] Rastreamento 5s funciona
- [x] Vibração funciona
- [x] Offline cache funciona
- [x] API conecta
- [x] Autenticação funciona
- [x] Design responsivo

## 🚀 Deployment

### Para Produção

1. **Atualize URLs:**
   ```javascript
   // js/config.js
   const API_URL = 'https://seu-dominio.com/api';
   ```

2. **Configure HTTPS:**
   - Gere certificado SSL
   - Service Worker requer HTTPS

3. **Build APK:**
   ```bash
   cordova build android --release
   cordova build ios --release
   ```

4. **Deploy na Play Store:**
   - Crie conta de desenvolvedor
   - Faça upload da APK assinada
   - Preencha metadados

## 📞 Troubleshooting

### Service Worker não registra?
```javascript
// DevTools → Application → Clear Storage
// Limpe: Cache Storage, IndexedDB, Cookies
// Recarregue: Ctrl + F5
```

### PWA não instala?
- Verifique HTTPS (ou localhost)
- Aguarde 30s navegando no app
- Abra DevTools → Application → Manifest

### App vai offline?
- Verifique internet
- Revise URLs da API
- Veja console.log errors

## 📚 Documentação

- [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) - Resumo técnico
- [APP_READY_TO_DEPLOY.md](APP_READY_TO_DEPLOY.md) - Guia de deploy
- [PUBLIC_APK_INSTALLATION_GUIDE.md](PUBLIC_APK_INSTALLATION_GUIDE.md) - Guia de instalação
- [Código-fonte](app-cliente/js/orders.js) - Rastreamento em tempo real

## 🤝 Contribuição

Faça um fork, crie uma branch (`git checkout -b feature/AmazingFeature`) e envie um PR.

## 📄 Licença

Este projeto está sob a licença MIT. Veja [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Autor

**Misael Ribeiro**
- GitHub: [@misaelfribeiro](https://github.com/misaelfribeiro)
- Email: contato@myd.com.br

## 🙏 Agradecimentos

- Laravel Community
- Bootstrap Team
- Font Awesome
- Apache Cordova
- Chrome PWA Team

---

**Status**: ✅ Pronto para Produção  
**Última atualização**: 14/11/2025  
**Versão**: 1.0.0  

**Instale agora**: http://localhost/app-cliente/ 🚀
