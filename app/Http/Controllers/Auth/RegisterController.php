<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the registration landing page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function landing()
    {
        return view('auth.register-landing');
    }

    /**
     * Show the registration page for campers
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function camper()
    {
        return view('auth.register', ['type' => 'camper']);
    }

    /**
     * Show the registration page for camp makers
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function campmaker()
    {
        return view('auth.register', ['type' => 'campmaker']);
    }

    /**
     * Register new account.
     *
     * @param Request $request
     * @return User
     */
    protected function register(Request $request)
    {
        /** @var User $user */
        $validatedData = $request->validate([
            'username'      => 'required|string|max:255',
            'nameen'        => 'required|string|max:255',
            'surnameen'     => 'required|string|max:255',
            'nicknameen'    => 'required|string|max:255',
            'nameth'        => 'nullable|string|max:255',
            'surnameth'     => 'nullable|string|max:255',
            'nicknameth'    => 'nullable|string|max:255',
            'nationality'   => 'required|integer|min:0|max:1',
            'gender'        => 'required|integer|min:0|max:2',
            'citizenid'     => 'required|integer|digits:13|unique:users',
            'dob'           => 'required|date',
            'address'       => 'required|string|max:300',
            'allergy'       => 'nullable|string|max:255',
            'zipcode'       => 'required|integer',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:6|confirmed',
        ]);
        try {
            $validatedData['password']        = bcrypt(array_get($validatedData, 'password'));
            $validatedData['activation_code'] = str_random(30).time();
            $user                             = app(User::class)->create($validatedData);
        } catch (\Exception $exception) {
            logger()->error($exception);
            return redirect()->back()->with('message', 'Unable to create new user.');
        }
        $user->notify(new UserRegisteredSuccessfully($user));
        return redirect()->back()->with('message', 'Successfully created a new account. Please check your email and activate your account.');
    }
    /**
     * Activate the user with given activation code.
     * @param string $activationCode
     * @return string
     */
    public function activateUser(string $activationCode)
    {
        try {
            $user = app(User::class)->where('activation_code', $activationCode)->first();
            if (!$user) {
                return "The code does not exist for any user in our system.";
            }
            $user->status          = 1;
            $user->activation_code = null;
            $user->save();
            auth()->login($user);
        } catch (\Exception $exception) {
            logger()->error($exception);
            return "Whoops! something went wrong.";
        }
        return redirect()->to('/home');
    }
}
