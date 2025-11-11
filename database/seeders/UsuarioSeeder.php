<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin do sistema
        Usuario::firstOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'nome' => 'Administrador Sistema',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ]
        );

        // Gerente do restaurante
        Usuario::firstOrCreate(
            ['email' => 'gerente@restaurante.com'],
            [
                'nome' => 'João Gerente',
                'password' => Hash::make('gerente123'),
                'role' => 'gerente'
            ]
        );

        // Garçom 1
        Usuario::firstOrCreate(
            ['email' => 'maria2@restaurante.com'],
            [
                'nome' => 'Maria Garçonete',
                'password' => Hash::make('garcom123'),
                'role' => 'garcom'
            ]
        );

        // Garçom 2
        Usuario::firstOrCreate(
            ['email' => 'pedro@restaurante.com'],
            [
                'nome' => 'Pedro Garçom',
                'password' => Hash::make('garcom123'),
                'role' => 'garcom'
            ]
        );

        // Cliente VIP
        Usuario::firstOrCreate(
            ['email' => 'ana@email.com'],
            [
                'nome' => 'Ana Cliente VIP',
                'password' => Hash::make('cliente123'),
                'role' => 'cliente'
            ]
        );

        echo "✅ Usuários criados com diferentes perfis:\n";
        echo "📧 admin@sistema.com (admin) - Senha: admin123\n";
        echo "📧 gerente@restaurante.com (gerente) - Senha: gerente123\n";
        echo "📧 maria2@restaurante.com (garcom) - Senha: garcom123\n";
        echo "📧 pedro@restaurante.com (garcom) - Senha: garcom123\n";
        echo "📧 ana@email.com (cliente) - Senha: cliente123\n";
    }
}
