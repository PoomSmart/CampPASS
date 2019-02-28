<?php

namespace App\Http\Controllers\Auth;

use App\Common;
use App\User;
use App\Program;
use App\Religion;
use App\School;
use App\Province;
use App\Organization;

use App\Enums\EducationLevel;

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
    use RegistersUsers;

    protected $CAMPER;
    protected $CAMPMAKER;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
        $this->CAMPER = config('const.account.camper');
        $this->CAMPMAKER = config('const.account.campmaker');
        $this->programs = Common::values(Program::class);
        $this->religions = Common::values(Religion::class);
        $this->organizations = null;
        $this->schools = Common::values(School::class);
        $this->provinces = Common::values(Province::class);
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
        $type = $this->CAMPER;
        $religions = $this->religions;
        $programs = $this->programs;
        $schools = $this->schools;
        $provinces = $this->provinces;
        $education_levels = EducationLevel::getLocalizedConstants('camper');
        return view('auth.register', compact('type', 'religions', 'programs', 'schools', 'provinces', 'education_levels'));
    }

    /**
     * Show the registration page for camp makers
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function campmaker()
    {
        if (is_null($this->organizations))
            $this->organizations = Organization::all();
        return view('auth.register', [
            'type' => $this->CAMPMAKER,
            'religions' => $this->religions,
            'provinces' => $this->provinces,
            'organizations' => $this->organizations,
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
            $data['password'] = bcrypt(array_get($data, 'password'));
            $data['activation_code'] = str_random(30).time();
            $user = app(User::class)->create($data);
            // TODO: Reenable when we should test it
            // $user->notify(new UserRegisteredSuccessfully($user));
            return $user;
        } catch (\Exception $exception) {
            logger()->error($exception);
            throw new \CampPASSException($exception->getMessage());
        }
    }

    /**
     * Register new account.
     *
     * @param \App\StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function register(StoreUserRequest $request)
    {
        try {
            $user = $this->create($request->all());
            if ($request->input('type') == $this->CAMPER)
                $user->assignRole('camper');
            else if ($request->input('type') == $this->CAMPMAKER)
                $user->assignRole('campmaker');
            event(new Registered($user));
        } catch (\Exception $exception) {
            logger()->error($exception);
            throw new \CampPASSException($exception->getMessage());
        }
        return redirect()->back()->with('message', trans ('message.NewAccountCreated'));
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
            if (!$user)
                throw new \CampPASSExpcetion(trans ('exception.CodeNotExist'));
            $user->activate();
            auth()->login($user);
        } catch (\Exception $exception) {
            logger()->error($exception);
            throw new \CampPASSException($exception->getMessage());
        }
        return redirect()->to('/');
    }
}
