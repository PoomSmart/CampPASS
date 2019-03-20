<?php

namespace App\Http\Controllers;

use App\Common;
use App\User;
use App\Religion;
use App\School;
use App\Province;
use App\Program;
use App\Organization;
use App\Badge;

use App\Http\Requests\StoreUserRequest;

use App\Enums\EducationLevel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'edit', 'update', 'my_camps']]);
        $this->organizations = null;
    }

    public function index()
    {
        return $this->show(auth()->user());
    }

    public static function authenticate(User $user, bool $me = false)
    {
        if ($me && $user->id != auth()->user()->id)
            throw new \CampPASSExceptionPermission();
        if (!$user->isActivated() && (!auth()->user() || !auth()->user()->isAdmin()))
            throw new \CampPASSException(trans('exception.AccountNotActivated'));
        if ($user->isAdmin())
            throw new \CampPASSException(trans('exception.ErrorDisplayUser'));
    }

    public function show(User $user)
    {
        $this->authenticate($user);
        $badges = $user->isCamper() ? $user->badges : null;
        return view('profiles.show', compact('user', 'badges'));
    }

    public static function edit(User $user, bool $me = true, bool $no_extra_button = false)
    {
        self::authenticate($user, $me = $me);
        $religions = Common::values(Religion::class);
        $schools = Common::values(School::class);
        $provinces = Common::values(Province::class);
        $programs = Common::values(Program::class);
        $education_levels = EducationLevel::getLocalizedConstants('year');
        $organizations = $user->isCamper() ? null : auth()->user()->isAdmin() ? Organization::all() : [ $user->organization ];
        View::share('object', $user);
        if ($no_extra_button)
            View::share('no_extra_button', true);
        return view('profiles.edit', compact('user', 'religions', 'schools', 'provinces', 'programs', 'education_levels', 'organizations'));
    }

    public function update(StoreUserRequest $request, User $user)
    {
        $this->authenticate($user, $me = true);
        $input = $request->except(User::$once);
        $user->update($input);
        $directory = Common::fileDirectory($user->id);
        if ($request->hasFile('transcript'))
            Storage::disk('local')->putFileAs($directory, $request->file('transcript'), 'transcript.pdf');
        if ($request->hasFile('confirmationletter'))
            Storage::disk('local')->putFileAs($directory, $request->file('confirmationletter'), 'confirmationletter.pdf');
        if ($request->hasFile('profile')) {
            $name = "profile.{$request->profile->getClientOriginalExtension()}";
            $user->update([
                'avatar' => $name,
            ]);
            Storage::disk('local')->putFileAs($directory, $request->file('profile'), $name);
        }
        auth()->login($user);
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function my_camps(User $user)
    {
        if (!$user->isCamper())
            throw new \CampPASSException(trans('app.UnavailableFeature'));
        $this->authenticate($user, $me = true);
        $categorized_registrations = [];
        foreach ($user->registrations as $registration) {
            $status = $registration->getStatus();
            if (!isset($categorized_registrations[$status]))
                $categorized_registrations[$status] = [];
            $categorized_registrations[$status][] = $registration;
        }
        ksort($categorized_registrations);
        return view('profiles.my_camps', compact('categorized_registrations'));
    }

    public function document_download(User $user, $type)
    {
        $directory = Common::fileDirectory($user->id);
        $path = "{$directory}/{$type}.pdf";
        return Common::downloadFile($path);
    }

    public function document_delete(User $user, $type)
    {
        $directory = Common::fileDirectory($user->id);
        $path = "{$directory}/{$type}.pdf";
        return Common::deleteFile($path);
    }

    public static function profile_picture_path(User $user, bool $actual = false, bool $display = true)
    {
        $directory = Common::fileDirectory($user->id);
        $path = "{$directory}/{$user->avatar}";
        if (Storage::disk('local')->exists($path))
            return $display ? Storage::url($path) : $path;
        return $actual ? null : asset('images/profiles/Profile_'.[ 'M', 'F' ][$user->gender % 2].'.jpg');
    }

    public function profile_picture_delete(User $user)
    {
        $path = $this->profile_picture_path($user, $actual = true, $display = false);
        if (!Storage::disk('local')->delete($path))
            throw new \CampPASSExceptionRedirectBack('The profile picture cannot be removed (or already has been removed).');
        return redirect()->back()->with('success', 'The profile picture has been removed.');
    }
}