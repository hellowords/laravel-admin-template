<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AddPermission;
use App\Http\Requests\AddRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRole;
use App\Http\Requests\PutSyncPermissions;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * @desc 权限分页数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function permissions ()
    {
        $permissions = Permission::withCount('users')->paginate(10);

        return $this->successResponseData(200, $permissions);
    }

    /**
     * @desc 角色分页数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function roles ()
    {
        $roles = Role::with('permissions:id,name')->withCount('users')->paginate(10);

        return $this->successResponseData(200, $roles);
    }

    /**
     * @desc 添加角色
     * @param AddRole $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRole (AddRole $role)
    {
        return Role::create($role->only('name')) ? $this->successResponseData() : $this->errorResponseData();
    }

    /**
     * @desc 添加权限
     * @param AddPermission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPermission (AddPermission $permission)
    {
        return Permission::create($permission->only('name')) ? $this->successResponseData() : $this->errorResponseData();
    }


    /**
     * 更新用户角色
     * @param PostRole $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncPermissionByUser (User $user, PostRole $request)
    {
        return $user->syncPermissions($request->get('data')) ? $this->successResponseData(200, __('success')) : $this->errorResponseData();
    }

    /**
     * 更新用户权限
     * @param PostRole $postRole
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncRoleByUser (User $user, PostRole $postRole)
    {
        return $user->syncRoles($postRole->get('data')) ? $this->successResponseData(200, __('success')) : $this->errorResponseData();
    }

    /**同步角色的权限
     * @param Role $role
     * @param PutSyncPermissions $permissions
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncPermissionsByRole(Role $role, PutSyncPermissions $permissions)
    {
        $role->syncPermissions($permissions->input());
        return $this->successResponseData(200,__('success'));
    }
}
