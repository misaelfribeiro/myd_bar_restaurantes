<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AIConversationSession extends Model
{
    protected $table = 'ai_conversation_sessions';
    
    protected $fillable = [
        'user_id',
        'session_token',
        'context_stack',
        'entities',
        'last_intent',
        'message_count',
        'last_activity',
        'expires_at'
    ];

    protected $casts = [
        'context_stack' => 'array',
        'entities' => 'array',
        'message_count' => 'integer',
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Usuário da sessão
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cria nova sessão
     */
    public static function createSession($userId = null)
    {
        return self::create([
            'user_id' => $userId,
            'session_token' => Str::random(64),
            'context_stack' => [],
            'entities' => [],
            'last_activity' => now(),
            'expires_at' => now()->addHours(2),
        ]);
    }

    /**
     * Atualiza atividade da sessão
     */
    public function touchActivity()
    {
        $this->last_activity = now();
        $this->expires_at = now()->addHours(2);
        $this->message_count++;
        // Não salvar aqui - será salvo pelo caller
    }

    /**
     * Adiciona contexto à pilha
     */
    public function pushContext($context)
    {
        $stack = $this->context_stack ?? [];
        $stack[] = $context;
        $this->context_stack = array_slice($stack, -5); // mantém últimos 5
        // Não salvar aqui - será salvo pelo caller
    }

    /**
     * Adiciona entidade extraída
     */
    public function addEntity($type, $value)
    {
        $entities = $this->entities ?? [];
        $entities[$type] = $value;
        $this->entities = $entities;
        $this->save();
    }

    /**
     * Verifica se sessão expirou
     */
    public function isExpired()
    {
        return $this->expires_at < now();
    }

    /**
     * Limpa sessões expiradas
     */
    public static function cleanExpiredSessions()
    {
        return self::where('expires_at', '<', now())->delete();
    }
}
