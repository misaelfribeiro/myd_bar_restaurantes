<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entregador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'entregadores';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'whatsapp',
        'cpf',
        'rg',
        'data_nascimento',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'banco',
        'agencia',
        'conta',
        'pix',
        'tipo_veiculo',
        'marca_veiculo',
        'modelo_veiculo',
        'placa_veiculo',
        'cor_veiculo',
        'ano_veiculo',
        'cnh_numero',
        'cnh_categoria',
        'cnh_validade',
        'foto_cnh',
        'foto_rg',
        'foto_cpf',
        'foto_comprovante_endereco',
        'foto_entregador',
        'status',
        'tipo',
        'observacoes_aprovacao',
        'data_aprovacao',
        'aprovado_por',
        'avaliacao_media',
        'total_avaliacoes',
        'entregas_realizadas',
        'taxa_sucesso',
        'disponivel',
        'ultimo_login',
        'localizacao_atual',
        'device_token',
        'notificacoes_push',
        'raio_entrega_km'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'cnh_validade' => 'date',
        'data_aprovacao' => 'datetime',
        'ultimo_login' => 'datetime',
        'localizacao_atual' => 'json',
        'avaliacao_media' => 'decimal:2',
        'taxa_sucesso' => 'decimal:2',
        'raio_entrega_km' => 'decimal:2',
        'disponivel' => 'boolean',
        'notificacoes_push' => 'boolean',
        'entregas_realizadas' => 'integer',
        'total_avaliacoes' => 'integer'
    ];

    protected $dates = ['deleted_at'];

    // Relacionamentos
    public function aprovador()
    {
        return $this->belongsTo(Usuario::class, 'aprovado_por');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'entregador_id');
    }

    public function avaliacoes()
    {
        return $this->hasMany(AvaliacaoEntregador::class);
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'ativo')->where('disponivel', true);
    }

    public function scopeInternos($query)
    {
        return $query->where('tipo', 'interno');
    }

    public function scopeExternos($query)
    {
        return $query->where('tipo', 'externo');
    }

    public function scopeAprovados($query)
    {
        return $query->where('status', 'aprovado');
    }

    // Métodos auxiliares
    public function getNomeCompletoAttribute()
    {
        return $this->nome;
    }

    public function getIdadeAttribute()
    {
        return $this->data_nascimento ? $this->data_nascimento->age : null;
    }

    public function isAtivo()
    {
        return $this->status === 'ativo';
    }

    public function isDisponivel()
    {
        return $this->disponivel && $this->isAtivo();
    }

    public function isAprovado()
    {
        return in_array($this->status, ['aprovado', 'ativo']);
    }

    public function temCNHValida()
    {
        return $this->cnh_validade && $this->cnh_validade->isFuture();
    }

    public function temDocumentosCompletos()
    {
        $documentosObrigatorios = [
            'foto_rg',
            'foto_cpf', 
            'foto_comprovante_endereco',
            'foto_entregador'
        ];

        if (in_array($this->tipo_veiculo, ['moto', 'carro'])) {
            $documentosObrigatorios[] = 'foto_cnh';
        }

        foreach ($documentosObrigatorios as $documento) {
            if (empty($this->$documento)) {
                return false;
            }
        }

        return true;
    }

    public function calcularTaxaSucesso()
    {
        if ($this->entregas_realizadas == 0) {
            return 0;
        }

        $pedidosEntregues = $this->pedidos()
            ->where('status', 'entregue')
            ->count();

        return round(($pedidosEntregues / $this->entregas_realizadas) * 100, 2);
    }

    public function atualizarEstatisticas()
    {
        $this->entregas_realizadas = $this->pedidos()
            ->whereIn('status', ['entregue', 'finalizado'])
            ->count();

        $this->taxa_sucesso = $this->calcularTaxaSucesso();

        if ($this->avaliacoes()->exists()) {
            $this->avaliacao_media = $this->avaliacoes()->avg('nota');
            $this->total_avaliacoes = $this->avaliacoes()->count();
        }

        $this->save();
    }

    public function marcarComoDisponivel()
    {
        $this->update([
            'disponivel' => true,
            'ultimo_login' => now()
        ]);
    }

    public function marcarComoIndisponivel()
    {
        $this->update(['disponivel' => false]);
    }

    public function atualizarLocalizacao($latitude, $longitude)
    {
        $this->update([
            'localizacao_atual' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'timestamp' => now()->toISOString()
            ]
        ]);
    }

    public function aprovar($aprovadoPor, $observacoes = null)
    {
        $this->update([
            'status' => 'aprovado',
            'data_aprovacao' => now(),
            'aprovado_por' => $aprovadoPor,
            'observacoes_aprovacao' => $observacoes
        ]);
    }

    public function reprovar($reprovadoPor, $observacoes)
    {
        $this->update([
            'status' => 'reprovado',
            'aprovado_por' => $reprovadoPor,
            'observacoes_aprovacao' => $observacoes
        ]);
    }

    public function suspender($suspensoPor, $observacoes)
    {
        $this->update([
            'status' => 'suspenso',
            'aprovado_por' => $suspensoPor,
            'observacoes_aprovacao' => $observacoes,
            'disponivel' => false
        ]);
    }

    public function ativar()
    {
        if ($this->isAprovado()) {
            $this->update(['status' => 'ativo']);
        }
    }

    public function desativar()
    {
        $this->update([
            'status' => 'inativo',
            'disponivel' => false
        ]);
    }
}