<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductPermissionController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use Milon\Barcode\DNS1D;
use App\Models\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\BarcodeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Route::get('/barcode/{code}', function ($code) {
//     $barcode = new DNS1D();
//     // Generate a PNG barcode as base64
//     $png = $barcode->getBarcodePNG($code, 'C128');
//     // Return it as an inline image response (no file saved)
//     return response(base64_decode($png))
//         ->header('Content-Type', 'image/png');
// });

Route::get('/get-product/{barcode}', [App\Http\Controllers\BarcodeController::class, 'getProductName']);



Route::get('/barcode-Admin', function () {
    return view('barcode-manager');
})->name('barcode.manager');

Route::get('/get-product-name/{barcode}', [App\Http\Controllers\BarcodeController::class, 'getProductName']);


Route::get('/barcode-Manager', function () {
    return view('manager.barcode');
})->name('manager.barcode');


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/admin', [AdminController::class, 'index'])->middleware('role:admin');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('role:admin');
    // Route::get('/manager-dashboard', [ManagerController::class, 'index'])->name('manager.dashboard')->middleware('role:manager');
});

// FOR MANAGER

Route::get('/manager/sales', [PurchaseItemController::class, 'managersales'])->name('manage.sales');

// Route::get('/manager/notifications', [ManagerController::class, 'getNotifications'])->name('manager.notification');

Route::get('/manager/profile', [ManagerController::class, 'editProfile'])->name('manager.profile');

// Handle Profile Update
Route::post('/manager/profile/update', [ManagerController::class, 'updateProfile'])->name('manager.profile.update');

Route::get('/manager/register', [ManagerController::class, 'showRegisterForm'])->name('manager.register');

Route::get('/manager/manage-roles', [ManagerController::class, 'role'])->name('manager.manage_role');
Route::patch('/manager/update-role/{id}', [ManagerController::class, 'updateRole'])->name('manager.updateRole');

Route::get('/manager/products', [ManagerController::class, 'viewProducts'])->name('manager.product');


    Route::get('/manager-dashboard', [ManagerController::class, 'dashboard'])->name('manager.jop')->middleware('role:manager');

Route::post('/manager/register', [ManagerController::class, 'storeStaff'])->name('manager.storeStaff');

// Route::get('/products/create', [ManagerController::class, 'create'])->name('manager.product');
// Route::post('/manager/product', [ManagerController::class, 'store'])->name('manager.store');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

// END OF MANAGER ROUTES







Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/manage-roles', [RoleController::class, 'index'])->name('admin.manage_roles');
    Route::patch('/admin/update-role/{id}', [RoleController::class, 'updateRole'])->name('admin.updateRole');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/register', [AdminController::class, 'showRegisterForm'])->name('admin.register');
});

Route::post('/admin/register', [AdminController::class, 'storeStaff'])->name('admin.storeStaff');


// Show Edit Profile Form
Route::get('/admin/profile', [AdminController::class, 'editProfile'])->name('admin.profile');

// Handle Profile Update
Route::post('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');

// Route for admin sales page
// Route::get('/admin/sales', [AdminController::class, 'sales'])->name('admin.sales');
// Route::get('/admin/filter-sales', [AdminController::class, 'filterSales'])->name('admin.sales.filter');

Route::get('/admin/sales', [AdminController::class, 'salespage'])->name('admin.sales');
Route::get('/admin/filter-sales', [AdminController::class, 'filterSales'])->name('admin.sales.filter');
// Route::delete('/admin/sales/{id}', [AdminController::class, 'deleteSale'])->name('admin.sales.delete');

Route::delete('/admin/sales/{id}', [PurchaseItemController::class, 'destroy'])->name('admin.sales.destroy');



Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
// Route::get('/admin/filter-sales', [PurchaseItemController::class, 'allSales']);


// PRODUCT CREATE
Route::middleware(['auth'])->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create')->middleware('admin');
    // Route::post('/products', [ProductController::class, 'store'])->name('products.store')->middleware('admin');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update')->middleware('admin');
});


// CATEGORY CREATE
Route::middleware(['auth'])->group(function () {
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

Route::get('/products/by-category/{categoryId}', [App\Http\Controllers\ProductController::class, 'getByCategory']);
// Search
Route::get('/products/search-suggestions', [ProductController::class, 'searchSuggestions']);
// FOR ADMIN AND MANAGER
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('user.notifications');
});

