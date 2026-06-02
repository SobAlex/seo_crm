<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Убедимся, что роль super_admin существует
        $role = Role::firstOrCreate(['name' => 'super_admin']);

        User::updateOrCreate(
            ['email' => 'admin@crm.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@crm.com',
                'password' => bcrypt('123123123'),
                'team_id' => null,
                'role' => 'super_admin',
            ]
        )->assignRole('super_admin');
    }
}
