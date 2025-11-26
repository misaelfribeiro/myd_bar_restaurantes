# 💰 Sistema de Gestão Financeira - EATSFOOD

## 📋 Visão Geral

Sistema completo para gestão financeira da plataforma EATSFOOD, incluindo controle de pagamentos via Mercado Pago, dashboard administrativo, estornos, e gerenciamento de saques dos parceiros.

---

## 🎯 Componentes Implementados

### 1. **Controller - FinanceiroController**
Local: `app/Http/Controllers/Admin/FinanceiroController.php`

**Novos Métodos:**

#### `dashboardPagamentos()`
- Estatísticas do mês atual
- Total de transações e valores
- Cálculo de taxas (10% plataforma)
- Líquido para restaurantes
- Top 10 restaurantes por faturamento
- Breakdown de métodos de pagamento

#### `listarPagamentos(Request $request)`
- Lista paginada (50 registros por página)
- Filtros: status, tenant, método, data
- Inclui relacionamento com pedido
- Ordenação por data decrescente

#### `detalhesPagamento($id)`
- Informações completas do pagamento
- Dados do pedido relacionado
- Cálculo de taxas e valores líquidos
- Histórico de status

#### `estornarPagamento(Request $request, $id)`
- Validação de status (apenas aprovados)
- Integração com API Mercado Pago
- Atualização local do status
- Registro do motivo do estorno
- **NÃO altera status do pedido** (separação de responsabilidades)

#### `relatorioPagamentos(Request $request)`
- Relatórios por período
- Agregação por restaurante
- Totais, taxas e líquidos
- Filtros flexíveis

---

### 2. **Model - Withdrawal**
Local: `app/Models/Withdrawal.php`

**Atributos:**
- `tenant_code` - Código do restaurante
- `valor` - Valor solicitado (decimal 10,2)
- `status` - pendente, aprovado, recusado, processado
- `observacao` - Notas adicionais
- `metodo_pagamento` - transferencia ou pix
- `dados_bancarios` - JSON com informações bancárias
- `aprovado_por` - ID do admin que aprovou
- `data_solicitacao`, `data_aprovacao`, `data_processamento`
- `comprovante` - Path do arquivo

**Relacionamentos:**
- `empresa()` - belongsTo Empresa via tenant_code
- `aprovador()` - belongsTo User (admin)

---

### 3. **Migration - withdrawals**
Local: `database/migrations/2025_11_25_021628_create_withdrawals_table.php`

**Status:** ✅ Migrada

**Índices:**
- tenant_code
- status
- data_solicitacao

---

### 4. **Views**

#### Dashboard (`pagamentos-dashboard.blade.php`)
Local: `resources/views/admin/financeiro/pagamentos-dashboard.blade.php`

**Recursos:**
- 4 Cards de estatísticas principais
  - Total transações
  - Valor aprovado
  - Taxa plataforma (10%)
  - Líquido restaurantes
- Tabela Top 10 Restaurantes
  - Ranking por faturamento
  - Número de transações
  - Breakdown de valores
- Painel de Métodos de Pagamento
  - Barras de progresso
  - Percentual de uso
  - Totais por método
- Card de Pendentes
  - Contador
  - Link direto para filtro
- Links Rápidos
  - Todas transações
  - Apenas aprovadas
  - Estornos
  - Relatórios

#### Lista (`pagamentos-lista.blade.php`)
Local: `resources/views/admin/financeiro/pagamentos-lista.blade.php`

**Recursos:**
- Filtros avançados
  - Status (pending, approved, rejected, cancelled, refunded)
  - Restaurante (tenant_code)
  - Método de pagamento
  - Data inicial e final
- Tabela completa
  - ID, Data, Pedido
  - Restaurante (código + nome)
  - Método (badge colorido)
  - Valores (total, taxa, líquido)
  - Status (badge dinâmico)
  - Ações (visualizar, estornar)
- Modal de Detalhes
  - Informações completas
  - Timeline de eventos
  - Botão de estorno
- Paginação
  - Mantém filtros ao navegar
  - Informação de total de registros
- Confirmação de Estorno
  - SweetAlert2 com textarea
  - Validação de motivo obrigatório
  - Envio via formulário oculto

---

### 5. **Rotas**
Local: `routes/web.php`

```php
// Dashboard Financeiro
Route::get('/financeiro/pagamentos/dashboard', [FinanceiroController::class, 'dashboardPagamentos'])
    ->name('financeiro.pagamentos.dashboard');

// Lista de Transações
Route::get('/financeiro/pagamentos/lista', [FinanceiroController::class, 'listarPagamentos'])
    ->name('financeiro.pagamentos.lista');

// Detalhes de Pagamento
Route::get('/financeiro/pagamentos/{id}', [FinanceiroController::class, 'detalhesPagamento'])
    ->name('financeiro.pagamentos.detalhes');

// Estornar Pagamento
Route::post('/financeiro/pagamentos/{id}/estornar', [FinanceiroController::class, 'estornarPagamento'])
    ->name('financeiro.pagamentos.estornar');

// Relatórios
Route::get('/financeiro/pagamentos/relatorios', [FinanceiroController::class, 'relatorioPagamentos'])
    ->name('financeiro.pagamentos.relatorios');
```

