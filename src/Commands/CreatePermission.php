<?php

namespace Larapacks\Authorization\Commands;

use Illuminate\Console\Command;
use Larapacks\Authorization\Authorization;

class CreatePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:permission {label}';

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

        $label = $this->argument('label');

        $name = str_slug($label);

        $exists = $model->whereName($name)->first();

        if (!$exists) {
            $model::forceCreate([
                'name'  => $name,
                'label' => $label,
            ]);

            $this->info("Successfully created permission: {$name}.");
        } else {
            $this->error("A permission named {$name} already exists.");
        }
    }
}
