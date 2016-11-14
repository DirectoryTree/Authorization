<?php

namespace Larapacks\Authorization\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Larapacks\Authorization\Authorization;

class CreatePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:permission {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a permission.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = Authorization::permission();

        $name = $this->argument('name');

        $exists = $model->whereName($name)->first();

        if (!$exists) {
            $model::forceCreate([
                'name'  => $name,
                'label' => Str::words($name),
            ]);

            $this->info('Successfully created permission.');
        } else {
            $this->error("A permission named {$name} already exists.");
        }
    }
}
