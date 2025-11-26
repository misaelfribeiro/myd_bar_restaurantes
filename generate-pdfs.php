<?php
/**
 * Script para gerar PDFs da documentação da API
 * EATSFOOD - LTDA
 * Tecnologia que alimenta resultados
 */

require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

// Configuração do Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$options->set('chroot', realpath(''));

// Função para converter Markdown para HTML usando CommonMark
function markdownToHtml($markdown) {
    $environment = new Environment([
        'html_input' => 'allow',
        'allow_unsafe_links' => false,
    ]);

    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new TableExtension());

    $converter = new MarkdownConverter($environment);
    return $converter->convert($markdown)->getContent();
}

// Função para adicionar estilo e estrutura HTML
function wrapHtmlDocument($title, $content) {
    return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>$title</title>
    <style>
        @page {
            margin: 2.5cm 2cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #1f2937;
        }
        .cover-page {
            text-align: center;
            padding-top: 3.5cm;
            page-break-after: always;
        }
        .cover-logo {
            width: 2.5cm;
            height: 2.5cm;
            margin: 0 auto 0.7cm auto;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb 60%, #1e40af 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cover-logo span {
            color: #fff;
            font-size: 1.3cm;
            font-weight: bold;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            letter-spacing: 2px;
        }
        .cover-page .company {
            font-size: 20pt;
            color: #2563eb;
            margin-bottom: 0.1cm;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .cover-page .slogan {
            font-size: 12pt;
            color: #64748b;
            font-style: italic;
            margin-bottom: 1.2cm;
        }
        .cover-page h1 {
            font-size: 22pt;
            color: #1e40af;
            margin-bottom: 0.7cm;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .cover-page .version {
            font-size: 11pt;
            color: #6b7280;
            margin-top: 2.5cm;
            margin-bottom: 0;
            font-weight: bold;
        }
        
        .page-header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 0.3cm;
            margin-bottom: 0.8cm;
        }
        
        h1 {
            color: #1e40af;
            font-size: 20pt;
            margin-top: 0.8cm;
            margin-bottom: 0.5cm;
            page-break-after: avoid;
        }
        
        h2 {
            color: #1e40af;
            font-size: 16pt;
            margin-top: 0.7cm;
            margin-bottom: 0.4cm;
            border-bottom: 2px solid #93c5fd;
            padding-bottom: 0.2cm;
            page-break-after: avoid;
        }
        
        h3 {
            color: #1e3a8a;
            font-size: 13pt;
            margin-top: 0.6cm;
            margin-bottom: 0.3cm;
            page-break-after: avoid;
        }
        
        h4 {
            color: #1e3a8a;
            font-size: 11pt;
            margin-top: 0.4cm;
            margin-bottom: 0.3cm;
            page-break-after: avoid;
        }
        
        p {
            margin-bottom: 0.4cm;
            text-align: justify;
        }
        
        ul, ol {
            margin-left: 0.8cm;
            margin-bottom: 0.4cm;
        }
        
        li {
            margin-bottom: 0.15cm;
        }
        
        code {
            background-color: #f1f5f9;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            color: #dc2626;
        }
        
        pre {
            background-color: #1e293b;
            color: #e2e8f0;
            padding: 0.4cm;
            border-radius: 4px;
            overflow-x: auto;
            margin: 0.4cm 0;
            page-break-inside: avoid;
            border-left: 4px solid #3b82f6;
        }
        
        pre code {
            background-color: transparent;
            color: #e2e8f0;
            padding: 0;
            font-size: 8pt;
            line-height: 1.4;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.5cm 0;
            page-break-inside: avoid;
            font-size: 9pt;
        }
        
        th, td {
            border: 1px solid #cbd5e1;
            padding: 0.2cm;
            text-align: left;
        }
        
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        a {
            color: #2563eb;
            text-decoration: underline;
        }
        
        strong {
            color: #1e40af;
            font-weight: bold;
        }
        
        blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 0.5cm;
            margin: 0.4cm 0;
            color: #475569;
            background-color: #f1f5f9;
            padding: 0.3cm 0.5cm;
        }
        
        hr {
            border: none;
            border-top: 2px solid #cbd5e1;
            margin: 0.6cm 0;
        }
        
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            margin-top: 1cm;
            padding-top: 0.3cm;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="cover-page">
        <div class="cover-logo"><span>EF</span></div>
        <div class="company">EATSFOOD - LTDA</div>
        <div class="slogan">Tecnologia que alimenta resultados</div>
        <h1>$title</h1>
        <div class="version">Versão 1.0.0 | Novembro 2025</div>
    </div>
    
    <div class="page-header">
        <strong style="color: #2563eb;">EATSFOOD - LTDA</strong> | Tecnologia que alimenta resultados
    </div>
    
    $content
    
    <div class="footer">
        <strong>Produto:</strong> EATSFOOD - LTDA<br>
        <span style="color:#64748b;">Tecnologia que alimenta resultados</span><br><br>
        Empresa responsável: MYD Facilyta Technology | CNPJ: 24.223.868/0001-19<br>
        Av dos Holandeses/Cons. Hilton Rodrigues, 15, Olho D'Água, São Luís/MA<br>
        E-mail: misael_ribeiro@mydsistemas.com.br | Telefone: (98) 8848-9512<br><br>
        © 2025 MYD Facilyta Technology. Todos os direitos reservados.<br>
        Este documento é confidencial e destinado exclusivamente aos parceiros autorizados.
    </div>
</body>
</html>
HTML;
}

// Arquivos para processar
$files = [
    [
        'input' => 'API_INTEGRACAO_PARCEIROS.md',
        'output' => 'docs/API_Integracao_Parceiros.pdf',
        'title' => 'API de Integração para Parceiros'
    ],
    [
        'input' => 'IMPLEMENTACAO_API_PARCEIROS.md',
        'output' => 'docs/Implementacao_API_Parceiros.pdf',
        'title' => 'Guia de Implementação - API de Parceiros'
    ]
];

// Criar diretório docs se não existir
if (!is_dir('docs')) {
    mkdir('docs', 0755, true);
}

echo "=== Geração de PDFs - EATSFOOD ===\n\n";

foreach ($files as $file) {
    echo "Processando: {$file['input']}...\n";
    
    // Ler arquivo Markdown
    if (!file_exists($file['input'])) {
        echo "  ❌ Arquivo não encontrado: {$file['input']}\n";
        continue;
    }
    
    $markdown = file_get_contents($file['input']);
    
    // Converter para HTML
    echo "  → Convertendo Markdown para HTML...\n";
    $htmlContent = markdownToHtml($markdown);
    
    // Adicionar estrutura HTML completa
    $fullHtml = wrapHtmlDocument($file['title'], $htmlContent);
    
    // Gerar PDF
    echo "  → Gerando PDF...\n";
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($fullHtml);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Salvar PDF
    $output = $dompdf->output();
    file_put_contents($file['output'], $output);
    
    $fileSize = filesize($file['output']);
    $fileSizeKB = round($fileSize / 1024, 2);
    
    echo "  ✅ PDF gerado com sucesso!\n";
    echo "     Arquivo: {$file['output']}\n";
    echo "     Tamanho: {$fileSizeKB} KB\n\n";
}

echo "=== Processo concluído! ===\n";
echo "Os PDFs estão disponíveis no diretório 'docs/'\n";
