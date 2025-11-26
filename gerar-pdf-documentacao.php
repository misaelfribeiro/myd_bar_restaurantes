<?php
/**
 * Script para gerar PDFs da documentação da API de Parceiros
 * 
 * Uso: php gerar-pdf-documentacao.php
 */

require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use Parsedown;

class DocumentacaoToPDF
{
    private $dompdf;
    private $parsedown;

    public function __construct()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        
        $this->dompdf = new Dompdf($options);
        $this->parsedown = new Parsedown();
    }

    public function converterMarkdownParaPDF($arquivoMd, $arquivoPdf)
    {
        echo "Convertendo {$arquivoMd}...\n";

        if (!file_exists($arquivoMd)) {
            echo "Erro: Arquivo {$arquivoMd} não encontrado!\n";
            return false;
        }

        // Ler arquivo Markdown
        $markdown = file_get_contents($arquivoMd);

        // Converter Markdown para HTML
        $html = $this->parsedown->text($markdown);

        // Adicionar CSS para melhor formatação
        $htmlCompleto = $this->gerarHTMLCompleto($html);

        // Gerar PDF
        $this->dompdf->loadHtml($htmlCompleto);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        // Salvar PDF
        file_put_contents($arquivoPdf, $this->dompdf->output());

        echo "✅ PDF gerado: {$arquivoPdf}\n";
        return true;
    }

    private function gerarHTMLCompleto($conteudo)
    {
        // Limpar caracteres problemáticos
        $conteudo = str_replace(['```php', '```javascript', '```python', '```json', '```http', '```bash', '```'], ['<pre><code>', '<pre><code>', '<pre><code>', '<pre><code>', '<pre><code>', '<pre><code>', '</code></pre>'], $conteudo);
        
        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 2cm;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #333;
        }
        
        h1 {
            color: #EA1D2C;
            font-size: 24pt;
            border-bottom: 3px solid #EA1D2C;
            padding-bottom: 10px;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        
        h2 {
            color: #2C3E50;
            font-size: 18pt;
            border-bottom: 2px solid #ECF0F1;
            padding-bottom: 8px;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        
        h3 {
            color: #34495E;
            font-size: 14pt;
            margin-top: 20px;
            margin-bottom: 12px;
        }
        
        h4 {
            color: #7F8C8D;
            font-size: 12pt;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        
        code {
            background-color: #F8F9FA;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            color: #C7254E;
        }
        
        pre {
            background-color: #F8F9FA;
            border: 1px solid #E1E4E8;
            border-radius: 5px;
            padding: 12px;
            overflow-x: auto;
            margin: 15px 0;
        }
        
        pre code {
            background-color: transparent;
            padding: 0;
            color: #24292E;
            font-size: 8pt;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
        }
        
        table thead {
            background-color: #EA1D2C;
            color: white;
        }
        
        table th, table td {
            border: 1px solid #DDD;
            padding: 8px;
            text-align: left;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #F8F9FA;
        }
        
        ul, ol {
            margin: 10px 0;
            padding-left: 25px;
        }
        
        li {
            margin: 5px 0;
        }
        
        blockquote {
            border-left: 4px solid #EA1D2C;
            padding-left: 15px;
            margin: 15px 0;
            color: #666;
            font-style: italic;
        }
        
        a {
            color: #EA1D2C;
            text-decoration: none;
        }
        
        .alert {
            padding: 12px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid;
        }
        
        .alert-info {
            background-color: #D1ECF1;
            border-color: #0C5460;
            color: #0C5460;
        }
        
        .alert-warning {
            background-color: #FFF3CD;
            border-color: #856404;
            color: #856404;
        }
        
        .alert-success {
            background-color: #D4EDDA;
            border-color: #155724;
            color: #155724;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        hr {
            border: none;
            border-top: 2px solid #ECF0F1;
            margin: 25px 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header img {
            max-width: 200px;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #7F8C8D;
            border-top: 1px solid #ECF0F1;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MyD Bar & Restaurantes</h1>
        <p style="color: #7F8C8D; font-size: 12pt;">Documentação Técnica</p>
        <p style="color: #95A5A6; font-size: 10pt;">Gerado em: <?php echo date('d/m/Y H:i'); ?></p>
    </div>
    
    {$conteudo}
    
    <div class="footer">
        <p>© 2025 MyD Delivery Systems - Todos os direitos reservados | Versão 1.0.0</p>
    </div>
</body>
</html>
HTML;
    }
}

// Verificar se Dompdf está instalado
if (!class_exists('Dompdf\Dompdf')) {
    echo "❌ Erro: Dompdf não está instalado!\n";
    echo "Execute: composer require dompdf/dompdf\n";
    echo "        composer require erusev/parsedown\n";
    exit(1);
}

// Criar instância
$conversor = new DocumentacaoToPDF();

// Arquivos para converter
$arquivos = [
    [
        'md' => __DIR__ . '/API_INTEGRACAO_PARCEIROS.md',
        'pdf' => __DIR__ . '/docs/API_Integracao_Parceiros.pdf'
    ],
    [
        'md' => __DIR__ . '/IMPLEMENTACAO_API_PARCEIROS.md',
        'pdf' => __DIR__ . '/docs/Implementacao_API_Parceiros_Tecnico.pdf'
    ]
];

// Criar diretório docs se não existir
if (!is_dir(__DIR__ . '/docs')) {
    mkdir(__DIR__ . '/docs', 0755, true);
    echo "📁 Diretório 'docs' criado\n";
}

echo "\n🚀 Iniciando conversão de documentação para PDF...\n\n";

// Converter cada arquivo
$sucesso = 0;
$falha = 0;

foreach ($arquivos as $arquivo) {
    if ($conversor->converterMarkdownParaPDF($arquivo['md'], $arquivo['pdf'])) {
        $sucesso++;
    } else {
        $falha++;
    }
    echo "\n";
}

// Resumo
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Conversões bem-sucedidas: {$sucesso}\n";
if ($falha > 0) {
    echo "❌ Falhas: {$falha}\n";
}
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📦 PDFs gerados no diretório 'docs/'\n";
echo "Pronto para compartilhar com os parceiros!\n";
