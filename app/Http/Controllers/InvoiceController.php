<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function create()
    {
        $customers = Customer::all();
        $shops = Shop::all(); // 👈 Add this so the view has $shops
        $products = Product::where('shop_id', Auth::user()->shop_id)->get();

        if (Auth::user()->role === 'admin') {
            return view('admin.invoices.create', compact('customers', 'products', 'shops'));
        } elseif (Auth::user()->role === 'manager') {
            return view('manager.invoices.create', compact('customers', 'products', 'shops'));
        } else {
            return view('cashier.invoices.create', compact('customers', 'products', 'shops'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'goods' => 'required|array',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total' => 'required|numeric',
        ]);

        Invoice::create([
            'customer_id' => $request->customer_id,
            'user_id' => Auth::id(),
            'shop_id' => Auth::user()->shop_id,
            'invoice_number' => 'INV-' . time(),
            'invoice_date' => now(),
            'goods' => $request->goods,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'total' => $request->total,
        ]);

        return redirect()->back()->with('success', 'Invoice created successfully!');
    }
}

