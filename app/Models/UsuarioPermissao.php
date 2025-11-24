<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioPermissao extends Model
{
    use HasFactory;

    protected $table = 'usuario_permissoes';

    protected $fillable = [
        'usuario_id',
        'modulo',
        'visualizar',
        'criar',
        'editar',
        'excluir',
        'ativo'
    ];

    protected $casts = [
        'visualizar' => 'boolean',
        'criar' => 'boolean',
        'editar' => 'boolean',
        'excluir' => 'boolean',
        'ativo' => 'boolean'
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Criar permissÃµes padrÃ£o para um usuÃ¡rio
     */
    public static function criarPermissoesPadrao($usuarioId, $role)
    {
        $permissoes = self::getPermissoesPorRole($role);
        
        foreach ($permissoes as $modulo => $acoes) {
            self::updateOrCreate(
                [
                    'usuario_id' => $usuarioId,
                    'modulo' => $modulo
                ],
                [
                    'visualizar' => $acoes['visualizar'] ?? false,
                    'criar' => $acoes['criar'] ?? false,
                    'editar' => $acoes['editar'] ?? false,
                    'excluir' => $acoes['excluir'] ?? false,
                    'ativo' => true
                ]
            );
        }
    }

    /**
     * Retornar permissÃµes padrÃ£o por role
     */
    private static function getPermissoesPorRole($role)
    {
        $permissoesPadrao = [
            'admin' => [
                'produtos' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => true],
                'categorias' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => true],
                'pedidos' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => true],
                'mesas' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => true],
                'clientes' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => true],
                'delivery' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => true],
                'usuarios' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => true],
                'relatorios' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => true],
                'caixa' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => true],
            ],
            'gerente' => [
                'produtos' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => false],
                'categorias' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => false],
                'pedidos' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => false],
                'mesas' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => false],
                'clientes' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => false],
                'delivery' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => false],
                'relatorios' => ['visualizar' => true, 'criar' => false, 'editar' => false, 'excluir' => false],
                'caixa' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => false],
            ],
            'garcom' => [
                'pedidos' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => false],
                'mesas' => ['visualizar' => true, 'criar' => false, 'editar' => true, 'excluir' => false],
                'produtos' => ['visualizar' => true, 'criar' => false, 'editar' => false, 'excluir' => false],
            ],
            'caixa' => [
                'pedidos' => ['visualizar' => true, 'criar' => false, 'editar' => true, 'excluir' => false],
                'caixa' => ['visualizar' => true, 'criar' => true, 'editar' => true, 'excluir' => false],
            ],
        ];

        return $permissoesPadrao[$role] ?? [];
    }
}
