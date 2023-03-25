<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use App\Roles;
use App\Permissions;
use Auth;
use DB;
use Str;
use Gate;

class PermissionController extends Controller
{
    public function index() {
        Gate::allows('modules', 'permissions_role_access');

        $roles = config('roles');
        $role_all = 0;
        if (Auth::user()->permission == 'supper-admin') {
            $role_all = 1;
        }
        $special_rights = array_splice($roles, 2);
        $data = [
            'role_all' => $role_all,
            'role' => Roles::paginate(10),
            'special_rights' => $special_rights,
        ];
        return view('admin.permissions.role.index', $data);
    }

    public function getAddRole() {
        Gate::allows('modules', 'permissions_role_add');

        $modules_all = config('admin_menu');
        $modules = [];
        foreach($modules_all as $key => $value) {
            $name_module = substr($value['name'], 0, -7);
            $modules[] = $name_module;
            if(isset($value['sub'])) {
                foreach($value['sub'] as $k => $v) {
                    $name_module = substr($v['name'], 0, -7);
                    $modules[] = $name_module;
                }
            }
        }
        $data = [
            'modules' => $modules,
        ];
        return view('admin.permissions.role.add', $data);
    }

    public function postAddRole(Request $request) {
        Gate::allows('modules', 'permissions_role_add');

        $this->validate($request,
        [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required',
        ]);
        $role = Roles::create([
            'name' => $request->name,
            'slug' => trim(str::slug($request->name)),
            // 'permissions' => json_encode($request->permissions, true),
        ]);
        foreach($request->permissions as $value) {
            if(!is_null($value)) {
                Permissions::create([
                    'id_role' => $role->id,
                    'slug_module' => $value,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('admin.permissions.role')->with('alert_success', 'Thêm quyền thành công.');
    }

    public function getEdit($id, Request $request) {
        Gate::allows('modules', 'permissions_role_edit');
        
        $role = Roles::findorFail($id);
        if($role->slug == 'supper-admin') {
            return redirect()->route('admin.permissions.role')->with('alert_success', 'Supper admin được sử dụng toàn quyền');
        }
        $modules_all = config('admin_menu');
        $modules = [];
        foreach($modules_all as $key => $value) {
            $name_module = substr($value['name'], 0, -7);
            $modules[] = $name_module;
            if(isset($value['sub'])) {
                foreach($value['sub'] as $k => $v) {
                    $name_module = substr($v['name'], 0, -7);
                    $modules[] = $name_module;
                }
            }
        }
        $permissions = Permissions::where('id_role', $role->id)->pluck('slug_module')->toArray();
        $data = [
            'role' => $role,
            'modules' => $modules,
            'permissions' => $permissions
        ];
        return view('admin.permissions.role.edit', $data);
    }

    public function postEdit($id, Request $request) {
        Gate::allows('modules', 'permissions_role_edit');

        $role = Roles::findOrFail($id);
        if($role->slug == 'supper-admin') {
            return redirect()->route('admin.permissions.role')->with('alert_error', 'Supper admin không được thay đổi');
        }
        $this->validate($request,
        [
            'name' => 'required',
            'permissions' => 'required',
        ]);
        $role->update([
            'name' => $request->name,
            'slug' => trim(str::slug($request->name)),
        ]);
        Permissions::where('id_role', $role->id)->delete();
        foreach($request->permissions as $value) {
            if(!is_null($value)) {
                Permissions::create([
                    'id_role' => $role->id,
                    'slug_module' => $value,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('admin.permissions.role')->with('alert_success', 'Cập nhật quyền thành công.');
    }

    public function getDelete($id) {
        Gate::allows('modules', 'permissions_role_delete');

        $role = Roles::findOrFail($id);
        if($role->slug == 'supper-admin') {
            return redirect()->back()->with('alert_error', 'Supper admin không được thay đổi.');
        }
        $role->delete();

        return redirect()->back()->with('alert_success','Delete permission successful.');
    }
    
    public function getTrash() {
        Gate::allows('modules', 'permissions_role_delete');
        
        $roles = Roles::onlyTrashed()->orderBy('id', 'desc')->paginate('10');
        
        $data = [
            'role' => $roles,
        ];

        return view('admin.permissions.role.trash', $data);
    }

    public function getRestore($id) {
        $role = Roles::withTrashed()->findOrFail($id)->restore();

        return redirect()->back()->with('alert_success', 'Role Restore successful.');
    }

    public function deleteTrash($id) {
        Gate::allows('modules', 'permissions_role_delete');

        $role = Roles::withTrashed()->findOrFail($id);
        Permissions::where('id_role', $role->id)->delete();
        $role->forceDelete();
        
        return redirect()->back()->with('alert_success', 'Role deleted successfully.');
    }

    public function getUser() {
        Gate::allows('modules', 'permissions_user_access');
        
        $data = [
            'user' => User::whereNotNull('permission')->where('roles', 'LIKE', '%'."admin".'%')->paginate(10),
        ];
        return view('admin.permissions.user.index', $data);
    }

    public function getAddUser() {
        Gate::allows('modules', 'permissions_user_add');

        $data = [
            'role' => Roles::all(),
        ];
        return view('admin.permissions.user.add', $data);
    }
    public function postAddUser(Request $request) {
        Gate::allows('modules', 'permissions_user_add');

        $this->validate($request,
        [
            'username' => 'string|required',
            'permission' => 'required',
        ]);

        $role = Roles::where('slug', $request->permission)->first();
        if(is_null($role)) {
            return redirect()->back()->with('alert_error', 'Quyền quản trị không tồn tại.');
        }
        $user = User::where('username', $request->username)->orwhere('email', $request->username)->get()->first();
        if (is_null($user)) {
            return redirect()->back()->with('alert_error', 'User '.$request->username.' không có trong hệ thống');
        } else {
            $user->update([
                'roles' => '["admin","user"]',
                'permission' => $role->slug,
            ]);
        }

        return redirect()->route('admin.permissions.user')->with('alert_success', 'Thêm User thành công.');
    }

    public function getEditUser($id) {
        Gate::allows('modules', 'permissions_user_edit');

        $user = User::findorFail($id);
        $data = [
            'user' => $user,
            'roles' => Roles::all(),
        ];
        return view('admin.permissions.user.edit', $data);
    }

    public function postEditUser(Request $request, $id) {
        Gate::allows('modules', 'permissions_user_edit');

        $user = User::findorFail($id);
        $this->validate($request,
        [
            'permission' => 'required',
        ]);
        $role = Roles::where('slug', $request->permission)->first();
        if(is_null($role)) {
            return redirect()->back()->with('alert_error', 'Quyền quản trị không tồn tại.');
        }
        $user->update([
            'permission' => $role->slug,
        ]);

        return redirect()->route('admin.permissions.user')->with('alert_success', 'Cập nhật User '.$user->username.' thành công.');
    }

    public function getDeleteUser($id) {
        Gate::allows('modules', 'permissions_user_delete');
        
        $user = User::findorFail($id);
        $user->update([
            'permission' => null,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('alert_success', 'Xóa quyền user thành công.');
    }
}
