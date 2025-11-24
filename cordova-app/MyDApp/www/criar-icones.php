<?php
// Gerador de ícones PWA simples

$sizes = [192, 512];

foreach ($sizes as $size) {
    // Criar imagem
    $img = imagecreatetruecolor($size, $size);
    
    // Cores
    $vermelho = imagecolorallocate($img, 234, 29, 44); // #EA1D2C
    $branco = imagecolorallocate($img, 255, 255, 255);
    
    // Fundo vermelho
    imagefilledrectangle($img, 0, 0, $size, $size, $vermelho);
    
    // Adicionar texto "MyD"
    $fontSize = $size * 0.3;
    $fontFile = 'C:/Windows/Fonts/arialbd.ttf'; // Arial Bold
    
    // Se não encontrar Arial, usar fonte padrão
    if (file_exists($fontFile)) {
        // Calcular posição centralizada
        $bbox = imagettfbbox($fontSize, 0, $fontFile, 'MyD');
        $x = ($size - ($bbox[2] - $bbox[0])) / 2;
        $y = ($size - ($bbox[7] - $bbox[1])) / 2;
        
        imagettftext($img, $fontSize, 0, $x, $y, $branco, $fontFile, 'MyD');
    } else {
        // Fonte interna do GD
        $text = 'MyD';
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $x = ($size - $textWidth) / 2;
        $y = ($size - $textHeight) / 2;
        imagestring($img, $font, $x, $y, $text, $branco);
    }
    
    // Salvar
    $filename = __DIR__ . "/icons/icon-{$size}x{$size}.png";
    imagepng($img, $filename);
    imagedestroy($img);
    
    echo "✅ Criado: icon-{$size}x{$size}.png\n";
}

echo "\n🎉 Ícones criados com sucesso!\n";
echo "Recarregue a página e teste a instalação.\n";
