<?php

namespace App\Http\Controllers;

use App\Common;
use App\User;
use App\Religion;
use App\School;
use App\Program;

use App\Http\Requests\StoreUserRequest;

use App\Enums\EducationLevel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ProfileController extends Controller
{
    function __construct()
    {
        $this->middleware('auth', ['only' => ['edit', 'store', 'update']]);
        $this->religions = Common::values(Religion::class);
        $this->organizations = null;
        $this->schools = Common::values(School::class);
        $this->programs = Common::values(Program::class);
        $this->education_levels = EducationLevel::getLocalizedConstants('camper');
    }

    public function index()
    {
        return $this->show(\Auth::user());
    }

    public function authenticate(User $user, $me = false)
    {
        if ($me && $user->id != \Auth::user()->id)
            throw new \App\Exceptions\CampPASSException();
        if (!$user->isActivated())
            throw new \App\Exceptions\CampPASSException('This account has not been activated.');
        if ($user->isAdmin())
            throw new \App\Exceptions\CampPASSException('Error displaying the user.');
    }

    public function show(User $user)
    {
        $this->authenticate($user);
        View::share('object', $user);
        return view('profiles.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authenticate($user, $me = true);
        $religions = $this->religions;
        $schools = $this->schools;
        $programs = $this->programs;
        $education_levels = $this->education_levels;
        View::share('object', $user);
        return view('profiles.edit', compact('user', 'religions', 'schools', 'programs', 'education_levels'));
    }

    public function update(StoreUserRequest $request, User $user)
    {
        $camp->update($request->all());
        return redirect('profiles.index')->with('success', 'Profile updated successfully');
    }

    public function my_camps(User $user)
    {
        $camps = $user->belonging_camps()->latest()->get();
        return view('profiles.my_camps', compact('camps'));
    }
}
