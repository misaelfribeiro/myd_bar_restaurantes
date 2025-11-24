# 🎛️ Painel Admin - Carla IA

## 📍 Acesso
**URL:** `http://localhost:8000/admin-carla.html`

---

## 🎯 O que o Painel Oferece

### 1. **Dashboard de Métricas em Tempo Real**

#### 📊 Cards Principais
- **Neurônios**: Total de neurônios na rede (170)
- **Sinapses**: Total de conexões sinápticas (6.000)
- **Contextos**: Quantidade de conhecimento/padrões aprendidos
- **Interações**: Total de conversas processadas

#### 📈 Indicadores de Performance
- **Taxa de Acerto**: % de respostas corretas (meta: 90%+)
  - 🟢 Verde: 90%+ (Excelente)
  - 🔵 Azul: 70-89% (Bom)
  - 🟡 Amarelo: 50-69% (Regular)
  - 🔴 Vermelho: <50% (Precisa treinar)

- **Confiança Média**: Quão segura a Carla está nas respostas (0-100%)
- **Taxa de Sucesso (Contextos)**: % de contextos que funcionam bem

#### 🎓 Status do Treinamento
- **Dados Treinados**: Quantas interações foram usadas para aprender
- **Pendentes**: Quantas ainda não foram processadas
- **Atualizações**: Total de ajustes nos pesos sinápticos
- **Peso Médio**: Valor médio das sinapses (indica aprendizado)

---

### 2. **Ações de Treinamento**

#### 🎓 Treinar com Histórico
```
Botão: [Treinar com Histórico]
```
**O que faz:**
- Pega todas as interações pendentes
- Aplica algoritmo de backpropagation
- Atualiza pesos de TODAS as 6.000 sinapses
- Melhora a precisão da Carla

**Quando usar:**
- Após acumular 20-50+ novas interações
- Quando a taxa de acerto cai abaixo de 85%
- Diariamente em produção

**Tempo:** 10-30 segundos (dependendo do volume)

#### ➕ Adicionar Contexto
```
Botão: [Adicionar Contexto]
```
**O que faz:**
- Abre modal para adicionar novo conhecimento
- Ensina a Carla a reconhecer novos padrões
- Define respostas e ações personalizadas

**Quando usar:**
- Produto novo no cardápio
- Nova funcionalidade no app
- Pergunta frequente não reconhecida
- Promoção ou campanha específica

**Exemplo de uso:**
```
Categoria: menu
Key: search_feijoada
Padrão: *(feijoada|feijão)*
Resposta: Sou a Carla da EatsFood! Temos feijoada completa aos sábados por R$ 35,00. Serve 2 pessoas!
Ação: searchProduct
Limiar: 0.7
```

#### 🔄 Atualizar Estatísticas
```
Botão: [Atualizar Estatísticas]
```
**O que faz:**
- Recarrega todos os dados em tempo real
- Atualiza gráficos e métricas

**Quando usar:**
- Após treinar
- Após adicionar contextos
- Para monitorar em tempo real

---

### 3. **Visualização de Contextos**

#### 📋 Tabela de Contextos
Mostra TODOS os contextos ativos da Carla:

| Coluna | Descrição |
|--------|-----------|
| **Categoria** | Tipo (greeting, menu, search, cart, orders...) |
| **Key** | Identificador único |
| **Padrão** | Regex que a Carla usa para reconhecer |
| **Uso** | Quantas vezes foi ativado |
| **Taxa Sucesso** | % de vezes que funcionou bem |
| **Confiança** | Limiar mínimo para ativar (0.0-1.0) |
| **Status** | ✅ Ativo ou ❌ Inativo |

**Análise:**
- Se **Uso** é 0 → Padrão pode estar errado
- Se **Taxa Sucesso** < 70% → Precisa ajustar resposta
- Se **Confiança** muito alta → Pode nunca ativar

---

### 4. **Últimas Interações**

#### 💬 Log de Conversas
Mostra as 10 conversas mais recentes:

**Informações exibidas:**
- 👤 **Entrada do usuário**
- 🤖 **Resposta da Carla**
- 🎯 **Intent detectado**: O que a Carla entendeu
- 📝 **Context usado**: Qual contexto foi acionado
- 🎲 **Confiança**: % de certeza (0-100%)
- ✅ **Status**: Correto / Não validado

**Como usar:**
- Identifique respostas ruins
- Veja quais contextos são mais usados
- Detecte padrões não reconhecidos
- Monitore confiança baixa

---

### 5. **Estrutura da Rede Neural**

#### 🧠 Visualização de Camadas

```
[Camada de Entrada]  →  [Camada Oculta]  →  [Camada de Saída]
   100 neurônios         50 neurônios          20 neurônios
```

**O que significa:**
- **Entrada**: Converte texto em 100 valores numéricos
- **Oculta**: Processa padrões complexos
- **Saída**: Gera 20 possíveis intenções

---

## 🎯 Fluxo de Trabalho Recomendado

### 📅 Rotina Diária (5 min)
1. Acesse o painel
2. Verifique **Taxa de Acerto**
   - Se < 85%, clique em "Treinar com Histórico"
3. Olhe **Últimas Interações**
   - Identifique respostas ruins
