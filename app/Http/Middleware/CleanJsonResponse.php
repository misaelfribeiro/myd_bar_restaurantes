<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CleanJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Apenas para requisições que esperam JSON
        if ($request->wantsJson() || $request->ajax()) {
            \Log::info('=== CleanJsonResponse Middleware ===');
            \Log::info('URL: ' . $request->fullUrl());
            \Log::info('Method: ' . $request->method());
            
            // Limpa todos os output buffers antes de enviar resposta
            $buffersLimpos = 0;
            while (ob_get_level() > 0) {
                ob_end_clean();
                $buffersLimpos++;
            }
            \Log::info('Output buffers limpos: ' . $buffersLimpos);
            
            // Se a resposta for JSON, remove BOM se existir
            if ($response->headers->get('Content-Type') === 'application/json' ||
                str_contains($response->headers->get('Content-Type') ?? '', 'application/json')) {
                
                $content = $response->getContent();
                $contentOriginal = $content;
                $tamanhoOriginal = strlen($content);
                
                \Log::info('Tamanho original: ' . $tamanhoOriginal . ' bytes');
                
                // Verifica BOM no início
                $temBOM = false;
                if (strlen($content) >= 3) {
                    $firstBytes = substr($content, 0, 3);
                    if ($firstBytes === "\xEF\xBB\xBF") {
                        $temBOM = true;
                        \Log::warning('🚨 BOM UTF-8 DETECTADO no início!');
                    }
                }
                
                // Mostra primeiros bytes em hexadecimal
                $primeiros20 = substr($content, 0, 20);
                $hex = bin2hex($primeiros20);
                \Log::info('Primeiros 20 bytes (hex): ' . $hex);
                \Log::info('Primeiros 100 caracteres: ' . substr($content, 0, 100));
                
                // Remove BOM UTF-8 (EF BB BF)
                $content = str_replace("\xEF\xBB\xBF", '', $content);
                
                // Remove espaços em branco antes/depois
                $content = trim($content);
                
                $tamanhoFinal = strlen($content);
                $bytesRemovidos = $tamanhoOriginal - $tamanhoFinal;
                
                if ($bytesRemovidos > 0) {
                    \Log::warning('Bytes removidos: ' . $bytesRemovidos);
                }
                
                \Log::info('Tamanho final: ' . $tamanhoFinal . ' bytes');
                \Log::info('Primeiros 50 (limpo): ' . substr($content, 0, 50));
                \Log::info('Últimos 50 (limpo): ' . substr($content, -50));
                
                // Verifica se é JSON válido
                json_decode($content);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    \Log::error('❌ JSON INVÁLIDO após limpeza: ' . json_last_error_msg());
                } else {
                    \Log::info('✅ JSON válido após limpeza');
                }
                
                $response->setContent($content);
            } else {
                \Log::info('Content-Type não é JSON: ' . $response->headers->get('Content-Type'));
            }
        }
        
        return $response;
    }
}
