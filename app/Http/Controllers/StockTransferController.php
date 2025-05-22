<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockAlert;

class StockTransferController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id', // Ensure the product exists
            'shop_id' => 'required|exists:shops,id', // Ensure the source shop exists
            'to_shop_id' => 'required|exists:shops,id', // Ensure the destination shop exists
            'quantity' => 'required|integer|min:1', // Ensure the quantity is a valid positive integer
            'cost_price' => 'required|numeric|min:0', // Ensure cost price is a valid number
            'selling_price' => 'required|numeric|min:0', // Ensure selling price is a valid number
        ]);

        // Fetch the product from the source shop
        $product = Product::where('id', $request->product_id)
                          ->where('shop_id', $request->shop_id)
                          ->first();

        // Ensure the product exists and has enough stock
        if (!$product || $product->stock_quantity < $request->quantity) {
            return back()->withErrors(['stock' => 'Not enough stock in source shop']);
        }

        // Save the stock transfer data to the database
        $stockTransfer = StockTransfer::create([
            'product_id' => $request->product_id,
            'shop_id' => $request->shop_id,
            'to_shop_id' => $request->to_shop_id,
            'quantity' => $request->quantity,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
        ]);

        // Deduct stock from source shop's product
        $product->stock_quantity -= $request->quantity;
        $product->save();

        // Check if stock is low in the source shop and notify admins
        if ($product->stock_quantity <= $product->stock_limit) {
            $admins = User::whereIn('role', ['admin', 'manager'])->get();
            Notification::send($admins, new LowStockAlert($product));
        }

        // Fetch the product from the destination shop
        $destProduct = Product::where('id', $request->product_id)
                              ->where('shop_id', $request->to_shop_id)
                              ->first();

        // If the product exists in the destination shop, add stock
        if ($destProduct) {
            $destProduct->stock_quantity += $request->quantity;
            $destProduct->save();
        } else {
            // If the product doesn't exist in the destination shop, create a new product entry
            Product::create([
                'name' => $product->name,
                'category_id' => $product->category_id,
                'cost_price' => $product->cost_price,
                'price' => $product->price,
                'shop_id' => $request->to_shop_id,
                'stock_quantity' => $request->quantity,
                'stock_limit' => $product->stock_limit,
            ]);
        }

        // Redirect with success message
        return redirect()->back()->with('success', 'Stock transfer completed successfully!');
    }

    public function create()
    {
        // Fetch the data you need for the view
        $shops = Shop::all(); // Fetch all shops
        $products = Product::all(); // Fetch all products
        $categories = Category::all(); // Fetch all categories

        // Return the view with the data
        return view('stock-transfers.create', compact('shops', 'products', 'categories'));
    }
}
