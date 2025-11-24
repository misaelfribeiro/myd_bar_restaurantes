# Script para remover BOM UTF-8 de arquivos

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "  Removedor de BOM UTF-8" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

$baseDir = "C:\xampp\htdocs\myd_bar_restaurantes"
$bomFiles = @()
$fixedFiles = 0

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

Write-Host "3. Removendo BOM dos arquivos..." -ForegroundColor Yellow

foreach ($file in $bomFiles) {
    try {
        $content = [System.IO.File]::ReadAllBytes($file.FullName)
        if ($content.Length -ge 3 -and $content[0] -eq 0xEF -and $content[1] -eq 0xBB -and $content[2] -eq 0xBF) {
            $newContent = $content[3..($content.Length - 1)]
            [System.IO.File]::WriteAllBytes($file.FullName, $newContent)
            $fixedFiles++
            $relativePath = $file.FullName.Replace($baseDir + "\", "")
            Write-Host "   OK: $relativePath" -ForegroundColor Green
        }
    }
    catch {
        Write-Host "   ERRO: $($file.Name) - $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "  Concluido!" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Arquivos corrigidos: $fixedFiles de $($bomFiles.Count)" -ForegroundColor Green
Write-Host ""