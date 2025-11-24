<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClienteAuthController extends Controller
{
    /**
     * Login ou registro via telefone
     */
    public function loginOrRegister(Request $request)
    {
        $request->validate([
            'telefone' => 'required|string|min:10|max:15',
            'nome' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'fcm_token' => 'nullable|string' // Token FCM para notificações
        ]);

        $telefone = preg_replace('/\D/', '', $request->telefone);

        // Buscar cliente pelo telefone
        $cliente = Cliente::where('telefone', $telefone)->first();

        if ($cliente) {
            // Cliente existente - fazer login
            $token = $cliente->createToken('app-cliente')->plainTextToken;

            // Salvar/atualizar token FCM se fornecido
            if ($request->fcm_token) {
                \DB::table('fcm_tokens')->updateOrInsert(
                    ['user_id' => $cliente->id],
                    [
                        'token' => $request->fcm_token,
                        'device_type' => 'android',
                        'ativo' => true,
                        'updated_at' => now()
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'cliente' => $cliente,
                'token' => $token
            ]);
        }

        // Cliente não existe - verificar se enviou nome para criar
        if (!$request->nome) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente não cadastrado. Envie o nome para criar uma conta.',
                'requires_registration' => true
            ], 404);
        }

        // Validar email único se informado
        if ($request->email) {
            $emailExists = Cliente::where('email', $request->email)->exists();
            if ($emailExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este email já está cadastrado. Use outro email ou faça login.'
                ], 422);
            }
        }

        try {
            // Criar novo cliente
            $cliente = Cliente::create([
                'nome' => $request->nome,
                'telefone' => $telefone,
                'email' => $request->email,
                'tenant_cod' => 'APP/EXTERNO', // Identificar origem como app externo
                'ativo' => true
            ]);

            $token = $cliente->createToken('app-cliente')->plainTextToken;

            // Salvar token FCM se fornecido
            if ($request->fcm_token) {
                \DB::table('fcm_tokens')->insert([
                    'user_id' => $cliente->id,
                    'token' => $request->fcm_token,
                    'device_type' => 'android',
                    'ativo' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Conta criada com sucesso',
                'cliente' => $cliente,
                'token' => $token,
                'is_new' => true
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            // Se for erro de duplicação, significa que o cliente existe mas não foi encontrado
            // (pode ter sido criado entre a busca e a criação)
            if ($e->getCode() == 23000) {
                $cliente = Cliente::where('telefone', $telefone)->first();
                if ($cliente) {
                    $token = $cliente->createToken('app-cliente')->plainTextToken;
                    return response()->json([
                        'success' => true,
                        'message' => 'Login realizado com sucesso',
                        'cliente' => $cliente,
                        'token' => $token
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar conta. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Verificar token e retornar cliente
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'cliente' => $request->user()
        ]);
    }

    /**
     * Logout - revogar token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Atualizar dados do cliente
     */
    public function updateProfile(Request $request)
    {
        \Log::info('Dados recebidos no updateProfile:', $request->all());
        
        $validated = $request->validate([
            'nome' => 'sometimes|string|max:255',
            'telefone' => 'sometimes|string|max:20',
            'email' => 'sometimes|nullable|email|max:255|unique:clientes,email,' . $request->user()->id,
            'endereco_rua' => 'nullable|string|max:255',
            'endereco_numero' => 'nullable|string|max:20',
            'endereco_bairro' => 'nullable|string|max:100',
            'endereco_cidade' => 'nullable|string|max:100',
            'endereco_cep' => 'nullable|string|max:10',
        ], [
            'nome.string' => 'Nome inválido',
            'email.email' => 'Email inválido',
            'email.unique' => 'Este email já está sendo usado por outro cliente',
        ]);

        \Log::info('Dados validados:', $validated);

        $cliente = $request->user();
        
        // Atualizar cada campo individualmente
        foreach ($validated as $key => $value) {
            $cliente->$key = $value;
        }
        
        $cliente->save();
        
        // Buscar direto do banco para evitar cache
        $clienteAtualizado = Cliente::find($cliente->id);
        
        \Log::info('Cliente após update:', $clienteAtualizado->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'cliente' => $clienteAtualizado
        ]);
    }
}
