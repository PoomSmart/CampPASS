<?php

namespace App\Http\Controllers;

use App\User;
use App\Religion;
use App\School;
use App\Program;

use App\Enums\EducationLevel;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    function __construct()
    {
        $this->middleware('auth', ['only' => ['edit', 'store']]);
        $this->religions = Religion::all(['id', 'name']);
        $this->organizations = null;
        $this->schools = School::all();
        $this->programs = Program::all();
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
        return view('profiles.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authenticate($user, $me = true);
        $religions = $this->religions;
        $schools = $this->schools;
        $programs = $this->programs;
        $education_levels = $this->education_levels;
        return view('profiles.edit', compact('user', 'religions', 'schools', 'programs', 'education_levels'));
    }
}
