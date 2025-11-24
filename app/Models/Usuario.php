<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\BelongsToTenant;
class Usuario extends Authenticatable
{
 use HasFactory, Notifiable, HasApiTokens, BelongsToTenant;
 protected $fillable = [
 'nome',
 'email',
 'password',
 'role',
 'tenant_code',
 ];
 protected $hidden = [
 'password',
 'remember_token',
 ];
 protected $casts = [
 'email_verified_at' => 'datetime',
 ];
 public function pedidos()
 {
 return $this->hasMany(Pedido::class);
 }
}