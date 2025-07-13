<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {name}';
    protected $description = 'Cria um Repository em app/Repositories';

    public function handle()
    {
        $name = $this->argument('name');
        $className = str($name)->replace(['Repository.php', '.php'], '')->finish('Repository')->ucfirst();
        $path = app_path("Repositories/{$className}.php");

        if (File::exists($path)) {
            $this->error("❌ Repository {$className} já existe.");
            return Command::FAILURE;
        }

        File::ensureDirectoryExists(app_path('Repositories'));

        File::put($path, <<<PHP
<?php

namespace App\Repositories;

class {$className}
{
    //
}
PHP);

        $this->info("✅ Repository {$className} criado com sucesso em app/Repositories.");
        return Command::SUCCESS;
    }
}
