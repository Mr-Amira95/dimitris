<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['username' => 'sarah_sales',   'full_name' => 'Sarah Al-Ahmad',  'role' => 'sales',      'password' => 'Pass@123'],
            ['username' => 'omar_prod',     'full_name' => 'Omar Khaled',     'role' => 'production', 'password' => 'Pass@123'],
            ['username' => 'faris_fleet',   'full_name' => 'Faris Al-Najjar', 'role' => 'fleet',      'password' => 'Pass@123'],
            ['username' => 'ali_driver',    'full_name' => 'Ali Hassan',      'role' => 'driver',     'password' => 'Pass@123'],
            ['username' => 'rami_driver',   'full_name' => 'Rami Khalil',     'role' => 'driver',     'password' => 'Pass@123'],
            ['username' => 'khaled_driver', 'full_name' => 'Khaled Mansour',  'role' => 'driver',     'password' => 'Pass@123'],
            ['username' => 'nour_mgmt',     'full_name' => 'Nour Al-Khalidi', 'role' => 'management', 'password' => 'Pass@123'],
        ];

        foreach ($users as $data) {
            $role = Role::where('slug', $data['role'])->firstOrFail();

            User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'full_name' => $data['full_name'],
                    'email'     => $data['username'] . '@dimitriscoffee.com',
                    'password'  => Hash::make($data['password']),
                    'role_id'   => $role->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
