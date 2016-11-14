<?php

namespace Larapacks\Authorization\Commands;

use Illuminate\Console\Command;
use Larapacks\Authorization\Authorization;

class CreateRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:role {label}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a role.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = Authorization::role();

        $label = $this->argument('label');

        $name = str_slug($label);

        $exists = $model->whereName($name)->first();

        if (!$exists) {
            $model::forceCreate([
                'name'  => $name,
                'label' => $label,
            ]);

            $this->info("Successfully created role: {$name}.");
        } else {
            $this->error("A role named {$label} already exists.");
        }
    }
}
