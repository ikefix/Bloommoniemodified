<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class BarcodeController extends Controller
{
    public function getProductName($barcode)
    {
        $product = Product::where('barcode_number', $barcode)->first(); // ✅ match your real column name

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        return response()->json([
            'success' => true,
            'name' => $product->name,
        ]);
    }
}