Route::get('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.delete');

Route::get('/products/search', [ProductController::class, 'liveSearch'])->name('products.live-search');




//CASHIER CONTROLLER
Route::get('/purchaseitem/products/{categoryId}', [PurchaseItemController::class, 'getProductsByCategory']);

Route::post('/purchaseitem/store', [PurchaseItemController::class, 'store'])->name('purchaseitem.store');
Route::get('/purchaseitem/receipt/{id}', [PurchaseItemController::class, 'showReceipt'])->name('purchaseitem.receipt');


Route::get('/home', [PurchaseItemController::class, 'index'])->name('home');
Route::get('/cashiersales', [PurchaseItemController::class, 'cashiersales'])->name('cashier.home-sales');



Route::get('/api/product-stock/{id}', function ($id) {
    $product = \App\Models\Product::findOrFail($id);
    return response()->json(['stock' => $product->stock_quantity]);
});



// PERMISSION ACCESS

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/manager-permissions', [ProductPermissionController::class, 'show'])->name('admin.manager-permissions');
    Route::post('/admin/grant-product-access', [ProductPermissionController::class, 'grantAccess'])->name('admin.give-product-access');
    Route::post('/admin/revoke-product-access', [ProductPermissionController::class, 'revokeAccess'])->name('admin.revoke-product-access');
});




// MULTIPLE SHOPS ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/shops/create', [ShopController::class, 'index'])->name('shops.create');
    // Route::get('/shops/create', [ShopController::class, 'create'])->name('shops.create');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::get('/shops/{id}/edit', [ShopController::class, 'edit'])->name('shops.edit');
    Route::put('/shops/{id}', [ShopController::class, 'update'])->name('shops.update');
    Route::delete('/shops/{id}', [ShopController::class, 'destroy'])->name('shops.destroy');
});


// STOCK TRANSFER

// Route to display the stock transfer form
Route::get('/stock-transfers/create', [StockTransferController::class, 'create'])->name('stock-transfers.create');
Route::post('/stock-transfers', [StockTransferController::class, 'store'])->name('stock-transfers.store');


// Route::get('/shops/{shop}/products', function (\App\Models\Shop $shop) {
//     $products = $shop->products()->with('category')->get();
//     return response()->json(['products' => $products]);
// });

Route::get('/products-by-shop/{shopId}', [StockTransferController::class, 'getProductsByShop']);
// Route::get('/products-by-shop/{shopId}', [App\Http\Controllers\ProductController::class, 'getProductsByShop']);

Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');


Route::get('/receipt/search', [PurchaseItemController::class, 'searchReceipt'])->name('receipt.search');
Route::get('/api/product-stock/{id}', [ProductController::class, 'getStock']);




// ROUTES FOR EXPENSES
Route::get('/Adminexpenses', [ExpenseController::class, 'index'])->name('expenses.index');
Route::get('/Adminexpenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
Route::post('/Adminexpenses', [ExpenseController::class, 'store'])->name('expenses.store');
Route::delete('/Adminexpenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');



Route::get('/Cashierexpense', [ExpenseController::class, 'indexcash'])->name('cashierexpense.index');
Route::get('/Cashierexpenses/create', [ExpenseController::class, 'createcash'])->name('cashierexpense.create');
Route::post('/Cashierexpenses', [ExpenseController::class, 'storecash'])->name('cashierexpense.store');
Route::delete('/Cashierexpenses/{id}', [ExpenseController::class, 'destroycash'])->name('cashierexpense.destroy');




Route::get('/Managerexpense', [ExpenseController::class, 'indexmanager'])->name('managerexpense.index');
Route::get('/Managerexpenses/create', [ExpenseController::class, 'createmanager'])->name('managerexpense.create');
Route::post('/Managerexpenses', [ExpenseController::class, 'storemanager'])->name('managerexpense.store');
Route::delete('/Managerexpenses/{id}', [ExpenseController::class, 'destroymanager'])->name('managerexpense.destroy');

//ROUTE FOR EXPENSES END

//ROUTE FOR REPORTS START
// Route::get('/reports/sales', [AdminController::class, 'salesReport'])->name('reports.sales');