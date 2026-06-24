<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerPhone;
use Illuminate\Database\Seeder;

class DemoCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name'      => 'Al-Rashid Supermarket',
                'phones'    => ['+962799001122'],
                'addresses' => ['Shmeisani, Amman'],
            ],
            [
                'name'      => 'Blue Mountain Café',
                'phones'    => ['+962797334455', '+962790001111'],
                'addresses' => ['Rainbow Street, Amman', 'Sweifieh branch, Amman'],
            ],
            [
                'name'      => 'Mecca Street Hotels Group',
                'phones'    => ['+962796556677'],
                'addresses' => ['Mecca Street, Amman', 'Airport Road, Amman'],
            ],
            [
                'name'      => 'Gardens Restaurant',
                'phones'    => ['+962795778899'],
                'addresses' => ['Al-Gardens, Amman'],
            ],
            [
                'name'      => 'Jabal Amman Coffee',
                'phones'    => ['+962798990011'],
                'addresses' => ['Paris Circle, Amman'],
            ],
        ];

        foreach ($customers as $data) {
            $customer = Customer::firstOrCreate(
                ['name' => $data['name']],
                ['is_active' => true]
            );

            foreach ($data['phones'] as $i => $phone) {
                CustomerPhone::firstOrCreate(
                    ['customer_id' => $customer->id, 'phone' => $phone],
                    ['is_primary' => $i === 0]
                );
            }

            foreach ($data['addresses'] as $i => $address) {
                CustomerAddress::firstOrCreate(
                    ['customer_id' => $customer->id, 'address' => $address],
                    ['is_primary' => $i === 0]
                );
            }
        }
    }
}
