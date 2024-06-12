<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Image;
use App\Models\Post;

class TenantPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting to seed TenantPostSeeder data...');

        Post::factory(10)
            ->has(Image::factory()->count(5))
            ->create();

        $this->command->info('Seeding completed for TenantPostSeeder data.');
    }
}
