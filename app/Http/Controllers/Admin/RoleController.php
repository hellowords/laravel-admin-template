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
    public function permissions()
    {
        $permissions = \Cache::rememberForever('permissions', function () {
            return Permission::withCount('users')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });
        });

        return $this->successResponseData(200, $permissions);
    }

    /**
     * @desc 角色分页数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function roles()
    {
        $roles = Role::with('permissions:id,name')->withCount('users')->paginate(10);

        return $this->successResponseData(200, $roles);
    }

    /**
     * @desc 添加角色
     * @param AddRole $role
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRole(AddRole $role)
    {
        cache()->forget('yewuRoleName');

        return Role::create($role->only('name')) ? $this->successResponseData() : $this->errorResponseData();
    }

    /**
     * @desc 添加权限
     * @param AddPermission $permission
     * @throws
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPermission(AddPermission $permission)
    {
        cache()->forget('permissions');

        return Permission::create($permission->only('name')) ? $this->successResponseData() : $this->errorResponseData();
    }


    /**
     * 更新用户角色
     * @param User $user
     * @param PostRole $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function syncPermissionByUser(User $user, PostRole $request)
    {
        cache()->forget('addOrderUsers');

        //Todo 1号用户管理员角色不能取消
        return $user->syncPermissions($request->get('data')) ? $this->successResponseData(200, __('success')) : $this->errorResponseData();
    }

    /**更新用户权限
     * @param User $user
     * @param PostRole $postRole
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncRoleByUser(User $user, PostRole $postRole)
    {
        cache()->forget('addOrderUsers');

        return $user->syncRoles($postRole->get('data')) ? $this->successResponseData(200, __('success')) : $this->errorResponseData();
    }

    /**角色的权限更新
     * @param Role $role
     * @param PutSyncPermissions $permissions
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncPermissionsByRole(Role $role, PutSyncPermissions $permissions)
    {
        cache()->forget('addOrderUsers');
        $role->syncPermissions($permissions->input());

        return $this->successResponseData(200, __('success'));
    }
}
