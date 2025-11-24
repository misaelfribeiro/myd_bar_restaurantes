<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\BelongsToTenant;
class User extends Authenticatable
{
 use HasApiTokens, HasFactory, Notifiable, BelongsToTenant;
 protected $fillable = [
 'name',
 'email',
 'password',
 'tenant_code',
 ];
 protected $hidden = [
 'password',
 'remember_token',
 ];
 protected $casts = [
 'email_verified_at' => 'datetime',
 ];
 public function empresa()
 {
 return $this->belongsTo(Empresa::class, 'tenant_code', 'tenant_code');
 }
}