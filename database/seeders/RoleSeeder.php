<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [ 'name' => 'Administrator', 'slug' => 'administrator' ],
            [ 'name' => 'Project Staff', 'slug' => 'project-staff' ],
        ];

        Role::insert($roles);
    }
}
