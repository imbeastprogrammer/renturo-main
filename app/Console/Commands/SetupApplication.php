<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;

class SetupApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:application';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup application with Passport clients and seed data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Setting up Passport clients...');
        $this->call('passport:install', ['--force' => true]);  // Use 'passport:install' to ensure clients are created

        $this->info('Seeding the database...');
        Artisan::call('db:seed'); // Running the database seeder

        $this->info('Application setup complete.');
    }
}
