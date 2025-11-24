<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AISynapse extends Model
{
    protected $table = 'ai_synapses';
    
    protected $fillable = [
        'from_neuron_id',
        'to_neuron_id',
        'weight',
        'delta',
        'updates'
    ];

    protected $casts = [
        'weight' => 'float',
        'delta' => 'float',
        'updates' => 'integer',
    ];

    /**
     * Neurônio de origem
     */
    public function fromNeuron()
    {
        return $this->belongsTo(AINeuron::class, 'from_neuron_id');
    }

    /**
     * Neurônio de destino
     */
    public function toNeuron()
    {
        return $this->belongsTo(AINeuron::class, 'to_neuron_id');
    }

    /**
     * Atualiza o peso da sinapse com aprendizado incremental
     */
    public function updateWeight($delta, $learningRate = 0.01)
    {
        $this->weight += $learningRate * $delta;
        $this->delta = $delta;
        $this->updates++;
        $this->save();
    }
}
