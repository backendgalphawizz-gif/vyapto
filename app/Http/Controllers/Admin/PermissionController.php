<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function filter(Request $request) 
    {
        $filters = $request->only(['search']);
        session(['permission_filters' => $filters]);

        return redirect()->route('permissions.index', ['filter' => 1]);
    }

    public function index(Request $request)
    {
        if (!$request->has('filter')) {
            session()->forget('permission_filters');
            $filters = [];
        } else {
            $filters = session('permission_filters', []);
        }

        $query = Permission::query();

        // Search
        $search = $filters['search'] ?? null;
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('route', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort_by = $request->get('sort_by', 'created_at'); 
        $sort_order = $request->get('sort_order', 'desc');
        
        $validSorts = ['id', 'module', 'name', 'route', 'created_at'];
        if (in_array($sort_by, $validSorts)) {
            $query->orderBy($sort_by, $sort_order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $permissions = $query->paginate(10); 

        // Counts
        $totalPermissions = Permission::count();

        return view('admin.permissions.index', compact('permissions', 'totalPermissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'module' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:permissions,name',
            'route' => 'required|string|max:255|unique:permissions,route'
        ]);

        Permission::create([
            'module' => $request->module,
            'name' => $request->name,
            'route' => $request->route,
            'guard_name' => 'web'
        ]);

        return redirect()->route('permissions.index')->with('success','Permission created successfully!');
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'module' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:permissions,name,'.$permission->id,
            'route' => 'required|string|max:255|unique:permissions,route,'.$permission->id
        ]);

        $permission->update([
            'module' => $request->module,
            'name' => $request->name,
            'route' => $request->route
        ]);

        return redirect()->route('permissions.index')->with('success','Permission updated successfully!');
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permissions.index')->with('success','Permission deleted successfully!');
    }
}