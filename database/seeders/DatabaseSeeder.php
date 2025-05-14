<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(MaintenanceCategoriesSeeder::class);
        $this->call(HumanPersonaSeeder::class);
        $this->call(ForumDiscussionSeeder::class);
        $this->call(ContentSourceSeeder::class);

    }
}
