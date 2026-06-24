<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerPhone;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $customers = Customer::with(['phones', 'addresses'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('phones', fn($p) => $p->where('phone', 'like', "%{$search}%"))
                  ->orWhereHas('addresses', fn($a) => $a->where('address', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'phones'      => 'required|array|min:1',
            'phones.*'    => 'required|string|max:30',
            'addresses'   => 'nullable|array',
            'addresses.*' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create(['name' => $validated['name']]);

        foreach ($validated['phones'] as $i => $phone) {
            if (trim($phone) === '') continue;
            CustomerPhone::create([
                'customer_id' => $customer->id,
                'phone'       => trim($phone),
                'is_primary'  => $i === 0,
            ]);
        }

        foreach ($validated['addresses'] ?? [] as $i => $address) {
            if (trim($address) === '') continue;
            CustomerAddress::create([
                'customer_id' => $customer->id,
                'address'     => trim($address),
                'is_primary'  => $i === 0,
            ]);
        }

        return back()->with('success', "Customer \"{$customer->name}\" added.");
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'phones'      => 'required|array|min:1',
            'phones.*'    => 'required|string|max:30',
            'addresses'   => 'nullable|array',
            'addresses.*' => 'nullable|string|max:500',
        ]);

        $customer->update(['name' => $validated['name']]);

        // Replace phones
        $customer->phones()->delete();
        foreach ($validated['phones'] as $i => $phone) {
            if (trim($phone) === '') continue;
            CustomerPhone::create([
                'customer_id' => $customer->id,
                'phone'       => trim($phone),
                'is_primary'  => $i === 0,
            ]);
        }

        // Replace addresses
        $customer->addresses()->delete();
        foreach ($validated['addresses'] ?? [] as $i => $address) {
            if (trim($address) === '') continue;
            CustomerAddress::create([
                'customer_id' => $customer->id,
                'address'     => trim($address),
                'is_primary'  => $i === 0,
            ]);
        }

        return back()->with('success', "Customer \"{$customer->name}\" updated.");
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return back()->with('success', "Customer \"{$customer->name}\" removed.");
    }
}
