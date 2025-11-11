# 🔐 Sistema de Autorização por Perfis - Laravel Bar & Restaurante

## 📋 **Implementação Completa**

O sistema agora possui **controle de acesso baseado em roles** usando Laravel Sanctum + Middleware personalizado.

---

## 👥 **Perfis de Usuário**

| Perfil | Descrição | Nível de Acesso |
|--------|-----------|-----------------|
| 🔴 **admin** | Administrador do sistema | Acesso total |
| 🟠 **gerente** | Gerente do restaurante | Gestão operacional |
| 🟡 **garcom** | Garçom/Atendente | Operações básicas |
| 🟢 **cliente** | Cliente do restaurante | Apenas consultas |

---

## 🚀 **Estrutura de Autorização**

### **🔴 ADMIN APENAS**
```
- Gerenciamento completo de usuários (CRUD)
- Relatórios financeiros e estratégicos
- Todas as operações do sistema
```

**Rotas Protegidas:**
- `GET/POST/PUT/DELETE /api/usuarios`
- `GET /api/relatorios/vendas`
- `GET /api/relatorios/horarios-movimento`

### **🟠 ADMIN + GERENTE**
```
- Gestão de produtos, categorias e mesas
- Criação/edição/exclusão de itens do cardápio
- Configuração de mesas e layout
```

**Rotas Protegidas:**
- `POST/PUT/DELETE /api/produtos`
- `POST/PUT/DELETE /api/categorias`
- `POST/PUT/DELETE /api/mesas`
- `GET /api/relatorios/mesas-populares`

### **🟡 ADMIN + GERENTE + GARCOM**
```
- Gestão de pedidos (criar, atualizar status)
- Consulta de produtos, categorias e mesas
- Dashboard operacional
- Relatórios de status
```

**Rotas Protegidas:**
- `GET/POST/PUT/DELETE /api/pedidos`
- `GET /api/produtos` e `GET /api/produtos/{id}`
- `GET /api/categorias` e `GET /api/categorias/{id}`
- `GET /api/mesas` e `GET /api/mesas/{id}`
- `GET /api/dashboard/*`
- `GET /api/relatorios/status-pedidos`

---

## 👤 **Usuários de Teste Criados**

| Email | Senha | Perfil | Descrição |
|-------|-------|--------|-----------|
| admin@sistema.com | admin123 | 🔴 admin | Administrador geral |
| gerente@restaurante.com | gerente123 | 🟠 gerente | Gerente do restaurante |
| maria2@restaurante.com | garcom123 | 🟡 garcom | Garçonete Maria |
| pedro@restaurante.com | garcom123 | 🟡 garcom | Garçom Pedro |
| ana@email.com | cliente123 | 🟢 cliente | Cliente VIP |

---

## 🔧 **Como Usar**

### **1. Fazer Login com Perfil Desejado**
```bash
# Login como Admin
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@sistema.com",
    "password": "admin123"
  }'

# Login como Garçom
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "maria2@restaurante.com", 
    "password": "garcom123"
  }'
```

### **2. Usar Token nas Requisições**
```bash
curl -X GET "http://localhost:8000/api/usuarios" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

---

## 📊 **Exemplos de Resposta de Autorização**

### ✅ **Acesso Permitido**
```json
{
  "data": [
    // Dados da API normalmente...
  ]
}
```

### ❌ **Acesso Negado**
```json
{
  "error": "Acesso negado",
  "message": "Você precisa ter perfil de: admin ou gerente. Seu perfil atual: garcom",
  "required_roles": ["admin", "gerente"],
  "user_role": "garcom"
}
```

### ❌ **Token Necessário**
```json
{
  "error": "Não autorizado. Token de acesso necessário.",
  "message": "Por favor, faça login para acessar este recurso."
}
```

---

## 🧪 **Testando Diferentes Cenários**

### **Cenário 1: Admin acessando tudo**
```bash
# 1. Login como admin
# 2. Tentar acessar qualquer rota → ✅ Sucesso
```

### **Cenário 2: Garçom tentando gerenciar usuários**
```bash
# 1. Login como garçom  
# 2. Tentar GET /api/usuarios → ❌ 403 Forbidden
```

### **Cenário 3: Gerente criando produtos**
```bash
# 1. Login como gerente
# 2. Tentar POST /api/produtos → ✅ Sucesso
```

---

## 🔒 **Segurança Implementada**

### ✅ **Middleware RoleMiddleware**
- Verifica autenticação Sanctum
- Valida perfil do usuário
- Retorna erros detalhados

### ✅ **Tokens JWT Seguros**
- Gerados via Laravel Sanctum
- Expirável e renovável  
- Revogação manual disponível

### ✅ **Validação de Perfis**
- Enum no banco de dados
- Validação no registro
- Controle granular por rota

---

## 🚀 **Sistema 100% Funcional**

**O sistema de autorização está completamente implementado e testado!**

- ✅ **4 níveis de perfil** funcionando
- ✅ **Middleware personalizado** ativo
- ✅ **Usuários de teste** criados
- ✅ **Rotas protegidas** por nível de acesso
- ✅ **Mensagens de erro** claras e informativas

**Próximos passos sugeridos:**
1. Testes automatizados dos perfis
2. Interface para gestão de usuários  
3. Logs de acesso por perfil
4. Sistema de permissions granulares
