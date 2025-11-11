# 🔐 Autenticação JWT/Sanctum - Sistema Bar & Restaurante

## 📋 **Implementação Completa**

A autenticação foi implementada usando **Laravel Sanctum** para fornecer tokens de API seguros.

---

## 🚀 **Endpoints de Autenticação**

### **1. Registro de Usuário**
```http
POST /api/auth/register
Content-Type: application/json

{
    "nome": "Nome do Usuário",
    "email": "usuario@email.com",
    "password": "senha123",
    "password_confirmation": "senha123"
}
```

**Resposta de Sucesso (201):**
```json
{
    "message": "Usuário registrado com sucesso",
    "usuario": {
        "id": 1,
        "nome": "Nome do Usuário",
        "email": "usuario@email.com",
        "created_at": "2025-11-10T19:00:00.000000Z",
        "updated_at": "2025-11-10T19:00:00.000000Z"
    },
    "access_token": "1|abcdef123456789...",
    "token_type": "Bearer"
}
```

### **2. Login**
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "usuario@email.com",
    "password": "senha123"
}
```

**Resposta de Sucesso (200):**
```json
{
    "message": "Login realizado com sucesso",
    "usuario": {
        "id": 1,
        "nome": "Nome do Usuário",
        "email": "usuario@email.com"
    },
    "access_token": "2|xyz789456123...",
    "token_type": "Bearer"
}
```

### **3. Informações do Usuário Logado**
```http
GET /api/auth/me
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "usuario": {
        "id": 1,
        "nome": "Nome do Usuário",
        "email": "usuario@email.com"
    }
}
```

### **4. Logout**
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "message": "Logout realizado com sucesso"
}
```

### **5. Renovar Token**
```http
POST /api/auth/refresh
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "message": "Token renovado com sucesso",
    "access_token": "3|newtoken123...",
    "token_type": "Bearer"
}
```

### **6. Revogar Todos os Tokens**
```http
POST /api/auth/revoke-all
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "message": "Todos os tokens foram revogados"
}
```

---

## 🛡️ **Rotas Protegidas**

Todas as seguintes rotas **requerem autenticação**:

```http
Authorization: Bearer {seu_token_aqui}
```

### **APIs Protegidas:**
- `GET|POST|PUT|DELETE /api/produtos`
- `GET|POST|PUT|DELETE /api/categorias`
- `GET|POST|PUT|DELETE /api/pedidos`
- `GET|POST|PUT|DELETE /api/mesas`
- `GET|POST|PUT|DELETE /api/usuarios`

### **APIs Públicas (sem autenticação):**
- `GET /api/categorias-public` - Listar categorias
- `GET /api/produtos-public` - Listar produtos

---

## 🔧 **Como Usar**

### **1. Registrar-se ou Fazer Login**
```bash
# PowerShell Example
$body = '{
    "email": "admin@barrestaurante.com",
    "password": "senha123"
}' 

$response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/auth/login" -Method Post -Body $body -ContentType "application/json"
$token = $response.access_token
```

### **2. Usar o Token nas Requisições**
```bash
# PowerShell Example
$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}

$produtos = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/produtos" -Method Get -Headers $headers
```

### **3. Exemplo com cURL**
```bash
# Login
curl -X POST "http://127.0.0.1:8000/api/auth/login" \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@barrestaurante.com","password":"senha123"}'

# Usar token
curl -X GET "http://127.0.0.1:8000/api/produtos" \
     -H "Authorization: Bearer SEU_TOKEN_AQUI" \
     -H "Accept: application/json"
```

---

## 🔒 **Segurança Implementada**

### **Recursos de Segurança:**
- ✅ **Tokens únicos** por sessão
- ✅ **Hash bcrypt** para senhas
- ✅ **Revogação de tokens** no logout
- ✅ **Validação de email único**
- ✅ **Middleware de autenticação**
- ✅ **Tokens com escopo limitado**

### **Validações:**
- **Email**: Formato válido e único
- **Senha**: Mínimo 6 caracteres + confirmação
- **Nome**: Obrigatório, máximo 255 caracteres
- **Token**: Verificação automática em cada requisição

---

## 📱 **Integração Frontend**

### **JavaScript/Fetch Example:**
```javascript
// Login
const login = async (email, password) => {
    const response = await fetch('http://127.0.0.1:8000/api/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    localStorage.setItem('token', data.access_token);
    return data;
};

// Usar API protegida
const getProdutos = async () => {
    const token = localStorage.getItem('token');
    const response = await fetch('http://127.0.0.1:8000/api/produtos', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
};
```

### **React Example:**
```jsx
import axios from 'axios';

// Configurar interceptor
axios.defaults.baseURL = 'http://127.0.0.1:8000/api';
axios.defaults.headers.common['Accept'] = 'application/json';

// Interceptor para adicionar token
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Hook de autenticação
const useAuth = () => {
    const login = async (email, password) => {
        const { data } = await axios.post('/auth/login', { email, password });
        localStorage.setItem('token', data.access_token);
        return data;
    };
    
    const logout = async () => {
        await axios.post('/auth/logout');
        localStorage.removeItem('token');
    };
    
    return { login, logout };
};
```

---

## 🧪 **Testes de Validação**

### **Cenários Testados:**
- ✅ **Registro com dados válidos**
- ✅ **Login com credenciais corretas**
- ✅ **Acesso a rotas protegidas com token**
- ✅ **Bloqueio de acesso sem token (401)**
- ✅ **Funcionamento de rotas públicas**
- ✅ **Logout e revogação de token**
- ✅ **Renovação de token**

### **Casos de Erro:**
- ❌ **Email já registrado** (422)
- ❌ **Credenciais inválidas** (422)
- ❌ **Token inválido/expirado** (401)
- ❌ **Senha muito fraca** (422)
- ❌ **Campos obrigatórios** (422)

---

## 🔧 **Configurações Avançadas**

### **Personalizar Tempo de Expiração:**
```php
// config/sanctum.php
'expiration' => 60 * 24, // 24 horas
```

### **Configurar CORS:**
```php
// config/cors.php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'supports_credentials' => true,
```

### **Middleware Personalizado:**
```php
// Aplicar a grupos de rotas
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('usuarios', UsuarioController::class);
});
```

---

## ✅ **Status da Implementação**

### **Funcionalidades Completas:**
- 🔐 **Sistema de registro e login**
- 🎫 **Gerenciamento de tokens Sanctum**
- 🛡️ **Middleware de autenticação**
- 🔒 **Proteção de rotas sensíveis**
- 📱 **Compatibilidade com SPAs e Mobile**
- 🔄 **Renovação e revogação de tokens**

### **Próximos Passos (Opcional):**
1. **Sistema de Roles/Permissões**
2. **Autenticação por OAuth (Google, Facebook)**
3. **Rate limiting personalizado**
4. **Auditoria de login**
5. **2FA (Autenticação em duas etapas)**

---

**🎉 AUTENTICAÇÃO 100% FUNCIONAL!**

O sistema agora possui autenticação robusta e está pronto para uso em produção com APIs seguras.
