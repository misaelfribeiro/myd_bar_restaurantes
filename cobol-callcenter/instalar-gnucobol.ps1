# Instalador Automatico GnuCOBOL - EATSFOOD
# Baixa e configura versao pre-compilada

Write-Host "==========================================" -ForegroundColor Green
Write-Host "  INSTALADOR GNUCOBOL - EATSFOOD" -ForegroundColor Yellow
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""

# Verificar se esta executando como Administrador
$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "AVISO: Execute como Administrador para configurar PATH automaticamente" -ForegroundColor Yellow
    Write-Host "Continuando com instalacao local..." -ForegroundColor Cyan
    Write-Host ""
}

# Definir caminhos
$downloadUrl = "https://sourceforge.net/projects/gnucobol/files/gnucobol/3.1/GnuCOBOL_3.1.2_MinGW_x86_64.zip/download"
$tempZip = "$env:TEMP\GnuCOBOL_MinGW.zip"
$installPath = "C:\GnuCOBOL"

Write-Host "[1/5] Baixando GnuCOBOL 3.1.2 (versao pre-compilada)..." -ForegroundColor Cyan
Write-Host "URL: $downloadUrl" -ForegroundColor Gray
Write-Host "Tamanho: ~45MB - Aguarde..." -ForegroundColor Gray
Write-Host ""

try {
    # Habilitar TLS 1.2
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
    
    # Baixar com barra de progresso
    $ProgressPreference = 'SilentlyContinue'
    Invoke-WebRequest -Uri $downloadUrl -OutFile $tempZip -UseBasicParsing
    $ProgressPreference = 'Continue'
    
    Write-Host "[OK] Download concluido!" -ForegroundColor Green
    Write-Host ""
    
} catch {
    Write-Host "[ERRO] Falha ao baixar: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Baixe manualmente de:" -ForegroundColor Yellow
    Write-Host "https://sourceforge.net/projects/gnucobol/files/gnucobol/3.1/" -ForegroundColor Cyan
    Write-Host "Arquivo: GnuCOBOL_3.1.2_MinGW_x86_64.zip" -ForegroundColor Cyan
    pause
    exit 1
}

Write-Host "[2/5] Limpando instalacao anterior..." -ForegroundColor Cyan
if (Test-Path $installPath) {
    Remove-Item "$installPath\*" -Recurse -Force -ErrorAction SilentlyContinue
} else {
    New-Item -ItemType Directory -Path $installPath | Out-Null
}
Write-Host "[OK] Pasta preparada" -ForegroundColor Green
Write-Host ""

Write-Host "[3/5] Extraindo arquivos..." -ForegroundColor Cyan
try {
    Expand-Archive -Path $tempZip -DestinationPath $installPath -Force
    Write-Host "[OK] Arquivos extraidos" -ForegroundColor Green
} catch {
    Write-Host "[ERRO] Falha ao extrair: $_" -ForegroundColor Red
    pause
    exit 1
}
Write-Host ""

# Verificar estrutura extraida
$extractedFolder = Get-ChildItem -Path $installPath -Directory | Select-Object -First 1
if ($extractedFolder) {
    Write-Host "Movendo arquivos da pasta: $($extractedFolder.Name)" -ForegroundColor Gray
    Get-ChildItem -Path $extractedFolder.FullName | Move-Item -Destination $installPath -Force
    Remove-Item $extractedFolder.FullName -Force -ErrorAction SilentlyContinue
}

