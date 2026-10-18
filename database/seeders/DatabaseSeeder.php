<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use Modules\Grade\Database\Seeders\GradeDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;
use Modules\Common\Database\Seeders\CommonDatabaseSeeder;
use Modules\Subject\Database\Seeders\SubjectDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserDatabaseSeeder::class,
            CommonDatabaseSeeder::class,
            // GradeDatabaseSeeder::class,
            // SubjectDatabaseSeeder::class,
        ]);
    }
}
