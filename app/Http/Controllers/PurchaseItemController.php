<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

use App\Notifications\LowStockAlert;
use App\Models\User;

class PurchaseItemController extends Controller
{
    // Get the cashier page with categories and products
    public function index()
    {
        // Fetch all categories with associated products
        $categories = Category::with('products')->get();

        // Pass categories to the view
        return view('home', compact('categories'));
    }

    // Get products based on the selected category (AJAX)
    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();

        return response()->json($products);
    }

    // Store the purchase item and update stock
    public function store(Request $request)
{
    $validated = $request->validate([
        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
        'payment_method' => 'required|in:cash,card,transfer',
    ]);

    // Generate a transaction ID (timestamp + random suffix to avoid collisions)
    $transactionId = 'TXN-' . now()->format('YmdHis') . '-' . rand(1000, 9999);
    $lastPurchase = null;

    foreach ($validated['products'] as $item) {
        $product = Product::findOrFail($item['product_id']);
        $quantityRequested = $item['quantity'];

        if ($product->stock_quantity < $quantityRequested) {
            return back()->withErrors([
                'stock' => "Not enough stock for {$product->name}. Available: {$product->stock_quantity}"
            ]);
        }

        // Save purchase with transaction ID
        $lastPurchase = PurchaseItem::create([
            'product_id' => $product->id,
            'category_id' => $product->category_id,
            'quantity' => $quantityRequested,
            'total_price' => $product->price * $quantityRequested,
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $transactionId, // 🆕
            'shop_id' => $product->shop_id, // ✅ Here’s the fix
        ]);

        // Update stock
        $product->stock_quantity -= $quantityRequested;
        $product->save();

        // Check if stock is low and notify admins
        if ($product->stock_quantity <= $product->stock_limit) {
            $admins = User::whereIn('role', ['admin', 'manager'])->get();
            Notification::send($admins, new LowStockAlert($product));
        }
    }

    return response()->json([
        'success' => true,
        'receipt_id' => $lastPurchase->id  // Send the purchase item's ID
    ]);
}



    // View all sales with search and date filtering FOR ADMIN
    public function allSales(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date', now()->toDateString()); // 👈 Default to today

        $sales = PurchaseItem::with(['product.category', 'shop'])
            ->when($search, function ($query, $search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();
        

        
            $shops = Shop::all();

        return view('admin.sales', compact('sales', 'search', 'date', 'shops'));
    }


        // View all sales FOR CASHIER
        public function cashiersales(Request $request)
        {
            $search = $request->input('search');
            $date = $request->input('date', now()->toDateString()); // 👈 Default to today
    
            $sales = PurchaseItem::with(['product.category'])
                ->when($search, function ($query, $search) {
                    $query->whereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                })
                ->whereDate('created_at', $date) // 👈 Only today's records
                ->orderBy('created_at', 'desc')
                ->get();
    
            return view('cashier.home-sales', compact('sales', 'search', 'date'));
        }

        // View all sales FOR MANAGER
        public function managersales(Request $request)
        {
            $search = $request->input('search');
            $date = $request->input('date', now()->toDateString()); // 👈 Default to today
    
            $sales = PurchaseItem::with(['product.category'])
                ->when($search, function ($query, $search) {
                    $query->whereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                })
                ->whereDate('created_at', $date) // 👈 Only today's records
                ->orderBy('created_at', 'desc')
                ->get();
    
            return view('manager.manage-sales', compact('sales', 'search', 'date'));
        }

        public function showReceipt($id)
        {
            $item = PurchaseItem::findOrFail($id);
            $transactionId = $item->transaction_id;
        
            $items = PurchaseItem::with('product')
                ->where('transaction_id', $transactionId)
                ->get();
        
            $total = $items->sum('total_price');
        
            return view('receipts.receipt', compact('items', 'total'));
        }
        

    

}
