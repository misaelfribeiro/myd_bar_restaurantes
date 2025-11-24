<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda #{{ $pedido->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 10px;
            width: 80mm; /* Largura padrão de impressora térmica */
            margin: 0 auto;
        }
        
        .comanda {
            border: 2px solid #000;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header .pedido-numero {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .info-section {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .itens-section {
            margin-bottom: 10px;
        }
        
        .itens-header {
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .item {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dotted #ccc;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .item-nome {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 3px;
        }
        
        .item-qtd {
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            min-width: 30px;
        }
        
        .item-categoria {
            color: #666;
            font-size: 11px;
            margin-bottom: 3px;
        }
        
        .item-obs {
            background: #f0f0f0;
            padding: 5px;
            margin-top: 5px;
            border-left: 3px solid #000;
        }
        
        .item-obs-label {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .footer {
            border-top: 2px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
            text-align: center;
        }
        
        .data-hora {
            font-size: 11px;
            margin-top: 5px;
        }
        
        @media print {
            body {
                width: 80mm;
            }
            
            .comanda {
                border: none;
                padding: 0;
            }
            
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="comanda">
        <div class="header">
            <h1>COMANDA COZINHA</h1>
            <div class="pedido-numero">#{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
        
        <div class="info-section">
            @if($pedido->mesa)
                <div class="info-row">
                    <span class="info-label">MESA:</span>
                    <span>{{ $pedido->mesa->identificador }}</span>
                </div>
            @else
                <div class="info-row">
                    <span class="info-label">TIPO:</span>
                    <span>DELIVERY</span>
                </div>
                @if($pedido->delivery)
                    <div class="info-row">
                        <span class="info-label">CLIENTE:</span>
                        <span>{{ $pedido->delivery->cliente_nome }}</span>
                    </div>
                @endif
            @endif
            
            @if($pedido->usuario)
                <div class="info-row">
                    <span class="info-label">GARÇOM:</span>
                    <span>{{ $pedido->usuario->nome }}</span>
                </div>
            @endif
            
            <div class="info-row">
                <span class="info-label">DATA/HORA:</span>
                <span>{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        
        <div class="itens-section">
            <div class="itens-header">ITENS DO PEDIDO</div>
            
            @foreach($pedido->itens as $item)
                <div class="item">
                    <div class="item-nome">
                        <span class="item-qtd">{{ $item->quantidade }}x</span>
                        @if($item->tipo_item === 'combo')
                            🔥 
                        @endif
                        {{ $item->nome_item }}
                        @if($item->tipo_item === 'combo')
                            [COMBO]
                        @endif
                    </div>
                    
                    @if($item->tipo_item === 'combo' && $item->combo)
                        <div class="item-categoria">
                            Produtos: @foreach($item->combo->produtos as $prod){{ $prod->nome }}@if(!$loop->last), @endif @endforeach
                        </div>
                    @elseif($item->produto && $item->produto->categoria)
                        <div class="item-categoria">
                            Categoria: {{ $item->produto->categoria->nome }}
                        </div>
                    @endif
                    
                    @if($item->observacoes)
                        <div class="item-obs">
                            <div class="item-obs-label">OBS:</div>
                            {{ $item->observacoes }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        
        @if($pedido->observacoes)
            <div class="info-section">
                <div class="info-label">OBSERVAÇÕES GERAIS:</div>
                <div style="margin-top: 5px; background: #f0f0f0; padding: 5px;">
                    {{ $pedido->observacoes }}
                </div>
            </div>
        @endif
        
        <div class="footer">
            <div style="font-weight: bold; font-size: 14px;">BOM PREPARO!</div>
            <div class="data-hora">Impresso em: {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>
    
    <script>
        // Auto-print quando abrir a janela
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