**Proteção:** Todas as rotas estão dentro do grupo `admin` com middleware de autenticação.

---

### 6. **Menu Admin**
Local: `resources/views/layouts/admin.blade.php`

**Adicionado:**
```html
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.financeiro.pagamentos.*') ? 'active' : '' }}" 
       href="{{ route('admin.financeiro.pagamentos.dashboard') }}">
        <i class="bi bi-credit-card"></i> Pagamentos
        @php
            $pendentes = \App\Models\Payment::where('status', 'pending')->count();
        @endphp
        @if($pendentes > 0)
            <span class="badge badge-warning ml-2">{{ $pendentes }}</span>
        @endif
    </a>
</li>
```

**Recursos:**
- Ícone de cartão de crédito
- Badge dinâmico mostrando pagamentos pendentes
- Highlighting quando na seção de pagamentos
- Separado do item "Faturas" (antigas cobranças de plano)

---

## 🗄️ Estrutura do Banco de Dados

### Tabela: `payments`
**Propósito:** Controle financeiro de transações

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| id | bigint | Identificador único |
| pedido_id | bigint | FK para pedidos |
| numero_pedido | string | Número visível do pedido |
| tenant_code | string | Código do restaurante |
| mp_payment_id | bigint | ID no Mercado Pago |
| mp_preference_id | string | ID da preferência MP |
| payment_method | string | pix, credit_card, debit_card |
| status | enum | pending, approved, rejected, cancelled, refunded |
| amount | decimal(10,2) | Valor total |
| platform_fee | decimal(10,2) | Taxa plataforma (10%) |
| gateway_fee | decimal(10,2) | Taxa gateway |
| net_amount | decimal(10,2) | Líquido para restaurante |
| pix_qr_code | text | QR code base64 |
| pix_qr_code_url | text | URL do QR code |
| pix_copy_paste | text | Código PIX copia/cola |
| paid_at | timestamp | Data de pagamento |
| expires_at | timestamp | Expiração (30min) |
| mp_response | text | Resposta JSON do MP |
| refund_reason | text | Motivo do estorno |

**Índices:**
- mp_payment_id (unique)
- status
- tenant_code

---

### Tabela: `withdrawals`
**Propósito:** Solicitações de saque dos parceiros

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| id | bigint | Identificador único |
| tenant_code | string | Código do restaurante |
| valor | decimal(10,2) | Valor solicitado |
| status | enum | pendente, aprovado, recusado, processado |
| observacao | text | Notas adicionais |
| metodo_pagamento | string | transferencia, pix |
| dados_bancarios | json | Banco, agência, conta, PIX key |
| aprovado_por | bigint | FK users (admin) |
| data_solicitacao | timestamp | Data do pedido |
| data_aprovacao | timestamp | Data da aprovação |
| data_processamento | timestamp | Data do processamento |
| comprovante | string | Path do arquivo |

**Índices:**
- tenant_code
- status
- data_solicitacao

---

## 🔐 Arquitetura de Segurança

### Separação de Responsabilidades

#### Tabela `pedidos` - Operacional
- Gerencia o fluxo do pedido
- Estados: aberto → em_preparo → pronto → entregue
- **NÃO controla dinheiro**

#### Tabela `payments` - Financeira
- Registra transações
- Estados: pending → approved/rejected/cancelled/refunded
- **NÃO altera status operacional**

### Motivo da Separação
- ✅ Flexibilidade para resolver problemas
- ✅ Estornar pagamento sem cancelar pedido
- ✅ Cancelar pedido sem afetar pagamento
- ✅ Auditoria independente
- ✅ Escalabilidade (múltiplos pagamentos por pedido)

---

## 💼 Funcionalidades por Perfil

### Admin (EATSFOOD)
✅ Ver todas as transações de todos os restaurantes  
✅ Filtrar por restaurante, método, data, status  
✅ Processar estornos com justificativa  
✅ Visualizar taxas da plataforma (10%)  
✅ Acompanhar líquido devido aos parceiros  
✅ Aprovar/recusar solicitações de saque  
✅ Gerar relatórios consolidados  
✅ Dashboard com métricas globais  

### Parceiro (Restaurante)
⏳ Ver apenas suas transações (a implementar)  
⏳ Solicitar saque do valor acumulado (a implementar)  
⏳ Ver histórico de saques (a implementar)  
⏳ Dashboard com suas métricas (a implementar)  

---

## 📊 Cálculo de Taxas

### Taxa da Plataforma: 10%
```
Exemplo:
- Pedido: R$ 100,00
- Taxa Plataforma: R$ 10,00 (10%)
- Taxa Gateway: R$ 0,00 (PIX é grátis)
- Líquido Restaurante: R$ 90,00
```

### Método PIX
- Taxa Gateway: R$ 0,00
- Taxa Plataforma: 10%
- Expiração: 30 minutos

