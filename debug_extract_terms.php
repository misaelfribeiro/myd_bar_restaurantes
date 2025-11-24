<?php

$message = 'a mais barata';
$text = mb_strtolower($message);

// Remove palavras comuns
$stopWords = ['o', 'a', 'de', 'da', 'do', 'para', 'com', 'sem', 'mais', 'menos', 'quero', 'busca', 'procura', 'mostra', 'me', 'um', 'uma'];

$words = preg_split('/\s+/', $text);
echo "Palavras: " . json_encode($words) . "\n";

$terms = array_filter($words, function($word) use ($stopWords) {
    return strlen($word) > 2 && !in_array($word, $stopWords);
});

echo "Termos filtrados: " . json_encode(array_values($terms)) . "\n";
echo "empty(): " . (empty($terms) ? 'TRUE' : 'FALSE') . "\n";
