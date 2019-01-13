<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
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
        return view('auth.register', ['type' => $this->CAMPER]);
    }

    /**
     * Show the registration page for camp makers
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function campmaker()
    {
        return view('auth.register', ['type' => $this->CAMPMAKER]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            // common
            'type'          => "required|in:{$this->CAMPER},{$this->CAMPMAKER}",
            'username'      => 'required|string|max:50',
            'name_en'        => 'nullable|string|max:50|required_without:name_th',
            'surname_en'     => 'nullable|string|max:50|required_without:surname_th',
            'nickname_en'    => 'nullable|string|max:50|required_without:nickname_th',
            'name_th'        => 'nullable|string|max:50|required_without:name_en',
            'surname_th'     => 'nullable|string|max:50|required_without:surname_en',
            'nickname_th'    => 'nullable|string|max:50|required_without:nickname_en',
            'nationality'   => 'required|integer|min:0|max:1',
            'gender'        => 'required|integer|min:0|max:2',
            'citizen_id'     => 'required|string|digits:13|unique:users',
            'dob'           => 'required|date_format:Y-m-d|before:today',
            'address'       => 'required|string|max:300',
            'allergy'       => 'nullable|string|max:200',
            'zipcode'       => 'required|string:max:20',
            'email'         => 'required|string|email|max:100|unique:users',
            'password'      => 'required|string|min:6|confirmed',
            // camper
            'short_biography'    => 'nullable|string|max:500',
            'mattayom'          => 'nullable|integer|min:1|max:6',
            'blood_group'        => "nullable|integer|required_if:type,{$this->CAMPER}",
            'guardian_name'      => 'nullable|string',
            'guardian_role'      => 'nullable|integer|min:0|max:2|required_with:guardian_name',
            'guardian_mobile_no'  => 'nullable|string|required_with:guardian_name',
        ]);
    }

    /* Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        try {
            $data['password']        = bcrypt(array_get($data, 'password'));
            $data['activation_code'] = str_random(30).time();
            $user                    = app(User::class)->create($data);
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
     * @param Request $request
     * @return User
     */
    protected function register(Request $request)
    {
        $this->validator($request->all())->validate();
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
