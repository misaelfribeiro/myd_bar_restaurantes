# Sistema Call Center Desktop - EATSFOOD
## Aplicação Desktop Profissional em C# WinForms

### Pré-requisitos

1. **Instalar .NET 8 SDK:**
   - Link direto: https://dotnet.microsoft.com/download/dotnet/8.0
   - Baixe: ".NET 8.0 SDK (v8.0) - Windows x64 Installer"
   - Execute o instalador
   - Após instalar, feche e reabra o PowerShell

2. **Verificar instalação:**
   ```powershell
   dotnet --version
   ```

### Criar o Projeto

```powershell
cd c:\xampp\htdocs\myd_bar_restaurantes\CallCenterDesktop

# Criar projeto WinForms
dotnet new winforms -n EatsFoodCallCenter -f net8.0-windows

# Adicionar pacotes necessários
cd EatsFoodCallCenter
dotnet add package MySql.Data --version 8.2.0
dotnet add package Newtonsoft.Json --version 13.0.3
dotnet add package BCrypt.Net-Next --version 4.0.3
dotnet add package MetroFramework --version 1.4.0
```

### Estrutura do Projeto

```
EatsFoodCallCenter/
├── Forms/
│   ├── LoginForm.cs              # Tela de login
│   ├── MainForm.cs                # Dashboard principal
│   ├── AtendimentoForm.cs         # Atendimento ao cliente
│   ├── PedidoDetalhesForm.cs      # Detalhes do pedido
│   ├── EstornoForm.cs             # Gestão de estornos
│   └── RelatoriosForm.cs          # Relatórios
├── Models/
│   ├── Usuario.cs
│   ├── Pedido.cs
│   ├── Cliente.cs
│   ├── Estorno.cs
│   └── AuditoriaLog.cs
├── Services/
│   ├── DatabaseService.cs         # Conexão MySQL
│   ├── AuthService.cs             # Autenticação
│   ├── PedidoService.cs           # Gestão de pedidos
│   ├── EstornoService.cs          # Gestão de estornos
│   └── AuditoriaService.cs        # Logs de auditoria
└── Utils/
    ├── ConfigManager.cs
    ├── Cryptography.cs
    └── Permissions.cs
```

### Funcionalidades Implementadas

#### 1. Sistema de Autenticação
- Login com usuário e senha (criptografado)
- Controle de sessão
- Timeout automático por inatividade
- Bloqueio após 3 tentativas incorretas

#### 2. Hierarquias de Usuários
- **Administrador:** Acesso total
- **Supervisor:** Aprovar estornos, visualizar todos os pedidos
- **Atendente:** Atendimento básico, solicitar estornos

#### 3. Painel de Atendimento
- Busca rápida de cliente (telefone, CPF, nome)
- Histórico completo de pedidos
- Status em tempo real
- Informações de entrega (entregador, localização)

#### 4. Gestão de Estornos
- **Estorno Parcial:** Por produto específico
- **Estorno Total:** Pedido completo
- Sistema de aprovação (Supervisor)
- Motivos obrigatórios
- Registro em auditoria

#### 5. Auditoria e Logs
- Registro de todas as operações
- Data/hora, usuário, ação, IP
- Rastreabilidade completa
- Relatórios de auditoria

#### 6. Interface Moderna
- Design profissional com MetroFramework
- Tema escuro/claro
- Ícones intuitivos
- Notificações em tempo real
- Atalhos de teclado

### Compilar e Executar

```powershell
# Compilar
dotnet build

# Executar em modo debug
dotnet run

# Publicar versão final
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true
```

### Configuração

Editar `appsettings.json`:

```json
{
  "Database": {
    "Server": "localhost",
    "Port": 3306,
    "Database": "myd_bar_restaurantes",
    "User": "root",
    "Password": "",
    "ConnectionTimeout": 30
  },
  "Security": {
    "SessionTimeout": 30,
    "MaxLoginAttempts": 3,
    "PasswordMinLength": 6,
    "RequireStrongPassword": true
  },
  "Company": {
    "Name": "EATSFOOD",
    "Logo": "logo.png",
    "Support": "suporte@eatsfood.com",
    "Phone": "(11) 99999-9999"
  }
}
```

### Distribuição

O executável final estará em:
```
bin/Release/net8.0-windows/win-x64/publish/EatsFoodCallCenter.exe
```

Pode distribuir este único arquivo para os atendentes.

### Próximos Passos

Após instalar o .NET SDK, execute:

```powershell
cd c:\xampp\htdocs\myd_bar_restaurantes\CallCenterDesktop
.\setup.ps1
```

Este script irá criar todo o projeto automaticamente.
