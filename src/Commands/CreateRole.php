<?php

namespace Larapacks\Authorization\Commands;

use Larapacks\Authorization\Authorization;
use Illuminate\Console\Command;

class CreateRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:role {name}';

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

        $name = $this->argument('name');

        $exists = $model->whereName($name)->first();

        if (!$exists) {
            $model::forceCreate([
                'name' => $name,
                'label' => ucfirst($name),
            ]);

            $this->info('Successfully created role.');
        } else {
            $this->error("A role named {$name} already exists.");
        }
    }
}
