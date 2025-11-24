# Sistema de Permissões - MyD Bar & Restaurantes

## Estrutura de Roles (Papéis)

### 1. **Superadmin / Admin**
- ✅ Acesso total a todos os módulos
- ✅ Pode criar, editar, excluir e visualizar tudo
- ✅ Gerenciar usuários e permissões
- ✅ Acessar relatórios financeiros
- ✅ Configurações do sistema

### 2. **Gerente**
- ✅ Visualizar e editar produtos
- ✅ Visualizar e editar categorias
- ✅ Gerenciar pedidos (criar, editar, cancelar)
- ✅ Gerenciar mesas
- ✅ Gerenciar clientes
- ✅ Gerenciar delivery
- ✅ Visualizar relatórios
- ✅ Abrir/fechar caixa
- ❌ Não pode excluir registros críticos
- ❌ Não pode gerenciar usuários

### 3. **Garçom**
- ✅ Visualizar produtos e categorias
- ✅ Criar e editar pedidos
- ✅ Visualizar e atualizar status de mesas
- ✅ Registrar observações em pedidos
- ❌ Não pode excluir pedidos
- ❌ Não pode acessar caixa
- ❌ Não pode acessar relatórios financeiros
- ❌ Não pode gerenciar produtos/categorias

### 4. **Caixa**
- ✅ Visualizar pedidos
- ✅ Processar pagamentos
- ✅ Abrir/fechar caixa
- ✅ Visualizar movimentações do caixa
- ✅ Editar status de pedidos (para pagamento)
- ❌ Não pode criar/editar produtos
- ❌ Não pode gerenciar mesas
- ❌ Não pode acessar configurações

### 5. **Entregador**
- ✅ Visualizar suas entregas
- ✅ Atualizar status de entrega
- ✅ Visualizar endereço e dados do cliente
- ❌ Não pode acessar outros módulos

## Módulos e Permissões

### Produtos
| Ação | Admin | Gerente | Garçom | Caixa |
|------|-------|---------|--------|-------|
| Visualizar | ✅ | ✅ | ✅ | ❌ |
| Criar | ✅ | ✅ | ❌ | ❌ |
| Editar | ✅ | ✅ | ❌ | ❌ |
| Excluir | ✅ | ❌ | ❌ | ❌ |

### Categorias
| Ação | Admin | Gerente | Garçom | Caixa |
|------|-------|---------|--------|-------|
| Visualizar | ✅ | ✅ | ✅ | ❌ |
| Criar | ✅ | ✅ | ❌ | ❌ |
| Editar | ✅ | ✅ | ❌ | ❌ |
| Excluir | ✅ | ❌ | ❌ | ❌ |

### Pedidos
| Ação | Admin | Gerente | Garçom | Caixa |
|------|-------|---------|--------|-------|
| Visualizar | ✅ | ✅ | ✅ | ✅ |
| Criar | ✅ | ✅ | ✅ | ❌ |
| Editar | ✅ | ✅ | ✅ | ✅* |
| Excluir | ✅ | ❌ | ❌ | ❌ |

*Caixa só pode editar para processar pagamento

### Mesas
| Ação | Admin | Gerente | Garçom | Caixa |
|------|-------|---------|--------|-------|
| Visualizar | ✅ | ✅ | ✅ | ❌ |
| Criar | ✅ | ✅ | ❌ | ❌ |
| Editar | ✅ | ✅ | ✅* | ❌ |
| Excluir | ✅ | ❌ | ❌ | ❌ |

*Garçom só pode alterar status

### Clientes
| Ação | Admin | Gerente | Garçom | Caixa |
|------|-------|---------|--------|-------|
| Visualizar | ✅ | ✅ | ✅ | ❌ |
| Criar | ✅ | ✅ | ✅ | ❌ |
| Editar | ✅ | ✅ | ❌ | ❌ |
| Excluir | ✅ | ❌ | ❌ | ❌ |

### Delivery
| Ação | Admin | Gerente | Garçom | Caixa |
|------|-------|---------|--------|-------|
| Visualizar | ✅ | ✅ | ❌ | ❌ |
| Criar | ✅ | ✅ | ❌ | ❌ |
| Editar | ✅ | ✅ | ❌ | ❌ |
| Excluir | ✅ | ❌ | ❌ | ❌ |

### Caixa
| Ação | Admin | Gerente | Garçom | Caixa |
|------|-------|---------|--------|-------|
| Visualizar | ✅ | ✅ | ❌ | ✅ |
| Abrir | ✅ | ✅ | ❌ | ✅ |
| Fechar | ✅ | ✅ | ❌ | ✅ |
| Relatórios | ✅ | ✅ | ❌ | ✅ |

### Usuários
| Ação | Admin | Gerente | Garçom | Caixa |
|------|-------|---------|--------|-------|
| Visualizar | ✅ | ❌ | ❌ | ❌ |
| Criar | ✅ | ❌ | ❌ | ❌ |
| Editar | ✅ | ❌ | ❌ | ❌ |
| Excluir | ✅ | ❌ | ❌ | ❌ |

### Relatórios
| Ação | Admin | Gerente | Garçom | Caixa |
|------|-------|---------|--------|-------|
| Vendas | ✅ | ✅ | ❌ | ✅ |
| Financeiro | ✅ | ✅ | ❌ | ❌ |
| Produtos | ✅ | ✅ | ❌ | ❌ |
| Dashboard | ✅ | ✅ | ❌ | ✅ |

## Aplicação das Permissões

### Nas Rotas (web.php)

```php
// Exemplo: Apenas Admin
Route::middleware(['auth', 'check.role:admin'])->group(function () {
    Route::resource('usuarios', UsuarioController::class);
});

// Exemplo: Admin ou Gerente
Route::middleware(['auth', 'check.role:admin|gerente'])->group(function () {
    Route::resource('produtos', ProdutoController::class);
});

// Exemplo: Com permissão específica
Route::middleware(['auth', 'permission:pedidos,criar'])->group(function () {
    Route::post('pedidos', [PedidoController::class, 'store']);
});
```

### Nos Controllers

```php
// Verificar permissão no método
public function destroy($id)
{
    if (!auth()->user()->temPermissao('produtos', 'excluir')) {
        abort(403, 'Sem permissão');
    }
    // ... código
}
```

### Nas Views (Blade)

```php
@can('visualizar', 'produtos')
    <a href="{{ route('produtos.index') }}">Produtos</a>
@endcan

@if(auth()->user()->temPermissao('produtos', 'criar'))
    <button>Novo Produto</button>
@endif
```

## Métodos do Model Usuario

```php
// Verificar permissão
$user->temPermissao('produtos', 'criar'); // true/false

// Verificar role
$user->hasRole('admin'); // true/false

// Verificar múltiplas roles
$user->hasAnyRole(['admin', 'gerente']); // true/false

// Obter permissões
$user->getPermissoes(); // Collection
```

## Sincronizar Permissões

Quando um usuário é criado ou seu role é alterado:

```php
$usuario->sincronizarPermissoesPadrao();
```

Isso cria automaticamente as permissões padrão baseadas no role.
