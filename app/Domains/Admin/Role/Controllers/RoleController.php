<?php

namespace App\Domains\Admin\Role\Controllers;

use App\Domains\Admin\Role\DataTables\RoleDataTable;
use App\Domains\Core\Role\Models\Role;
use App\Domains\Core\Permission\Models\Permission;
use App\Domains\Admin\Role\Requests\RoleStoreRequest;
use App\Domains\Admin\Role\Requests\RoleUpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RoleDataTable $dataTable)
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('Role::index');
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function create()
    {
        abort_if(Gate::denies('role_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $permissions = Permission::where('status', 1)
            ->where('name', 'not like', '%_access') 
            ->where('name', 'not like', '%staff_%')
            
            ->where('name', 'not like', '%role_%')
            ->where('name', 'not like', '%favorite_place_%')
            ->get();
            $groupedPermissions = [];
            foreach ($permissions as $permission) {
                $name = $permission->name;

                if (Str::startsWith($name, 'report_review_')) {
                    $groupedPermissions['report_review'][] = $permission;
                } elseif (Str::startsWith($name, 'report_user_')) {
                    $groupedPermissions['report_user'][] = $permission;
                } elseif (Str::startsWith($name, 'service_category_')) {
                    $groupedPermissions['service_category'][] = $permission;
                }
                else {
                    $group = explode('_', $name, 2)[0]; // fallback
                    $groupedPermissions[$group][] = $permission;
                }
            }
            $viewHTML = view('Role::create',compact('groupedPermissions'))->render();
            return response()->json(['success' => true, 'htmlView' => $viewHTML]);
        } catch (\Throwable $e) {
            // dd($e);
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    public function store(RoleStoreRequest $request)
    {
        abort_if(Gate::denies('role_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $input = $request->only('name');
            
            $role = Role::create($input);
            $selectedPermissions = $request->input('permissions', []);
            $permissions = Permission::whereIn('id', $selectedPermissions)->get();
            $modules = $permissions->map(function ($perm) {
                $parts = explode('_', $perm->name);
                array_pop($parts);
                return implode('_', $parts);
            })->unique();
            $accessPermissions = Permission::whereIn('name', $modules->map(fn($module) => $module . '_access'))->pluck('id')->toArray();
            $finalPermissions = array_unique(array_merge($selectedPermissions, $accessPermissions));
            $role->permissions()->sync($finalPermissions);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.add_record'),
            ], 200);
            
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        abort_if(Gate::denies('role_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{
                $role = Role::with(['permissions' => function ($q) {
                    $q->where('name', 'not like', '%_access')
                    ->where('name', 'not like', 'role_%')
                    ->where('name', 'not like', '%staff_%');
                }])->where('id', $id)->firstOrFail();
                $groupedPermissions = $role->permissions->groupBy('module_key');
                $viewHTML = view('Role::show', compact('role','groupedPermissions'))->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            }
            catch (\Throwable $th) {
                return response()->json(['success' => false, 'error' => $th->getmessage()], 400 );
            }
        }
        return response()->json(['success' => false, 'error' => trans('messages.error_message')], 400 );
    }

    public function edit(Request $request, $id)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $role = Role::where('id', $id)->first();
            $permissions = Permission::query()
                // Exclude permissions that contain 'user' or 'role' in their name
                ->where('name', 'not like', '%staff_%')
                ->where('name', 'not like', '%role_%')
                ->where('name', 'not like', '%deleted_receipt_%')
                // Exclude permissions that contain '_access' in their name
                ->where('name', 'not like', '%_access%')
                // Order by id for consistent grouping
                ->orderBy('id')
                ->select('id', 'name', 'title', 'module_key')
                ->get();

            // Group the retrieved permissions by the 'module_key' column.
            $groupedPermissions = $permissions->groupBy('module_key')->toArray();

            $viewHTML = view('Role::edit', compact('role','groupedPermissions'))->render();
            return response()->json(['success' => true, 'htmlView' => $viewHTML]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th->getmessage()], 400 );
        }
    }

    public function update(RoleUpdateRequest $request, Role $role)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $input = $request->only('name');
            $role->update($input);

            // Old permissions
            $oldPermissions = $role->permissions()->pluck('id')->sort()->values()->toArray();

            $selectedPermissions = $request->input('permissions', []);
            $permissions = Permission::whereIn('id', $selectedPermissions)->get(); 
            $modules = $permissions->map(function ($perm) {
                $parts = explode('_', $perm->name);
                array_pop($parts);
                return implode('_', $parts);
            })->unique();
            $accessPermissions = Permission::whereIn('name', $modules->map(fn($module) => $module . '_access'))->pluck('id')->toArray();
            $finalPermissions = array_unique(array_merge($selectedPermissions, $accessPermissions));
            $role->permissions()->sync($finalPermissions);

            // New permissions
            $newPermissions = collect($finalPermissions)->sort()->values()->toArray();
            // 🚨 If permissions changed → force logout users
            if($oldPermissions !== $newPermissions) {
                $users = $role->users()->get();
                foreach ($users as $user) {
                    // Delete all Sanctum tokens (mobile logout)
                    $user->tokens()->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.update_record'),
            ], 200);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $th->getmessage()], 400 );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $role = Role::where('id', $id)->first();
                if($role && $role->users()->count() > 0){
                    return response()->json([
                        'success' => false,
                        'error' => trans('messages.has_user_error'),
                    ], 400);
                }
                $role->delete();
                
                DB::commit();
                $response = [
                    'success'    => true,
                    'message'    => trans('messages.crud.delete_record'),
                ];
                return response()->json($response);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['success' => false, 'error' => $th->getMessage()], 400 );
            }
        }
        return response()->json(['success' => false, 'error' => trans('messages.error_message')], 400 );
    }
}
