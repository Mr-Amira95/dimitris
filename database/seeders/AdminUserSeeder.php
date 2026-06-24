<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        User::updateOrCreate(
            ['username' => 'Qutibah'],
            [
                'full_name' => 'Qutibah Alhamdan',
                'email'     => 'admin@dimitriscoffee.com',
                'password'  => Hash::make('Admin@1234'),
                'role_id'   => $adminRole?->id,
                'is_active' => true,
            ]
        );
    }
}
