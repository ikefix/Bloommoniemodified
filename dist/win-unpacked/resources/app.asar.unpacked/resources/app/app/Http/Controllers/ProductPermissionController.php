<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use App\Models\ProductPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductPermissionController extends Controller
{
    public function show()
    {
        $managers = User::where('role', 'manager')->with('shop')->get();
        $permissions = ProductPermission::pluck('manager_id')->toArray();
        $shops = Auth::user()->shops ?? collect(); // fallback to empty collection

        return view('admin.manage-manager-permissions', compact('managers', 'permissions', 'shops'));
    }

    public function grantAccess(Request $request)
    {
        $request->validate([
            'manager_id' => 'required|exists:users,id'
        ]);

        ProductPermission::firstOrCreate([
            'manager_id' => $request->manager_id
        ]);

        return back()->with('success', 'Access granted successfully.');
    }
    public function revokeAccess(Request $request)
{
    $request->validate([
        'manager_id' => 'required|exists:users,id'
    ]);

    // Find the product permission record for the manager and delete it
    $permission = ProductPermission::where('manager_id', $request->manager_id)->first();

    if ($permission) {
        $permission->delete();
        return back()->with('success', 'Access revoked successfully.');
    }

    return back()->with('error', 'No access record found for this manager.');
}

}

