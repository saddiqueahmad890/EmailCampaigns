<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create admin role
        $adminRole = Role::create(['name' => 'admin']);

         // Create user role
         $userRole = Role::create(['name' => 'user']);

        $adminrole = User::create([
            'name' => 'Atiq Khalil',
            'email' => 'atiq@gmail.com',
            'password' => bcrypt('1234'),
        ]);

        $adminrole->assignRole('admin');

        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('1234'),
        ]);
        $user->assignRole('user');
    }
}
