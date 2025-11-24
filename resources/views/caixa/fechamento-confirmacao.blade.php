@extends('layouts.app')
@section('title', 'Fechamento de Caixa - Confirmação')
@section('content')
<div class="container-fluid no-print">
 <!-- Page Header -->
 <div class="page-header text-center mb-4">
 <div class="alert alert-success">
 <i class="fas fa-check-circle fa-3x mb-3"></i>
 <h2 class="mb-2">Caixa Fechado com Sucesso!</h2>
 <p class="mb-0">Confira abaixo o resumo detalhado do fechamento</p>
 </div>
 </div>
 <!-- Informações do Caixa -->
 <div class="card shadow-sm mb-4">
 <div class="card-header bg-dark text-white">
 <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações do Caixa</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-6">
 <table class="table table-borderless mb-0">
 <tr>
 <td class="fw-bold" width="200">Código do Caixa:</td>
 <td>#{{ str_pad($caixa->id, 6, '0', STR_PAD_LEFT) }}</td>
 </tr>
 <tr>
 <td class="fw-bold">Operador:</td>
 <td>{{ $caixa->usuario->nome ?? 'Sistema' }}</td>
 </tr>
 <tr>
 <td class="fw-bold">Data de Abertura:</td>
 <td>{{ $caixa->data_abertura->format('d/m/Y H:i:s') }}</td>
 </tr>
 <tr>
 <td class="fw-bold">Data de Fechamento:</td>
 <td>{{ $caixa->data_fechamento->format('d/m/Y H:i:s') }}</td>
 </tr>
 </table>
 </div>
 <div class="col-md-6">
 <table class="table table-borderless mb-0">
 <tr>
 <td class="fw-bold" width="200">Tempo Total:</td>
 <td>{{ $caixa->data_abertura->diffForHumans($caixa->data_fechamento, true) }}</td>
 </tr>
 <tr>
 <td class="fw-bold">Saldo Inicial:</td>
 <td>R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="fw-bold">Saldo Final (Dinheiro):</td>
 <td class="text-success fw-bold fs-5">R$ {{ number_format($saldoFinalDinheiro, 2, ',', '.') }}</td>
 </tr>
 @if($caixa->observacoes_fechamento)
 <tr>
 <td class="fw-bold">Observações:</td>
 <td>{{ $caixa->observacoes_fechamento }}</td>
 </tr>
 @endif
 </table>
 </div>
 </div>
 </div>
 </div>
 <!-- Resumo de Vendas -->
 <div class="row g-4 mb-4">
 <div class="col-md-3">
 <div class="card border-primary h-100">
 <div class="card-body text-center">
 <i class="fas fa-receipt fa-3x text-primary mb-3"></i>
 <h3 class="text-primary mb-2">{{ $totalizacoes['quantidade_vendas'] }}</h3>
 <p class="text-muted mb-0">Vendas Realizadas</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-success h-100">
 <div class="card-body text-center">
 <i class="fas fa-dollar-sign fa-3x text-success mb-3"></i>
 <h3 class="text-success mb-2">R$ {{ number_format($totalizacoes['total_vendas'], 2, ',', '.') }}</h3>
 <p class="text-muted mb-0">Total de Vendas</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-warning h-100">
 <div class="card-body text-center">
 <i class="fas fa-coins fa-3x text-warning mb-3"></i>
 <h3 class="text-warning mb-2">R$ {{ number_format($totalizacoes['total_troco'] ?? 0, 2, ',', '.') }}</h3>
 <p class="text-muted mb-0">Troco Dado</p>
 </div>
 </div>
 </div>
 <div class="col-md-3">
 <div class="card border-info h-100">
 <div class="card-body text-center">
 <i class="fas fa-hand-holding-usd fa-3x text-info mb-3"></i>
 <h3 class="text-info mb-2">R$ {{ number_format($totalizacoes['total_recebido'] ?? 0, 2, ',', '.') }}</h3>
 <p class="text-muted mb-0">Total Recebido</p>
 </div>
 </div>
 </div>
 </div>
 <!-- Detalhamento por Forma de Pagamento -->
 <div class="card shadow-sm mb-4">
 <div class="card-header bg-success text-white">
 <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Detalhamento por Forma de Pagamento</h5>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-bordered table-hover align-middle mb-0">
 <thead class="table-light">
 <tr>
 <th>Forma de Pagamento</th>
 <th class="text-center" width="150">Quantidade</th>
 <th class="text-end" width="200">Valor Total</th>
 <th class="text-end" width="150">Percentual</th>
 </tr>
 </thead>
 <tbody>
 @php
 $formasPagamento = [
 'dinheiro' => ['nome' => 'Dinheiro', 'icone' => 'fa-money-bill-wave', 'cor' => 'success'],
 'cartao_credito' => ['nome' => 'Cartão de Crédito', 'icone' => 'fa-credit-card', 'cor' => 'primary'],
 'cartao_debito' => ['nome' => 'Cartão de Débito', 'icone' => 'fa-credit-card', 'cor' => 'info'],
 'pix' => ['nome' => 'PIX', 'icone' => 'fa-qrcode', 'cor' => 'warning'],
 'vale_refeicao' => ['nome' => 'Vale Refeição', 'icone' => 'fa-ticket-alt', 'cor' => 'secondary'],
 'vale_alimentacao' => ['nome' => 'Vale Alimentação', 'icone' => 'fa-utensils', 'cor' => 'dark']
 ];
 @endphp
 @foreach($formasPagamento as $forma => $info)
 @php
 $dados = $totalizacoes['por_forma_pagamento'][$forma] ?? ['quantidade' => 0, 'total' => 0];
 $percentual = $totalizacoes['total_vendas'] > 0 ? ($dados['total'] / $totalizacoes['total_vendas']) * 100 : 0;
 @endphp
 <tr>
 <td>
 <i class="fas {{ $info['icone'] }} text-{{ $info['cor'] }} me-2"></i>
 <strong>{{ $info['nome'] }}</strong>
 </td>
 <td class="text-center">
 <span class="badge bg-{{ $dados['quantidade'] > 0 ? $info['cor'] : 'light' }} text-{{ $dados['quantidade'] > 0 ? 'white' : 'dark' }} fs-6">{{ $dados['quantidade'] }}</span>
 </td>
 <td class="text-end fw-bold">R$ {{ number_format($dados['total'], 2, ',', '.') }}</td>
 <td class="text-end">
 @if($dados['quantidade'] > 0)
 <span class="badge bg-{{ $info['cor'] }} fs-6">{{ number_format($percentual, 1) }}%</span>
 @else
 <span class="badge bg-light text-muted fs-6">0%</span>
 @endif
 </td>
 </tr>
 @endforeach
 </tbody>
 <tfoot class="table-dark">
 <tr class="fw-bold">
 <td>TOTAL</td>
 <td class="text-center">{{ $totalizacoes['quantidade_vendas'] }}</td>
 <td class="text-end">R$ {{ number_format($totalizacoes['total_vendas'], 2, ',', '.') }}</td>
 <td class="text-end">100%</td>
 </tr>
 </tfoot>
 </table>
 </div>
 </div>
 </div>
 <!-- Resumo Financeiro Consolidado -->
 <div class="card shadow-sm mb-4">
 <div class="card-header bg-info text-white">
 <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Resumo Financeiro Consolidado</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-6">
 <h6 class="mb-3 text-primary"><i class="fas fa-cash-register me-2"></i>Totais de Vendas:</h6>
 <table class="table table-borderless">
 <tr class="fs-5 text-primary">
 <td><strong>TOTAL DE VENDAS (Todas as Formas)</strong></td>
 <td class="text-end"><strong>R$ {{ number_format($totalizacoes['total_vendas'], 2, ',', '.') }}</strong></td>
 </tr>
 <tr>
 <td class="ps-3">├─ Dinheiro</td>
 <td class="text-end">R$ {{ number_format($totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0, 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="ps-3">├─ Cartão de Crédito</td>
 <td class="text-end">R$ {{ number_format($totalizacoes['por_forma_pagamento']['cartao_credito']['total'] ?? 0, 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="ps-3">├─ Cartão de Débito</td>
 <td class="text-end">R$ {{ number_format($totalizacoes['por_forma_pagamento']['cartao_debito']['total'] ?? 0, 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="ps-3">├─ PIX</td>
 <td class="text-end">R$ {{ number_format($totalizacoes['por_forma_pagamento']['pix']['total'] ?? 0, 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="ps-3">└─ Vale Refeição</td>
 <td class="text-end">R$ {{ number_format($totalizacoes['por_forma_pagamento']['vale_refeicao']['total'] ?? 0, 2, ',', '.') }}</td>
 </tr>
 </table>
 </div>
 <div class="col-md-6">
 <h6 class="mb-3 text-success"><i class="fas fa-money-bill-wave me-2"></i>Resumo Geral:</h6>
 <table class="table table-borderless">
 <tr>
 <td>Saldo Inicial em Dinheiro</td>
 <td class="text-end">R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td>Quantidade de Vendas</td>
 <td class="text-end">{{ $totalizacoes['quantidade_vendas'] }} vendas</td>
 </tr>
 <tr>
 <td>Ticket Médio</td>
 <td class="text-end">R$ {{ number_format($totalizacoes['quantidade_vendas'] > 0 ? $totalizacoes['total_vendas'] / $totalizacoes['quantidade_vendas'] : 0, 2, ',', '.') }}</td>
 </tr>
 <tr class="border-top border-2 border-success fs-5 text-success">
 <td><strong>Dinheiro Final no Caixa</strong></td>
 <td class="text-end"><strong>R$ {{ number_format($saldoFinalDinheiro, 2, ',', '.') }}</strong></td>
 </tr>
 <tr class="text-muted">
 <td colspan="2" class="small">
 <i class="fas fa-info-circle me-1"></i>
 (Saldo Inicial + Vendas em Dinheiro - Troco)
 </td>
 </tr>
 </table>
 </div>
 </div>
 </div>
 </div>
 <!-- Cálculo do Saldo Final -->
 <div class="card shadow-sm mb-4">
 <div class="card-header bg-warning text-dark">
 <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Detalhamento do Dinheiro Físico no Caixa</h5>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-6">
 <h6 class="mb-3">Movimentação em Dinheiro:</h6>
 <table class="table table-borderless">
 <tr>
 <td>Saldo Inicial</td>
 <td class="text-end">R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</td>
 </tr>
 <tr class="text-success">
 <td><strong>(+) Vendas em Dinheiro</strong></td>
 <td class="text-end"><strong>R$ {{ number_format($totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0, 2, ',', '.') }}</strong></td>
 </tr>
 <tr class="text-danger">
 <td><strong>(-) Troco Dado</strong></td>
 <td class="text-end"><strong>R$ {{ number_format($totalizacoes['total_troco'] ?? 0, 2, ',', '.') }}</strong></td>
 </tr>
 <tr class="border-top border-2 border-dark fs-5 text-success">
 <td><strong>Saldo Final em Dinheiro</strong></td>
 <td class="text-end"><strong>R$ {{ number_format($saldoFinalDinheiro, 2, ',', '.') }}</strong></td>
 </tr>
 </table>
 </div>
 <div class="col-md-6">
 <h6 class="mb-3">Valores a Conferir:</h6>
 <div class="alert alert-info">
 <p class="mb-2"><i class="fas fa-info-circle me-2"></i><strong>Conferir Fisicamente:</strong></p>
 <ul class="mb-0">
 <li>Dinheiro no caixa deve estar em: <strong>R$ {{ number_format($saldoFinalDinheiro, 2, ',', '.') }}</strong></li>
 @if(($totalizacoes['por_forma_pagamento']['cartao_credito']['total'] ?? 0) > 0)
 <li>Comprovantes de Cartão de Crédito: <strong>R$ {{ number_format($totalizacoes['por_forma_pagamento']['cartao_credito']['total'], 2, ',', '.') }}</strong></li>
 @endif
 @if(($totalizacoes['por_forma_pagamento']['cartao_debito']['total'] ?? 0) > 0)
 <li>Comprovantes de Cartão de Débito: <strong>R$ {{ number_format($totalizacoes['por_forma_pagamento']['cartao_debito']['total'], 2, ',', '.') }}</strong></li>
 @endif
 @if(($totalizacoes['por_forma_pagamento']['pix']['total'] ?? 0) > 0)
 <li>Confirmações de PIX: <strong>R$ {{ number_format($totalizacoes['por_forma_pagamento']['pix']['total'], 2, ',', '.') }}</strong></li>
 @endif
 @if(($totalizacoes['por_forma_pagamento']['vale_refeicao']['total'] ?? 0) > 0)
 <li>Cupons de Vale Refeição: <strong>R$ {{ number_format($totalizacoes['por_forma_pagamento']['vale_refeicao']['total'], 2, ',', '.') }}</strong></li>
 @endif
 </ul>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Botões de Ação -->
 <div class="text-center mb-4">
 <button onclick="imprimirRelatorio()" class="btn btn-primary btn-lg me-2">
 <i class="fas fa-print me-2"></i>
 Imprimir Relatório Profissional
 </button>
 <a href="{{ route('caixa.relatorio', $caixa->id) }}" class="btn btn-info btn-lg me-2" target="_blank">
 <i class="fas fa-file-pdf me-2"></i>
 Relatório Detalhado
 </a>
 <a href="{{ route('caixa.index') }}" class="btn btn-success btn-lg">
 <i class="fas fa-home me-2"></i>
 Voltar ao Dashboard
 </a>
 </div>
</div>
<!-- Versão para Impressão - Estilo Relatório Gerencial PDV -->
<div class="print-only relatorio-gerencial">
 <!-- Cabeçalho -->
 <div class="header-relatorio">
 <div class="nome-sistema">Intercomp Sistemas e Comércio de Informática Ltda.</div>
 <div class="nome-sistema-abrev">INTERCOMP</div>
 <div class="endereco-sistema">Av Dr. Romeu Tortima, 413 - Jd. Santa Genebra</div>
 <div class="dados-fiscais">
 CNPJ: 04.562.447/0001-77<br>
 IE: 244836530110 | IM: 162.933-0
 </div>
 <div class="dados-documento">
 <span class="data-hora">{{ now()->format('d/m/Y') }} {{ now()->format('H:i:s') }}</span>
 <span class="codigos">CNF:000{{ $caixa->id }} COO:00{{ str_pad($caixa->id, 4, '0', STR_PAD_LEFT) }}</span>
 </div>
 </div>
 <div class="linha-separador">***********************************************</div>
 <div class="linha-separador">INTERCAMP SISTEMAS E COMÉRCIO DE INFORMÁTICA S.A</div>
 <div class="linha-separador">       PRAÇA EQUIT LIMA OLIVEIRA, 44</div>
 <div class="linha-separador">***********************************************</div>
 <!-- Info Movimento -->
 <div class="box-destaque">
 <div class="movimento-info">
 MOV: {{ $caixa->data_abertura->format('d/m/Y') }} TN:1 PDV:1 RESP:{{ strtoupper(substr($caixa->usuario->nome ?? 'OPERADOR', 0, 15)) }} MATHEUS
 </div>
 <div class="documento-fiscal">NÃO É DOCUMENTO FISCAL</div>
 </div>
 <div class="timestamp-emissao">Emitido em {{ now()->format('d/m/Y') }} às {{ now()->format('H:i') }} hs</div>
 <div class="linha-igual">===============================================</div>
 <!-- BLOCO 1: Total de pessoas atendidas -->
 <div class="secao-bloco">
 <div class="numero-bloco">1</div>
 <div class="conteudo-bloco">
 <div class="linha-dado">
 <span class="label">Total de pessoas atendidas</span>
 <span class="numero">{{ $totalizacoes['quantidade_vendas'] }}</span>
 </div>
 <div class="linha-dado">
 <span class="label">Ticket médio geral de vendas</span>
 <span class="valor">R$ {{ number_format($totalizacoes['quantidade_vendas'] > 0 ? $totalizacoes['total_vendas'] / $totalizacoes['quantidade_vendas'] : 0, 2, ',', '.') }}</span>
 </div>
 </div>
 </div>
 <div class="linha-igual">===============================================</div>
 <!-- BLOCO 2: Totalizadores -->
 <div class="secao-bloco">
 <div class="numero-bloco">2</div>
 <div class="conteudo-bloco">
 <div class="titulo-bloco">TOTALIZADORES</div>
 <table class="tabela-dados">
 <tr>
 <td class="descricao">Vendas Brutas</td>
 <td class="qtd">{{ $totalizacoes['quantidade_vendas'] }}</td>
 <td class="valor">R$ {{ number_format($totalizacoes['total_vendas'], 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="descricao">Cancelamentos</td>
 <td class="qtd">0</td>
 <td class="valor">R$ 0,00</td>
 </tr>
 <tr>
 <td class="descricao">Descontos</td>
 <td class="qtd">0</td>
 <td class="valor">R$ 0,00</td>
 </tr>
 <tr class="total-linha">
 <td class="descricao">TOTAL VENDAS LÍQUIDAS</td>
 <td class="qtd">{{ $totalizacoes['quantidade_vendas'] }}</td>
 <td class="valor">R$ {{ number_format($totalizacoes['total_vendas'], 2, ',', '.') }}</td>
 </tr>
 </table>
 </div>
 </div>
 <div class="linha-igual">===============================================</div>
 <!-- BLOCO 3: Formas de Pagamento -->
 <div class="secao-bloco">
 <div class="numero-bloco">3</div>
 <div class="conteudo-bloco">
 <div class="titulo-bloco">FORMAS DE PAGAMENTO</div>
 <table class="tabela-dados">
 @php
 $formasPagamentoNomes = [
 'dinheiro' => 'Dinheiro',
 'cartao_credito' => 'Cartão de Crédito',
 'cartao_debito' => 'Cartão de Débito',
 'pix' => 'PIX',
 'vale_refeicao' => 'Vale Refeição',
 'vale_alimentacao' => 'Vale Alimentação'
 ];
 @endphp
 @foreach($totalizacoes['por_forma_pagamento'] as $forma => $dados)
 <tr>
 <td class="descricao">{{ $formasPagamentoNomes[$forma] ?? ucfirst(str_replace('_', ' ', $forma)) }}</td>
 <td class="qtd">{{ $dados['quantidade'] ?? '-' }}</td>
 <td class="valor">R$ {{ number_format($dados['total'] ?? $dados, 2, ',', '.') }}</td>
 </tr>
 @endforeach
 <tr class="total-linha">
 <td class="descricao">TOTAL</td>
 <td class="qtd">{{ $totalizacoes['quantidade_vendas'] }}</td>
 <td class="valor">R$ {{ number_format($totalizacoes['total_recebido'], 2, ',', '.') }}</td>
 </tr>
 </table>
 </div>
 </div>
 <div class="linha-igual">===============================================</div>
 <!-- BLOCO 4: Informe de Troco -->
 <div class="secao-bloco">
 <div class="numero-bloco">4</div>
 <div class="conteudo-bloco">
 <div class="titulo-bloco">INFORME DE TROCO</div>
 <table class="tabela-dados">
 <tr>
 <td class="descricao">Dinheiro</td>
 <td class="qtd">-</td>
 <td class="valor">R$ {{ number_format($totalizacoes['total_troco'], 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="descricao">Cheque</td>
 <td class="qtd">-</td>
 <td class="valor">R$ 0,00</td>
 </tr>
 <tr>
 <td class="descricao">Cartão Débito/Crédito</td>
 <td class="qtd">-</td>
 <td class="valor">R$ 0,00</td>
 </tr>
 <tr class="total-linha">
 <td class="descricao">TOTAL TROCO</td>
 <td class="qtd">-</td>
 <td class="valor">R$ {{ number_format($totalizacoes['total_troco'], 2, ',', '.') }}</td>
 </tr>
 </table>
 </div>
 </div>
 <div class="linha-igual">===============================================</div>
 <!-- BLOCO 5: Sangria/Suprimento -->
 <div class="secao-bloco">
 <div class="numero-bloco">5</div>
 <div class="conteudo-bloco">
 <div class="titulo-bloco">SANGRIA / SUPRIMENTO / REFORÇO</div>
 <table class="tabela-dados">
 <tr>
 <td class="descricao">Sangria</td>
 <td class="qtd">0</td>
 <td class="valor">R$ 0,00</td>
 </tr>
 <tr>
 <td class="descricao">Suprimento</td>
 <td class="qtd">0</td>
 <td class="valor">R$ 0,00</td>
 </tr>
 <tr>
 <td class="descricao">Reforço</td>
 <td class="qtd">0</td>
 <td class="valor">R$ 0,00</td>
 </tr>
 </table>
 </div>
 </div>
 <div class="linha-igual">===============================================</div>
 <!-- BLOCO 6: Caixa - Diferença -->
 <div class="secao-bloco">
 <div class="numero-bloco">6</div>
 <div class="conteudo-bloco">
 <div class="titulo-bloco">CAIXA - DIFERENÇA</div>
 <table class="tabela-dados">
 <tr>
 <td class="descricao">Saldo Inicial</td>
 <td class="qtd">-</td>
 <td class="valor">R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="descricao">Total Recebido</td>
 <td class="qtd">-</td>
 <td class="valor">R$ {{ number_format($totalizacoes['total_recebido'], 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="descricao">(-) Total Troco</td>
 <td class="qtd">-</td>
 <td class="valor">-R$ {{ number_format($totalizacoes['total_troco'], 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="descricao">(-) Sangria</td>
 <td class="qtd">-</td>
 <td class="valor">R$ 0,00</td>
 </tr>
 <tr>
 <td class="descricao">(+) Suprimento</td>
 <td class="qtd">-</td>
 <td class="valor">R$ 0,00</td>
 </tr>
 <tr class="total-linha">
 <td class="descricao">SALDO ESPERADO</td>
 <td class="qtd">-</td>
 <td class="valor">R$ {{ number_format($saldoFinalDinheiro, 2, ',', '.') }}</td>
 </tr>
 <tr>
 <td class="descricao">Saldo Informado</td>
 <td class="qtd">-</td>
 <td class="valor">R$ {{ number_format($caixa->saldo_final, 2, ',', '.') }}</td>
 </tr>
 <tr class="diferenca-linha">
 <td class="descricao">DIFERENÇA</td>
 <td class="qtd">-</td>
 <td class="valor">R$ {{ number_format($caixa->saldo_final - $saldoFinalDinheiro, 2, ',', '.') }}</td>
 </tr>
 </table>
 </div>
 </div>
 <div class="linha-asteriscos">***********************************************</div>
 <!-- Rodapé com assinatura -->
 <div class="rodape-relatorio">
 <div class="linha-total">
 <strong>SUB-TOTAL R$:</strong> {{ number_format($totalizacoes['total_vendas'], 2, ',', '.') }}
 </div>
 <div class="linha-total">
 <strong>DIFERENÇA R$:</strong> {{ number_format($caixa->saldo_final - $saldoFinalDinheiro, 2, ',', '.') }}
 </div>
 <div class="assinatura-box">
 <div>CAIXA: _________________________________</div>
 <div>CONFERENTE: ____________________________</div>
 </div>
 </div>
</div>
<div style="display: none !important;">
@push('styles')
 <th width="20%" class="text-center">Quantidade</th>
 <th width="30%" class="text-end">Valor (R$)</th>
 </tr>
 </thead>
 <tbody>
 <tr>
 <td><strong>Saldo Inicial (Abertura do Caixa)</strong></td>
 <td class="text-center">-</td>
 <td class="text-end">R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</td>
 </tr>
 <tr style="background: #e3f2fd; border-left: 4px solid #0d6efd;">
 <td><strong>Total de Vendas Realizadas</strong></td>
 <td class="text-center"><strong style="font-size: 11pt;">{{ $totalizacoes['quantidade_vendas'] }}</strong></td>
 <td class="text-end"><strong style="color: #198754; font-size: 11pt;">+ R$ {{ number_format($totalizacoes['total_vendas'], 2, ',', '.') }}</strong></td>
 </tr>
 <tr>
 <td style="padding-left: 30px;">• Ticket Médio por Venda</td>
 <td class="text-center">-</td>
 <td class="text-end">
 R$ {{ number_format($totalizacoes['quantidade_vendas'] > 0 ? $totalizacoes['total_vendas'] / $totalizacoes['quantidade_vendas'] : 0, 2, ',', '.') }}
 </td>
 </tr>
 <tr style="background: #fff3cd;">
 <td><strong>Total Recebido em Dinheiro</strong></td>
 <td class="text-center">-</td>
 <td class="text-end">R$ {{ number_format($totalizacoes['total_recebido'] ?? 0, 2, ',', '.') }}</td>
 </tr>
 <tr style="background: #f8d7da; border-left: 4px solid #dc3545;">
 <td><strong>(-) Troco Devolvido aos Clientes</strong></td>
 <td class="text-center">-</td>
 <td class="text-end"><strong style="color: #dc3545; font-size: 11pt;">- R$ {{ number_format($totalizacoes['total_troco'] ?? 0, 2, ',', '.') }}</strong></td>
 </tr>
 <tr>
 <td><strong>Entradas de Outras Fontes</strong></td>
 <td class="text-center">-</td>
 <td class="text-end">R$ 0,00</td>
 </tr>
 <tr>
 <td><strong>(-) Saídas e Retiradas</strong></td>
 <td class="text-center">-</td>
 <td class="text-end">R$ 0,00</td>
 </tr>
 </tbody>
 <tfoot>
 <tr class="total-row" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 5px solid #198754;">
 <td colspan="2" style="text-align: right; font-size: 13pt;"><strong>SALDO FINAL ESPERADO:</strong></td>
 <td class="text-end" style="font-size: 14pt; color: #198754;">
 <strong>R$ {{ number_format($caixa->saldo_final, 2, ',', '.') }}</strong>
 </td>
 </tr>
 </tfoot>
 </table>
 </div>
 <!-- Detalhamento por Forma de Pagamento -->
 <div class="secao-formas-pagamento">
 <h3 class="secao-titulo"><i class="fas fa-credit-card"></i> DETALHAMENTO POR FORMA DE PAGAMENTO</h3>
 <table class="tabela-profissional" style="margin-bottom: 20px;">
 <thead>
 <tr>
 <th width="30%">Forma de Pagamento</th>
 <th width="12%" class="text-center">Qtd Transações</th>
 <th width="18%" class="text-end">Valor Total</th>
 <th width="15%" class="text-end">Ticket Médio</th>
 <th width="10%" class="text-end">%</th>
 <th width="15%" class="text-end">Participação</th>
 </tr>
 </thead>
 <tbody>
 @php
 $formasPagamento = [
 'dinheiro' => ['nome' => 'Dinheiro', 'icone' => 'fa-money-bill-wave'],
 'cartao_credito' => ['nome' => 'Cartão de Crédito', 'icone' => 'fa-credit-card'],
 'cartao_debito' => ['nome' => 'Cartão de Débito', 'icone' => 'fa-credit-card'],
 'pix' => ['nome' => 'PIX', 'icone' => 'fa-qrcode'],
 'vale_refeicao' => ['nome' => 'Vale Refeição', 'icone' => 'fa-ticket-alt']
 ];
 $dadosGrafico = [];
 @endphp
 @foreach($formasPagamento as $forma => $info)
 @php
 $dados = $totalizacoes['por_forma_pagamento'][$forma] ?? ['quantidade' => 0, 'total' => 0];
 $percentual = $totalizacoes['total_vendas'] > 0 ? ($dados['total'] / $totalizacoes['total_vendas']) * 100 : 0;
 $ticketMedio = $dados['quantidade'] > 0 ? $dados['total'] / $dados['quantidade'] : 0;
 if($dados['quantidade'] > 0) {
 $dadosGrafico[] = [
 'nome' => $info['nome'],
 'valor' => $dados['total'],
 'percentual' => $percentual
 ];
 }
 @endphp
 <tr style="{{ $dados['quantidade'] > 0 ? '' : 'display:none;' }}">
 <td><i class="fas {{ $info['icone'] }}" style="color: #0d6efd; margin-right: 8px;"></i> <strong>{{ $info['nome'] }}</strong></td>
 <td class="text-center"><strong>{{ $dados['quantidade'] }}</strong></td>
 <td class="text-end"><strong>R$ {{ number_format($dados['total'], 2, ',', '.') }}</strong></td>
 <td class="text-end">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</td>
 <td class="text-end"><strong>{{ number_format($percentual, 1) }}%</strong></td>
 <td>
 <div class="barra-progresso">
 <div class="barra-fill" style="width: {{ $percentual }}%;">
 @if($percentual > 15)
 {{ number_format($percentual, 1) }}%
 @endif
 </div>
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 <tfoot>
 <tr class="total-row">
 <td><strong>TOTAL GERAL</strong></td>
 <td class="text-center"><strong>{{ $totalizacoes['quantidade_vendas'] }}</strong></td>
 <td class="text-end"><strong>R$ {{ number_format($totalizacoes['total_vendas'], 2, ',', '.') }}</strong></td>
 <td class="text-end">
 <strong>R$ {{ number_format($totalizacoes['quantidade_vendas'] > 0 ? $totalizacoes['total_vendas'] / $totalizacoes['quantidade_vendas'] : 0, 2, ',', '.') }}</strong>
 </td>
 <td class="text-end"><strong>100%</strong></td>
 <td></td>
 </tr>
 </tfoot>
 </table>
 <!-- Gráfico -->
 <div style="text-align: center; margin: 20px 0; padding: 15px; border: 2px solid #ddd; background: #f8f9fa;">
 <h4 style="font-size: 11pt; margin-bottom: 15px;"><i class="fas fa-chart-pie"></i> DISTRIBUIÇÃO PERCENTUAL DAS VENDAS</h4>
 <canvas id="graficoFormasPagamento" width="400" height="250"></canvas>
 </div>
 </div>
 <!-- Movimentação Financeira Detalhada -->
 <div class="secao-movimentacao">
 <h3 class="secao-titulo"><i class="fas fa-calculator"></i> MOVIMENTAÇÃO FINANCEIRA DETALHADA</h3>
 <table class="tabela-profissional" style="margin-bottom: 20px;">
 <thead>
 <tr>
 <th width="50%">Descrição da Movimentação</th>
 <th width="25%" class="text-end">Valor (R$)</th>
 <th width="25%" class="text-end">Saldo Acumulado (R$)</th>
 </tr>
 </thead>
 <tbody>
 @php
 $saldoAcumulado = $caixa->saldo_inicial;
 @endphp
 <tr>
 <td><strong>1. Saldo Inicial (Abertura do Caixa)</strong></td>
 <td class="text-end">R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</td>
 <td class="text-end"><strong>R$ {{ number_format($saldoAcumulado, 2, ',', '.') }}</strong></td>
 </tr>
 <tr style="background: #e8f5e9;">
 <td><strong>2. (+) Recebimentos em Dinheiro</strong></td>
 <td class="text-end" style="color: #198754;">
 <strong>+ R$ {{ number_format($totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0, 2, ',', '.') }}</strong>
 </td>
 @php
 $saldoAcumulado += ($totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0);
 @endphp
 <td class="text-end"><strong>R$ {{ number_format($saldoAcumulado, 2, ',', '.') }}</strong></td>
 </tr>
 <tr style="padding-left: 30px;">
 <td style="padding-left: 40px;">• Quantidade de vendas em dinheiro</td>
 <td class="text-end">{{ $totalizacoes['por_forma_pagamento']['dinheiro']['quantidade'] ?? 0 }} transações</td>
 <td class="text-end">-</td>
 </tr>
 <tr style="background: #ffebee;">
 <td><strong>3. (-) Troco Devolvido</strong></td>
 <td class="text-end" style="color: #dc3545;">
 <strong>- R$ {{ number_format($totalizacoes['total_troco'] ?? 0, 2, ',', '.') }}</strong>
 </td>
 @php
 $saldoAcumulado -= ($totalizacoes['total_troco'] ?? 0);
 @endphp
 <td class="text-end"><strong>R$ {{ number_format($saldoAcumulado, 2, ',', '.') }}</strong></td>
 </tr>
 <tr>
 <td><strong>4. (+) Outras Entradas</strong></td>
 <td class="text-end">R$ 0,00</td>
 <td class="text-end"><strong>R$ {{ number_format($saldoAcumulado, 2, ',', '.') }}</strong></td>
 </tr>
 <tr>
 <td><strong>5. (-) Retiradas e Sangrias</strong></td>
 <td class="text-end">R$ 0,00</td>
 <td class="text-end"><strong>R$ {{ number_format($saldoAcumulado, 2, ',', '.') }}</strong></td>
 </tr>
 </tbody>
 <tfoot>
 <tr class="total-row" style="background: #d4edda;">
 <td><strong>SALDO FINAL EM DINHEIRO NO CAIXA</strong></td>
 <td></td>
 <td class="text-end" style="font-size: 13pt; color: #198754;">
 <strong>R$ {{ number_format($saldoAcumulado, 2, ',', '.') }}</strong>
 </td>
 </tr>
 </tfoot>
 </table>
 <!-- Recebimentos por outras formas de pagamento -->
 <h4 style="font-size: 11pt; font-weight: bold; margin: 20px 0 10px 0;">
 <i class="fas fa-credit-card"></i> RECEBIMENTOS POR OUTRAS FORMAS DE PAGAMENTO
 </h4>
 <table class="tabela-profissional">
 <thead>
 <tr>
 <th width="40%">Forma de Pagamento</th>
 <th width="20%" class="text-center">Quantidade</th>
 <th width="20%" class="text-end">Valor Total</th>
 <th width="20%">Status/Observação</th>
 </tr>
 </thead>
 <tbody>
 @php
 $formasNaoDinheiro = [
 'cartao_credito' => ['nome' => 'Cartão de Crédito', 'obs' => 'Verificar comprovantes'],
 'cartao_debito' => ['nome' => 'Cartão de Débito', 'obs' => 'Verificar comprovantes'],
 'pix' => ['nome' => 'PIX', 'obs' => 'Verificar confirmações'],
 'vale_refeicao' => ['nome' => 'Vale Refeição', 'obs' => 'Verificar cupons']
 ];
 @endphp
 @foreach($formasNaoDinheiro as $forma => $info)
 @php
 $dados = $totalizacoes['por_forma_pagamento'][$forma] ?? ['quantidade' => 0, 'total' => 0];
 @endphp
 @if($dados['quantidade'] > 0)
 <tr>
 <td><strong>{{ $info['nome'] }}</strong></td>
 <td class="text-center">{{ $dados['quantidade'] }}</td>
 <td class="text-end">R$ {{ number_format($dados['total'], 2, ',', '.') }}</td>
 <td><small>{{ $info['obs'] }}</small></td>
 </tr>
 @endif
 @endforeach
 </tbody>
 <tfoot>
 <tr class="total-row">
 <td><strong>TOTAL OUTRAS FORMAS</strong></td>
 <td class="text-center">
 <strong>
 {{ 
 ($totalizacoes['por_forma_pagamento']['cartao_credito']['quantidade'] ?? 0) +
 ($totalizacoes['por_forma_pagamento']['cartao_debito']['quantidade'] ?? 0) +
 ($totalizacoes['por_forma_pagamento']['pix']['quantidade'] ?? 0) +
 ($totalizacoes['por_forma_pagamento']['vale_refeicao']['quantidade'] ?? 0)
 }}
 </strong>
 </td>
 <td class="text-end">
 <strong>
 R$ {{ number_format(
 ($totalizacoes['por_forma_pagamento']['cartao_credito']['total'] ?? 0) +
 ($totalizacoes['por_forma_pagamento']['cartao_debito']['total'] ?? 0) +
 ($totalizacoes['por_forma_pagamento']['pix']['total'] ?? 0) +
 ($totalizacoes['por_forma_pagamento']['vale_refeicao']['total'] ?? 0),
 2, ',', '.'
 ) }}
 </strong>
 </td>
 <td></td>
 </tr>
 </tfoot>
 </table>
 </div>
 <!-- Indicadores de Performance e Análise -->
 <div class="secao-indicadores">
 <h3 class="secao-titulo"><i class="fas fa-chart-line"></i> INDICADORES DE PERFORMANCE E ANÁLISE</h3>
 <table class="tabela-profissional">
 <thead>
 <tr>
 <th width="50%">Indicador</th>
 <th width="25%" class="text-center">Valor</th>
 <th width="25%">Análise/Observação</th>
 </tr>
 </thead>
 <tbody>
 @php
 $horasOperacao = $caixa->data_abertura->diffInHours($caixa->data_fechamento) ?: 1;
 $minutosOperacao = $caixa->data_abertura->diffInMinutes($caixa->data_fechamento);
 $ticketMedio = $totalizacoes['quantidade_vendas'] > 0 ? $totalizacoes['total_vendas'] / $totalizacoes['quantidade_vendas'] : 0;
 $vendasPorHora = $horasOperacao > 0 ? $totalizacoes['quantidade_vendas'] / $horasOperacao : 0;
 $faturamentoPorHora = $horasOperacao > 0 ? $totalizacoes['total_vendas'] / $horasOperacao : 0;
 $percentualDinheiro = $totalizacoes['total_vendas'] > 0 ? (($totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0) / $totalizacoes['total_vendas']) * 100 : 0;
 $percentualCartao = $totalizacoes['total_vendas'] > 0 ? ((($totalizacoes['por_forma_pagamento']['cartao_credito']['total'] ?? 0) + ($totalizacoes['por_forma_pagamento']['cartao_debito']['total'] ?? 0)) / $totalizacoes['total_vendas']) * 100 : 0;
 @endphp
 <tr>
 <td><strong>Período de Operação</strong></td>
 <td class="text-center">{{ $horasOperacao }}h {{ $minutosOperacao % 60 }}min</td>
 <td><small>Tempo total do caixa aberto</small></td>
 </tr>
 <tr style="background: #e3f2fd;">
 <td><strong>Ticket Médio por Venda</strong></td>
 <td class="text-center"><strong>R$ {{ number_format($ticketMedio, 2, ',', '.') }}</strong></td>
 <td><small>Valor médio de cada venda</small></td>
 </tr>
 <tr>
 <td><strong>Vendas por Hora</strong></td>
 <td class="text-center">{{ number_format($vendasPorHora, 1) }} vendas/h</td>
 <td><small>Média de vendas por hora</small></td>
 </tr>
 <tr style="background: #e8f5e9;">
 <td><strong>Faturamento por Hora</strong></td>
 <td class="text-center"><strong>R$ {{ number_format($faturamentoPorHora, 2, ',', '.') }}/h</strong></td>
 <td><small>Receita média por hora</small></td>
 </tr>
 <tr>
 <td><strong>Percentual de Vendas em Dinheiro</strong></td>
 <td class="text-center">{{ number_format($percentualDinheiro, 1) }}%</td>
 <td><small>Representa {{ number_format($percentualDinheiro, 1) }}% das vendas</small></td>
 </tr>
 <tr>
 <td><strong>Percentual de Vendas com Cartão</strong></td>
 <td class="text-center">{{ number_format($percentualCartao, 1) }}%</td>
 <td><small>Crédito + Débito combinados</small></td>
 </tr>
 <tr style="background: #fff3cd;">
 <td><strong>Taxa Média de Troco</strong></td>
 <td class="text-center">
 {{ $totalizacoes['total_vendas'] > 0 ? number_format(($totalizacoes['total_troco'] / $totalizacoes['total_vendas']) * 100, 1) : 0 }}%
 </td>
 <td><small>Percentual de troco sobre vendas totais</small></td>
 </tr>
 <tr>
 <td><strong>Forma de Pagamento Mais Utilizada</strong></td>
 <td class="text-center">
 @php
 $formaMaisUtilizada = collect($totalizacoes['por_forma_pagamento'])
 ->sortByDesc('quantidade')
 ->first();
 $nomeFormaMaisUtilizada = collect($totalizacoes['por_forma_pagamento'])
 ->sortByDesc('quantidade')
 ->keys()
 ->first();
 $nomesFormas = [
 'dinheiro' => 'Dinheiro',
 'cartao_credito' => 'Cartão Crédito',
 'cartao_debito' => 'Cartão Débito',
 'pix' => 'PIX',
 'vale_refeicao' => 'Vale Refeição'
 ];
 @endphp
 <strong>{{ $nomesFormas[$nomeFormaMaisUtilizada] ?? '-' }}</strong>
 </td>
 <td><small>{{ $formaMaisUtilizada['quantidade'] ?? 0 }} transações</small></td>
 </tr>
 </tbody>
 </table>
 </div>
 @if($caixa->observacoes_fechamento)
 <!-- Observações -->
 <div class="secao-observacoes">
 <h3 class="secao-titulo"><i class="fas fa-comment"></i> Observações do Fechamento</h3>
 <div class="observacoes-texto">{{ $caixa->observacoes_fechamento }}</div>
 </div>
 @endif
 <!-- Assinaturas -->
 <div class="secao-assinaturas">
 <h3 class="secao-titulo"><i class="fas fa-signature"></i> ASSINATURAS E CONFERÊNCIA</h3>
 <div class="row">
 <div class="col-6">
 <div class="linha-assinatura"></div>
 <div class="label-assinatura">{{ $caixa->usuario->nome ?? 'Operador' }}</div>
 <div class="sublabel-assinatura">Operador de Caixa - CPF: ___.___.___-__</div>
 <div class="sublabel-assinatura" style="margin-top: 5px; font-size: 8pt;">Data: ____/____/______</div>
 </div>
 <div class="col-6">
 <div class="linha-assinatura"></div>
 <div class="label-assinatura">Responsável/Gerente</div>
 <div class="sublabel-assinatura">Conferência e Aprovação - CPF: ___.___.___-__</div>
 <div class="sublabel-assinatura" style="margin-top: 5px; font-size: 8pt;">Data: ____/____/______</div>
 </div>
 </div>
 <!-- Nota de Conferência -->
 <div style="margin-top: 25px; padding: 15px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; text-align: center;">
 <strong style="color: #856404;"><i class="fas fa-exclamation-triangle"></i> IMPORTANTE:</strong>
 <span style="color: #856404; font-size: 9.5pt;">
 Este documento comprova o fechamento do caixa. Guarde-o junto aos comprovantes físicos para auditoria e conferência contábil.
 </span>
 </div>
 </div>
 <!-- Rodapé -->
 <div class="rodape-documento">
 <div class="row">
 <div class="col-6">
 <small>Documento gerado automaticamente pelo Sistema MYD</small>
 </div>
 <div class="col-6 text-end">
 <small>Data de impressão: {{ now()->format('d/m/Y H:i:s') }}</small>
 </div>
 </div>
 </div>
</div>
</div>
@push('styles')
<style>
 .print-only {
 display: none;
 }
 .secao-info,
 .secao-resumo,
 .secao-formas-pagamento,
 .secao-movimentacao,
 .secao-indicadores,
 .secao-observacoes,
 .secao-assinaturas,
 .relatorio-profissional,
 .info-grid,
 .tabela-profissional {
 display: none !important;
 }
 .relatorio-gerencial {
 font-family: 'Courier New', Courier, monospace;
 font-size: 10pt;
 line-height: 1.3;
 color: #000;
 max-width: 80mm;
 margin: 0 auto;
 padding: 5mm;
 background: white;
 }
 .header-relatorio {
 text-align: center;
 margin-bottom: 3mm;
 }
 .nome-sistema {
 font-size: 9pt;
 font-weight: bold;
 }
 .nome-sistema-abrev {
 font-size: 11pt;
 font-weight: bold;
 margin: 1mm 0;
 }
 .endereco-sistema {
 font-size: 8pt;
 }
 .dados-fiscais {
 font-size: 8pt;
 margin: 2mm 0;
 }
 .dados-documento {
 font-size: 8pt;
 margin: 2mm 0;
 display: flex;
 justify-content: space-between;
 }
 .linha-separador {
 text-align: center;
 font-size: 9pt;
 margin: 1mm 0;
 font-weight: bold;
 }
 .box-destaque {
 border: 2px solid #000;
 padding: 2mm;
 margin: 2mm 0;
 text-align: center;
 }
 .movimento-info {
 font-size: 8.5pt;
 font-weight: bold;
 margin-bottom: 1mm;
 }
 .documento-fiscal {
 font-size: 8pt;
 }
 .timestamp-emissao {
 text-align: center;
 font-size: 8pt;
 margin: 2mm 0;
 }
 .linha-igual {
 text-align: center;
 font-size: 9pt;
 margin: 2mm 0;
 }
 .secao-bloco {
 margin: 3mm 0;
 }
 .numero-bloco {
 display: inline-block;
 width: 15px;
 height: 15px;
 border: 1px solid #000;
 text-align: center;
 font-weight: bold;
 font-size: 10pt;
 margin-right: 3mm;
 vertical-align: top;
 }
 .conteudo-bloco {
 display: inline-block;
 width: calc(100% - 20px);
 vertical-align: top;
 }
 .titulo-bloco {
 font-weight: bold;
 font-size: 9pt;
 margin-bottom: 1mm;
 }
 .linha-dado {
 display: flex;
 justify-content: space-between;
 font-size: 9pt;
 margin: 1mm 0;
 }
 .linha-dado .label {
 flex: 1;
 }
 .linha-dado .numero,
 .linha-dado .valor {
 font-weight: bold;
 margin-left: 2mm;
 }
 .tabela-dados {
 width: 100%;
 font-size: 9pt;
 border-collapse: collapse;
 }
 .tabela-dados td {
 padding: 1mm 0;
 }
 .tabela-dados .descricao {
 width: 60%;
 }
 .tabela-dados .qtd {
 width: 10%;
 text-align: center;
 }
 .tabela-dados .valor {
 width: 30%;
 text-align: right;
 }
 .tabela-dados .total-linha {
 border-top: 1px solid #000;
 border-bottom: 1px solid #000;
 font-weight: bold;
 margin-top: 1mm;
 }
 .tabela-dados .total-linha td {
 padding: 1.5mm 0;
 }
 .tabela-dados .diferenca-linha {
 border-top: 2px double #000;
 border-bottom: 2px double #000;
 font-weight: bold;
 background: #f0f0f0;
 }
 .tabela-dados .diferenca-linha td {
 padding: 2mm 0;
 font-size: 10pt;
 }
 .box-final {
 border: 2px solid #000;
 padding: 2mm;
 margin: 3mm 0;
 }
 .linha-total-final,
 .linha-diferenca {
 display: flex;
 justify-content: space-between;
 font-size: 10pt;
 font-weight: bold;
 margin: 1mm 0;
 }
 .assinatura-box {
 margin-top: 10mm;
 text-align: center;
 }
 .linha-assinatura-pdv {
 border-top: 1px solid #000;
 width: 70%;
 margin: 5mm auto 2mm auto;
 }
 .nome-assinatura {
 font-weight: bold;
 font-size: 9pt;
 margin: 1mm 0;
 }
 .cargo-assinatura {
 font-size: 8pt;
 color: #666;
 }
 .rodape-relatorio {
 text-align: center;
 font-size: 7pt;
 color: #666;
 margin-top: 5mm;
 padding-top: 2mm;
 border-top: 1px dashed #999;
 }
 @media print {
 .no-print,
 nav,
 .navbar,
 .sidebar,
 #sidebar,
 .menu-principal,
 header,
 .btn,
 button,
 .modal,
 .modal-backdrop {
 display: none !important;
 }
 body {
 margin: 0 !important;
 padding: 0 !important;
 background: white !important;
 }
 .print-only {
 display: block !important;
 }
 .relatorio-gerencial {
 max-width: 80mm !important;
 margin: 0 auto !important;
 padding: 3mm !important;
 }
 .relatorio-profissional-backup {
 display: none !important;
 }
 .relatorio-profissional {
 display: none !important;
 }
 .box-destaque,
 .box-final {
 -webkit-print-color-adjust: exact !important;
 print-color-adjust: exact !important;
 }
 .secao-bloco {
 page-break-inside: avoid !important;
 }
 h3 {
 page-break-after: avoid !important;
 }
 canvas {
 max-width: 100% !important;
 }
 .relatorio-profissional * {
 visibility: visible !important;
 }
 }
 .card {
 transition: transform 0.2s;
 }
 .card:hover {
 transform: translateY(-5px);
 box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
 }
 .relatorio-profissional {
 font-family: 'Segoe UI', 'Arial', sans-serif;
 font-size: 10pt;
 line-height: 1.5;
 color: #000;
 width: 100%;
 max-width: 210mm;
 margin: 0 auto;
 padding: 8mm;
 background: white;
 }
 .cabecalho-empresa {
 display: grid;
 grid-template-columns: 80px 1fr auto;
 gap: 15px;
 align-items: center;
 padding: 15px 20px;
 border: 3px solid #000;
 margin-bottom: 15px;
 background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
 border-radius: 8px;
 box-shadow: 0 2px 8px rgba(0,0,0,0.1);
 }
 .logo-empresa {
 width: 75px;
 height: 75px;
 background: linear-gradient(135deg, #000 0%, #333 100%);
 color: white;
 display: flex;
 align-items: center;
 justify-content: center;
 border-radius: 12px;
 font-size: 36pt;
 box-shadow: 0 4px 10px rgba(0,0,0,0.2);
 }
 .info-empresa {
 flex: 1;
 }
 .nome-empresa {
 font-size: 19pt;
 font-weight: bold;
 color: #000;
 margin: 0 0 8px 0;
 letter-spacing: -0.5px;
 text-transform: uppercase;
 }
 .dados-empresa {
 font-size: 9pt;
 color: #333;
 line-height: 1.6;
 }
 .dados-empresa div {
 margin: 2px 0;
 }
 .codigo-caixa {
 text-align: center;
 padding: 12px 18px;
 background: linear-gradient(135deg, #000 0%, #333 100%);
 color: white;
 border-radius: 10px;
 font-weight: bold;
 box-shadow: 0 4px 10px rgba(0,0,0,0.2);
 }
 .codigo-label {
 font-size: 8.5pt;
 opacity: 0.9;
 display: block;
 margin-bottom: 3px;
 }
 .codigo-numero {
 font-size: 17pt;
 display: block;
 font-weight: bold;
 letter-spacing: 1.5px;
 }
 .titulo-documento {
 text-align: center;
 margin: 15px 0 20px 0;
 padding: 14px;
 background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
 color: white;
 border-radius: 8px;
 box-shadow: 0 4px 12px rgba(0,0,0,0.3);
 }
 .titulo-documento h2 {
 font-size: 17pt;
 margin: 0;
 font-weight: bold;
 letter-spacing: 1px;
 text-transform: uppercase;
 }
 .subtitulo {
 font-size: 9.5pt;
 margin: 6px 0 0 0;
 opacity: 0.95;
 }
 .secao-titulo {
 font-size: 12pt;
 font-weight: bold;
 border-left: 6px solid #0d6efd;
 padding: 10px 0 10px 18px;
 margin: 25px 0 15px 0;
 color: #000;
 background: linear-gradient(90deg, rgba(13,110,253,0.1) 0%, transparent 100%);
 border-radius: 0 5px 5px 0;
 text-transform: uppercase;
 letter-spacing: 0.5px;
 }
 .secao-titulo i {
 margin-right: 12px;
 color: #0d6efd;
 font-size: 14pt;
 }
 .info-grid {
 display: grid;
 grid-template-columns: 1fr 1fr;
 gap: 10px;
 margin: 10px 0 25px 0;
 padding: 18px;
 background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
 border-radius: 8px;
 border: 2px solid #dee2e6;
 box-shadow: 0 2px 6px rgba(0,0,0,0.05);
 }
 .info-item {
 padding: 10px 14px;
 background: white;
 border-left: 4px solid #0d6efd;
 border-radius: 5px;
 box-shadow: 0 1px 3px rgba(0,0,0,0.1);
 transition: transform 0.2s;
 }
 .info-item:hover {
 transform: translateX(3px);
 }
 .info-label {
 font-weight: bold;
 color: #495057;
 font-size: 9pt;
 display: block;
 margin-bottom: 4px;
 text-transform: uppercase;
 letter-spacing: 0.3px;
 }
 .info-valor {
 display: block;
 color: #000;
 font-size: 10.5pt;
 font-weight: 600;
 }
 padding: 20mm;
 max-width: 210mm;
 margin: 0 auto;
 background: white;
 }
 .cabecalho-empresa {
 border: 2px solid #000;
 padding: 15px;
 margin-bottom: 10px;
 background: #f8f9fa;
 }
 .logo-empresa {
 width: 80px;
 height: 80px;
 border: 2px solid #000;
 border-radius: 50%;
 display: flex;
 align-items: center;
 justify-content: center;
 font-size: 40px;
 color: #333;
 margin: 0 auto;
 }
 .empresa-nome {
 font-size: 24pt;
 font-weight: bold;
 margin: 0;
 color: #000;
 }
 .empresa-info {
 font-size: 9pt;
 color: #333;
 }
 .codigo-caixa {
 border: 2px solid #000;
 padding: 10px;
 text-align: center;
 background: white;
 }
 .codigo-label {
 font-size: 9pt;
 font-weight: bold;
 }
 .codigo-numero {
 font-size: 18pt;
 font-weight: bold;
 color: #000;
 }
 .divisor-linha {
 border-top: 3px double #000;
 margin: 15px 0;
 }
 .titulo-documento {
 text-align: center;
 margin: 20px 0;
 padding: 10px;
 background: #000;
 color: white;
 }
 .titulo-documento h2 {
 font-size: 16pt;
 margin: 0;
 font-weight: bold;
 }
 .subtitulo {
 font-size: 9pt;
 margin: 5px 0 0 0;
 opacity: 0.9;
 }
 .secao-titulo {
 font-size: 12pt;
 font-weight: bold;
 border-bottom: 2px solid #000;
 padding-bottom: 5px;
 margin: 15px 0 10px 0;
 color: #000;
 }
 .secao-titulo i {
 margin-right: 8px;
 }
 .info-grid {
 display: grid;
 grid-template-columns: 1fr 1fr;
 gap: 10px;
 margin: 10px 0;
 }
 .info-item {
 padding: 8px;
 border: 1px solid #ddd;
 background: #f8f9fa;
 }
 .info-label {
 font-weight: bold;
 color: #555;
 font-size: 9pt;
 }
 .info-valor {
 display: block;
 color: #000;
 font-size: 10pt;
 margin-top: 3px;
 }
 .tabela-profissional {
 width: 100%;
 border-collapse: separate;
 border-spacing: 0;
 margin: 12px 0 20px 0;
 font-size: 10pt;
 border-radius: 8px;
 overflow: hidden;
 box-shadow: 0 2px 8px rgba(0,0,0,0.1);
 }
 .tabela-profissional thead {
 background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
 color: white;
 }
 .tabela-profissional th {
 padding: 12px 10px;
 text-align: left;
 font-weight: bold;
 border: none;
 font-size: 10pt;
 text-transform: uppercase;
 letter-spacing: 0.5px;
 }
 .tabela-profissional td {
 padding: 10px;
 border: 1px solid #dee2e6;
 border-left: none;
 border-right: none;
 font-size: 10pt;
 }
 .tabela-profissional tbody tr {
 transition: background-color 0.2s;
 }
 .tabela-profissional tbody tr:hover {
 background: #f8f9fa !important;
 }
 .tabela-profissional tbody tr:nth-child(even) {
 background: #f8f9fa;
 }
 .tabela-profissional tbody tr:nth-child(odd) {
 background: white;
 }
 .tabela-profissional tfoot .total-row {
 background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
 font-weight: bold;
 font-size: 11pt;
 }
 .tabela-profissional tfoot td {
 border: 2px solid #495057;
 border-left: none;
 border-right: none;
 padding: 12px 10px;
 font-weight: bold;
 }
 .text-center {
 text-align: center;
 }
 .text-end {
 text-align: right;
 }
 .valor-positivo {
 color: #198754;
 font-weight: bold;
 }
 .valor-negativo {
 color: #dc3545;
 font-weight: bold;
 }
 .barra-progresso {
 height: 24px;
 background: #e9ecef;
 border-radius: 4px;
 overflow: hidden;
 position: relative;
 border: 1px solid #dee2e6;
 }
 .barra-fill {
 height: 100%;
 background: linear-gradient(90deg, #198754 0%, #20c997 100%);
 transition: width 0.3s;
 display: flex;
 align-items: center;
 justify-content: flex-end;
 padding-right: 6px;
 color: white;
 font-size: 8.5pt;
 font-weight: bold;
 }
 .grafico-container {
 border: 2px solid #dee2e6;
 padding: 20px;
 text-align: center;
 background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
 border-radius: 8px;
 box-shadow: 0 2px 8px rgba(0,0,0,0.08);
 margin: 15px 0;
 }
 .grafico-titulo {
 font-size: 11pt;
 font-weight: bold;
 margin-bottom: 15px;
 color: #000;
 text-transform: uppercase;
 letter-spacing: 0.5px;
 }
 .secao-observacoes {
 margin: 25px 0;
 }
 .observacoes-texto {
 border: 2px solid #dee2e6;
 padding: 18px;
 background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
 font-size: 10pt;
 min-height: 80px;
 border-radius: 8px;
 line-height: 1.6;
 box-shadow: 0 2px 6px rgba(0,0,0,0.05);
 }
 .secao-assinaturas {
 margin-top: 50px;
 page-break-inside: avoid;
 padding: 20px 0;
 }
 .linha-assinatura {
 border-top: 2px solid #000;
 margin: 40px 30px 12px 30px;
 }
 .label-assinatura {
 text-align: center;
 font-weight: bold;
 font-size: 11pt;
 margin-bottom: 3px;
 color: #000;
 }
 .sublabel-assinatura {
 text-align: center;
 font-size: 9pt;
 color: #6c757d;
 font-style: italic;
 }
 .rodape-documento {
 margin-top: 40px;
 padding-top: 20px;
 border-top: 2px solid #dee2e6;
 font-size: 8.5pt;
 color: #6c757d;
 background: linear-gradient(90deg, #f8f9fa 0%, transparent 50%, #f8f9fa 100%);
 padding: 15px 20px;
 border-radius: 5px;
 }
 .badge-destaque {
 display: inline-block;
 padding: 4px 10px;
 background: #0d6efd;
 color: white;
 border-radius: 4px;
 font-size: 9pt;
 font-weight: bold;
 margin-left: 8px;
 }
 .badge-sucesso {
 background: #198754;
 }
 .badge-alerta {
 background: #ffc107;
 color: #000;
 }
 @media screen {
 .info-item,
 .tabela-profissional tbody tr,
 .grafico-container {
 animation: fadeIn 0.5s ease-in;
 }
 @keyframes fadeIn {
 from {
 opacity: 0;
 transform: translateY(10px);
 }
 to {
 opacity: 1;
 transform: translateY(0);
 }
 }
 }
 @media print {
 .relatorio-profissional {
 padding: 10mm;
 }
 .cabecalho-empresa,
 .codigo-caixa,
 .card-resumo,
 .box-calculo,
 .box-conferencia,
 .indicador {
 -webkit-print-color-adjust: exact;
 print-color-adjust: exact;
 }
 .titulo-documento {
 -webkit-print-color-adjust: exact;
 print-color-adjust: exact;
 background: #000 !important;
 color: white !important;
 }
 .tabela-profissional thead {
 -webkit-print-color-adjust: exact;
 print-color-adjust: exact;
 background: #000 !important;
 color: white !important;
 }
 .secao-info,
 .secao-resumo,
 .secao-formas-pagamento,
 .secao-movimentacao,
 .secao-assinaturas {
 page-break-inside: avoid;
 }
 h3 {
 page-break-after: avoid;
 }
 }
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
 let graficoInstance = null;
 function imprimirRelatorio() {
 gerarGrafico();
 setTimeout(function() {
 window.print();
 }, 500);
 }
 function gerarGrafico() {
 const ctx = document.getElementById('graficoFormasPagamento');
 if (!ctx) return;
 const dados = @json($dadosGrafico ?? []);
 if (dados.length === 0) return;
 if (graficoInstance) {
 graficoInstance.destroy();
 graficoInstance = null;
 }
 const cores = [
 '#198754',
 '#0d6efd',
 '#0dcaf0',
 '#ffc107',
 '#6c757d'
 ];
 graficoInstance = new Chart(ctx, {
 type: 'doughnut',
 data: {
 labels: dados.map(d => d.nome),
 datasets: [{
 data: dados.map(d => d.valor),
 backgroundColor: cores.slice(0, dados.length),
 borderWidth: 2,
 borderColor: '#fff'
 }]
 },
 options: {
 responsive: true,
 maintainAspectRatio: true,
 plugins: {
 legend: {
 position: 'bottom',
 labels: {
 font: {
 size: 10,
 weight: 'bold'
 },
 padding: 10,
 generateLabels: function(chart) {
 const data = chart.data;
 if (data.labels.length && data.datasets.length) {
 return data.labels.map((label, i) => {
 const value = data.datasets[0].data[i];
 const percentual = dados[i].percentual;
 return {
 text: `${label}: R$ ${value.toFixed(2).replace('.', ',')} (${percentual.toFixed(1)}%)`,
 fillStyle: data.datasets[0].backgroundColor[i],
 hidden: false,
 index: i
 };
 });
 }
 return [];
 }
 }
 },
 tooltip: {
 callbacks: {
 label: function(context) {
 const label = context.label || '';
 const value = context.parsed || 0;
 const percentual = dados[context.dataIndex].percentual;
 return `${label}: R$ ${value.toFixed(2).replace('.', ',')} (${percentual.toFixed(1)}%)`;
 }
 }
 }
 }
 }
 });
 }
 document.addEventListener('DOMContentLoaded', function() {
 gerarGrafico();
 });
</script>
@endpush
@endsection