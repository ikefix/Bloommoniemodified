<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();

        if (auth()->user()->role === 'admin') {
            return view('admin.invoices.create', compact('customers', 'products'));
        } else {
            return view('manager.invoices.create', compact('customers', 'products'));
        }
    }


    public function store(Request $request)
    {
        $invoice = Invoice::create([
            'customer_id' => $request->customer_id,
            'subtotal' => $request->subtotal,
            'discount' => $request->discount ?? 0,
            'total' => $request->total,
            'amount_paid' => $request->amount_paid,
            'balance' => $request->balance,
            'notes' => $request->notes,
        ]);

        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'total' => $item['total'],
            ]);
        }

        return back()->with('success', 'Invoice created successfully!');
    }
}
