# 🎛️ Painel Carla IA - Acesso Admin

## 🔐 Acesso Restrito

O Painel da Carla está integrado ao sistema administrativo e possui **acesso restrito apenas para EATSFOOD**.

### 📍 URL de Acesso
```
http://localhost:8000/admin/carla
```

### 🔑 Credenciais Necessárias
- **Guard**: `admin`
- **Tenant**: `EATSFOOD` (exclusivo)
- **Nível**: Master Admin

### 🚫 Proteção de Segurança
```php
// Só permite acesso se:
1. Está logado como admin
2. tenant_code === 'EATSFOOD'
3. Caso contrário, redireciona com erro
```

---

## 📱 Como Acessar

### Passo 1: Fazer Login
```
1. Acesse: http://localhost:8000/admin/login
2. Use credenciais de admin EATSFOOD
3. Será redirecionado para Dashboard Master
```

### Passo 2: Acessar Painel Carla
```
1. No menu lateral, procure seção "Inteligência Artificial"
2. Clique em "Carla IA" (ícone de robô)
3. Ou acesse diretamente: http://localhost:8000/admin/carla
```

---

## 🎨 Layout do Painel

### Integração com Admin Layout
- Usa `layouts.admin` (mesmo layout do dashboard)
- Menu lateral fixo
- Navbar superior com logout
- Estilo consistente com Bootstrap 5
- Ícones Bootstrap Icons

### Menu Lateral
```
Dashboard
━━━━━━━━━━━━━━━━━
GESTÃO DE TENANTS
  └─ Empresas
  └─ Admins

COMERCIAL
  └─ Planos
  └─ Contratos
  └─ Financeiro

RH & COMISSÕES
  └─ Cargos
  └─ Funcionários
  └─ Comissões
  └─ Bônus

INTELIGÊNCIA ARTIFICIAL ⭐ NOVO
  └─ Carla IA 🤖
```

---

## 🎯 Funcionalidades Disponíveis

### 1. **Dashboard Visual**
- 4 cards de estatísticas principais
- Gráficos de performance em tempo real
- Status do treinamento
- Estrutura da rede neural

### 2. **Ações Rápidas**
- ✅ Treinar com Histórico (backpropagation)
- ✅ Adicionar Novo Contexto (modal)
- ✅ Atualizar Estatísticas (refresh)

### 3. **Tabelas Interativas**
- 📋 Lista completa de contextos ativos
- 💬 Últimas 10 interações
- 🧠 Visualização de neurônios por camada

### 4. **Auto-Refresh**
- Atualiza a cada 30 segundos automaticamente
- Não precisa recarregar a página

---

## 🔧 API Endpoints Utilizados

O painel consome as seguintes rotas:

```javascript
GET  /api/ai/stats      → Estatísticas gerais
GET  /api/ai/contexts   → Lista de contextos
POST /api/ai/train      → Treinar com histórico
POST /api/ai/contexts   → Adicionar contexto
```

**CSRF Token**: Incluído automaticamente via Blade `{{ csrf_token() }}`

---

## 📊 Métricas Exibidas

### Cards Principais
1. **Neurônios**: Total de neurônios (170)
2. **Sinapses**: Total de conexões (6.000)
3. **Contextos**: Conhecimento ativo (31+)
4. **Interações**: Conversas processadas (90+)

### Indicadores de Performance
- **Taxa de Acerto**: % de respostas corretas
  - 🟢 90%+ = Excelente
  - 🔵 70-89% = Bom
  - 🟡 50-69% = Regular
  - 🔴 <50% = Crítico

- **Confiança Média**: Segurança das respostas (0-100%)
- **Taxa de Sucesso (Contextos)**: % contextos funcionando bem

### Treinamento
- **Dados Treinados**: Interações já processadas
- **Pendentes**: Aguardando backpropagation
- **Atualizações**: Total de ajustes em sinapses
- **Peso Médio**: Valor médio das conexões

---

## 🎓 Como Usar o Painel

### Treinar a Carla
```
1. Clique em "Treinar com Histórico"
2. Confirme a ação no popup
3. Aguarde processamento (10-30 seg)
4. Veja resultado e atualização automática
```

### Adicionar Novo Contexto
```
1. Clique em "Adicionar Contexto"
2. Preencha o formulário:
   - Categoria (greeting, search, menu...)
   - Key (identificador único)
   - Padrão (regex com wildcards)
   - Resposta da Carla
   - Ação (opcional)
   - Limiar de confiança (0.7 padrão)
3. Clique "Salvar Contexto"
4. Veja na tabela imediatamente
```

### Monitorar Performance
```
1. Observe as barras de progresso
2. Se Taxa de Acerto < 85%, treine!
3. Se Pendentes > 50, treine!
4. Revise contextos com Taxa Sucesso < 70%
```

---

## 🚀 Workflow Recomendado

### Diário (5 min)
1. ✅ Acesse o painel
2. ✅ Verifique Taxa de Acerto
3. ✅ Se baixa, clique "Treinar"
4. ✅ Revise últimas interações

### Semanal (15 min)
1. ✅ Analise tabela de contextos
2. ✅ Identifique padrões não usados
3. ✅ Adicione novos contextos
4. ✅ Treine novamente

### Mensal (30 min)
1. ✅ Revise todos os contextos
2. ✅ Ajuste padrões com baixa taxa
3. ✅ Documente melhorias
4. ✅ Planeje novos recursos

---

## 🔧 Troubleshooting

### Erro: "Acesso negado ao Painel IA"
**Causa**: Usuário não é EATSFOOD  
**Solução**: Fazer login com credenciais de admin EATSFOOD

### Erro: "Token CSRF inválido"
**Causa**: Sessão expirada  
**Solução**: Recarregar página e fazer login novamente

### Dados não carregam
**Causa**: API não está respondendo  
**Solução**: 
1. Verificar se servidor Laravel está rodando
2. Verificar rotas `/api/ai/*` no arquivo `routes/api.php`
3. Conferir logs em `storage/logs/laravel.log`

### Modal não abre
**Causa**: Bootstrap JS não carregado  
**Solução**: Limpar cache do navegador (Ctrl+F5)

---

## 📝 Diferenças vs Versão HTML Pública

| Recurso | HTML Público | Blade Admin |
|---------|-------------|-------------|
| Layout | Standalone | Integrado ao admin |
| Autenticação | Nenhuma | Guard admin + tenant |
| Menu | Nenhum | Sidebar completo |
| CSRF | Manual | Automático (Blade) |
| Acesso | Qualquer um | EATSFOOD apenas |
| URL | `/admin-carla.html` | `/admin/carla` |

---

## 🎯 Próximos Passos

### Melhorias Futuras
- [ ] Gráfico de evolução de taxa de acerto
- [ ] Exportar relatório em PDF
- [ ] Notificações quando taxa cai
- [ ] Dashboard comparativo (mês a mês)
- [ ] Editar contextos existentes
- [ ] Deletar contextos inativos
- [ ] Filtros avançados na tabela
- [ ] Busca por contexto/padrão

---

## 📚 Documentação Relacionada

- **Guia Treinamento**: `GUIA_TREINAMENTO_CARLA.md`
- **Painel HTML**: `PAINEL_ADMIN_CARLA.md`
- **API Docs**: `IA_PROPRIA_DOCUMENTACAO.md`
- **Apresentação**: `CARLA_APRESENTACAO.md`

---

**✅ Pronto! Agora você tem o Painel da Carla integrado ao sistema admin!**

Acesse: `http://localhost:8000/admin/carla` após fazer login como admin EATSFOOD.
