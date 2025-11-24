# Script para remover BOM UTF-8 de arquivos PHP, JS, CSS e Blade

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "  Removedor de BOM UTF-8" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

$baseDir = "C:\xampp\htdocs\myd_bar_restaurantes"
$bomFiles = @()
$fixedFiles = 0

# Pastas para verificar
$folders = @("bootstrap", "database", "public", "resources", "app", "routes", "config")

Write-Host "1. Procurando arquivos com BOM..." -ForegroundColor Yellow

foreach ($folder in $folders) {
    $path = Join-Path $baseDir $folder
    if (Test-Path $path) {
        Get-ChildItem -Path $path -Include "*.php","*.js","*.css","*.blade.php" -Recurse -ErrorAction SilentlyContinue | ForEach-Object {
            $bytes = Get-Content $_.FullName -Encoding Byte -TotalCount 3 -ErrorAction SilentlyContinue
            if ($bytes -and $bytes.Length -ge 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) {
                $bomFiles += $_
            }
        }
    }
}

Write-Host "   Encontrados: $($bomFiles.Count) arquivos" -ForegroundColor $(if($bomFiles.Count -gt 0){'Red'}else{'Green'})
Write-Host ""

if ($bomFiles.Count -eq 0) {
    Write-Host "Nenhum arquivo com BOM encontrado!" -ForegroundColor Green
    exit 0
}

Write-Host "2. Deseja remover o BOM de todos os arquivos? (S/N)" -ForegroundColor Yellow
$resposta = Read-Host

if ($resposta -ne "S" -and $resposta -ne "s") {
    Write-Host "Operação cancelada." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "3. Removendo BOM dos arquivos..." -ForegroundColor Yellow

foreach ($file in $bomFiles) {
    try {
        # Ler conteúdo completo
        $content = Get-Content $file.FullName -Raw -Encoding UTF8
        
        # Remover BOM se existir
        if ($content.Length -gt 0 -and $content[0] -eq [char]0xFEFF) {
            $content = $content.Substring(1)
        }
        
        # Salvar sem BOM
        $utf8NoBom = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($file.FullName, $content, $utf8NoBom)
        
        $fixedFiles++
        $relativePath = $file.FullName.Replace($baseDir + "\", "")
        Write-Host "   ✓ $relativePath" -ForegroundColor Green
    }
    catch {
        Write-Host "   ✗ Erro ao processar: $($file.Name)" -ForegroundColor Red
        Write-Host "     $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "  Concluído!" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Arquivos corrigidos: $fixedFiles de $($bomFiles.Count)" -ForegroundColor Green
Write-Host ""
Write-Host "IMPORTANTE:" -ForegroundColor Yellow
Write-Host "- Teste seu sistema após a remoção" -ForegroundColor White
Write-Host "- Verifique se não há erros de sintaxe" -ForegroundColor White
Write-Host "- Faça commit das alterações" -ForegroundColor White
Write-Host ""
