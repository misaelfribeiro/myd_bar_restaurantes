# Sistema Call Center COBOL - EATSFOOD

## Descrição
Sistema de atendimento de call center em COBOL baseado em comandos para consulta de pedidos, clientes e restaurantes.

## Instalação do Compilador COBOL

### Windows (GnuCOBOL)

1. **Baixar GnuCOBOL:**
   - Acesse: https://sourceforge.net/projects/gnucobol/
   - Baixe a versão para Windows (ex: GnuCOBOL-3.2-win64.exe)
   - Instale seguindo o assistente

2. **Configurar Path:**
   ```powershell
   # Adicionar ao PATH (ajuste o caminho conforme sua instalação)
   $env:Path += ";C:\Program Files\GnuCOBOL\bin"
   ```

3. **Verificar instalação:**
   ```powershell
   cobc --version
   ```

## Compilação

```powershell
# Navegar até a pasta
cd c:\xampp\htdocs\myd_bar_restaurantes\cobol-callcenter

# Compilar o programa
cobc -x -free CALLCENTER.cbl -o callcenter.exe

# Executar
.\callcenter.exe
```

## Comandos Disponíveis

### 1. CONSULTAR PEDIDO
- **Comando:** `1`
- **Parâmetro:** Número do pedido (ex: PED001)
- **Descrição:** Exibe informações completas de um pedido específico

### 2. CONSULTAR CLIENTE
- **Comando:** `2`
- **Parâmetro:** Telefone do cliente (ex: 11987654321)
- **Descrição:** Exibe dados cadastrais do cliente

### 3. CONSULTAR RESTAURANTE
- **Comando:** `3`
- **Parâmetro:** Código do restaurante (ex: REST001)
- **Descrição:** Exibe informações do estabelecimento

### 4. ATUALIZAR STATUS
- **Comando:** `4`
- **Parâmetro 1:** Número do pedido
- **Parâmetro 2:** Novo status
- **Status válidos:**
  - CONFIRMADO
  - PREPARANDO
  - ENVIADO
  - ENTREGUE
  - CANCELADO

### 5. LISTAR PEDIDOS ATIVOS
- **Comando:** `5`
- **Descrição:** Lista todos os pedidos que não estão entregues ou cancelados

### 6. AJUDA
- **Comando:** `6`
- **Descrição:** Exibe menu com todos os comandos disponíveis

### 0. SAIR
- **Comando:** `0`
- **Descrição:** Encerra o sistema

## Estrutura de Arquivos

O sistema utiliza 3 arquivos sequenciais:

### pedidos.dat
```
ID|NUMERO|CLIENTE_ID|RESTAURANTE|STATUS|VALOR|DATA|TELEFONE
```

### clientes.dat
```
ID|NOME|TELEFONE|EMAIL|ENDERECO|CPF
```

### restaurantes.dat
```
ID|NOME|CODIGO|TELEFONE|STATUS
```

## Exemplo de Uso

```
==========================================
    SISTEMA CALL CENTER - EATSFOOD
==========================================

COMANDOS DISPONIVEIS:
  1. PEDIDO [numero]    - Consultar pedido
  2. CLIENTE [telefone] - Consultar cliente
  3. RESTAURANTE [codigo] - Consultar restaurante
  4. STATUS [numero]    - Atualizar status pedido
  5. LISTAR PEDIDOS     - Listar pedidos ativos
  6. AJUDA              - Mostrar comandos
  0. SAIR               - Encerrar sistema

Digite o comando (ou 6 para ajuda): 1
Numero do pedido: PED001

=========================================
DADOS DO PEDIDO
=========================================
ID: 00000001
Numero: PED001
Cliente ID: 00000100
Restaurante: Restaurante Exemplo
Status: PREPARANDO
Valor: R$ 104.29
Data: 25/11/2025
Telefone: 11987654321
=========================================
```

## Integração com Sistema Laravel

Para integração futura, criar script PHP que:
1. Exporta dados do MySQL para arquivos .dat
2. Executa o programa COBOL via shell_exec()
3. Importa resultados de volta ao sistema

Exemplo de script de sincronização:

```php
// sync-cobol.php
<?php
// Exportar pedidos do MySQL para pedidos.dat
$pedidos = DB::table('pedidos')->get();
$file = fopen('cobol-callcenter/pedidos.dat', 'w');
foreach ($pedidos as $pedido) {
    fputcsv($file, (array)$pedido);
}
fclose($file);

// Executar programa COBOL
exec('cd cobol-callcenter && callcenter.exe');
```

## Próximas Funcionalidades

- [ ] Integração com banco MySQL via COBOL
- [ ] Relatórios de atendimento
- [ ] Histórico de chamadas
- [ ] Script de sincronização automática
- [ ] Interface web para visualizar logs COBOL
- [ ] Sistema de autenticação de atendentes
- [ ] Registro de tempo de atendimento
- [ ] Estatísticas de performance

## Suporte

Sistema desenvolvido para EATSFOOD
Versão: 1.0
Data: 25/11/2025
