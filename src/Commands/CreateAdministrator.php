<?php

namespace Larapacks\Authorization\Commands;

use Illuminate\Console\Command;
use Larapacks\Authorization\Authorization;

class CreateAdministrator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:administrator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an administrator role.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = Authorization::role();

        $exists = $model->where(['name' => $model::getAdministratorName()])->first();

        if (!$exists) {
            $name = $model::getAdministratorName();

            $model::forceCreate([
                'name'  => $name,
                'label' => ucfirst($name),
            ]);

            $this->info('Successfully created administrator role.');
        } else {
            $this->error('An administrator role already exists.');
        }
    }
}
