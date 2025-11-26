# Script de Setup Automático - Call Center Desktop
# EATSFOOD

Write-Host "==========================================" -ForegroundColor Green
Write-Host "  SETUP CALL CENTER DESKTOP - EATSFOOD" -ForegroundColor Yellow
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""

# Verificar .NET SDK
Write-Host "[1/6] Verificando .NET SDK..." -ForegroundColor Cyan
try {
    $dotnetVersion = dotnet --version
    Write-Host "[OK] .NET SDK $dotnetVersion instalado" -ForegroundColor Green
} catch {
    Write-Host "[ERRO] .NET SDK não encontrado!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Por favor, instale o .NET 8 SDK:" -ForegroundColor Yellow
    Write-Host "https://dotnet.microsoft.com/download/dotnet/8.0" -ForegroundColor Cyan
    Write-Host ""
    pause
    exit 1
}

Write-Host ""
Write-Host "[2/6] Criando projeto WinForms..." -ForegroundColor Cyan

# Criar projeto
if (Test-Path "EatsFoodCallCenter") {
    Write-Host "Projeto já existe. Removendo..." -ForegroundColor Yellow
    Remove-Item -Path "EatsFoodCallCenter" -Recurse -Force
}

dotnet new winforms -n EatsFoodCallCenter -f net8.0-windows | Out-Null
Write-Host "[OK] Projeto criado" -ForegroundColor Green

Write-Host ""
Write-Host "[3/6] Instalando pacotes NuGet..." -ForegroundColor Cyan

cd EatsFoodCallCenter

# Adicionar pacotes
Write-Host "  - MySql.Data..." -ForegroundColor Gray
dotnet add package MySql.Data --version 8.2.0 | Out-Null

Write-Host "  - Newtonsoft.Json..." -ForegroundColor Gray
dotnet add package Newtonsoft.Json --version 13.0.3 | Out-Null

Write-Host "  - BCrypt.Net-Next..." -ForegroundColor Gray
dotnet add package BCrypt.Net-Next --version 4.0.3 | Out-Null

Write-Host "[OK] Pacotes instalados" -ForegroundColor Green

Write-Host ""
Write-Host "[4/6] Criando estrutura de pastas..." -ForegroundColor Cyan

# Criar pastas
$folders = @("Forms", "Models", "Services", "Utils", "Resources")
foreach ($folder in $folders) {
    New-Item -ItemType Directory -Path $folder -Force | Out-Null
    Write-Host "  - $folder/" -ForegroundColor Gray
}

Write-Host "[OK] Estrutura criada" -ForegroundColor Green

Write-Host ""
Write-Host "[5/6] Gerando arquivos de configuração..." -ForegroundColor Cyan

# Criar appsettings.json
$appSettings = @"
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
    "PasswordMinLength": 6
  },
  "Company": {
    "Name": "EATSFOOD",
    "Support": "suporte@eatsfood.com",
    "Phone": "(11) 99999-9999"
  }
}
"@

$appSettings | Out-File -FilePath "appsettings.json" -Encoding UTF8

Write-Host "[OK] Configurações geradas" -ForegroundColor Green

Write-Host ""
Write-Host "[6/6] Compilando projeto..." -ForegroundColor Cyan

dotnet build | Out-Null

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Projeto compilado com sucesso!" -ForegroundColor Green
} else {
    Write-Host "[AVISO] Houve erros na compilação" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Green
Write-Host "  SETUP CONCLUÍDO!" -ForegroundColor Yellow
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Próximos passos:" -ForegroundColor Cyan
Write-Host "1. Aguarde a criação dos arquivos .cs" -ForegroundColor White
Write-Host "2. Execute: dotnet run" -ForegroundColor White
Write-Host "3. Para publicar: dotnet publish -c Release" -ForegroundColor White
Write-Host ""

cd ..
