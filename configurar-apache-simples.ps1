# Script de Configuracao do Apache - MyD Bar e Restaurantes
# Execute como ADMINISTRADOR

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Configuracao do Apache - MyD System  " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verificar se esta rodando como administrador
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "ERRO: Execute este script como ADMINISTRADOR!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Clique com botao direito no PowerShell e selecione 'Executar como Administrador'" -ForegroundColor Yellow
    pause
    exit
}

Write-Host "Script rodando como Administrador" -ForegroundColor Green
Write-Host ""

# Passo 1: Adicionar Include no httpd.conf
Write-Host "Passo 1: Configurando httpd.conf..." -ForegroundColor Yellow
$httpdConf = "C:\xampp\apache\conf\httpd.conf"

if (Test-Path $httpdConf) {
    $content = Get-Content $httpdConf -Raw
    $includeString = "Include conf/extra/httpd-vhosts-myd.conf"
    
    if ($content -notmatch [regex]::Escape($includeString)) {
        Add-Content $httpdConf "`n# MyD Bar e Restaurantes Virtual Host"
        Add-Content $httpdConf $includeString
        Write-Host "   Include adicionado ao httpd.conf" -ForegroundColor Green
    } else {
        Write-Host "   Include ja existe no httpd.conf" -ForegroundColor Cyan
    }
} else {
    Write-Host "   httpd.conf nao encontrado!" -ForegroundColor Red
}

Write-Host ""

# Passo 2: Adicionar entradas no arquivo hosts
Write-Host "Passo 2: Configurando arquivo hosts..." -ForegroundColor Yellow
$hostsFile = "C:\Windows\System32\drivers\etc\hosts"

if (Test-Path $hostsFile) {
    $hostsContent = Get-Content $hostsFile -Raw
    $mydLocal = "127.0.0.1    myd.local"
    $wwwMydLocal = "127.0.0.1    www.myd.local"
    
    if ($hostsContent -notmatch "myd\.local") {
        Add-Content $hostsFile "`n# MyD Bar e Restaurantes"
        Add-Content $hostsFile $mydLocal
        Add-Content $hostsFile $wwwMydLocal
        Write-Host "   Entradas adicionadas ao arquivo hosts" -ForegroundColor Green
    } else {
        Write-Host "   Entradas ja existem no arquivo hosts" -ForegroundColor Cyan
    }
} else {
    Write-Host "   Arquivo hosts nao encontrado!" -ForegroundColor Red
}

Write-Host ""

# Passo 3: Configurar permissoes do Laravel
Write-Host "Passo 3: Configurando permissoes do Laravel..." -ForegroundColor Yellow
$projectPath = "C:\xampp\htdocs\myd_bar_restaurantes"

if (Test-Path $projectPath) {
    try {
        icacls "$projectPath\storage" /grant Everyone:(OI)(CI)F /T /Q | Out-Null
        Write-Host "   Permissoes do storage configuradas" -ForegroundColor Green
        
        icacls "$projectPath\bootstrap\cache" /grant Everyone:(OI)(CI)F /T /Q | Out-Null
        Write-Host "   Permissoes do bootstrap/cache configuradas" -ForegroundColor Green
    } catch {
        Write-Host "   Erro ao configurar permissoes" -ForegroundColor Yellow
    }
} else {
    Write-Host "   Projeto nao encontrado" -ForegroundColor Red
}

Write-Host ""

# Passo 4: Limpar cache do Laravel
Write-Host "Passo 4: Limpando cache do Laravel..." -ForegroundColor Yellow
Set-Location $projectPath

try {
    php artisan cache:clear | Out-Null
    Write-Host "   Cache limpo" -ForegroundColor Green
    
    php artisan config:clear | Out-Null
    Write-Host "   Config limpo" -ForegroundColor Green
    
    php artisan route:clear | Out-Null
    Write-Host "   Rotas limpas" -ForegroundColor Green
} catch {
    Write-Host "   Erro ao limpar cache" -ForegroundColor Yellow
}

Write-Host ""

# Passo 5: Atualizar .env
Write-Host "Passo 5: Atualizando .env..." -ForegroundColor Yellow
$envFile = "$projectPath\.env"

if (Test-Path $envFile) {
    $envContent = Get-Content $envFile
    $newContent = $envContent -replace 'APP_URL=.*', 'APP_URL=http://myd.local'
    $newContent | Set-Content $envFile
    Write-Host "   APP_URL atualizado para http://myd.local" -ForegroundColor Green
} else {
    Write-Host "   Arquivo .env nao encontrado" -ForegroundColor Yellow
}

Write-Host ""

# Passo 6: Reiniciar Apache
Write-Host "Passo 6: Reiniciando Apache..." -ForegroundColor Yellow
Write-Host "   Parando Apache..." -ForegroundColor Cyan

try {
    Stop-Service -Name "Apache2.4" -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
    Write-Host "   Apache parado" -ForegroundColor Green
    
    Write-Host "   Iniciando Apache..." -ForegroundColor Cyan
    Start-Service -Name "Apache2.4" -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
    Write-Host "   Apache iniciado" -ForegroundColor Green
} catch {
    Write-Host "   Use o XAMPP Control Panel para reiniciar manualmente" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Configuracao Concluida!               " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Acesse o sistema:" -ForegroundColor Yellow
Write-Host "   http://myd.local/login-niveis" -ForegroundColor White
Write-Host ""
Write-Host "Usuarios demo:" -ForegroundColor Yellow
Write-Host "   admin@exemplo.com / 123456" -ForegroundColor White
Write-Host "   gerente@exemplo.com / 123456" -ForegroundColor White
Write-Host "   garcom@exemplo.com / 123456" -ForegroundColor White
Write-Host "   caixa@exemplo.com / 123456" -ForegroundColor White
Write-Host ""
Write-Host "Pressione qualquer tecla para sair..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
