<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'        => 'Admin',
                'slug'        => 'admin',
                'permissions' => array_keys(Role::PERMISSIONS),
                'is_system'   => true,
            ],
            [
                'name'        => 'Management',
                'slug'        => 'management',
                'permissions' => ['kanban', 'archive', 'reports', 'products_view', 'orders_advance', 'orders_cancel', 'orders_edit_full', 'view_delivery_cost'],
                'is_system'   => true,
            ],
            [
                'name'        => 'Sales',
                'slug'        => 'sales',
                'permissions' => ['kanban', 'customers_view', 'customers_manage', 'orders_advance', 'orders_edit_full'],
                'is_system'   => true,
            ],
            [
                'name'        => 'Production',
                'slug'        => 'production',
                'permissions' => ['kanban', 'orders_advance', 'orders_edit_items'],
                'is_system'   => true,
            ],
            [
                'name'        => 'Fleet Supervisor',
                'slug'        => 'fleet',
                'permissions' => ['kanban', 'orders_advance', 'assign_driver', 'view_delivery_cost'],
                'is_system'   => true,
            ],
            [
                'name'        => 'Driver',
                'slug'        => 'driver',
                'permissions' => ['kanban'],
                'is_system'   => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
