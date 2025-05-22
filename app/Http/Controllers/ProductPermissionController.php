<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProductPermission;
use Illuminate\Http\Request;

class ProductPermissionController extends Controller
{
    public function show()
    {
        $managers = User::where('role', 'manager')->get();
        $permissions = ProductPermission::pluck('manager_id')->toArray();

        return view('admin.manage-manager-permissions', compact('managers', 'permissions'));
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