4. Verifique **Pendentes**
   - Se > 50, treine imediatamente

### 📊 Rotina Semanal (15 min)
1. Analise **Contextos Ativos**
   - Identifique os com Taxa Sucesso < 70%
2. Revise **Uso dos Contextos**
   - Se uso = 0 por 1 semana, padrão está errado
3. Adicione **Novos Contextos**
   - Base em perguntas não reconhecidas

### 🎓 Melhorando a Taxa de Acerto

#### Se taxa < 70%:
```
1. Treinar com Histórico (urgente)
2. Adicionar contextos para perguntas comuns
3. Ajustar padrões de contextos existentes
4. Rodar novamente dar_feedback.php
```

#### Se taxa 70-85%:
```
1. Treinar com Histórico (semanalmente)
2. Revisar contextos com baixa taxa de sucesso
3. Adicionar variações de padrões
```

#### Se taxa > 85%:
```
✅ Carla está indo bem!
- Continue treinando semanalmente
- Adicione contextos para novas features
- Monitore interações
```

---

## 🛠️ Troubleshooting

### Problema: "Taxa de Acerto muito baixa"
**Solução:**
1. Clique em "Treinar com Histórico"
2. Aguarde processar
3. Atualize estatísticas
4. Se continuar baixa, adicione mais contextos

### Problema: "Confiança Média < 50%"
**Solução:**
1. Revise padrões dos contextos
2. Ajuste limiares de confiança (diminua para 0.5-0.6)
3. Treine mais com histórico

### Problema: "Contexto não é ativado"
**Solução:**
1. Verifique o padrão regex
2. Teste com regex101.com
3. Ajuste limiar de confiança
4. Use wildcards (*) ao invés de regex complexo

### Problema: "Muitas interações pendentes"
**Solução:**
1. Configure cron job para treinar automaticamente
2. Ou treine manualmente 2x por dia
3. Monitore via painel

---

## 🚀 Dicas Avançadas

### 1. **Padrões Regex Eficientes**
```
Simples (recomendado):
*(pizza)* → Encontra "pizza" em qualquer lugar

Com variações:
*(pizza|pizzaria)* → Aceita ambas

Mais específico:
(quero|desejo|preciso) *(pizza)* → Frase mais completa
```

### 2. **Respostas Dinâmicas**
Use variáveis nas respostas:
```
"Sou a Carla da EatsFood! Encontrei {count} opções de {product} para você!"
```

### 3. **Ações Úteis**
- `searchProduct`: Busca no cardápio
- `showMenu`: Abre menu completo
- `showCart`: Mostra carrinho
- `checkout`: Vai para pagamento
- `showOrders`: Lista pedidos
- `null`: Apenas responde (sem ação)

### 4. **Monitoramento Proativo**
Configure auto-refresh (já incluído a cada 30 seg)
```javascript
// Já configurado em admin-carla.js
setInterval(loadStats, 30000);
```

---

## 📊 Metas de Performance

| Métrica | Ruim | Regular | Bom | Excelente |
|---------|------|---------|-----|-----------|
| Taxa Acerto | <50% | 50-69% | 70-89% | 90%+ |
| Confiança Média | <40% | 40-59% | 60-79% | 80%+ |
| Taxa Sucesso Contextos | <50% | 50-69% | 70-84% | 85%+ |
| Pendentes | >100 | 50-99 | 20-49 | <20 |

---

## 🎯 Checklist de Sucesso

- [ ] Taxa de Acerto > 85%
- [ ] Confiança Média > 70%
- [ ] Todos os contextos com Uso > 0
- [ ] Taxa de Sucesso de contextos > 80%
- [ ] Menos de 20 interações pendentes
- [ ] Nenhum contexto com Taxa Sucesso < 60%
- [ ] Treinamento automático configurado
- [ ] Revisão semanal agendada

---

## 🔗 Links Relacionados

- **Treinar via PHP**: `php treinar_com_historico.php`
- **Adicionar contextos**: `php treinar_novos_contextos.php`
- **Feedback manual**: `php dar_feedback.php`
- **Simulação**: `php simular_treinamento.php`
- **Documentação**: `GUIA_TREINAMENTO_CARLA.md`

---

## 💡 Exemplo de Uso Completo

### Cenário: "Pizza Margherita" não está sendo reconhecida

#### Passo 1: Identificar no painel
- Veja em "Últimas Interações" → Usuário: "quero margherita"
- Carla não entendeu (baixa confiança)

#### Passo 2: Adicionar contexto
```
Clique: [Adicionar Contexto]

Categoria: search
Key: search_margherita
Padrão: *(margherita|margarita|margheritha)*
Resposta: Sou a Carla da EatsFood! Nossa Pizza Margherita é deliciosa! Queijo, tomate e manjericão frescos. Tamanho G por R$ 42,00.
Ação: searchProduct
Limiar: 0.7

Clique: [Salvar Contexto]
```

#### Passo 3: Testar
- Usuário pergunta novamente
- Carla responde corretamente
- Interação é registrada

#### Passo 4: Treinar
- Acumule 20-30 interações novas
- Clique: [Treinar com Histórico]
- Aguarde conclusão
- Taxa de acerto aumenta!

---

**Pronto! Agora você tem controle total sobre a Carla! 🎉**
