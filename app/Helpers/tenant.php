<?php
if (!function_exists('tenant')) {
 function tenant()
 {
 try {
 return app('tenant');
 } catch (\Exception $e) {
 return null;
 }
 }
}
if (!function_exists('isMaster')) {
 function isMaster()
 {
 $tenant = tenant();
 return $tenant && $tenant->is_master;
 }
}
if (!function_exists('tenantCode')) {
 function tenantCode()
 {
 $tenant = tenant();
 return $tenant ? $tenant->tenant_code : null;
 }
}
if (!function_exists('canUseFeature')) {
 function canUseFeature($feature)
 {
 $tenant = tenant();
 return $tenant && $tenant->possuiRecurso($feature);
 }
}
if (!function_exists('tenantLimit')) {
 function tenantLimit($type)
 {
 $tenant = tenant();
 if (!$tenant) return null;
 switch ($type) {
 case 'usuarios':
 return $tenant->max_usuarios;
 case 'produtos':
 return $tenant->max_produtos;
 case 'pedidos':
 return $tenant->max_pedidos_mes;
 case 'filiais':
 return $tenant->max_filiais;
 default:
 return null;
 }
 }
}