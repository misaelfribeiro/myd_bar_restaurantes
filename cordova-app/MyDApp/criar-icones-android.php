<?php
// Criar ícones para Android em diferentes tamanhos
$iconSizes = [
    ['size' => 36, 'name' => 'icon-36x36.png'],
    ['size' => 48, 'name' => 'icon-48x48.png'], 
    ['size' => 72, 'name' => 'icon-72x72.png'],
    ['size' => 96, 'name' => 'icon-96x96.png'],
    ['size' => 144, 'name' => 'icon-144x144.png'],
];

$splashSizes = [
    ['width' => 320, 'height' => 426, 'name' => 'screen-ldpi-portrait.png'],
    ['width' => 320, 'height' => 470, 'name' => 'screen-mdpi-portrait.png'],
    ['width' => 480, 'height' => 640, 'name' => 'screen-hdpi-portrait.png'],
    ['width' => 720, 'height' => 960, 'name' => 'screen-xhdpi-portrait.png'],
    ['width' => 960, 'height' => 1280, 'name' => 'screen-xxhdpi-portrait.png'],
    ['width' => 1280, 'height' => 1920, 'name' => 'screen-xxxhdpi-portrait.png'],
];

$landscapeSizes = [
    ['width' => 426, 'height' => 320, 'name' => 'screen-ldpi-landscape.png'],
    ['width' => 470, 'height' => 320, 'name' => 'screen-mdpi-landscape.png'],
    ['width' => 640, 'height' => 480, 'name' => 'screen-hdpi-landscape.png'],
    ['width' => 960, 'height' => 720, 'name' => 'screen-xhdpi-landscape.png'],
    ['width' => 1280, 'height' => 960, 'name' => 'screen-xxhdpi-landscape.png'],
    ['width' => 1920, 'height' => 1280, 'name' => 'screen-xxxhdpi-landscape.png'],
];

$iconDir = 'www/icons';
if (!file_exists($iconDir)) {
    mkdir($iconDir, 0777, true);
}

// Cores do tema
$bgColor = [234, 29, 44]; // #EA1D2C
$textColor = [255, 255, 255]; // Branco

// Criar ícones
foreach ($iconSizes as $icon) {
    $size = $icon['size'];
    $filename = $iconDir . '/' . $icon['name'];
    
    $img = imagecreatetruecolor($size, $size);
    $bg = imagecolorallocate($img, $bgColor[0], $bgColor[1], $bgColor[2]);
    $white = imagecolorallocate($img, $textColor[0], $textColor[1], $textColor[2]);
    
    imagefill($img, 0, 0, $bg);
    
    // Texto MyD
    $fontSize = max(8, $size * 0.25);
    $textBox = imagettfbbox($fontSize, 0, __DIR__ . '/arial.ttf', 'MyD');
    $textWidth = $textBox[4] - $textBox[0];
    $textHeight = $textBox[1] - $textBox[5];
    $x = ($size - $textWidth) / 2;
    $y = ($size + $textHeight) / 2;
    
    // Se não tiver Arial, usar imagestring
    if (function_exists('imagettftext') && file_exists(__DIR__ . '/arial.ttf')) {
        imagettftext($img, $fontSize, 0, $x, $y, $white, __DIR__ . '/arial.ttf', 'MyD');
    } else {
        $font = min(5, max(1, floor($size / 20)));
        imagestring($img, $font, ($size - strlen('MyD') * imagefontwidth($font)) / 2, 
                   ($size - imagefontheight($font)) / 2, 'MyD', $white);
    }
    
    imagepng($img, $filename);
    imagedestroy($img);
    echo "✅ Criado: {$icon['name']}\n";
}

// Criar splash screens portrait
foreach ($splashSizes as $splash) {
    $width = $splash['width'];
    $height = $splash['height'];
    $filename = $iconDir . '/' . $splash['name'];
    
    $img = imagecreatetruecolor($width, $height);
    $bg = imagecolorallocate($img, $bgColor[0], $bgColor[1], $bgColor[2]);
    $white = imagecolorallocate($img, $textColor[0], $textColor[1], $textColor[2]);
    
    imagefill($img, 0, 0, $bg);
    
    // Logo grande no centro
    $fontSize = $width * 0.15;
    $font = min(5, max(1, floor($fontSize / 10)));
    $text = 'MyD';
    $textWidth = strlen($text) * imagefontwidth($font);
    $textHeight = imagefontheight($font);
    
    imagestring($img, $font, ($width - $textWidth) / 2, 
               ($height - $textHeight) / 2 - 20, $text, $white);
    
    // Subtítulo
    $subFont = max(1, $font - 1);
    $subText = 'Bar & Restaurantes';
    $subWidth = strlen($subText) * imagefontwidth($subFont);
    imagestring($img, $subFont, ($width - $subWidth) / 2, 
               ($height - imagefontheight($subFont)) / 2 + 20, $subText, $white);
    
    imagepng($img, $filename);
    imagedestroy($img);
    echo "✅ Criado: {$splash['name']}\n";
}

// Criar splash screens landscape
foreach ($landscapeSizes as $splash) {
    $width = $splash['width'];
    $height = $splash['height'];
    $filename = $iconDir . '/' . $splash['name'];
    
    $img = imagecreatetruecolor($width, $height);
    $bg = imagecolorallocate($img, $bgColor[0], $bgColor[1], $bgColor[2]);
    $white = imagecolorallocate($img, $textColor[0], $textColor[1], $textColor[2]);
    
    imagefill($img, 0, 0, $bg);
    
    // Logo no centro
    $fontSize = $height * 0.2;
    $font = min(5, max(1, floor($fontSize / 10)));
    $text = 'MyD';
    $textWidth = strlen($text) * imagefontwidth($font);
    
    imagestring($img, $font, ($width - $textWidth) / 2, 
               ($height - imagefontheight($font)) / 2, $text, $white);
    
    imagepng($img, $filename);
    imagedestroy($img);
    echo "✅ Criado: {$splash['name']}\n";
}

echo "\n🎉 Todos os ícones e splash screens criados!\n";
?>