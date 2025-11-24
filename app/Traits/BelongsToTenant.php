<?php
namespace App\Traits;
use App\Scopes\TenantScope;
trait BelongsToTenant
{
 protected static function bootBelongsToTenant()
 {
 static::addGlobalScope(new TenantScope());
 static::creating(function ($model) {
 if (!$model->tenant_code) {
 try {
 $tenant = tenant();
 if ($tenant && $tenant->tenant_code) {
 $model->tenant_code = $tenant->tenant_code;
 }
 } catch (\Exception $e) {
 }
 }
 });
 }
 public function tenant()
 {
 return $this->belongsTo(\App\Models\Empresa::class, 'tenant_code', 'tenant_code');
 }
 public function scopeWithoutTenant($query)
 {
 return $query->withoutGlobalScope(TenantScope::class);
 }
 public function scopeAllTenants($query)
 {
 return $query->withoutGlobalScope(TenantScope::class);
 }
}