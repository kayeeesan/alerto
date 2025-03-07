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
            [ 'name' => 'User', 'slug' => 'user' ],
            [ 'name' => 'Administrator', 'slug' => 'administrator' ],
            [ 'name' => 'Quality Management Representative', 'slug' => 'quality-management-representative' ],
            [ 'name' => 'Document Management Team', 'slug' => 'document-management-team' ],
            [ 'name' => 'Project Staff', 'slug' => 'project-staff' ],
        ];

        Role::insert($roles);
    }
}
