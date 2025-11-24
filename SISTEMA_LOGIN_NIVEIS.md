# Sistema de Login com Níveis de Acesso

## 📋 Documentação Completa

### 🎯 Visão Geral

Sistema de autenticação e autorização baseado em roles (níveis de acesso) para o MyD Bar & Restaurantes. Implementado com Laravel Sanctum para autenticação API e interface moderna com Bootstrap 5.

### 🔐 Níveis de Acesso

#### 1. **Administrador** (`admin`)
- **Cor**: Vermelho (#dc3545)
- **Ícone**: fas fa-user-shield
- **Permissões**:
  - ✅ Acesso total ao sistema
  - ✅ Gerenciar todos os usuários
  - ✅ Gerenciar produtos e categorias
  - ✅ Visualizar e gerenciar todos os pedidos
  - ✅ Acesso a todos os relatórios
  - ✅ Gerenciar mesas e configurações
  - ✅ Gerenciar caixa e pagamentos
  - ✅ Configurações do sistema

**Módulos Disponíveis**:
- Usuários
- Produtos
- Pedidos
- Relatórios
- Caixa
- Configurações

#### 2. **Gerente** (`gerente`)
- **Cor**: Amarelo (#ffc107)
- **Ícone**: fas fa-user-tie
- **Permissões**:
  - ✅ Gerenciar produtos e categorias
  - ✅ Visualizar todos os pedidos
  - ✅ Gerenciar mesas
  - ✅ Acessar relatórios gerenciais
  - ✅ Gerenciar garçons
  - ✅ Aprovar descontos especiais

**Módulos Disponíveis**:
- Produtos
- Pedidos
- Mesas
- Relatórios

#### 3. **Garçom** (`garcom`)
- **Cor**: Verde (#28a745)
- **Ícone**: fas fa-concierge-bell
- **Permissões**:
  - ✅ Criar e gerenciar pedidos
  - ✅ Visualizar produtos disponíveis
  - ✅ Consultar mesas
  - ✅ Atualizar status de pedidos
  - ✅ Adicionar observações aos pedidos

**Módulos Disponíveis**:
- Pedidos
- Cardápio
- Mesas

#### 4. **Caixa** (`caixa`)
- **Cor**: Azul (#17a2b8)
- **Ícone**: fas fa-cash-register
- **Permissões**:
  - ✅ Processar pagamentos
  - ✅ Visualizar pedidos finalizados
  - ✅ Abrir e fechar caixa
  - ✅ Gerar relatórios de caixa
  - ✅ Aplicar descontos autorizados

**Módulos Disponíveis**:
- Caixa
- Pedidos (para pagamento)
- Relatório de Caixa

#### 5. **Cliente** (`cliente`)
- **Permissões**:
  - ✅ Visualizar cardápio
  - ✅ Ver status do pedido
  - ✅ Solicitar conta

**Módulos Disponíveis**:
- Cardápio
- Meu Pedido

---

## 🚀 Arquivos Criados

### 1. `resources/views/login-niveis.blade.php`
**Interface de Login Moderna**

**Características**:
- ✨ Design moderno com gradiente roxo
- 📱 Totalmente responsivo
- 🎨 Layout dividido em dois painéis
- 🔐 Formulário de login à esquerda
- 📊 Informações de níveis de acesso à direita
- 🚀 Login rápido com usuários demo
- ✅ Validação em tempo real
- 🎯 Redirecionamento automático baseado em role

**Funcionalidades**:
```javascript
- Login via API /api/auth/login
- Salvamento de token no localStorage
- Exibição de alertas com Bootstrap
- Usuários demo para teste rápido:
  * admin@exemplo.com / 123456
  * gerente@exemplo.com / 123456
  * garcom@exemplo.com / 123456
  * caixa@exemplo.com / 123456
```

**URL**: `http://localhost:8000/login-niveis`

### 2. `resources/views/dashboard-niveis.blade.php`
**Dashboard com Controle de Acesso**

**Características**:
- 🎨 Navbar personalizada com gradiente
- 👤 Badge de role no menu do usuário
- 📊 Cards de acesso baseados em permissões
- 📋 Lista completa de permissões do usuário
- 🔒 Menu dinâmico baseado no nível de acesso
- 🎯 Navegação contextual

**Funcionalidades**:
```javascript
- Carrega dados do usuário do localStorage
- Exibe apenas módulos permitidos
- Mostra permissões específicas do role
- Menu responsivo com dropdown
- Logout com revogação de token
```

**URL**: `http://localhost:8000/dashboard-niveis`

### 3. Rotas Adicionadas (`routes/web.php`)
```php
// Login com níveis de acesso
Route::get('/login-niveis', function () {
    return view('login-niveis');
});

// Dashboard com níveis de acesso
Route::get('/dashboard-niveis', function () {
    return view('dashboard-niveis');
});
```

---

## 🔧 Como Usar

### 1. **Acessar a Página de Login**
```
http://localhost:8000/login-niveis
```

### 2. **Fazer Login**

**Opção A: Login Manual**
1. Digite email e senha
2. Clique em "Entrar"
3. Será redirecionado automaticamente

**Opção B: Login Rápido (Demo)**
1. Clique em "Usar Usuários Demo"
2. Escolha um dos 4 níveis:
   - Admin (vermelho)
   - Gerente (amarelo)
   - Garçom (verde)
   - Caixa (azul)
3. Login automático

### 3. **Redirecionamento Automático**
Após login bem-sucedido, você será redirecionado para:
```javascript
admin → /dashboard
gerente → /dashboard
garcom → /garcom/dashboard
caixa → /caixa
cliente → /cardapio
```

### 4. **Ver Permissões**
No dashboard (`/dashboard-niveis`):
- ✅ Visualize todas as suas permissões
- ✅ Veja os módulos que você pode acessar
- ✅ Navegue pelos cards de acesso

---

## 🎨 Design e Interface

### Cores dos Níveis
```css
--admin-color: #dc3545     (Vermelho)
--gerente-color: #ffc107   (Amarelo)
--garcom-color: #28a745    (Verde)
--caixa-color: #17a2b8     (Azul)
```

### Gradiente Principal
```css
--primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%)
```

### Componentes Visuais
- 🎴 Cards com hover effect e elevação
- 🏷️ Badges coloridos por role
- 📊 Layout responsivo com Bootstrap 5
- 🎭 Ícones Font Awesome 6.4.0
- 🌈 Animações suaves de transição

---

## 🔐 Segurança e Autenticação

### Laravel Sanctum
```php
// API de Autenticação
POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout
GET  /api/auth/me
POST /api/auth/refresh
```

### Middleware de Roles
```php
// app/Http/Middleware/CheckRole.php
- Verifica se usuário tem permissão
- Retorna 403 se acesso negado
- Informa roles necessários
```

### Proteção de Rotas API
```php
// routes/api.php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function() {
    // Rotas apenas para admin
});

Route::middleware(['auth:sanctum', 'role:admin,gerente'])->group(function() {
    // Rotas para admin e gerente
});
```

---

## 📊 Fluxo de Autenticação

```
1. Usuário acessa /login-niveis
   ↓
2. Insere credenciais
   ↓
3. JavaScript faz POST para /api/auth/login
   ↓
4. Laravel valida com AuthController
   ↓
5. Sanctum gera token de acesso
   ↓
6. Token e dados do usuário salvos no localStorage
   ↓
7. Redirecionamento baseado no role
   ↓
8. Dashboard carrega com permissões específicas
```

---

## 🧪 Testando o Sistema

### 1. **Testar Login de Admin**
```javascript
Email: admin@exemplo.com
Senha: 123456
Resultado: Acesso total ao sistema
```

### 2. **Testar Login de Gerente**
```javascript
Email: gerente@exemplo.com
Senha: 123456
Resultado: Acesso a produtos, pedidos, mesas e relatórios
```

### 3. **Testar Login de Garçom**
```javascript
Email: garcom@exemplo.com
Senha: 123456
Resultado: Acesso a pedidos, cardápio e mesas
```

### 4. **Testar Login de Caixa**
```javascript
Email: caixa@exemplo.com
Senha: 123456
Resultado: Acesso a caixa, pagamentos e relatórios financeiros
```

### 5. **Verificar Permissões**
- ✅ Admin vê 6 módulos
- ✅ Gerente vê 4 módulos
- ✅ Garçom vê 3 módulos
- ✅ Caixa vê 3 módulos

---

## 🔄 Integração com Sistema Existente

### Para Substituir Login Padrão
1. Atualize a rota principal:
```php
Route::get('/login', function () {
    return view('login-niveis');
});
```

2. Atualize redirects após autenticação em Controllers
3. Ajuste as rotas de dashboard conforme necessário

### Para Usar em Produção
1. ✅ Remova ou proteja usuários demo
2. ✅ Configure variáveis de ambiente
3. ✅ Teste todas as permissões
4. ✅ Configure CORS se necessário
5. ✅ Implemente rate limiting

---

## 📱 Responsividade

### Mobile (< 768px)
- ✅ Layout em coluna única
- ✅ Menu hamburger
- ✅ Cards em 100% da largura
- ✅ Informações de roles acima do formulário

### Tablet (768px - 1024px)
- ✅ Cards em 2 colunas
- ✅ Menu completo
- ✅ Sidebar colapsável

### Desktop (> 1024px)
- ✅ Layout completo em 2 painéis
- ✅ Cards em 3 colunas
- ✅ Todas as funcionalidades visíveis

---

## 🎯 Próximos Passos

### Melhorias Sugeridas
1. **Registro de Usuários**
   - Adicionar formulário de registro
   - Validação de email único
   - Confirmação de senha

2. **Recuperação de Senha**
   - Link "Esqueceu sua senha?"
   - Email de recuperação
   - Reset de senha seguro

3. **Perfil do Usuário**
   - Página de edição de perfil
   - Upload de foto
   - Alteração de senha

4. **Auditoria**
   - Log de acessos (já implementado no AccessLog)
   - Histórico de ações
   - Relatório de segurança

5. **Two-Factor Authentication (2FA)**
   - Autenticação em duas etapas
   - Códigos via email/SMS
   - Aplicativo autenticador

---

## 📚 Referências

- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3)
- [Font Awesome Icons](https://fontawesome.com/icons)
- [MDN Web Docs - Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)

---

## ✅ Checklist de Implementação

- [x] Interface de login moderna
- [x] Dashboard com níveis de acesso
- [x] Sistema de roles (admin, gerente, garcom, caixa)
- [x] Middleware de verificação de roles
- [x] Redirecionamento baseado em permissões
- [x] Menu dinâmico por role
- [x] Badges visuais de identificação
- [x] Login rápido com usuários demo
- [x] Responsive design
- [x] Integração com API Sanctum
- [x] Documentação completa

---

## 🎉 Conclusão

O sistema de login com níveis de acesso está **totalmente implementado e funcional**!

### Acesse agora:
- 🔐 Login: `http://localhost:8000/login-niveis`
- 📊 Dashboard: `http://localhost:8000/dashboard-niveis`

### Para testar:
1. Inicie o servidor Laravel: `php artisan serve`
2. Acesse o login
3. Use um dos usuários demo
4. Explore as diferentes permissões!
