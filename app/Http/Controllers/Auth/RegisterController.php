<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Religion;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Notifications\UserRegisteredSuccessfully;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
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

    protected $CAMPER;
    protected $CAMPMAKER;

    protected $religions;

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
        $this->CAMPER = config('const.account.camper');
        $this->CAMPMAKER = config('const.account.campmaker');
        $this->middleware('guest');
        $this->religions = Religion::pluck('name', 'id')->all();
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
        return view('auth.register', [ 'type' => $this->CAMPER, 'religions' => $this->religions ]);
    }

    /**
     * Show the registration page for camp makers
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function campmaker()
    {
        return view('auth.register', [ 'type' => $this->CAMPMAKER, 'religions' => $this->religions ]);
    }

    /* Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        try {
            $data['password'] = bcrypt(array_get($data, 'password'));
            $data['activation_code'] = str_random(30).time();
            $user = app(User::class)->create($data);
            $user->notify(new UserRegisteredSuccessfully($user));
            return $user;
        } catch (\Exception $exception) {
            logger()->error($exception);
            return redirect()->back()->with('message', 'Unable to create new user.');
        }
    }

    /**
     * Register new account.
     *
     * @param \App\StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->create($request->all());
            event(new Registered($user));
        } catch (\Exception $exception) {
            logger()->error($exception);
            return redirect()->to('/home');
        }
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
            $user->status = 1;
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
