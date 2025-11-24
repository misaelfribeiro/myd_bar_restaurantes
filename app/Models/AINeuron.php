<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AINeuron extends Model
{
    protected $table = 'ai_neurons';
    
    protected $fillable = [
        'layer',
        'position',
        'bias',
        'activation',
        'type'
    ];

    protected $casts = [
        'bias' => 'float',
        'activation' => 'float',
    ];

    /**
     * Sinapses de saída (para onde este neurônio envia sinais)
     */
    public function outputSynapses()
    {
        return $this->hasMany(AISynapse::class, 'from_neuron_id');
    }

    /**
     * Sinapses de entrada (de onde este neurônio recebe sinais)
     */
    public function inputSynapses()
    {
        return $this->hasMany(AISynapse::class, 'to_neuron_id');
    }

    /**
     * Função de ativação
     */
    public function activate($input)
    {
        switch ($this->type) {
            case 'sigmoid':
                return 1 / (1 + exp(-$input));
            case 'tanh':
                return tanh($input);
            case 'relu':
            default:
                return max(0, $input);
        }
    }

    /**
     * Derivada da função de ativação (para backpropagation)
     */
    public function activationDerivative($output)
    {
        switch ($this->type) {
            case 'sigmoid':
                return $output * (1 - $output);
            case 'tanh':
                return 1 - pow($output, 2);
            case 'relu':
            default:
                return $output > 0 ? 1 : 0;
        }
    }
}
