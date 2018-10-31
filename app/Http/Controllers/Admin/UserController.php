<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PostUser;
use App\Http\Requests\UpdatePassword;
use App\Http\Resources\User as UserCollection;
use App\Mail\UserEmailVerifyed;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function userList()
    {
        $select = [
            'id as value',
            'name as label',
        ];

        $roles = Role::all($select);
        $permissions = Permission::all($select);

        return view('admin.user', compact('roles', 'permissions'));
    }

    public function index()
    {
        $user_info = collect(
            [
                'account' => Auth::user()->account,
                'email' => Auth::user()->email,
                'name' => Auth::user()->profile->real_name,
                'phone' => Auth::user()->profile->phone,
            ]
        );

        return view('admin.profile', compact('user_info'));
    }

    /**
     * @desc 用户列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        $users = User::with(['profile', 'roles', 'permissions'])->paginate($request->get('per_page', 10));
        $tmp = $users->map(
            function ($item) {
                return new UserCollection($item);
            }
        );
        $data = [
            'total' => $users->total(),
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'data' => $tmp,
        ];

        return $this->successResponseData(200, $data);
    }

    /**
     * @desc 更新用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->user()->profile->real_name = $request->get('name');
        $request->user()->profile->phone = $request->get('phone');
        $request->user()->profile->save();

        return $this->successResponseData(200, __('success'));
    }

    /**
     * @desc 添加用户
     * @param PostUser $postUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PostUser $postUser)
    {
        $user = DB::transaction(
            function () use ($postUser) {
                $user = User::create(
                    [
                        'account' => $postUser->get('account'),
                        'email' => $postUser->get('email'),
                        'password' => Hash::make(config('website.defaultPassword')),
                    ]
                );

                $user->profile()->save(
                    new UserProfile(
                        [
                            'real_name' => $postUser->get('name'),
                            'avatar' => '/images/faces/avatar'.rand(1, 10).'.jpg',
                        ]
                    )
                );

                return $user;
            }
        );

        return $user ? $this->successResponseData(200, __('success')) : $this->errorResponseData(422, __('failed'));
    }

    /**
     * @desc 验证原密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPass(Request $request)
    {
        return Auth::guard()->validate(
            [
                'account' => $request->user()->account,
                'password' => $request->get('old_pass'),
            ]
        ) ? $this->successResponseData(200) : $this->errorResponseData(201, __('passwords.old_password_not_match'));
    }

    /**
     * @desc 更新密码
     * @param UpdatePassword $updatePassword
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePass(UpdatePassword $updatePassword)
    {
        return $updatePassword->user()->update(
            [
                'password' => Hash::make($updatePassword->get('password')),
            ]
        ) ? $this->successResponseData(200, __('success')) : $this->errorResponseData();
    }

    /**
     * @desc 删除用户
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destory(User $user)
    {
        if ($user->id == 1) {
            return $this->errorResponseData(403, __('disableDeleteAdmin'));
        }

        $info = DB::transaction(
            function () use ($user) {
                $user->profile->delete();
                $user->delete();

                return true;
            }
        );

        return $info ? $this->successResponseData(200, __('success')) : $this->errorResponseData(500);
    }
}
