<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Cria um Service em app/Services';

    public function handle()
    {
        $name = $this->argument('name');
        $className = str($name)->replace(['Service.php', '.php'], '')->finish('Service')->ucfirst();
        $path = app_path("Services/{$className}.php");

        if (File::exists($path)) {
            $this->error("❌ Service {$className} já existe.");
            return Command::FAILURE;
        }

        File::ensureDirectoryExists(app_path('Services'));

        File::put($path, <<<PHP
<?php

namespace App\Services;

class {$className}
{
    //
}
PHP);

        $this->info("✅ Service {$className} criado com sucesso em app/Services.");
        return Command::SUCCESS;
    }
}
