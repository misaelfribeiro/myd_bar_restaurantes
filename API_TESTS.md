# Sistema Bar & Restaurante - Testes de API

## Testando os Endpoints

### 1. Categorias

#### Listar todas as categorias
```
GET http://127.0.0.1:8000/api/categorias
```

#### Criar nova categoria
```
POST http://127.0.0.1:8000/api/categorias
Content-Type: application/json

{
    "nome": "Vinhos"
}
```

### 2. Produtos

#### Listar todos os produtos
```
GET http://127.0.0.1:8000/api/produtos
```

#### Criar novo produto
```
POST http://127.0.0.1:8000/api/produtos
Content-Type: application/json

{
    "nome": "Cerveja Pilsen",
    "descricao": "Cerveja gelada 350ml",
    "preco": 5.50,
    "categoria_id": 1
}
```

### 3. Mesas

#### Listar todas as mesas
```
GET http://127.0.0.1:8000/api/mesas
```

#### Criar nova mesa
```
POST http://127.0.0.1:8000/api/mesas
Content-Type: application/json

{
    "identificador": "Mesa 09",
    "lugares": 4
}
```

### 4. Usuários

#### Listar todos os usuários
```
GET http://127.0.0.1:8000/api/usuarios
```

#### Criar novo usuário
```
POST http://127.0.0.1:8000/api/usuarios
Content-Type: application/json

{
    "nome": "João Silva",
    "email": "joao@email.com",
    "password": "123456"
}
```

### 5. Pedidos

#### Listar todos os pedidos
```
GET http://127.0.0.1:8000/api/pedidos
```

#### Criar novo pedido
```
POST http://127.0.0.1:8000/api/pedidos
Content-Type: application/json

{
    "mesa_id": 1,
    "usuario_id": 1,
    "total": 25.50,
    "status": "pendente"
}
```

## Status de Pedidos Válidos
- pendente
- em_preparo
- pronto
- entregue
- cancelado

## Como Testar

1. Use um cliente REST como Postman, Insomnia ou curl
2. Importe essas requisições ou copie e cole
3. Primeiro crie categorias e usuários
4. Depois crie produtos e mesas
5. Por fim, crie pedidos

## Banco de Dados

O banco já possui dados de exemplo:
- 5 categorias padrão
- 8 mesas configuradas

Execute `php artisan db:seed` para popular novamente se necessário.
