<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLogin;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct ()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username ()
    {
        return 'account';
    }

    protected function credentials (Request $request)
    {
        if(filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL)) {
            return [
                'email'    => $request->get($this->username()),
                'password' => $request->get('password'),
            ];
        }

        return $request->only($this->username(), 'password');
    }

    /**
     * @param Request $request
     * @param         $user
     */
    public function authenticated (Request $request, $user)
    {
        event(new UserLogin($user, $request->getClientIp()));
    }
}
