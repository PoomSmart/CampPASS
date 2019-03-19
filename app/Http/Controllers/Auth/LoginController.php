<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Validate the user login.
     * @param Request $request
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
                'identity' => 'required|string',
                'password' => 'required|string',
            ], [
                'identity.required' => trans('validation.required', ['attribute' => trans('account.Username').' '.trans('app.Or').' '.trans('account.Email')]),
                'password.required' => trans('validation.required', ['attribute' => 'password']),
            ]
        );
    }

    /**
     * Override the username for accepting both username and email for authentication.
     *
     * @return string
     */
    public function username()
    {
        $identity = request()->get('identity');
        $field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $identity]);
        return $field;
    }
}
