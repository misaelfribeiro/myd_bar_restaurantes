<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Delivery;
use Carbon\Carbon;

class NotificarEntregadoresDisponiveis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deliveries:notificar-entregadores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica entregadores disponíveis sobre entregas na plataforma aguardando aceite';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Buscando entregas disponíveis...');
        
        // Busca entregas disponíveis na plataforma sem entregador
        $deliveries = Delivery::where('disponivel_plataforma', true)
            ->whereNull('entregador_id')
            ->whereIn('status', ['confirmado', 'preparando', 'pronto'])
            ->get();
        
        if ($deliveries->isEmpty()) {
            $this->info('Nenhuma entrega aguardando entregador.');
            return 0;
        }
        
        $this->info('Encontradas ' . $deliveries->count() . ' entregas');
        
        foreach ($deliveries as $delivery) {
            // Notifica apenas se:
            // - Nunca foi notificado OU
            // - Última notificação foi há mais de 2 minutos
            $podeNotificar = !$delivery->ultima_notificacao_em || 
                           $delivery->ultima_notificacao_em->diffInMinutes(now()) >= 2;
            
            if (!$podeNotificar) {
                $this->line("Delivery #{$delivery->id}: aguardando intervalo de notificação");
                continue;
            }
            
            $this->line("Delivery #{$delivery->id}: notificando entregadores...");
            $resultado = $delivery->notificarEntregadores();
            
            if ($resultado['success']) {
                $this->info("  ✓ " . $resultado['message']);
            } else {
                $this->warn("  ✗ " . $resultado['message']);
            }
        }
        
        $this->info('Processo concluído!');
        return 0;
    }
}
