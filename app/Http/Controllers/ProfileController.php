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
        $this->middleware('auth', ['only' => ['index', 'edit', 'update', 'my_camps']]);
        $this->organizations = null;
    }

    public function index()
    {
        return $this->show(\Auth::user());
    }

    public function authenticate(User $user, bool $me = false)
    {
        if ($me && $user->id != \Auth::user()->id)
            throw new \CampPASSExceptionPermission();
        if (!$user->isActivated() && (!\Auth::user() || !\Auth::user()->isAdmin()))
            throw new \CampPASSException('This account has not been activated.');
        if ($user->isAdmin())
            throw new \CampPASSException('Error displaying the user.');
    }

    public function show(User $user)
    {
        $this->authenticate($user);
        if ($user->isCamper())
            $badges = $user->badges;
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
        // TODO: It seems that the user will get logged out after updating their password
        $this->authenticate($user, $me = true);
        $user->update($request->all());
        if ($request->hasFile('profile_picture')) {

        }
        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function my_camps(User $user)
    {
        // TODO: Categorize camps further
        $camps = $user->getBelongingCamps()->latest()->get();
        return view('profiles.my_camps', compact('camps'));
    }
}
