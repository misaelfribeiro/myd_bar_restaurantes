<?php
$sizes = [192, 512];
foreach ($sizes as $size) {
 $img = imagecreatetruecolor($size, $size);
 $vermelho = imagecolorallocate($img, 234, 29, 44);
 $branco = imagecolorallocate($img, 255, 255, 255);
 imagefilledrectangle($img, 0, 0, $size, $size, $vermelho);
 $fontSize = $size * 0.3;
 $fontFile = 'C:/Windows/Fonts/arialbd.ttf';
 if (file_exists($fontFile)) {
 $bbox = imagettfbbox($fontSize, 0, $fontFile, 'MyD');
 $x = ($size - ($bbox[2] - $bbox[0])) / 2;
 $y = ($size - ($bbox[7] - $bbox[1])) / 2;
 imagettftext($img, $fontSize, 0, $x, $y, $branco, $fontFile, 'MyD');
 } else {
 $text = 'MyD';
 $font = 5;
 $textWidth = imagefontwidth($font) * strlen($text);
 $textHeight = imagefontheight($font);
 $x = ($size - $textWidth) / 2;
 $y = ($size - $textHeight) / 2;
 imagestring($img, $font, $x, $y, $text, $branco);
 }
 $filename = __DIR__ . "/icons/icon-{$size}x{$size}.png";
 imagepng($img, $filename);
 imagedestroy($img);
 echo "✅ Criado: icon-{$size}x{$size}.png\n";
}
echo "\n🎉 Ícones criados com sucesso!\n";
echo "Recarregue a página e teste a instalação.\n";