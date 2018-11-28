<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PostPhoneCode;
use App\Http\Requests\PostUser;
use App\Http\Requests\UpdatePassword;
use App\Http\Resources\User as UserCollection;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\SmsServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        $roles       = Role::all($select);
        $permissions = Permission::all($select);

        return view('admin.user', compact('roles', 'permissions'));
    }

    public function index()
    {
        $user_info = collect([
            'account' => Auth::user()->account,
            'email'   => Auth::user()->email,
            'name'    => Auth::user()->profile->real_name,
            'phone'   => Auth::user()->profile->phone,
        ]);

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
        $tmp   = $users->map(function ($item) {
            return new UserCollection($item);
        });
        $data  = [
            'total'        => $users->total(),
            'current_page' => $users->currentPage(),
            'per_page'     => $users->perPage(),
            'data'         => $tmp,
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
        $request->user()->profile->phone     = $request->get('phone');
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
                $user = User::create([
                    'account'  => $postUser->get('account'),
                    'email'    => $postUser->get('email'),
                    'password' => Hash::make(config('website.defaultPassword')),
                ]);

                $user->profile()->save(
                    new UserProfile([
                        'real_name' => $postUser->get('name'),
                        'avatar'    => '/images/faces/avatar'.rand(1, 10).'.jpg',
                    ])
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
                'account'  => $request->user()->account,
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

    /**
     * 判断手机是否验证
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function verify()
    {
        return Auth::user()->hasVerifiedPhone() ? redirect('/home') : view('vendor.phone');
    }

    /**
     * 验证手机号码唯一性
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPhone(Request $request)
    {
        return UserProfile::wherePhone($request->get('phone'))->first(['id']) ? $this->successResponseData(200) : $this->errorResponseData(202);
    }

    /**
     * 验证码发送
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function code(Request $request)
    {
        //一定时间内获取验证码次数限制
        $times = \Cache::get($request->getClientIp());
        if ($times >= 5) {
            return $this->errorResponseData(444, __('Too Many Attemp Get Code'));
        } elseif ($times == null) {
            \Cache::put($request->getClientIp(), 1, now()->addMinutes(20));
        } else {
            \Cache::increment($request->getClientIp());
        }

        //记录用户验证码值缓存.
        $code = mt_rand(1000, 9999);
        \Cache::put($request->get('phone'), $code, now()->addMinutes(10));

        $phone      = $request->get('phone');
        $signName   = config('website.signName');
        $tempCode   = config('website.tempCheckCode');
        $tempParams = [
            'code' => $code,
        ];

        //调用验证码发送
        SmsServices::sendSms($phone, $signName, $tempCode, $tempParams);

        return $this->successResponseData(200, __('code has sended'));
    }

    /**
     * 验证用户手机
     * @param PostPhoneCode $phoneCode
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function checkCode(PostPhoneCode $phoneCode)
    {
        //验证验证码
        if (\Cache::get($phoneCode->get('phone')) != $phoneCode->get('code')) {
            return $this->errorResponseData(444, __('Error Code'));
        }
        $res = DB::transaction(function () use ($phoneCode) {
            $phoneCode->user()->profile->phone    = $phoneCode->get('phone');
            $phoneCode->user()->phone_verified_at = now();
            $phoneCode->user()->profile->save();
            //清除缓存
            \Cache::forget($phoneCode->get('phone'));
            \Cache::forget($phoneCode->getClientIp());

            return $phoneCode->user()->save();
        });

        return $res ? $this->successResponseData(200, __('success')) : $this->errorResponseData();
    }
}
