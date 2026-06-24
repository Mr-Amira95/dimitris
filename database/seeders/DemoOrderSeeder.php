<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Filling;
use App\Models\Grind;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DemoOrderSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── Users ────────────────────────────────────────────────────────────────
        $sarah = User::where('username', 'sarah_sales')->firstOrFail();
        $omar  = User::where('username', 'omar_prod')->firstOrFail();
        $faris = User::where('username', 'faris_fleet')->firstOrFail();
        $ali   = User::where('username', 'ali_driver')->firstOrFail();
        $rami  = User::where('username', 'rami_driver')->firstOrFail();

        // ── Catalog helpers ───────────────────────────────────────────────────────
        $prod = fn(string $n) => Product::where('name', $n)->firstOrFail();
        $fill = fn(string $n) => Filling::where('name', $n)->firstOrFail();
        $grnd = fn(string $n) => Grind::where('name', $n)->firstOrFail();

        // ── Customer / phone / address helpers ───────────────────────────────────
        $cust    = fn(string $n) => Customer::where('name', $n)->firstOrFail();
        $phoneId = fn(Customer $c, string $ph) => $c->phones()->where('phone', $ph)->value('id');
        $addrId  = fn(Customer $c, string $a)  => $c->addresses()->where('address', $a)->value('id');

        $today        = $now->toDateString();
        $dispatchedAt = $now->copy()->subMinutes(30);

        // ── Order definitions ─────────────────────────────────────────────────────
        //
        // Each entry has:
        //   'meta'  => Order fields
        //   'items' => [[Product, Filling, Grind, qty], ...]
        //   'logs'  => [[User, action, from_status, to_status], ...]  (oldest → newest)
        //
        $orders = [

            // Order 1 — New
            [
                'meta' => [
                    'customer'                 => 'Al-Rashid Supermarket',
                    'phone'                    => '+962799001122',
                    'address'                  => 'Shmeisani, Amman',
                    'order_date'               => $today,
                    'preferred_delivery_date'  => $today,
                    'preferred_delivery_time'  => '13:00:00',
                    'internal_notes'           => null,
                    'status'                   => 'new',
                    'created_by_user'          => $sarah,
                    'driver'                   => null,
                    'outsourced_driver_name'   => null,
                    'outsourced_delivery_cost' => null,
                    'dispatched_at'            => null,
                ],
                'items' => [
                    [$prod('Ethiopian Yirgacheffe'), $fill('1 kg'),  $grnd('حب'),    5],
                    [$prod('Colombia Huila'),        $fill('250 g'), $grnd('مطحون'), 3],
                ],
                'logs' => [
                    [$sarah, 'Order created', null, 'new'],
                ],
            ],

            // Order 2 — Preparation & Packing
            [
                'meta' => [
                    'customer'                 => 'Blue Mountain Café',
                    'phone'                    => '+962797334455',
                    'address'                  => 'Rainbow Street, Amman',
                    'order_date'               => $today,
                    'preferred_delivery_date'  => $today,
                    'preferred_delivery_time'  => '09:30:00',
                    'internal_notes'           => null,
                    'status'                   => 'packing',
                    'created_by_user'          => $sarah,
                    'driver'                   => null,
                    'outsourced_driver_name'   => null,
                    'outsourced_delivery_cost' => null,
                    'dispatched_at'            => null,
                ],
                'items' => [
                    [$prod('Espresso Blend'), $fill('1 kg'), $grnd('حب'), 10],
                ],
                'logs' => [
                    [$sarah, 'Order created',                            null,    'new'],
                    [$omar,  'Stage advanced to: Preparation & Packing', 'new',   'packing'],
                ],
            ],

            // Order 3 — Dispatch (assigned driver)
            [
                'meta' => [
                    'customer'                 => 'Mecca Street Hotels Group',
                    'phone'                    => '+962796556677',
                    'address'                  => 'Mecca Street, Amman',
                    'order_date'               => $today,
                    'preferred_delivery_date'  => $today,
                    'preferred_delivery_time'  => '15:00:00',
                    'internal_notes'           => null,
                    'status'                   => 'dispatch',
                    'created_by_user'          => $sarah,
                    'driver'                   => $ali,
                    'outsourced_driver_name'   => null,
                    'outsourced_delivery_cost' => null,
                    'dispatched_at'            => $dispatchedAt,
                ],
                'items' => [
                    [$prod('House Blend'), $fill('1 kg'),  $grnd('مطحون'), 8],
                    [$prod('Decaf Blend'), $fill('250 g'), $grnd('مطحون'), 4],
                ],
                'logs' => [
                    [$sarah, 'Order created',                            null,      'new'],
                    [$omar,  'Stage advanced to: Preparation & Packing', 'new',     'packing'],
                    [$faris, 'Stage advanced to: Dispatch',              'packing', 'dispatch'],
                ],
            ],

            // Order 4 — Picked Up (outsourced)
            [
                'meta' => [
                    'customer'                 => 'Gardens Restaurant',
                    'phone'                    => '+962795778899',
                    'address'                  => 'Al-Gardens, Amman',
                    'order_date'               => $today,
                    'preferred_delivery_date'  => $today,
                    'preferred_delivery_time'  => '16:00:00',
                    'internal_notes'           => 'Urgent — customer called twice',
                    'status'                   => 'picked_up',
                    'created_by_user'          => $sarah,
                    'driver'                   => null,
                    'outsourced_driver_name'   => 'Fast Delivery Co.',
                    'outsourced_delivery_cost' => 7.500,
                    'dispatched_at'            => $dispatchedAt,
                ],
                'items' => [
                    [$prod('Specialty Single Origin'), $fill('250 g'), $grnd('حب'), 6],
                ],
                'logs' => [
                    [$sarah, 'Order created',                                           null,       'new'],
                    [$faris, 'Stage advanced to: Dispatch (Outsourced: Fast Delivery Co.)', 'new', 'dispatch'],
                    [$faris, 'Stage advanced to: Picked Up',                           'dispatch', 'picked_up'],
                ],
            ],

            // Order 5 — Delivered (assigned driver)
            [
                'meta' => [
                    'customer'                 => 'Jabal Amman Coffee',
                    'phone'                    => '+962798990011',
                    'address'                  => 'Paris Circle, Amman',
                    'order_date'               => $today,
                    'preferred_delivery_date'  => $today,
                    'preferred_delivery_time'  => '09:00:00',
                    'internal_notes'           => null,
                    'status'                   => 'delivered',
                    'created_by_user'          => $sarah,
                    'driver'                   => $rami,
                    'outsourced_driver_name'   => null,
                    'outsourced_delivery_cost' => null,
                    'dispatched_at'            => $dispatchedAt,
                ],
                'items' => [
                    [$prod('Kenya AA'),       $fill('1 kg'),  $grnd('حب'),    3],
                    [$prod('Rwanda Bourbon'), $fill('250 g'), $grnd('مطحون'), 2],
                ],
                'logs' => [
                    [$sarah, 'Order created',                            null,        'new'],
                    [$omar,  'Stage advanced to: Preparation & Packing', 'new',       'packing'],
                    [$faris, 'Stage advanced to: Dispatch',              'packing',   'dispatch'],
                    [$rami,  'Stage advanced to: Picked Up',             'dispatch',  'picked_up'],
                    [$rami,  'Stage advanced to: Delivered',             'picked_up', 'delivered'],
                ],
            ],
        ];

        // ── Insert orders ─────────────────────────────────────────────────────────
        foreach ($orders as $data) {
            $m        = $data['meta'];
            $customer = $cust($m['customer']);

            $order = Order::create([
                'customer_id'              => $customer->id,
                'customer_phone_id'        => $phoneId($customer, $m['phone']),
                'customer_address_id'      => $addrId($customer, $m['address']),
                'order_date'               => $m['order_date'],
                'preferred_delivery_date'  => $m['preferred_delivery_date'],
                'preferred_delivery_time'  => $m['preferred_delivery_time'],
                'internal_notes'           => $m['internal_notes'],
                'status'                   => $m['status'],
                'created_by'               => $m['created_by_user']->id,
                'driver_user_id'           => $m['driver']?->id,
                'outsourced_driver_name'   => $m['outsourced_driver_name'],
                'outsourced_delivery_cost' => $m['outsourced_delivery_cost'],
                'dispatched_at'            => $m['dispatched_at'],
            ]);

            foreach ($data['items'] as [$product, $filling, $grind, $qty]) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'filling_id' => $filling->id,
                    'grind_id'   => $grind->id,
                    'qty'        => $qty,
                ]);
            }

            // Use DB::table to control created_at per log entry.
            // Logs are defined oldest-first; each step is 10 minutes apart.
            $logCount = count($data['logs']);
            foreach ($data['logs'] as $i => [$user, $action, $fromStatus, $toStatus]) {
                $logTime = $now->copy()->subMinutes(($logCount - 1 - $i) * 10);
                DB::table('order_logs')->insert([
                    'order_id'    => $order->id,
                    'user_id'     => $user->id,
                    'action'      => $action,
                    'from_status' => $fromStatus,
                    'to_status'   => $toStatus,
                    'note'        => null,
                    'created_at'  => $logTime,
                    'updated_at'  => $logTime,
                ]);
            }
        }
    }
}
