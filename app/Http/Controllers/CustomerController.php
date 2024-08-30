<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        Customer::create($request->all());

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->name,
            'password' => bcrypt('password'),
            'id_cust'=> Customer::latest()->first()->id,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        $customer->update($request->all());

        User::where('id_cust', $customer->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->name,
        ]);

        return redirect()->route('customers.index')
                        ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
                        ->with('success', 'Customer deleted successfully.');
    }
}
