# Instalação GnuCOBOL - Guia Passo a Passo

## Você baixou a versão ZIP (gnucobol-3.2_win.zip)

### Passo 1: Extrair o ZIP

1. Extraia todo o conteúdo do ZIP para: `C:\GnuCOBOL`
   - Clique com botão direito no ZIP
   - Selecione "Extrair Tudo..."
   - Escolha `C:\` como destino
   - Renomeie a pasta extraída para `GnuCOBOL`

2. A estrutura final deve ficar:
   ```
   C:\GnuCOBOL\
   ├── bin\
   ├── build_aux\
   ├── build_windows\
   ├── cobc\
   ├── config\
   ├── copy\
   ├── doc\
   ├── extras\
   ├── lib\
   ├── libcob\
   └── ... (outros arquivos)
   ```

### Passo 2: Adicionar ao PATH do Windows

**Opção A - Via PowerShell (Mais Rápido):**

```powershell
# Execute como Administrador
[Environment]::SetEnvironmentVariable(
    "Path",
    [Environment]::GetEnvironmentVariable("Path", "Machine") + ";C:\GnuCOBOL\bin",
    "Machine"
)

# Adicionar variável COB_CONFIG_DIR
[Environment]::SetEnvironmentVariable("COB_CONFIG_DIR", "C:\GnuCOBOL\config", "Machine")
[Environment]::SetEnvironmentVariable("COB_COPY_DIR", "C:\GnuCOBOL\copy", "Machine")
```

**Opção B - Via Interface Gráfica:**

1. Pressione `Win + Pause` (ou clique com direito em "Este Computador" > Propriedades)
2. Clique em "Configurações avançadas do sistema"
3. Clique em "Variáveis de Ambiente"
4. Em "Variáveis do sistema", encontre `Path` e clique em "Editar"
5. Clique em "Novo" e adicione: `C:\GnuCOBOL\bin`
6. Clique em "OK" em todas as janelas

7. Criar novas variáveis (botão "Novo"):
   - Nome: `COB_CONFIG_DIR` | Valor: `C:\GnuCOBOL\config`
   - Nome: `COB_COPY_DIR` | Valor: `C:\GnuCOBOL\copy`
   - Nome: `COB_LIBRARY_PATH` | Valor: `C:\GnuCOBOL\lib`

### Passo 3: Verificar Instalação

1. **Feche todos os PowerShell/CMD abertos**
2. Abra um **NOVO** PowerShell
3. Execute:

```powershell
cobc --version
```

**Resultado esperado:**
```
cobc (GnuCOBOL) 3.2.0
Copyright (C) 2023 Free Software Foundation, Inc.
...
```

### Passo 4: Compilar o Sistema Call Center

```powershell
# Ir para a pasta do projeto
cd c:\xampp\htdocs\myd_bar_restaurantes\cobol-callcenter

# Sincronizar dados do banco
php sync-data.php

# Compilar e executar
.\compilar.bat
```

## Solução de Problemas

### Erro: "cobc não é reconhecido"
- **Causa:** PATH não atualizado
- **Solução:** 
  1. Feche e reabra o PowerShell
  2. Verifique se adicionou ao PATH corretamente
  3. Execute: `$env:Path -split ';' | Select-String -Pattern 'GnuCOBOL'`

### Erro: "cannot find -lgmp"
- **Causa:** Bibliotecas não encontradas
- **Solução:** Baixe a versão com bibliotecas incluídas ou instale via instalador EXE

### Erro de compilação: "configuration file not found"
- **Causa:** COB_CONFIG_DIR não definido
- **Solução:** Execute:
  ```powershell
  $env:COB_CONFIG_DIR = "C:\GnuCOBOL\config"
  ```

### Teste Rápido (temporário para sessão atual)

Se quiser testar SEM configurar permanentemente:

```powershell
# Temporário apenas para esta sessão
$env:Path += ";C:\GnuCOBOL\bin"
$env:COB_CONFIG_DIR = "C:\GnuCOBOL\config"
$env:COB_COPY_DIR = "C:\GnuCOBOL\copy"

# Testar
cobc --version
```

## Alternativa: Usar o Instalador EXE

Se tiver problemas com o ZIP, baixe o instalador:
https://sourceforge.net/projects/gnucobol/files/gnucobol/3.2/

Procure por: `GnuCOBOL_3.2_vs2019_x64.exe`
Este é mais fácil e configura tudo automaticamente.
