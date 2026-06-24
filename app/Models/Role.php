<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'permissions', 'is_system'];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];

    public const PERMISSIONS = [
        'kanban'              => 'View Kanban Board',
        'archive'             => 'View Archive',
        'reports'             => 'View Reports',
        'products_view'       => 'View Products Catalog',
        'products_manage'     => 'Manage Products Catalog',
        'customers_view'      => 'View Customers',
        'customers_manage'    => 'Manage Customers',
        'orders_advance'      => 'Advance Orders',
        'orders_cancel'       => 'Cancel Orders',
        'orders_edit_full'    => 'Full Order Edit',
        'orders_edit_items'   => 'Edit Order Items',
        'assign_driver'       => 'Assign Driver',
        'view_delivery_cost'  => 'View Delivery Cost',
        'users_manage'        => 'Manage Users',
        'settings_manage'     => 'Manage Settings',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }
}
