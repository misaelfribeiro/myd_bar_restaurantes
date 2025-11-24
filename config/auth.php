<?php
return [
 'defaults' => [
 'guard' => 'web',
 'passwords' => 'users',
 ],
 'guards' => [
 'web' => [
 'driver' => 'session',
 'provider' => 'usuarios',
 ],
 'admin' => [
 'driver' => 'session',
 'provider' => 'admins',
 ],
 'api' => [
 'driver' => 'sanctum',
 'provider' => 'usuarios',
 ],
 ],
 'providers' => [
 'usuarios' => [
 'driver' => 'eloquent',
 'model' => App\Models\Usuario::class,
 ],
 'admins' => [
 'driver' => 'eloquent',
 'model' => App\Models\User::class,
 ],
 ],
 'passwords' => [
 'usuarios' => [
 'provider' => 'usuarios',
 'table' => 'password_resets',
 'expire' => 60,
 'throttle' => 60,
 ],
 'admins' => [
 'provider' => 'admins',
 'table' => 'password_resets',
 'expire' => 60,
 'throttle' => 60,
 ],
 ],
 'password_timeout' => 10800,
];