### Cartão de Crédito (futuro)
- Taxa Gateway: ~3,5%
- Taxa Plataforma: 10%
- Total: ~13,5%

---

## 🚀 Como Usar

### 1. Acessar o Dashboard
```
URL: /admin/financeiro/pagamentos/dashboard
```

Você verá:
- Cards com totais do mês
- Top 10 restaurantes
- Gráfico de métodos de pagamento
- Contador de pendentes

### 2. Listar Transações
```
URL: /admin/financeiro/pagamentos/lista
```

**Filtros disponíveis:**
- Status (todos, pending, approved, rejected, cancelled, refunded)
- Restaurante (código)
- Método (pix, credit_card, debit_card)
- Período (data_inicio, data_fim)

### 3. Ver Detalhes
Clique no número do pedido ou no botão 👁️

**Modal mostra:**
- Informações do pagamento
- ID Mercado Pago
- Valores (total, taxas, líquido)
- Datas (criação, pagamento)
- Dados do restaurante

### 4. Estornar Pagamento
**Apenas pagamentos aprovados podem ser estornados**

1. Clique no botão 🔄 Estornar
2. SweetAlert solicita motivo
3. Confirme a ação
4. Sistema:
   - Chama API Mercado Pago
   - Atualiza status local
   - Registra motivo
   - **NÃO cancela o pedido**

### 5. Gerar Relatório
```
URL: /admin/financeiro/pagamentos/relatorios
```

**Parâmetros:**
- `periodo` - mensal, trimestral, anual, customizado
- `data_inicio`, `data_fim` - para período customizado
- `tenant` - filtrar por restaurante

---

## 🧪 Testes

### Verificar Dashboard
```bash
# Verificar se rota existe
php artisan route:list | grep pagamentos.dashboard

# Verificar modelo Payment
php artisan tinker
>>> App\Models\Payment::count()
>>> App\Models\Payment::where('status', 'approved')->sum('amount')
```

### Verificar Withdrawal
```bash
php artisan tinker
>>> App\Models\Withdrawal::count()
>>> $w = new App\Models\Withdrawal()
>>> $w->tenant_code = 'REST001'
>>> $w->valor = 500.00
>>> $w->status = 'pendente'
>>> $w->save()
```

### Testar Interface
Abrir navegador em: `http://localhost/teste-dashboard-financeiro.html`

---

## 📝 Próximas Implementações

### Painel do Parceiro
- [ ] Dashboard com suas transações
- [ ] Botão "Solicitar Saque"
- [ ] Histórico de saques
- [ ] Saldo disponível x retido

### Sistema de Saques
- [ ] Fluxo de aprovação admin
- [ ] Upload de comprovante
- [ ] Notificações por email
- [ ] Webhook para banco digital

### Relatórios Avançados
- [ ] Exportar para Excel
- [ ] Gráficos interativos
- [ ] Comparativo mês a mês
- [ ] Previsão de faturamento

### Automações
- [ ] Saque automático (acima de X)
- [ ] Alerta de estornos frequentes
- [ ] Notificação de pendentes antigos
- [ ] Reconciliação com banco

---

## 🐛 Troubleshooting

### Badge de Pendentes não Aparece
**Problema:** Badge sempre mostra 0  
**Solução:** Verificar se existe Payment com status='pending'
```php
\App\Models\Payment::where('status', 'pending')->count()
```

### Erro 404 ao Acessar Dashboard
**Problema:** Rota não encontrada  
**Solução:** Limpar cache de rotas
```bash
php artisan route:clear
php artisan route:cache
```

### Estorno não Funciona
**Problema:** Erro ao processar estorno  
**Solução:** 
1. Verificar credenciais MP no `.env`
2. Verificar se pagamento tem `mp_payment_id`
3. Verificar status (apenas 'approved' pode estornar)

### SweetAlert não Aparece
**Problema:** Modal de confirmação não abre  
**Solução:** Adicionar SweetAlert2 CDN no layout
```html
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

---

## 📚 Dependências

- **Laravel:** Framework base
- **Bootstrap 4:** Layout e componentes
- **Font Awesome 5:** Ícones
- **SweetAlert2:** Modais de confirmação
- **jQuery:** Manipulação DOM
- **Mercado Pago SDK:** Integração de pagamentos

---

## ✅ Checklist de Implementação

- [x] Migration withdrawals criada
- [x] Model Withdrawal com relacionamentos
- [x] Métodos no FinanceiroController
- [x] View pagamentos-dashboard.blade.php
- [x] View pagamentos-lista.blade.php
- [x] Rotas adicionadas
- [x] Menu admin atualizado
- [x] Badge de pendentes
- [x] Separação faturas/pagamentos
- [x] Documentação completa
- [ ] Painel do parceiro
- [ ] Sistema de saques
- [ ] Testes automatizados

---

## 📞 Suporte

Para dúvidas ou problemas:
1. Verificar esta documentação
2. Consultar código fonte
3. Testar em ambiente local
4. Analisar logs do Laravel

---

**Versão:** 1.0  
**Data:** 25/11/2025  
**Autor:** Sistema EATSFOOD  
**Status:** ✅ Produção
