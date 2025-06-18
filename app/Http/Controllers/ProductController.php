<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Shop;
use App\Models\Notification;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // Fetch all products (with category data)
    public function index(Request $request)
    {
        $query = Product::with('category');
    
        // If user is typing in search box
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
    
        // Pagination — show 10 products per page
        $products = $query->paginate(10);
    
        // AJAX response: just send the table fragment
        if ($request->ajax()) {
            return view('products.partials.table', compact('products'))->render();
        }
    
        // Normal view load
        $categories = Category::all(); // if you still need it on the page
        return view('products.index', compact('products', 'categories'));
    }
    

    // Store a new product (admin only)
//     public function store(Request $request)
// {
//     if (Auth::user()->role !== 'admin') {
//         abort(403, 'Unauthorized action.');
//     }

//     $request->validate([
//         'category_id' => 'required|exists:categories,id',
//         'name' => 'required|string|max:255',
//         'price' => 'required|numeric|min:0',
//         'cost_price' => 'required|numeric|min:0',
//         'stock_quantity' => 'required|integer|min:0',
//     ]);

//     $product = Product::create($request->all());

//     // Notify if stock is low
//     $this->checkStockNotification($product);

//     session()->flash('success', 'Product stocked successfully!');
//     return redirect()->route('products.create');
// }

// public function store(Request $request)
// {
//     $user = Auth::user();

//     // Authorization logic
//     if ($user->role === 'manager') {
//         $hasPermission = \App\Models\ProductPermission::where('manager_id', $user->id)->exists();

//         if (!$hasPermission) {
//             abort(403, 'You are not allowed to add products.');
//         }
//     } elseif ($user->role !== 'admin') {
//         abort(403, 'Unauthorized action.');
//     }

//     // Validation
//     $request->validate([
//         'category_id' => 'required|exists:categories,id',
//         'shop_id' => 'required|exists:shops,id', // 💥 Add this line
//         'name' => 'required|string|max:255',
//         'price' => 'required|numeric|min:0',
//         'cost_price' => 'required|numeric|min:0',
//         'stock_quantity' => 'required|integer|min:0',
//     ]);

//     // Product creation
//     $product = Product::create($request->all());

//     // Check and notify if stock is low
//     $this->checkStockNotification($product);

//     session()->flash('success', 'Product stocked successfully!');
//     return redirect()->route('products.create');
// }


