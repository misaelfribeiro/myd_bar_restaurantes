<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIContext extends Model
{
    protected $table = 'ai_contexts';
    
    protected $fillable = [
        'category',
        'key',
        'pattern',
        'response_template',
        'parameters',
        'action',
        'usage_count',
        'success_rate',
        'confidence_threshold',
        'active',
        'requires_context'
    ];

    protected $casts = [
        'parameters' => 'array',
        'usage_count' => 'integer',
        'success_rate' => 'float',
        'confidence_threshold' => 'float',
        'active' => 'boolean',
        'requires_context' => 'boolean',
    ];

    /**
     * Verifica se o input combina com o padrão
     */
    public function matches($input)
    {
        $pattern = $this->pattern;
        
        // Substitui * por .* (qualquer caractere)
        $pattern = str_replace('*', '.*', $pattern);
        
        // Se não tem regex especial, escapa caracteres especiais
        // Mas mantém ( ) | que são regex válidos
        if (!preg_match('/[\(\)\|]/', $pattern)) {
            $pattern = preg_quote($pattern, '/');
            $pattern = str_replace('\.\*', '.*', $pattern); // Mantém .*
        }
        
        return @preg_match('/' . $pattern . '/i', $input) === 1;
    }

    /**
     * Extrai parâmetros do input baseado no padrão
     */
    public function extractParameters($input)
    {
        $params = [];
        
        // Extrai entidades nomeadas do padrão
        preg_match_all('/{(\w+)}/', $this->pattern, $matches);
        
        if (!empty($matches[1])) {
            $pattern = preg_quote($this->pattern, '/');
            foreach ($matches[1] as $param) {
                $pattern = str_replace('\{' . $param . '\}', '(?P<' . $param . '>[^\s]+)', $pattern);
            }
            
            if (preg_match('/' . $pattern . '/i', $input, $values)) {
                foreach ($matches[1] as $param) {
                    if (isset($values[$param])) {
                        $params[$param] = $values[$param];
                    }
                }
            }
        }
        
        return $params;
    }

    /**
     * Registra uso do contexto
     */
    public function recordUsage($success = true)
    {
        $this->usage_count++;
        
        // Atualiza taxa de sucesso com média móvel
        $this->success_rate = ($this->success_rate * ($this->usage_count - 1) + ($success ? 1 : 0)) / $this->usage_count;
        
        $this->save();
    }

    /**
     * Gera resposta baseada no template
     */
    public function generateResponse($params = [])
    {
        $response = $this->response_template;
        
        foreach ($params as $key => $value) {
            $response = str_replace('{' . $key . '}', $value, $response);
        }
        
        return $response;
    }
}
