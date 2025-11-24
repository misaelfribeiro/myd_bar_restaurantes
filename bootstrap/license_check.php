<?php
// Verificacao de Licenca - Nao Remover
if (!function_exists('verificar_licenca_sistema')) {
    function verificar_licenca_sistema() {
        $licenseFile = __DIR__ . '/../storage/app/license.key';
        
        if (!file_exists($licenseFile)) {
            die('<h1>Sistema nao ativado</h1><p>Entre em contato com o fornecedor para ativar sua licenca.</p>');
        }
        
        $licenseData = json_decode(file_get_contents($licenseFile), true);
        
        if (!$licenseData || !isset($licenseData['key'])) {
            die('<h1>Licenca invalida</h1><p>Arquivo de licenca corrompido.</p>');
        }
        
        // Verificar expiracao
        if (isset($licenseData['expires_at'])) {
            $expiresAt = strtotime($licenseData['expires_at']);
            if (time() > $expiresAt) {
                die('<h1>Licenca expirada</h1><p>Sua licenca expirou em ' . date('d/m/Y', $expiresAt) . '</p>');
            }
        }
        
        // Verificar hardware (se configurado)
        if (isset($licenseData['hardware_id']) && !empty($licenseData['hardware_id'])) {
            $currentHardware = '';
            
            if (stripos(PHP_OS, 'WIN') === 0) {
                exec('wmic csproduct get uuid', $output);
                $currentHardware = trim($output[1] ?? '');
            } else {
                exec('cat /sys/class/dmi/id/product_uuid 2>/dev/null', $output);
                $currentHardware = trim($output[0] ?? '');
            }
            
            if ($currentHardware && $currentHardware !== $licenseData['hardware_id']) {
                die('<h1>Licenca invalida</h1><p>Esta licenca nao e valida para este computador.</p>');
            }
        }
        
        return true;
    }
}

verificar_licenca_sistema();
