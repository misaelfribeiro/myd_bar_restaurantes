<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class AdminUserSeeder extends Seeder
{
 public function run()
 {
 \App\Models\Usuario::firstOrCreate([
 'email' => 'admin@admin.com'
 ], [
 'nome' => 'Admin Teste',
 'password' => bcrypt('123456')
 ]);
 \App\Models\Entregador::firstOrCreate([
 'email' => 'joao@teste.com'
 ], [
 'nome' => 'João da Silva',
 'telefone' => '(11) 99999-9999',
 'cpf' => '123.456.789-10',
 'data_nascimento' => '1990-01-01',
 'cep' => '12345-678',
 'endereco' => 'Rua Teste',
 'numero' => '123',
 'bairro' => 'Centro',
 'cidade' => 'São Paulo',
 'estado' => 'SP',
 'tipo' => 'externo',
 'tipo_veiculo' => 'moto',
 'placa_veiculo' => 'ABC-1234',
 'status' => 'pendente',
 'disponivel' => true
 ]);
 }
}