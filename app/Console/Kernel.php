<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
 protected function schedule(Schedule $schedule)
 {
 // Notifica entregadores sobre entregas disponíveis a cada 2 minutos
 $schedule->command('deliveries:notificar-entregadores')
 ->everyTwoMinutes()
 ->withoutOverlapping();
 }
 protected function commands()
 {
 $this->load(__DIR__.'/Commands');
 require base_path('routes/console.php');
 }
}