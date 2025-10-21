<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeAll extends Command
{
    protected $signature = 'optimize:all';
    protected $description = 'Genera todas las cachés de configuración, rutas, vistas y optimiza autoload';

    public function handle()
    {
        $this->info('⚙️ Optimizando aplicación y generando cachés...');

        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        Artisan::call('event:cache');
        Artisan::call('optimize');

        $this->info('🚀 Optimización completada con éxito.');
        return 0;
    }
}
