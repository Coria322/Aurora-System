<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearCaches extends Command
{
    protected $signature = 'optimize:clear-all';
    protected $description = 'Limpia todas las cachés del sistema (config, route, view, cache, compiled)';

    public function handle()
    {
        $this->info('🧹 Limpiando todas las cachés...');

        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('optimize:clear');

        $this->info('✅ Todas las cachés fueron limpiadas correctamente.');
        return 0;
    }
}
