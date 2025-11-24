# 🎓 Guia de Treinamento da Carla

## 📋 Índice
1. [Adicionar Novos Conhecimentos](#1-adicionar-novos-conhecimentos)
2. [Treinar com Histórico](#2-treinar-com-histórico)
3. [Feedback Manual](#3-feedback-manual)
4. [Via API](#4-via-api)
5. [Monitorar Evolução](#5-monitorar-evolução)

---

## 1. Adicionar Novos Conhecimentos

### 🎯 Quando usar:
- Ensinar novos produtos/categorias
- Adicionar novos comandos
- Expandir vocabulário

### 💻 Como fazer:

```bash
php treinar_novos_contextos.php
```

### ✏️ Ou editar o seeder:

```bash
# Editar: database/seeders/AIKnowledgeSeeder.php
# Depois rodar:
php artisan db:seed --class=AIKnowledgeSeeder
```

### 📝 Exemplo de novo contexto:

```php
[
    'category' => 'search',
    'key' => 'search_acai',
    'pattern' => '*(açaí|acai)*',
    'response_template' => 'Temos açaí delicioso! Vou mostrar!',
    'action' => 'searchProduct',
    'parameters' => ['query' => 'acai'],
    'confidence_threshold' => 0.6,
]
```

### 🎨 Tipos de Padrões:

```
Regex puro:         (oi|olá|hey)
Wildcard:           *(pizza)*
Wildcard + regex:   *(hamburguer|burger)*
Parâmetros:         quero {quantidade} {produto}
```

---

## 2. Treinar com Histórico

### 🎯 Quando usar:
- Após usuários interagirem com a Carla
- Ajustar pesos da rede neural
- Melhorar respostas baseado em uso real

### 💻 Como fazer:

```bash
php treinar_com_historico.php
```

### 🔄 O que acontece:
1. Busca interações corretas não treinadas
2. Aplica **backpropagation** em cada uma
3. Ajusta pesos das 6.000 sinapses
4. Melhora próximas respostas

### 📊 Resultados esperados:

```
Antes:  Confiança média: 0.65 | Taxa de acerto: 75%
Depois: Confiança média: 0.82 | Taxa de acerto: 89%
```

---

## 3. Feedback Manual

### 🎯 Quando usar:
- Corrigir resposta incorreta
- Reforçar resposta boa
- Ajustar comportamento específico

### 💻 Como fazer:

```bash
# Ver últimas interações
php dar_feedback.php

# Ou via código:
php artisan tinker
>>> $service = new App\Services\AILearningService();
>>> $service->learnFromFeedback($trainingDataId, $correct, $score);
```

### 📝 Parâmetros:

```php
learnFromFeedback(
    $trainingDataId,  // ID da interação (veja em dar_feedback.php)
    $correct,         // true = correta, false = incorreta
    $feedbackScore    // 1-5 estrelas (opcional)
);
```

### 💡 Exemplo:

```php
// Interação ID 10 foi incorreta (score 2)
$service->learnFromFeedback(10, false, 2);
// ✅ Backpropagation aplicado! Pesos ajustados.

// Interação ID 15 foi excelente (score 5)
$service->learnFromFeedback(15, true, 5);
// ✅ Padrão reforçado!
```

---

## 4. Via API

### 🎯 Quando usar:
- Integrar com interface web
- Automatizar treinamento
- Feedback em tempo real

### 🌐 Endpoints:

#### Adicionar Contexto:
```bash
curl -X POST http://localhost/api/ai/contexts \
  -H "Content-Type: application/json" \
  -d '{
    "category": "search",
    "key": "search_japonesa",
    "pattern": "*(sushi|sashimi|japonesa)*",
    "response_template": "Comida japonesa! Vou mostrar nosso menu!",
    "action": "searchProduct",
    "parameters": {"query": "japonesa"}
  }'
```

#### Dar Feedback:
```bash
curl -X POST http://localhost/api/ai/feedback \
  -H "Content-Type: application/json" \
  -d '{
    "training_data_id": 123,
    "correct": true,
    "feedback_score": 5
  }'
```

#### Treinar em Lote:
```bash
curl -X POST http://localhost/api/ai/train \
  -H "Content-Type: application/json" \
  -d '{"limit": 100}'
```

#### Ver Estatísticas:
```bash
curl http://localhost/api/ai/stats
```

---

## 5. Monitorar Evolução

### 📊 Painel de Estatísticas:

```bash
# Via browser:
http://localhost/teste-ia.html

# Clicar em "Carregar Estatísticas"
```

### 📈 Métricas Importantes:

```json
{
  "training_data": {
    "total": 324,           // Total de interações
    "trained": 156,         // Já processadas
    "pending": 168,         // Aguardando treinamento
    "correct_rate": 0.89,   // 89% de acertos
    "avg_confidence": 0.78  // Confiança média
  },
  "contexts": {
    "total": 29,            // Contextos cadastrados
    "total_usage": 452,     // Vezes que foram usados
    "avg_success_rate": 0.91 // Taxa de sucesso
  },
  "synapses": {
    "total_updates": 1543   // Atualizações nos pesos
  }
}
```

### 🎯 Sinais de que a IA está melhorando:

✅ `correct_rate` subindo (>85% é bom)  
✅ `avg_confidence` aumentando (>0.75 é ótimo)  
✅ `total_updates` crescendo (pesos sendo ajustados)  
✅ `avg_success_rate` dos contextos melhorando

---

## 🚀 Workflow Completo de Treinamento

### Dia 1: Setup Inicial
```bash
# 1. Inicializar rede neural
php inicializar_ia.php

# 2. Carregar conhecimento básico
php artisan db:seed --class=AIKnowledgeSeeder

# 3. Adicionar contextos específicos do seu negócio
php treinar_novos_contextos.php
```

### Durante o uso:
```
1. Clientes interagem → Dados são registrados automaticamente
2. Sistema marca feedback automático (todas iniciam como "correct: true")
```

### Semanal:
```bash
# Treinar com dados acumulados
php treinar_com_historico.php

# Ver estatísticas
php dar_feedback.php
```

### Quando necessário:
```bash
# Corrigir resposta ruim específica
php artisan tinker
>>> $service = new App\Services\AILearningService();
>>> $service->learnFromFeedback($id, false, 2);

# Adicionar novo produto/categoria
# Editar: treinar_novos_contextos.php
php treinar_novos_contextos.php
```

---

## 💡 Dicas Avançadas

### 1. Contextos Genéricos vs Específicos

**Genérico (threshold baixo):**
```php
[
    'pattern' => '*(bebida)*',
    'confidence_threshold' => 0.6,  // Pega qualquer menção a bebida
]
```

**Específico (threshold alto):**
```php
[
    'pattern' => '(quanto custa|qual o preço|valor)',
    'confidence_threshold' => 0.8,  // Só quando muito confiante
]
```

### 2. Prioridade de Contextos

A IA escolhe por **confiança**. Para priorizar um contexto:
- Use padrões mais específicos
- Aumente o threshold
- Dê feedback positivo quando acertar

### 3. Testar Antes de Publicar

```bash
# Sempre teste novos contextos:
php testar_ia.php

# Ou use a página de teste:
http://localhost/teste-ia.html
```

### 4. Backup dos Pesos

```bash
# Fazer backup da rede treinada
mysqldump -u root myd_bar_restaurantes ai_synapses > backup_pesos.sql

# Restaurar se necessário
mysql -u root myd_bar_restaurantes < backup_pesos.sql
```

---

## 🎓 Resumo Rápido

| Tarefa | Comando |
|--------|---------|
| Adicionar conhecimento | `php treinar_novos_contextos.php` |
| Treinar com histórico | `php treinar_com_historico.php` |
| Ver interações | `php dar_feedback.php` |
| Dar feedback | `$service->learnFromFeedback($id, $correct, $score)` |
| Ver estatísticas | `http://localhost/api/ai/stats` |
| Testar IA | `php testar_ia.php` |

---

**🎉 Com esses métodos, a Carla vai evoluir continuamente!**
