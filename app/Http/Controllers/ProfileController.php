<?php

namespace App\Http\Controllers;

use App\Common;
use App\User;
use App\Religion;
use App\School;
use App\Province;
use App\Program;
use App\Badge;

use App\Http\Requests\StoreUserRequest;

use App\Enums\EducationLevel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ProfileController extends Controller
{
    function __construct()
    {
        $this->middleware('auth', ['only' => ['edit', 'store', 'update']]);
        $this->organizations = null;
    }

    public function index()
    {
        return $this->show(\Auth::user());
    }

    public function authenticate(User $user, $me = false)
    {
        if ($me && $user->id != \Auth::user()->id)
            throw new \CampPASSExceptionPermission();
        if (!$user->isActivated())
            throw new \CampPASSException('This account has not been activated.');
        if ($user->isAdmin())
            throw new \CampPASSException('Error displaying the user.');
    }

    public function show(User $user)
    {
        $this->authenticate($user);
        if ($user->isCamper())
            $badges = Common::values(Badge::class, 'camper_id', $user->id);
        return view('profiles.show', compact('user', 'badges'));
    }

    public function edit(User $user)
    {
        $this->authenticate($user, $me = true);
        $religions = Common::values(Religion::class);
        $schools = Common::values(School::class);
        $provinces = Common::values(Province::class);
        $programs = Common::values(Program::class);
        $education_levels = EducationLevel::getLocalizedConstants('camper');
        View::share('object', $user);
        return view('profiles.edit', compact('user', 'religions', 'schools', 'provinces', 'programs', 'education_levels'));
    }

    public function update(StoreUserRequest $request, User $user)
    {
        $camp->update($request->all());
        return redirect('profiles.index')->with('success', 'Profile updated successfully');
    }

    public function my_camps(User $user)
    {
        $camps = $user->getBelongingCamps()->latest()->get();
        return view('profiles.my_camps', compact('camps'));
    }
}
