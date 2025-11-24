<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class Cliente extends Model
{
    use HasFactory, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'nome',
        'telefone', 
        'email',
        'endereco_rua',
        'endereco_numero',
        'endereco_complemento',
        'endereco_bairro',
        'endereco_cidade',
        'endereco_cep',
        'cpf',
        'data_nascimento',
        'ativo',
        'ultimo_pedido',
        'total_pedidos',
        'preferencias',
        'observacoes',
        'tenant_cod'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'ultimo_pedido' => 'datetime',
        'ativo' => 'boolean',
        'preferencias' => 'array'
    ];

    protected $dates = ['deleted_at'];

    // Relacionamentos
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'cliente_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    public function enderecos()
    {
        return $this->hasMany(EnderecoCliente::class, 'cliente_id');
    }

    public function enderecoPadrao()
    {
        return $this->hasOne(EnderecoCliente::class, 'cliente_id')->where('padrao', true);
    }

    // Métodos auxiliares
    public function getTelefoneFormatadoAttribute()
    {
        $telefone = preg_replace('/\D/', '', $this->telefone);
        
        if (strlen($telefone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
        } else if (strlen($telefone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
        }
        
        return $this->telefone;
    }

    public function getCpfFormatadoAttribute()
    {
        if (!$this->cpf) return null;
        
        $cpf = preg_replace('/\D/', '', $this->cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    public function getIdadeAttribute()
    {
        return $this->data_nascimento ? $this->data_nascimento->age : null;
    }

    public function getEnderecoCompletoAttribute()
    {
        $endereco = [];
        
        if ($this->endereco_rua) {
            $endereco[] = $this->endereco_rua . ($this->endereco_numero ? ', ' . $this->endereco_numero : '');
        }
        
        if ($this->endereco_complemento) {
            $endereco[] = $this->endereco_complemento;
        }
        
        if ($this->endereco_bairro) {
            $endereco[] = $this->endereco_bairro;
        }
        
        if ($this->endereco_cidade) {
            $endereco[] = $this->endereco_cidade;
        }
        
        if ($this->endereco_cep) {
            $endereco[] = 'CEP: ' . $this->endereco_cep;
        }
        
        return implode(', ', $endereco);
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorTelefone($query, $telefone)
    {
        $telefone = preg_replace('/\D/', '', $telefone);
        return $query->where('telefone', 'like', '%' . $telefone . '%');
    }

    public function scopePorEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    // Método para buscar ou criar cliente
    public static function buscarOuCriarPorTelefone($dados)
    {
        $telefone = preg_replace('/\D/', '', $dados['telefone']);
        
        $cliente = self::where('telefone', $telefone)->first();
        
        if (!$cliente) {
            $dadosCliente = [
                'nome' => $dados['nome'],
                'telefone' => $telefone,
                'email' => $dados['email'] ?? null,
                'endereco_rua' => $dados['endereco_rua'] ?? null,
                'endereco_numero' => $dados['endereco_numero'] ?? null,
                'endereco_complemento' => $dados['endereco_complemento'] ?? null,
                'endereco_bairro' => $dados['endereco_bairro'] ?? null,
                'endereco_cidade' => $dados['endereco_cidade'] ?? null,
                'endereco_cep' => $dados['endereco_cep'] ?? null,
            ];
            
            $cliente = self::create($dadosCliente);
        } else {
            // Atualizar dados se necessário (apenas se novos dados foram fornecidos)
            $dadosParaAtualizar = [];
            
            if (isset($dados['nome']) && $dados['nome'] !== $cliente->nome) {
                $dadosParaAtualizar['nome'] = $dados['nome'];
            }
            
            if (isset($dados['email']) && $dados['email'] !== $cliente->email) {
                $dadosParaAtualizar['email'] = $dados['email'];
            }
            
            // Atualizar endereço se fornecido
            $camposEndereco = ['endereco_rua', 'endereco_numero', 'endereco_complemento', 'endereco_bairro', 'endereco_cidade', 'endereco_cep'];
            foreach ($camposEndereco as $campo) {
                if (isset($dados[$campo]) && $dados[$campo] !== $cliente->{$campo}) {
                    $dadosParaAtualizar[$campo] = $dados[$campo];
                }
            }
            
            if (!empty($dadosParaAtualizar)) {
                $cliente->update($dadosParaAtualizar);
            }
        }
        
        return $cliente;
    }
}