Write-Host "[4/5] Verificando executaveis..." -ForegroundColor Cyan
$cobcExe = "$installPath\bin\cobc.exe"
if (Test-Path $cobcExe) {
    Write-Host "[OK] cobc.exe encontrado!" -ForegroundColor Green
    Write-Host "Localizacao: $cobcExe" -ForegroundColor Gray
} else {
    Write-Host "[AVISO] cobc.exe nao encontrado em bin\" -ForegroundColor Yellow
    Write-Host "Procurando em outras pastas..." -ForegroundColor Gray
    
    $found = Get-ChildItem -Path $installPath -Filter "cobc.exe" -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($found) {
        Write-Host "[OK] Encontrado em: $($found.DirectoryName)" -ForegroundColor Green
        $installPath = $found.DirectoryName -replace '\\bin$', ''
    } else {
        Write-Host "[ERRO] Executaveis nao encontrados!" -ForegroundColor Red
        Write-Host "Verifique se baixou a versao MinGW (pre-compilada)" -ForegroundColor Yellow
        pause
        exit 1
    }
}
Write-Host ""

Write-Host "[5/5] Configurando variaveis de ambiente..." -ForegroundColor Cyan

$binPath = "$installPath\bin"
$configPath = "$installPath\config"
$copyPath = "$installPath\copy"

if ($isAdmin) {
    try {
        # Adicionar ao PATH do sistema
        $currentPath = [Environment]::GetEnvironmentVariable("Path", "Machine")
        if ($currentPath -notlike "*$binPath*") {
            [Environment]::SetEnvironmentVariable("Path", "$currentPath;$binPath", "Machine")
            Write-Host "[OK] PATH do sistema atualizado" -ForegroundColor Green
        } else {
            Write-Host "[OK] PATH ja configurado" -ForegroundColor Green
        }
        
        # Configurar variaveis COBOL
        [Environment]::SetEnvironmentVariable("COB_CONFIG_DIR", $configPath, "Machine")
        [Environment]::SetEnvironmentVariable("COB_COPY_DIR", $copyPath, "Machine")
        [Environment]::SetEnvironmentVariable("COB_LIBRARY_PATH", "$installPath\lib", "Machine")
        
        Write-Host "[OK] Variaveis de ambiente configuradas" -ForegroundColor Green
        
    } catch {
        Write-Host "[AVISO] Erro ao configurar PATH: $_" -ForegroundColor Yellow
        $isAdmin = $false
    }
}

if (-not $isAdmin) {
    Write-Host "[MANUAL] Execute estes comandos como Administrador:" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "[Environment]::SetEnvironmentVariable('Path', [Environment]::GetEnvironmentVariable('Path', 'Machine') + ';$binPath', 'Machine')" -ForegroundColor Cyan
    Write-Host "[Environment]::SetEnvironmentVariable('COB_CONFIG_DIR', '$configPath', 'Machine')" -ForegroundColor Cyan
    Write-Host "[Environment]::SetEnvironmentVariable('COB_COPY_DIR', '$copyPath', 'Machine')" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Green
Write-Host "  INSTALACAO CONCLUIDA!" -ForegroundColor Yellow
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""

# Limpar arquivo temporario
Remove-Item $tempZip -Force -ErrorAction SilentlyContinue

# Adicionar ao PATH da sessao atual
$env:Path += ";$binPath"
$env:COB_CONFIG_DIR = $configPath
$env:COB_COPY_DIR = $copyPath

Write-Host "Testando instalacao..." -ForegroundColor Cyan
Write-Host ""

# Testar cobc
try {
    $version = & "$binPath\cobc.exe" --version 2>&1 | Select-Object -First 1
    Write-Host "[OK] $version" -ForegroundColor Green
    Write-Host ""
    Write-Host "GnuCOBOL instalado com sucesso!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Proximos passos:" -ForegroundColor Yellow
    Write-Host "1. Feche e reabra o PowerShell" -ForegroundColor White
    Write-Host "2. Execute: cd c:\xampp\htdocs\myd_bar_restaurantes\cobol-callcenter" -ForegroundColor White
    Write-Host "3. Execute: php sync-data.php" -ForegroundColor White
    Write-Host "4. Execute: .\compilar.bat" -ForegroundColor White
    
} catch {
    Write-Host "[ERRO] Falha ao executar cobc: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Tente executar manualmente:" -ForegroundColor Yellow
    Write-Host "$binPath\cobc.exe --version" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "Pressione qualquer tecla para sair..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
