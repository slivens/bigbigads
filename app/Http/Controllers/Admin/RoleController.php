<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Role;
use App\Policy;
use TCG\Voyager\Models\Permission;

class RoleController extends Controller
{
    public function showPermissionMap()
    {
        $roles = Role::where('id', '>', 2)->with('policies')->get();
        $groupedPermissions = Permission::all()->groupBy('table_name');
        $groupedPolicies = Policy::all()->groupBy('key');
        foreach ($roles as $role) {
            $role->groupedPolicies = $role->generateGroupedPolicies();
        }
        return view('admin.permission_map', ['roles' => $roles, 'groupedPermissions' => $groupedPermissions, 'groupedPolicies' => $groupedPolicies, 'types' => Policy::TYPE_DESC]);
    }

    public function storePermissionMap(Request $request)
    {
        dd($request->all());
    }
}
