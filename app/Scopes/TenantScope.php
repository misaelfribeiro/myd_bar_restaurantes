<?php
namespace App\Scopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
class TenantScope implements Scope
{
 public function apply(Builder $builder, Model $model)
 {
 $tenant = null;
 try {
 $tenant = tenant();
 } catch (\Exception $e) {
 return;
 }
 if ($tenant && $tenant->is_master) {
 return;
 }
 if ($tenant && $tenant->tenant_code) {
 $builder->where($model->getTable() . '.tenant_code', $tenant->tenant_code);
 }
 }
 public function extend(Builder $builder)
 {
 $builder->macro('withoutTenant', function (Builder $builder) {
 return $builder->withoutGlobalScope($this);
 });
 $builder->macro('allTenants', function (Builder $builder) {
 return $builder->withoutGlobalScope($this);
 });
 }
}