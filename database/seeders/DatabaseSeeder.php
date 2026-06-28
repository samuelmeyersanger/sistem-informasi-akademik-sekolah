<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Perbaikan: Arahkan ke namespace Seeds\DatabaseSeeder
        //$this->call(\Laravolt\Indonesia\Seeds\DatabaseSeeder::class);
        $this->call(RoleAndPermissionSeeder::class);
        //$this->call(MenuSeeder::class);
    }
}
