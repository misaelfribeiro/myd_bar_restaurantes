<?php
// Criar ícones para Android Studio

$outputDir = 'app/src/main/res';

// Tamanhos DPI padrão Android
$sizes = [
    'ldpi' => 36,
    'mdpi' => 48,
    'hdpi' => 72,
    'xhdpi' => 96,
    'xxhdpi' => 144,
    'xxxhdpi' => 192
];

$bgColor = [234, 29, 44]; // #EA1D2C
$textColor = [255, 255, 255]; // Branco

foreach ($sizes as $dpi => $size) {
    $dir = "$outputDir/mipmap-$dpi";
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Criar ic_launcher.png
    $img = imagecreatetruecolor($size, $size);
    $bg = imagecolorallocate($img, $bgColor[0], $bgColor[1], $bgColor[2]);
    $white = imagecolorallocate($img, $textColor[0], $textColor[1], $textColor[2]);
    
    imagefill($img, 0, 0, $bg);
    
    // Desenhar M de MyD
    $font = max(1, floor($size / 3));
    $text = 'M';
    $textWidth = strlen($text) * imagefontwidth($font);
    $textHeight = imagefontheight($font);
    
    $x = ($size - $textWidth) / 2;
    $y = ($size - $textHeight) / 2;
    
    imagestring($img, $font, $x, $y, $text, $white);
    
    imagepng($img, "$dir/ic_launcher.png");
    imagedestroy($img);
    
    // Criar ic_launcher_round.png (com border-radius)
    $img = imagecreatetruecolor($size, $size);
    
    // Criar imagem com fundo redondo
    $bg = imagecolorallocate($img, $bgColor[0], $bgColor[1], $bgColor[2]);
    $white = imagecolorallocate($img, $textColor[0], $textColor[1], $textColor[2]);
    
    imagefill($img, 0, 0, $bg);
    
    // Desenhar círculo branco e depois remover cantos
    imagefilledarc($img, $size/2, $size/2, $size, $size, 0, 360, $bg, IMG_ARC_PIE);
    imagestring($img, $font, $x, $y, $text, $white);
    
    imagepng($img, "$dir/ic_launcher_round.png");
    imagedestroy($img);
    
    echo "✅ Ícones criados para $dpi ($size x $size px)\n";
}

echo "\n🎉 Todos os ícones foram criados em: app/src/main/res/mipmap-*/\n";
?>
