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

    // Get products based on the selected category (AJAX)a
    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();

        return response()->json($products);
    }
public function searchReceipt(Request $request)
{
    $request->validate([
        'transaction_id' => 'required|string'
    ]);

    $transactionId = $request->transaction_id;

    $items = PurchaseItem::with(['product', 'shop'])
        ->where('transaction_id', $transactionId)
        ->get();

    if ($items->isEmpty()) {
        return back()->with('error', 'Transaction not found');
    }

    $total = $items->sum('total_price');
    $cashier = auth()->check() ? auth()->user()->name : 'Unknown Cashier';
    $shopName = $items->first()->shop ? $items->first()->shop->name : 'Unknown Shop';

    return view('receipts.receipt', compact('items', 'total', 'cashier', 'shopName', 'transactionId'));
}

    // Store the purchase item and update stock
   public function store(Request $request)
{
    try {
        // validate safely
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,transfer',
        ]);

        $transactionId = 'TXN-' . now()->format('YmdHis') . '-' . rand(1000, 9999);
        $lastPurchase = null;

        foreach ($validated['products'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $quantityRequested = $item['quantity'];

            // Stock check → JSON response
            if ($product->stock_quantity < $quantityRequested) {
                return response()->json([
                    'success' => false,
                    'message' => "Not enough stock for {$product->name}. Available: {$product->stock_quantity}"
                ], 400);
            }

            // Save purchaseaa
            $lastPurchase = PurchaseItem::create([
                'product_id'     => $product->id,
                'category_id'    => $product->category_id,
                'quantity'       => $quantityRequested,
                'total_price'    => $product->price * $quantityRequested,
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $transactionId,
                'shop_id'        => $product->shop_id,
            ]);

            // Update stock
            $product->decrement('stock_quantity', $quantityRequested);

            // Low stock notification
            if ($product->stock_quantity <= $product->stock_limit) {
                $admins = User::whereIn('role', ['admin', 'manager'])->get();
                Notification::send($admins, new LowStockAlert($product));
            }
        }

        return response()->json([
            'success'    => true,
            'receipt_id' => $lastPurchase->id,
            'txn_id'     => $transactionId
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Validation error → JSON
        return response()->json([
            'success' => false,
            'message' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {
        // Unexpected error → JSON
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage(),
        ], 500);
    }
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

public function showReceipt(Request $request, $id)
{
    $item = PurchaseItem::with('shop')->findOrFail($id); // load shop relation
    $transactionId = $item->transaction_id;

    $items = PurchaseItem::with('product')
        ->where('transaction_id', $transactionId)
        ->get();

    $total = $items->sum('total_price');

    $cashier = auth()->check() ? auth()->user()->name : 'Unknown Cashier';
    $shopName = $item->shop ? $item->shop->name : 'Unknown Shop';

    if ($request->wantsJson()) {
        return response()->json([
            'success'        => true,
            'transaction_id' => $transactionId,
            'items'          => $items,
            'total'          => $total,
            'cashier'        => $cashier,
            'shop'           => $shopName,
        ]);
    }

    return view('receipts.receipt', compact('items', 'total', 'cashier', 'shopName'));
}

}
