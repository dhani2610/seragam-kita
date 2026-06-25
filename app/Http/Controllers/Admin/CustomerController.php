<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{
    // List all customers
    public function index()
    {
        return view('admin.customers.index');
    }

    // Ajax data for Datatables
    public function data()
    {
        $customers = User::where('role', 'customer')
            ->withCount(['orders' => function($q) {
                $q->where('payment_status', 'Paid');
            }])
            ->get();

        return response()->json([
            'data' => $customers
        ]);
    }

    // Update customer status (AJAX toggle active/inactive)
    public function toggleStatus($id)
    {
        $customer = User::findOrFail($id);
        $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Status customer berhasil diubah.',
            'status' => $customer->status
        ]);
    }

    // Show details for edit modal
    public function show($id)
    {
        $customer = User::findOrFail($id);
        return response()->json($customer);
    }

    // Update customer details (AJAX)
    public function update(Request $request, $id)
    {
        $customer = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required',
            'address' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data customer berhasil diperbarui.'
        ]);
    }

    // Delete customer
    public function destroy($id)
    {
        $customer = User::findOrFail($id);
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil dihapus.'
        ]);
    }
}