public function store(Request $request)
{
    $user = Auth::user();

    // Authorization logic
    if ($user->role === 'manager') {
        $hasPermission = \App\Models\ProductPermission::where('manager_id', $user->id)->exists();

        if (!$hasPermission) {
            abort(403, 'You are not allowed to add products.');
        }
    } elseif ($user->role !== 'admin') {
        abort(403, 'Unauthorized action.');
    }

    // Validation
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'shop_id' => 'required|exists:shops,id',
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'cost_price' => 'required|numeric|min:0',
        'stock_quantity' => 'required|integer|min:0',
    ]);

    // Check if product already exists in the same shop & category
    $existingProduct = Product::where('name', $request->name)
        ->where('shop_id', $request->shop_id)
        ->where('category_id', $request->category_id)
        ->first();

    if ($existingProduct) {
        session()->flash('error', 'Product already exists in this shop and category.');
        return redirect()->back()->withInput();
    }

    // Safe creation
    $data = $request->only(['category_id', 'shop_id', 'name', 'price', 'cost_price', 'stock_quantity']);
    $product = Product::create($data);

    // Stock check
    $this->checkStockNotification($product);

    session()->flash('success', 'Product stocked successfully!');
    return redirect()->route('products.create');
}



    // Show details of a specific product
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }

    // Update an existing product (admin only)
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'shop_id' => 'required|exists:shops,id', // 💥 Add this line
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        // Prevent stock quantity from going below zero
        if ($request->stock_quantity < 0) {
            return response()->json(['error' => 'Stock quantity cannot be negative'], 422);
        }

        $product->update($request->all());

        // Notify admin if stock quantity is at or below reorder level after update
        $this->checkStockNotification($product);

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }

    // Delete a product (admin only)
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    // // 🔍 Search products by name for autocomplete feature
    // public function searchSuggestions(Request $request)
    // {
    //     $query = strtolower($request->input('query'));
    
    //     $products = Product::whereRaw('LOWER(name) LIKE ?', ["%{$query}%"])
    //         ->limit(10) // ✅ Limit to top 10 results
    //         ->get(['id', 'name', 'price']);
    
    //     return response()->json($products);
    // }

    public function searchSuggestions(Request $request)
{
    $query = strtolower($request->input('query'));

    // Get the shop_id assigned to the cashier (you might get it from the authenticated user)
    $shopId = auth()->user()->shop_id;  // Assuming the shop_id is stored in the user model

    $products = Product::where('shop_id', $shopId)  // Filter by the cashier's shop_id
        ->whereRaw('LOWER(name) LIKE ?', ["%{$query}%"]) // Search by product name
        ->limit(10)  // Limit to top 10 results
        ->get(['id', 'name', 'price']);

    return response()->json($products);
}


    // ✅ Private function to check and send low stock notifications
    private function checkStockNotification(Product $product)
    {
        if ($product->stock_quantity <= $product->reorder_level) {
            $admin = User::whereIn('role', ['admin', 'manager'])->first();

            if ($admin) {
                $admin->notify(new LowStockAlert($product));
            }
        }
    }

    // Create a product (admin only)
    public function create(Request $request)
{
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Unauthorized action.');
    }

    $search = $request->input('search');
    $categories = Category::all();
    $shops = Shop::all(); // 🛒 get all shops

    $products = Product::with('category')
        ->when($search, function ($query, $search) {
            $query->where('name', 'like', "%$search%");
        })
        ->paginate(10);

    if ($request->ajax()) {
        return view('products.partials.table', compact('products'))->render();
    }

    return view('products.create', compact('categories', 'products', 'shops'));
}

    

    // Fetch products by category (for AJAX suggestions)
    public function getByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get(['name']);
        return response()->json($products);
    }




    // Update stock quantity and notify admin if stock is low
    private function updateStockAndNotify(Product $product, $quantitySold)
    {
        // Check if stock is below the stock limit
        if ($product->stock_quantity <= $product->stock_limit && $product->stock_limit > 0) {
            // Send notification to admin if stock is below the limit
            $admin = User::where('role', ['admin', 'manager'])->first();
            if ($admin) {
                $admin->notify(new LowStockAlert($product)); // Send LowStockAlert notification
            }
        }
    
        // Optional: If stock is zero, delete the product (you can change this logic if needed)
        if ($product->stock_quantity <= 0) {
            $product->delete();
        }
    }
    


    // Example use case when a sale happens in your controller:
    // Sale processing method
public function sellProduct(Request $request, $productId)
{
    // Validate the request
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    // Find the product
    $product = Product::findOrFail($productId);
    $quantitySold = $request->input('quantity');

    // Check if there's enough stock
    if ($product->stock_quantity < $quantitySold) {
        return redirect()->back()->with('error', 'Not enough stock available.');
    }

    // Reduce stock quantity after sale
    $product->stock_quantity -= $quantitySold;
    $product->save();

    // Call the function to update stock and check for low stock
    $this->updateStockAndNotify($product, $quantitySold);

    // Create the SaleItem (optional, based on your sale tracking)
    // SaleItem::create([
    //     'product_id' => $product->id,
    //     'quantity' => $quantitySold,
    //     'total_price' => $product->price * $quantitySold,
    //     'user_id' => Auth::id(),
    // ]);

    // Return a success message
    return redirect()->back()->with('success', 'Sale processed successfully!');
}

public function liveSearch(Request $request)
{
    $query = $request->input('query');

    $products = Product::with('category')
        ->where('name', 'like', "%$query%")
        ->paginate(10);

    return view('products.partials.table', compact('products'))->render(); // return partial
}
}