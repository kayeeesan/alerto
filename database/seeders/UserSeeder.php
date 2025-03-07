<?php

namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->username = 'superadmin@example.com';
        $user->first_name = 'Super';
        $user->last_name = 'Admin';
        $user->password = Hash::make('superadmin');
        $user->save();
        $user->roles()->sync([1]);
    }
}
