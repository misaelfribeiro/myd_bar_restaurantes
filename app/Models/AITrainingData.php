<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AITrainingData extends Model
{
    protected $table = 'ai_training_data';

    protected $fillable = [
        'user_id',
        'input',
        'expected_output',
        'actual_output',
        'intent',
        'context',
        'confidence',
        'correct',
        'feedback_score',
        'used_for_training',
        'trained_at'
    ];

    protected $casts = [
        'context' => 'array',
        'confidence' => 'float',
        'correct' => 'boolean',
        'feedback_score' => 'integer',
        'used_for_training' => 'boolean',
        'trained_at' => 'datetime',
    ];

    /**
     * Usuário que gerou o dado
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Marca como usado para treinamento
     */
    public function markAsTrained()
    {
        $this->used_for_training = true;
        $this->trained_at = now();
        $this->save();
    }
}
